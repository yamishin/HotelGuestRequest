<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /* ============================================================
     |  DASHBOARD
     ============================================================ */
    public function dashboard()
    {
        $user = Auth::user();

        $myTasks = ServiceRequest::where('assigned_to', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['stay.guest', 'stay.room'])
            ->latest()->take(5)->get();

        $stats = [
            'active'          => ServiceRequest::where('assigned_to', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed_today' => ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'resolved_week'   => ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'resolved')
                ->whereBetween('resolved_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'resolved_total'  => ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'resolved')->count(),
        ];

        return view('dashboards.staff.index', compact('user', 'myTasks', 'stats'));
    }

    /* ============================================================
     |  MY TASKS
     ============================================================ */
    public function tasks(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('assigned_to', $user->id)
            ->with(['stay.guest', 'stay.room']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Show all except resolved (which goes to history)
            $query->whereIn('status', ['assigned', 'in_progress', 'completed']);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', fn($r) => $r->where('room_number', 'like', "%{$s}%"));
            });
        }

        $tasks = $query->latest()->paginate(30)->withQueryString();

        return view('dashboards.staff.tasks', compact('tasks'));
    }

    /* ============================================================
     |  START TASK (assigned → in_progress)
     ============================================================ */
    public function startTask(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($serviceRequest->assigned_to !== $user->id) {
            abort(403, 'This task is not assigned to you.');
        }

        if ($serviceRequest->status !== 'assigned') {
            return back()->with('error', 'This task cannot be started in its current state.');
        }

        $serviceRequest->startByStaff();

        return back()->with('success', 'Task started.');
    }

    /* ============================================================
     |  COMPLETE TASK (in_progress → completed)
     ============================================================ */
    public function completeTask(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($serviceRequest->assigned_to !== $user->id) {
            abort(403, 'This task is not assigned to you.');
        }

        if (!in_array($serviceRequest->status, ['in_progress', 'assigned'])) {
            return back()->with('error', 'This task cannot be completed in its current state.');
        }

        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:500',
        ]);

        $serviceRequest->completeByStaff($validated['staff_notes'] ?? null);

        return back()->with('success', 'Task marked completed. Awaiting supervisor review.');
    }

    /* ============================================================
     |  MARK COULD NOT RESOLVE
     ============================================================ */
    public function markUnresolved(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($serviceRequest->assigned_to !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $serviceRequest->markUnresolvedByStaff($validated['reason']);

        return back()->with('success', 'Supervisor has been notified that this could not be resolved.');
    }

    /* ============================================================
     |  ALL DEPARTMENT REQUESTS (peer visibility)
     ============================================================ */
    public function allRequests(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('department', $user->department)
            ->whereIn('status', ['routed', 'assigned', 'in_progress', 'completed'])
            ->with(['stay.guest', 'stay.room', 'assignee']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%");
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        return view('dashboards.staff.all_requests', compact('requests'));
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('target_role', 'staff:' . $user->id)
            ->latest()->paginate(20);
        return view('dashboards.staff.notifications', compact('notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        Notification::where('target_role', 'staff:' . $user->id)
            ->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    /* ============================================================
     |  HISTORY — my resolved tasks
     ============================================================ */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('assigned_to', $user->id)
            ->where('status', 'resolved')
            ->with(['stay.guest', 'stay.room'])
            ->latest('resolved_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%");
            });
        }

        $history = $query->paginate(15)->withQueryString();
        return view('dashboards.staff.history', compact('history'));
    }

    /* ============================================================
     |  PROFILE
     ============================================================ */
    public function profile()
    {
        return view('dashboards.staff.profile');
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);
        $user->update($validated);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);
        return back()->with('success', 'Password updated successfully.');
    }
}