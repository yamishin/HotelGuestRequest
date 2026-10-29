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
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'active'         => ServiceRequest::where('assigned_to', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed_today'=> ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'completed_week' => ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'completed_total'=> ServiceRequest::where('assigned_to', $user->id)
                ->where('status', 'completed')->count(),
        ];

        return view('dashboards.staff.index', compact('user', 'myTasks', 'stats'));
    }

        /* ============================================================
     |  MY TASKS (assigned + in_progress + completed awaiting guest)
     ============================================================ */
    public function tasks(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('assigned_to', $user->id)
            ->with(['stay.guest', 'stay.room']);

        // Show only active/awaiting/returned, not verified
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['assigned', 'in_progress', 'completed']);
            $query->where(function ($q) {
                $q->whereNull('guest_confirmed_at'); // exclude already-verified
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', function ($r) use ($s) {
                      $r->where('room_number', 'like', "%{$s}%");
                  });
            });
        }

        $tasks = $query->latest()->paginate(30)->withQueryString();

        return view('dashboards.staff.tasks', compact('tasks'));
    }

    /* ============================================================
     |  ALL DEPARTMENT REQUESTS (open requests in my dept)
     ============================================================ */
    public function allRequests(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('department', $user->department)
            ->with(['stay.guest', 'stay.room', 'assignee']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', function ($r) use ($s) {
                      $r->where('room_number', 'like', "%{$s}%");
                  });
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboards.staff.all_requests', compact('requests'));
    }

        /* ============================================================
     |  UPDATE TASK STATUS
     ============================================================ */
    public function updateTaskStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($serviceRequest->assigned_to !== $user->id) {
            abort(403, 'This task is not assigned to you.');
        }

        $validated = $request->validate([
            'status'      => 'required|in:in_progress,completed',
            'staff_notes' => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'completed') {
            $serviceRequest->markCompleted($validated['staff_notes'] ?? null);

            \App\Models\Notification::create([
                'type'        => 'request',
                'title'       => 'Task Completed',
                'message'     => "{$user->name} completed: {$serviceRequest->title} (Room {$serviceRequest->stay->room->room_number})",
                'target_role' => 'supervisor',
                'is_read'     => false,
            ]);

            return back()->with('success', 'Task marked completed. Awaiting guest confirmation.');
        }

        // in_progress
        $serviceRequest->updateStatus('in_progress');
        return back()->with('success', 'Task updated to In Progress.');
    }
    /* ============================================================
     |  NOTIFICATIONS (staff-targeted)
     ============================================================ */
    public function notifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('target_role', 'staff:' . $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('target_role', 'staff')
                  ->whereNull('id'); // never matches, just for structure
            })
            ->latest()
            ->paginate(20);

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
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /* ============================================================
     |  HISTORY — my completed tasks
     ============================================================ */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->with(['stay.guest', 'stay.room'])
            ->latest('completed_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', function ($r) use ($s) {
                      $r->where('room_number', 'like', "%{$s}%");
                  });
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

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}