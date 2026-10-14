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
     |  MY TASKS (assigned + in_progress)
     ============================================================ */
    public function tasks(Request $request)
    {
        $user = Auth::user();

        $query = ServiceRequest::where('assigned_to', $user->id)
            ->with(['stay.guest', 'stay.room']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['assigned', 'in_progress']);
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

        $tasks = $query->orderByRaw("FIELD(status, 'in_progress', 'assigned')")->latest()->paginate(15)->withQueryString();

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

        // Ensure this request is assigned to this staff member
        if ($serviceRequest->assigned_to !== $user->id) {
            abort(403, 'This task is not assigned to you.');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed',
        ]);

        // Use updateStatus() so the guest is notified automatically
        $serviceRequest->updateStatus($validated['status']);

        // Notify supervisor when completed
        if ($validated['status'] === 'completed') {
            Notification::create([
                'type'        => 'request',
                'title'       => 'Task Completed',
                'message'     => "{$user->name} completed: {$serviceRequest->title} (Room {$serviceRequest->stay->room->room_number})",
                'target_role' => 'supervisor',
                'is_read'     => false,
            ]);
        }

        $label = $validated['status'] === 'completed' ? 'completed' : 'updated';
        return back()->with('success', "Task {$label} successfully.");
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