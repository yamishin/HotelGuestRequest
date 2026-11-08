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
        $dept = auth()->user()->department;

        if ($dept === 'Front Desk') {
            $routedCount = ServiceRequest::where('status', 'pending')->count();
        } else {
            $routedCount = ServiceRequest::where('status', 'routed')->where('department', $dept)->count();
        }

        $assignedCount   = ServiceRequest::where('status', 'assigned')->where('department', $dept)->count();
        $inProgressCount = ServiceRequest::where('status', 'in_progress')->where('department', $dept)->count();
        $awaitingReview  = ServiceRequest::where('status', 'completed')->where('department', $dept)->count();

        $recentRequests = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee'])
            ->where('department', $dept)
            ->whereIn('status', ['pending', 'routed', 'assigned', 'in_progress', 'completed'])
            ->latest()->take(6)->get();

        $staffWorkload = User::where('role', 'staff')
            ->where('department', $dept)
            ->withCount(['activeRequests'])
            ->orderByDesc('active_requests_count')
            ->take(5)->get();

        return view('dashboards.supervisor.index', compact(
            'routedCount', 'assignedCount', 'inProgressCount',
            'awaitingReview', 'recentRequests', 'staffWorkload'
        ));
    }

    /* ============================================================
     |  REQUEST QUEUE
     ============================================================ */
    public function requestQueue(Request $request)
    {
        $dept = auth()->user()->department;
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'router']);

        if ($dept === 'Front Desk') {
            $query->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere(function($q2) {
                      $q2->where('department', 'Front Desk')
                         ->whereIn('status', ['routed', 'assigned', 'in_progress']);
                  });
            });
        } else {
            $query->where('department', $dept)
                  ->whereIn('status', ['routed', 'assigned', 'in_progress']);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', fn($r) => $r->where('room_number', 'like', "%{$s}%"));
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        $staff = User::where('role', 'staff')->where('department', $dept)->orderBy('name')->get();

        return view('dashboards.supervisor.request_queue', compact('requests', 'staff'));
    }

    /* ============================================================
     |  FRONT DESK: Accept / Reject / Unavailable
     ============================================================ */
    public function acceptRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if (auth()->user()->department !== 'Front Desk' || $serviceRequest->status !== 'pending') abort(403);

        $validated = $request->validate(['department' => 'required|in:Housekeeping,Maintenance,IT,Front Desk']);
        $serviceRequest->acceptByFrontDesk(auth()->id(), $validated['department']);

        return back()->with('success', "Request routed to {$validated['department']}.");
    }

    public function rejectRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if (auth()->user()->department !== 'Front Desk' || $serviceRequest->status !== 'pending') abort(403);

        $validated = $request->validate(['reason' => 'required|string|max:500']);
        $serviceRequest->rejectByFrontDesk(auth()->id(), $validated['reason']);

        return back()->with('success', 'Request rejected.');
    }

    public function markUnavailable(Request $request, ServiceRequest $serviceRequest)
    {
        if (auth()->user()->department !== 'Front Desk' || $serviceRequest->status !== 'pending') abort(403);

        $validated = $request->validate(['reason' => 'required|string|max:500']);
        $serviceRequest->markUnavailable(auth()->id(), $validated['reason']);

        return back()->with('success', 'Request marked unavailable.');
    }

    /* ============================================================
     |  ASSIGN / REASSIGN (Fixed 403 Logic)
     ============================================================ */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        // Allow if user is Front Desk OR if department matches
        if (auth()->user()->department !== 'Front Desk' && $serviceRequest->department !== auth()->user()->department) {
            abort(403, 'Unauthorized department assignment.');
        }

        $validated = $request->validate(['assigned_to' => 'required|exists:users,id']);
        $staff = User::findOrFail($validated['assigned_to']);

        $serviceRequest->assignToStaff($staff->id);

        return back()->with('success', "Request assigned to {$staff->name}.");
    }

    public function reassign(Request $request, ServiceRequest $serviceRequest)
    {
        if (auth()->user()->department !== 'Front Desk' && $serviceRequest->department !== auth()->user()->department) {
            abort(403, 'Unauthorized department reassignment.');
        }

        $validated = $request->validate(['assigned_to' => 'required|exists:users,id']);
        $staff = User::findOrFail($validated['assigned_to']);

        $serviceRequest->reassignTo($staff->id, auth()->id());

        return back()->with('success', "Request reassigned to {$staff->name}.");
    }

    /* ============================================================
     |  PROXY STATUS UPDATE (Fixed 403 Logic)
     ============================================================ */
    public function proxyUpdateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        if (auth()->user()->department !== 'Front Desk' && $serviceRequest->department !== auth()->user()->department) {
            abort(403, 'Unauthorized proxy update.');
        }

        $validated = $request->validate(['status' => 'required|in:in_progress,completed']);

        if ($validated['status'] === 'in_progress') {
            $serviceRequest->update(['status' => 'in_progress']);
        } else {
            $serviceRequest->completeByStaff('Updated via Supervisor.');
        }

        return back()->with('success', "Task marked as " . str_replace('_', ' ', $validated['status']) . ".");
    }

    /* ============================================================
     |  AWAITING REVIEW
     ============================================================ */
    public function awaitingReview(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee'])
            ->where('department', auth()->user()->department)
            ->where('status', 'completed')
            ->latest('completed_at');

        $requests = $query->paginate(20)->withQueryString();

        return view('dashboards.supervisor.awaiting_review', compact('requests'));
    }

    public function confirmResolved(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->department !== auth()->user()->department || $serviceRequest->status !== 'completed') abort(403);

        $serviceRequest->confirmResolved(auth()->id());
        return back()->with('success', 'Request resolved.');
    }

    public function markUnresolved(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->department !== auth()->user()->department || $serviceRequest->status !== 'completed') abort(403);

        $validated = $request->validate(['reason' => 'required|string|max:500']);
        $serviceRequest->markUnresolvedBySupervisor($validated['reason']);

        return back()->with('success', 'Request sent back to staff.');
    }

    /* ============================================================
     |  STAFF MANAGEMENT
     ============================================================ */
    public function staff()
    {
        $dept = auth()->user()->department;
        
        $staff = User::where('role', 'staff')
            ->where('department', $dept)
            ->withCount(['activeRequests', 'assignedRequests as total_requests_count'])
            ->orderBy('name')->get();

        $staffByDepartment = $staff->groupBy('department');
        $totalStaff    = $staff->count();
        $activeStaff   = $staff->where('status', 'active')->count();
        $inactiveStaff = $staff->where('status', 'inactive')->count();
        $busyStaff     = $staff->where('active_requests_count', '>', 2)->count();

        return view('dashboards.supervisor.staff', compact('staffByDepartment', 'totalStaff', 'activeStaff', 'inactiveStaff', 'busyStaff'));
    }

    public function staffCreate()
    {
        return view('dashboards.supervisor.staff_create', ['department' => auth()->user()->department]);
    }

    public function staffStore(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'shift' => 'required|in:morning,evening,night',
        ]);

        $dept = auth()->user()->department;
        
        $lastEmployee = User::whereNotNull('employee_code')->orderByDesc('id')->first();
        $nextNumber = $lastEmployee && preg_match('/EMP-(\d+)/', $lastEmployee->employee_code, $m) ? (int) $m[1] + 1 : 1;
        
        $staff = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make('password'),
            'phone'         => $validated['phone'] ?? null,
            'role'          => 'staff',
            'department'    => $dept,
            'employee_code' => 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
            'hire_date'     => now(),
            'shift'         => $validated['shift'],
            'status'        => 'active',
        ]);

        return redirect()->route('supervisor.staff')
            ->with('success', "Staff member {$staff->name} added to {$dept}.")
            ->with('new_staff_credentials', [
                'name' => $staff->name, 'email' => $staff->email,
                'employee_code' => $staff->employee_code, 'department' => $staff->department, 'password' => 'password',
            ]);
    }

    public function staffShow(User $user)
    {
        if ($user->role !== 'staff' || $user->department !== auth()->user()->department) abort(404);

        $activeRequests = $user->assignedRequests()->whereIn('status', ['assigned', 'in_progress'])->with(['stay.room', 'stay.guest'])->latest()->get();
        $completedThisWeek = $user->assignedRequests()->where('status', 'completed')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $completedAllTime = $user->assignedRequests()->whereIn('status', ['completed', 'resolved'])->count();
        $totalAssigned = $user->assignedRequests()->count();

        return view('dashboards.supervisor.staff_show', compact('user', 'activeRequests', 'completedThisWeek', 'completedAllTime', 'totalAssigned'));
    }

    public function staffEdit(User $user)
    {
        if ($user->role !== 'staff' || $user->department !== auth()->user()->department) abort(404);
        return view('dashboards.supervisor.staff_edit', compact('user'));
    }

    public function staffUpdate(Request $request, User $user)
    {
        if ($user->role !== 'staff' || $user->department !== auth()->user()->department) abort(404);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'shift' => 'required|in:morning,evening,night',
        ]);

        $user->update($validated);
        return redirect()->route('supervisor.staff.show', $user)->with('success', 'Staff updated.');
    }

    public function staffToggleStatus(User $user)
    {
        if ($user->role !== 'staff' || $user->department !== auth()->user()->department) abort(404);
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);
        return back()->with('success', "{$user->name} has been " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".");
    }

    public function staffResetPassword(User $user)
    {
        if ($user->role !== 'staff' || $user->department !== auth()->user()->department) abort(404);
        $user->update(['password' => Hash::make('password')]);
        return back()->with('success', "Password reset.")->with('new_staff_credentials', [
            'name' => $user->name, 'email' => $user->email, 'employee_code' => $user->employee_code, 'department' => $user->department, 'password' => 'password',
        ]);
    }

    /* ============================================================
     |  REPORTS & HISTORY
     ============================================================ */
    public function history(Request $request)
    {
        $dept = auth()->user()->department;
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'resolver'])
            ->where('department', $dept)
            ->whereIn('status', ['resolved', 'rejected', 'unavailable']);

        $statusFilter = $request->get('status', 'resolved');
        if (in_array($statusFilter, ['resolved', 'rejected', 'unavailable'])) {
            $query->where('status', $statusFilter);
        }

        $history = $query->latest('updated_at')->paginate(15)->withQueryString();
        $counts = [
            'resolved' => ServiceRequest::where('department', $dept)->where('status', 'resolved')->count(),
            'rejected' => ServiceRequest::where('department', $dept)->where('status', 'rejected')->count(),
            'unavailable' => ServiceRequest::where('department', $dept)->where('status', 'unavailable')->count(),
        ];

        return view('dashboards.supervisor.history', compact('history', 'counts'));
    }

    public function reports()
    {
        $dept = auth()->user()->department;
        
        $totalRequests = ServiceRequest::where('department', $dept)->count();
        $resolvedRequests = ServiceRequest::where('department', $dept)->where('status', 'resolved')->count();
        
        $avgResolutionTime = ServiceRequest::where('department', $dept)
            ->where('status', 'resolved')
            ->whereNotNull('completed_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 60) as avg_time')
            ->value('avg_time');

        $topServices = ServiceRequest::where('department', $dept)
            ->selectRaw('title, COUNT(*) as count')
            ->groupBy('title')->orderByDesc('count')->take(5)->get();

        return view('dashboards.supervisor.reports', compact('totalRequests', 'resolvedRequests', 'avgResolutionTime', 'topServices', 'dept'));
    }

    /* ============================================================
     |  NOTIFICATIONS & PROFILE
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'supervisor')->latest()->paginate(20);
        return view('dashboards.supervisor.notifications', compact('notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllNotificationsRead()
    {
        Notification::where('target_role', 'supervisor')->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function profile() { return view('dashboards.supervisor.profile'); }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30']);
        $user->update($validated);
        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate(['current_password' => 'required', 'new_password' => 'required|string|min:6|confirmed']);
        if (!Hash::check($validated['current_password'], $user->password)) return back()->withErrors(['current_password' => 'Incorrect password.']);
        $user->update(['password' => Hash::make($validated['new_password'])]);
        return back()->with('success', 'Password updated.');
    }

    /* ============================================================
     |  FRONT DESK: Guest & Check-In Management
     ============================================================ */
    
    public function checkInForm()
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);
        $availableRooms = \App\Models\Room::where('status', 'available')->orderBy('floor')->orderBy('room_number')->get();
        return view('dashboards.frontdesk.check_in', compact('availableRooms'));
    }

    public function checkInStore(Request $request)
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);

        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'nullable|email|max:150',
            'phone'            => 'nullable|string|max:30',
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date',
            'check_out_date'   => 'required|date|after:check_in_date',
            'num_guests'       => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string',
        ]);

        $lastGuestCode = \App\Models\Guest::selectRaw("MAX(CAST(SUBSTR(guest_code, 3) AS INTEGER)) as max_num")->value('max_num');
        $nextGuestNumber = ($lastGuestCode ?? 0) + 1;
        do {
            $newGuestCode = 'G-' . str_pad($nextGuestNumber, 4, '0', STR_PAD_LEFT);
            $exists = \App\Models\Guest::where('guest_code', $newGuestCode)->exists();
            if ($exists) $nextGuestNumber++;
        } while ($exists);

        $guest = \App\Models\Guest::create([
            'guest_code' => $newGuestCode,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ]);

        $stay = \App\Models\Stay::create([
            'guest_id'         => $guest->id,
            'room_id'          => $validated['room_id'],
            'check_in_date'    => $validated['check_in_date'],
            'check_out_date'   => $validated['check_out_date'],
            'actual_check_in'  => now(),
            'status'           => 'active',
            'access_token'     => \Illuminate\Support\Str::random(32),
            'num_guests'       => $validated['num_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        \App\Models\Room::find($validated['room_id'])->update(['status' => 'occupied']);

        Notification::create([
            'type'        => 'check_in',
            'title'       => 'Check-in',
            'message'     => "{$guest->full_name} has checked in to Room {$stay->room->room_number}",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        return redirect()->route('supervisor.check_in.qr', $stay)
            ->with('success', "Guest {$guest->full_name} checked in. Print the QR below.");
    }

    public function showQr(\App\Models\Stay $stay)
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);
        $stay->load(['guest', 'room']);
        
        // ✅ FIXED: Dynamically uses the Render URL
        $accessUrl = url('/g/' . $stay->access_token);

        return view('dashboards.frontdesk.qr_code', compact('stay', 'accessUrl'));
    }

    public function guestsStays(Request $request)
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);
        
        $query = \App\Models\Guest::with(['stays.room']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        $guests = $query->latest()->paginate(15)->withQueryString();
        return view('dashboards.frontdesk.guests_stays', compact('guests'));
    }

    public function rooms()
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);
        
        $rooms = \App\Models\Room::with('currentStay.guest')->orderBy('floor')->orderBy('room_number')->get();
        $roomsByFloor = $rooms->groupBy('floor');
        $roomCounts = [
            'available'   => \App\Models\Room::where('status', 'available')->count(),
            'occupied'    => \App\Models\Room::where('status', 'occupied')->count(),
            'cleaning'    => \App\Models\Room::where('status', 'cleaning')->count(),
            'maintenance' => \App\Models\Room::where('status', 'maintenance')->count(),
        ];
        return view('dashboards.frontdesk.rooms', compact('roomsByFloor', 'roomCounts'));
    }

    public function guestDetail(\App\Models\Guest $guest)
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);
        $guest->load(['stays.room', 'stays.serviceRequests', 'reviews']);
        return view('dashboards.frontdesk.guest_detail', compact('guest'));
    }

    public function checkOut(\App\Models\Stay $stay)
    {
        if (auth()->user()->department !== 'Front Desk') abort(403);

        if ($stay->status !== 'active') {
            return back()->with('error', 'This stay is not active.');
        }

        $stay->update(['status' => 'completed', 'actual_check_out' => now()]);
        $stay->room->update(['status' => 'cleaning']);

        $lastCode = ServiceRequest::selectRaw("MAX(CAST(SUBSTR(request_code, 5) AS INTEGER)) as max_num")->value('max_num');
        $nextNumber = ($lastCode ?? 0) + 1;
        do {
            $newCode = 'REQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = ServiceRequest::where('request_code', $newCode)->exists();
            if ($exists) $nextNumber++;
        } while ($exists);

        ServiceRequest::create([
            'request_code' => $newCode,
            'stay_id'      => $stay->id,
            'title'        => 'Room Cleaning & Turnover',
            'description'  => "Guest checked out. Room {$stay->room->room_number} needs to be cleaned.",
            'department'   => 'Housekeeping',
            'status'       => 'routed',
            'routed_by'    => auth()->id(),
            'routed_at'    => now(),
        ]);

        Notification::create([
            'type'        => 'check_out',
            'title'       => 'Check-out',
            'message'     => "{$stay->guest->full_name} checked out. Cleaning request sent.",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        return back()->with('success', "Guest checked out. Cleaning request sent to Housekeeping.");
    }

    public function printTicket(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['stay.room', 'stay.guest', 'assignee']);
        return view('dashboards.supervisor.print_ticket', ['request' => $serviceRequest]);
    }
}