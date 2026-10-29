<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    /* ============================================================
     |  DASHBOARD — overview
     ============================================================ */
    public function dashboard()
    {
        $pendingCount    = ServiceRequest::where('status', 'pending')->count();
        $assignedCount   = ServiceRequest::where('status', 'assigned')->count();
        $inProgressCount = ServiceRequest::where('status', 'in_progress')->count();
        $completedToday  = ServiceRequest::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        // Recent requests (last 6, any status)
        $recentRequests = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee'])
            ->latest()
            ->take(6)
            ->get();

        // Active staff with workload counts
        $staffWorkload = User::where('role', 'staff')
            ->withCount(['activeRequests'])
            ->orderByDesc('active_requests_count')
            ->take(5)
            ->get();

        return view('dashboards.supervisor.index', compact(
            'pendingCount',
            'assignedCount',
            'inProgressCount',
            'completedToday',
            'recentRequests',
            'staffWorkload'
        ));
    }

    /* ============================================================
     |  REQUEST QUEUE — pending + assigned + in_progress
     ============================================================ */
    public function requestQueue(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default: show only open requests
            $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
        }

        // Filter by department
        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
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

        // Available staff list for assignment
        $staff = User::where('role', 'staff')
            ->orderBy('department')
            ->orderBy('name')
            ->get();

        return view('dashboards.supervisor.request_queue', compact('requests', 'staff'));
    }

    /* ============================================================
     |  ASSIGN REQUEST TO STAFF
     ============================================================ */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        /** @var User $staff */
        $staff = User::findOrFail($validated['assigned_to']);

        // Update request with assignee
        $serviceRequest->update([
            'assigned_to' => $staff->id,
        ]);

        // Update status to 'assigned' + auto-notifies the guest
        $serviceRequest->updateStatus('assigned');

        // Notify the staff member
        Notification::create([
            'type'        => 'request',
            'title'       => 'New Task Assigned',
            'message'     => "Room {$serviceRequest->stay->room->room_number} — {$serviceRequest->title}",
            'target_role' => 'staff:' . $staff->id,
            'is_read'     => false,
        ]);

        return back()->with('success', "Request assigned to {$staff->name}.");
    }

    /* ============================================================
     |  UNASSIGN REQUEST
     ============================================================ */
    public function unassign(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'assigned_to' => null,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Request unassigned and returned to queue.');
    }

    /* ============================================================
     |  STAFF LIST
     ============================================================ */
    public function staff()
    {
        $staff = User::where('role', 'staff')
            ->withCount([
                'activeRequests',
                'assignedRequests as total_requests_count',
            ])
            ->orderBy('department')
            ->orderBy('name')
            ->get();

        // Group by department
        $staffByDepartment = $staff->groupBy('department');

        // Counters for stat cards
        $totalStaff    = $staff->count();
        $activeStaff   = $staff->where('status', 'active')->count();
        $inactiveStaff = $staff->where('status', 'inactive')->count();
        $busyStaff     = $staff->where('active_requests_count', '>', 2)->count();

        return view('dashboards.supervisor.staff', compact(
            'staffByDepartment',
            'totalStaff',
            'activeStaff',
            'inactiveStaff',
            'busyStaff'
        ));
    }

    /* ============================================================
     |  HISTORY — verified (completed & confirmed) + cancelled
     ============================================================ */
    public function history(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee']);

        // Default: show only verified + cancelled
        $statusFilter = $request->get('status', 'verified');

        if ($statusFilter === 'verified') {
            $query->where('status', 'verified');
        } elseif ($statusFilter === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($statusFilter === 'awaiting') {
            $query->where('status', 'completed')->whereNull('guest_confirmed_at');
        } else {
            // 'all' — verified + cancelled
            $query->whereIn('status', ['verified', 'cancelled']);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', function ($r) use ($s) {
                      $r->where('room_number', 'like', "%{$s}%");
                  })
                  ->orWhereHas('stay.guest', function ($g) use ($s) {
                      $g->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%");
                  });
            });
        }

        $history = $query->latest('updated_at')->paginate(15)->withQueryString();

        // Counts for filter tabs
        $counts = [
            'verified' => ServiceRequest::where('status', 'verified')->count(),
            'awaiting' => ServiceRequest::where('status', 'completed')->whereNull('guest_confirmed_at')->count(),
            'cancelled'=> ServiceRequest::where('status', 'cancelled')->count(),
        ];

        return view('dashboards.supervisor.history', compact('history', 'counts'));
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'supervisor')
            ->latest()
            ->paginate(20);

        return view('dashboards.supervisor.notifications', compact('notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllNotificationsRead()
    {
        Notification::where('target_role', 'supervisor')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /* ============================================================
     |  PROFILE
     ============================================================ */
    public function profile()
    {
        return view('dashboards.supervisor.profile');
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
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
        /** @var User $user */
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

    /* ============================================================
     |  STAFF — Add form
     ============================================================ */
    public function staffCreate()
    {
        return view('dashboards.supervisor.staff_create');
    }

    /* ============================================================
     |  STAFF — Store new staff member (default password: "password")
     ============================================================ */
    public function staffStore(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:30',
            'department' => 'required|in:Housekeeping,Maintenance,IT,Front Desk',
            'shift'      => 'required|in:morning,evening,night',
            'hire_date'  => 'required|date',
        ]);

        // Generate employee code
        $lastEmployee = User::whereNotNull('employee_code')
            ->orderByDesc('id')
            ->first();
        $nextNumber = 1;
        if ($lastEmployee && preg_match('/EMP-(\d+)/', $lastEmployee->employee_code, $m)) {
            $nextNumber = (int) $m[1] + 1;
        }
        $employeeCode = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Default password for all new staff members
        $plainPassword = 'password';

        $staff = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($plainPassword),
            'phone'         => $validated['phone'] ?? null,
            'role'          => 'staff',
            'department'    => $validated['department'],
            'employee_code' => $employeeCode,
            'hire_date'     => $validated['hire_date'],
            'shift'         => $validated['shift'],
            'status'        => 'active',
        ]);

        // Redirect back to staff page with credentials to display
        return redirect()
            ->route('supervisor.staff')
            ->with('success', "Staff member {$staff->name} added successfully.")
            ->with('new_staff_credentials', [
                'name'          => $staff->name,
                'email'         => $staff->email,
                'employee_code' => $staff->employee_code,
                'department'    => $staff->department,
                'password'      => $plainPassword,
            ]);
    }

    /* ============================================================
     |  STAFF — View profile
     ============================================================ */
    public function staffShow(User $user)
    {
        if ($user->role !== 'staff') {
            abort(404, 'Staff member not found.');
        }

        $user->load(['assignedRequests.stay.room', 'assignedRequests.stay.guest']);

        $activeRequests = $user->assignedRequests()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['stay.room', 'stay.guest'])
            ->latest()
            ->get();

        $completedThisWeek = $user->assignedRequests()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $completedAllTime = $user->assignedRequests()
            ->where('status', 'completed')
            ->count();

        $totalAssigned = $user->assignedRequests()->count();

        return view('dashboards.supervisor.staff_show', compact(
            'user',
            'activeRequests',
            'completedThisWeek',
            'completedAllTime',
            'totalAssigned'
        ));
    }

    /* ============================================================
     |  STAFF — Edit form
     ============================================================ */
    public function staffEdit(User $user)
    {
        if ($user->role !== 'staff') {
            abort(404, 'Staff member not found.');
        }

        return view('dashboards.supervisor.staff_edit', compact('user'));
    }

    /* ============================================================
     |  STAFF — Update
     ============================================================ */
    public function staffUpdate(Request $request, User $user)
    {
        if ($user->role !== 'staff') {
            abort(404, 'Staff member not found.');
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:30',
            'department' => 'required|in:Housekeeping,Maintenance,IT,Front Desk',
            'shift'      => 'required|in:morning,evening,night',
            'hire_date'  => 'required|date',
        ]);

        $user->update($validated);

        return redirect()
            ->route('supervisor.staff.show', $user)
            ->with('success', 'Staff information updated successfully.');
    }

    /* ============================================================
     |  STAFF — Toggle status (active ↔ inactive)
     ============================================================ */
    public function staffToggleStatus(User $user)
    {
        if ($user->role !== 'staff') {
            abort(404, 'Staff member not found.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "{$user->name} has been {$label}.");
    }

    /* ============================================================
     |  STAFF — Reset password (default: "password")
     ============================================================ */
    public function staffResetPassword(User $user)
    {
        if ($user->role !== 'staff') {
            abort(404, 'Staff member not found.');
        }

        // Reset to default password
        $plainPassword = 'password';

        $user->update([
            'password' => Hash::make($plainPassword),
        ]);

        return back()
            ->with('success', "Password reset for {$user->name}.")
            ->with('new_staff_credentials', [
                'name'          => $user->name,
                'email'         => $user->email,
                'employee_code' => $user->employee_code,
                'department'    => $user->department,
                'password'      => $plainPassword,
            ]);
    }
}