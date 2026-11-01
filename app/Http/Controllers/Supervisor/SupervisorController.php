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
     |  DASHBOARD
     ============================================================ */
    public function dashboard()
    {
        $routedCount       = ServiceRequest::where('status', 'routed')->count();
        $assignedCount     = ServiceRequest::where('status', 'assigned')->count();
        $inProgressCount   = ServiceRequest::where('status', 'in_progress')->count();
        $awaitingReview    = ServiceRequest::where('status', 'completed')->count();
        $resolvedToday     = ServiceRequest::where('status', 'resolved')
            ->whereDate('resolved_at', today())->count();

        $recentRequests = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee'])
            ->whereIn('status', ['routed', 'assigned', 'in_progress', 'completed'])
            ->latest()->take(6)->get();

        $staffWorkload = User::where('role', 'staff')
            ->withCount(['activeRequests'])
            ->orderByDesc('active_requests_count')
            ->take(5)->get();

        return view('dashboards.supervisor.index', compact(
            'routedCount', 'assignedCount', 'inProgressCount',
            'awaitingReview', 'resolvedToday',
            'recentRequests', 'staffWorkload'
        ));
    }

    /* ============================================================
     |  REQUEST QUEUE — only ROUTED requests (Front Desk accepted)
     ============================================================ */
    public function requestQueue(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'router'])
            ->whereIn('status', ['routed', 'assigned', 'in_progress'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', fn($r) => $r->where('room_number', 'like', "%{$s}%"));
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $staff = User::where('role', 'staff')->orderBy('department')->orderBy('name')->get();

        return view('dashboards.supervisor.request_queue', compact('requests', 'staff'));
    }

    /* ============================================================
     |  AWAITING REVIEW — staff marked completed, supervisor confirms
     ============================================================ */
    public function awaitingReview(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee'])
            ->where('status', 'completed')
            ->latest('completed_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%");
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('dashboards.supervisor.awaiting_review', compact('requests'));
    }

    /* ============================================================
     |  ASSIGN to staff
     ============================================================ */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        if (!in_array($serviceRequest->status, ['routed', 'assigned', 'in_progress'])) {
            return back()->with('error', 'This request cannot be assigned in its current status.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $staff = User::findOrFail($validated['assigned_to']);
        $serviceRequest->assignToStaff($staff->id);

        return back()->with('success', "Request assigned to {$staff->name}.");
    }

    /* ============================================================
     |  REASSIGN to a different staff
     ============================================================ */
    public function reassign(Request $request, ServiceRequest $serviceRequest)
    {
        if (!in_array($serviceRequest->status, ['assigned', 'in_progress', 'completed'])) {
            return back()->with('error', 'This request cannot be reassigned.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $staff = User::findOrFail($validated['assigned_to']);
        $serviceRequest->reassignTo($staff->id, auth()->id());

        return back()->with('success', "Request reassigned to {$staff->name}.");
    }

    /* ============================================================
     |  CONFIRM RESOLVED
     ============================================================ */
    public function confirmResolved(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'completed') {
            return back()->with('error', 'Only completed requests can be confirmed.');
        }

        $serviceRequest->confirmResolved(auth()->id());

        return back()->with('success', 'Request resolved. Guest has been notified.');
    }

    /* ============================================================
     |  MARK UNRESOLVED (send back to staff)
     ============================================================ */
    public function markUnresolved(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'completed') {
            return back()->with('error', 'Only completed requests can be marked unresolved.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $serviceRequest->markUnresolvedBySupervisor($validated['reason']);

        return back()->with('success', 'Request sent back to staff for follow-up.');
    }

    /* ============================================================
     |  UNASSIGN (return to queue)
     ============================================================ */
    public function unassign(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'assigned_to' => null,
            'status'      => 'routed',
        ]);

        return back()->with('success', 'Request unassigned and returned to queue.');
    }

    /* ============================================================
     |  STAFF LIST
     ============================================================ */
    public function staff()
    {
        $staff = User::where('role', 'staff')
            ->withCount(['activeRequests', 'assignedRequests as total_requests_count'])
            ->orderBy('department')->orderBy('name')->get();

        $staffByDepartment = $staff->groupBy('department');

        $totalStaff    = $staff->count();
        $activeStaff   = $staff->where('status', 'active')->count();
        $inactiveStaff = $staff->where('status', 'inactive')->count();
        $busyStaff     = $staff->where('active_requests_count', '>', 2)->count();

        return view('dashboards.supervisor.staff', compact(
            'staffByDepartment', 'totalStaff', 'activeStaff',
            'inactiveStaff', 'busyStaff'
        ));
    }

    /* ============================================================
     |  HISTORY — resolved + rejected + unavailable
     ============================================================ */
    public function history(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'resolver']);
        $statusFilter = $request->get('status', 'resolved');

        if (in_array($statusFilter, ['resolved', 'rejected', 'unavailable'])) {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['resolved', 'rejected', 'unavailable']);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.guest', function ($g) use ($s) {
                      $g->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%");
                  });
            });
        }

        $history = $query->latest('updated_at')->paginate(15)->withQueryString();

        $counts = [
            'resolved'    => ServiceRequest::where('status', 'resolved')->count(),
            'rejected'    => ServiceRequest::where('status', 'rejected')->count(),
            'unavailable' => ServiceRequest::where('status', 'unavailable')->count(),
        ];

        return view('dashboards.supervisor.history', compact('history', 'counts'));
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'supervisor')
            ->latest()->paginate(20);
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
            ->where('is_read', false)->update(['is_read' => true]);
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

        $user->update(['password' => Hash::make($validated['new_password'])]);
        return back()->with('success', 'Password updated successfully.');
    }

    /* ============================================================
     |  STAFF MANAGEMENT
     ============================================================ */
    public function staffCreate()
    {
        return view('dashboards.supervisor.staff_create');
    }

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

        $lastEmployee = User::whereNotNull('employee_code')->orderByDesc('id')->first();
        $nextNumber = 1;
        if ($lastEmployee && preg_match('/EMP-(\d+)/', $lastEmployee->employee_code, $m)) {
            $nextNumber = (int) $m[1] + 1;
        }
        $employeeCode = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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

        return redirect()->route('supervisor.staff')
            ->with('success', "Staff member {$staff->name} added successfully.")
            ->with('new_staff_credentials', [
                'name'          => $staff->name,
                'email'         => $staff->email,
                'employee_code' => $staff->employee_code,
                'department'    => $staff->department,
                'password'      => $plainPassword,
            ]);
    }

    public function staffShow(User $user)
    {
        if ($user->role !== 'staff') abort(404);

        $activeRequests = $user->assignedRequests()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['stay.room', 'stay.guest'])->latest()->get();

        $completedThisWeek = $user->assignedRequests()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $completedAllTime = $user->assignedRequests()
            ->whereIn('status', ['completed', 'resolved'])->count();

        $totalAssigned = $user->assignedRequests()->count();

        return view('dashboards.supervisor.staff_show', compact(
            'user', 'activeRequests', 'completedThisWeek',
            'completedAllTime', 'totalAssigned'
        ));
    }

    public function staffEdit(User $user)
    {
        if ($user->role !== 'staff') abort(404);
        return view('dashboards.supervisor.staff_edit', compact('user'));
    }

    public function staffUpdate(Request $request, User $user)
    {
        if ($user->role !== 'staff') abort(404);

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:30',
            'department' => 'required|in:Housekeeping,Maintenance,IT,Front Desk',
            'shift'      => 'required|in:morning,evening,night',
            'hire_date'  => 'required|date',
        ]);

        $user->update($validated);

        return redirect()->route('supervisor.staff.show', $user)
            ->with('success', 'Staff information updated.');
    }

    public function staffToggleStatus(User $user)
    {
        if ($user->role !== 'staff') abort(404);

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "{$user->name} has been {$label}.");
    }

    public function staffResetPassword(User $user)
    {
        if ($user->role !== 'staff') abort(404);

        $plainPassword = 'password';
        $user->update(['password' => Hash::make($plainPassword)]);

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