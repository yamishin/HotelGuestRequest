<?php

namespace App\Http\Controllers\FrontDesk;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Notification;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Models\Stay;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FrontDeskController extends Controller
{
    /* ============================================================
     |  DASHBOARD
     ============================================================ */
    public function dashboard()
    {
        $today = now()->toDateString();

        $checkInsToday     = Stay::whereDate('check_in_date', $today)->count();
        $checkOutsToday    = Stay::whereDate('check_out_date', $today)->count();
        $pendingInbox      = ServiceRequest::where('status', 'pending')->count();
        $activeStays       = Stay::where('status', 'active')->count();
        $totalRooms        = Room::count();

        $roomCounts = [
            'available'   => Room::where('status', 'available')->count(),
            'occupied'    => Room::where('status', 'occupied')->count(),
            'cleaning'    => Room::where('status', 'cleaning')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        $sampleRooms = Room::where('floor', 5)->orderBy('room_number')->get();

        $recentActivity = Notification::where('target_role', 'frontdesk')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.frontdesk.index', compact(
            'checkInsToday',
            'checkOutsToday',
            'pendingInbox',
            'activeStays',
            'totalRooms',
            'roomCounts',
            'sampleRooms',
            'recentActivity'
        ));
    }

    /* ============================================================
     |  REQUEST INBOX — pending requests awaiting Front Desk review
     ============================================================ */
    public function inbox(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room'])
            ->where('status', 'pending')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('request_code', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', fn($r) => $r->where('room_number', 'like', "%{$s}%"))
                  ->orWhereHas('stay.guest', function ($g) use ($s) {
                      $g->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%");
                  });
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        $pendingCount = ServiceRequest::where('status', 'pending')->count();

        return view('dashboards.frontdesk.inbox', compact('requests', 'pendingCount'));
    }

    /* ============================================================
     |  MONITOR — all routed / in progress / completed
     ============================================================ */
    public function monitor(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'router'])
            ->whereIn('status', ['routed', 'assigned', 'in_progress', 'completed'])
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

        $requests = $query->paginate(20)->withQueryString();

        $counts = [
            'routed'      => ServiceRequest::where('status', 'routed')->count(),
            'assigned'    => ServiceRequest::where('status', 'assigned')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed'   => ServiceRequest::where('status', 'completed')->count(),
        ];

        return view('dashboards.frontdesk.monitor', compact('requests', 'counts'));
    }

    /* ============================================================
     |  ACCEPT + ROUTE
     ============================================================ */
    public function acceptRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'department' => 'required|in:Housekeeping,Maintenance,IT,Front Desk',
        ]);

        $serviceRequest->acceptByFrontDesk(auth()->id(), $validated['department']);

        return back()->with('success', "Request accepted and routed to {$validated['department']}.");
    }

    /* ============================================================
     |  REJECT
     ============================================================ */
    public function rejectRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $serviceRequest->rejectByFrontDesk(auth()->id(), $validated['reason']);

        return back()->with('success', 'Request rejected. Guest has been notified.');
    }

    /* ============================================================
     |  MARK UNAVAILABLE
     ============================================================ */
    public function markUnavailable(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $serviceRequest->markUnavailable(auth()->id(), $validated['reason']);

        return back()->with('success', 'Request marked unavailable. Guest has been notified.');
    }

    /* ============================================================
     |  GUESTS & STAYS
     ============================================================ */
    public function guestsStays(Request $request)
    {
        $query = Guest::with(['stays.room']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('guest_code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('filter') && $request->filter !== 'all') {
            $filter = $request->filter;
            $query->whereHas('stays', fn($q) => $q->where('status', $filter));
        }

        $guests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboards.frontdesk.guests_stays', compact('guests'));
    }

    public function guestDetail(Guest $guest)
    {
        $guest->load(['stays.room', 'stays.serviceRequests', 'reviews']);
        return view('dashboards.frontdesk.guest_detail', compact('guest'));
    }

    /* ============================================================
     |  CHECK-IN / CHECK-OUT
     ============================================================ */
    public function checkInForm()
    {
        $availableRooms = Room::where('status', 'available')
            ->orderBy('floor')->orderBy('room_number')->get();

        return view('dashboards.frontdesk.check_in', compact('availableRooms'));
    }

    public function checkInStore(Request $request)
    {
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

        // Unique guest code
        $lastGuestCode = Guest::selectRaw("MAX(CAST(SUBSTR(guest_code, 3) AS INTEGER)) as max_num")->value('max_num');
        $nextGuestNumber = ($lastGuestCode ?? 0) + 1;
        do {
            $newGuestCode = 'G-' . str_pad($nextGuestNumber, 4, '0', STR_PAD_LEFT);
            $exists = Guest::where('guest_code', $newGuestCode)->exists();
            if ($exists) $nextGuestNumber++;
        } while ($exists);

        $guest = Guest::create([
            'guest_code' => $newGuestCode,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ]);

        $stay = Stay::create([
            'guest_id'         => $guest->id,
            'room_id'          => $validated['room_id'],
            'check_in_date'    => $validated['check_in_date'],
            'check_out_date'   => $validated['check_out_date'],
            'actual_check_in'  => now(),
            'status'           => 'active',
            'access_token'     => Str::random(32),
            'num_guests'       => $validated['num_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        Room::find($validated['room_id'])->update(['status' => 'occupied']);

        Notification::create([
            'type'        => 'check_in',
            'title'       => 'Check-in',
            'message'     => "{$guest->full_name} has checked in to Room {$stay->room->room_number}",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        return redirect()
            ->route('frontdesk.qr', $stay)
            ->with('success', "Guest {$guest->full_name} checked in. Print the QR below.");
    }

        public function checkOut(Stay $stay)
    {
        if ($stay->status !== 'active') {
            return back()->with('error', 'This stay is not active.');
        }

        // 1. Complete the stay
        $stay->update(['status' => 'completed', 'actual_check_out' => now()]);

        // 2. Set room to cleaning
        $stay->room->update(['status' => 'cleaning']);

        // 3. AUTO-GENERATE A HOUSEKEEPING REQUEST
        // Generate request code
        $lastCode = ServiceRequest::selectRaw("MAX(CAST(SUBSTR(request_code, 5) AS INTEGER)) as max_num")->value('max_num');
        $nextNumber = ($lastCode ?? 0) + 1;
        do {
            $newCode = 'REQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = ServiceRequest::where('request_code', $newCode)->exists();
            if ($exists) $nextNumber++;
        } while ($exists);

        ServiceRequest::create([
            'request_code' => $newCode,
            'stay_id'      => $stay->id, // Linked to the checked-out stay
            'title'        => 'Room Cleaning & Turnover',
            'description'  => "Guest checked out. Room {$stay->room->room_number} needs to be cleaned before the next guest.",
            'department'   => 'Housekeeping',
            'status'       => 'routed', // Skips Front Desk review, goes straight to Supervisor
            'routed_by'    => auth()->id(),
            'routed_at'    => now(),
        ]);

        // Notify Front Desk
        Notification::create([
            'type'        => 'check_out',
            'title'       => 'Check-out',
            'message'     => "{$stay->guest->full_name} checked out. Room {$stay->room->room_number} is set to cleaning.",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        // Notify Supervisor
        Notification::create([
            'type'        => 'request',
            'title'       => 'Housekeeping Task Routed',
            'message'     => "Room {$stay->room->room_number} requires cleaning.",
            'target_role' => 'supervisor',
            'is_read'     => false,
        ]);

        return back()->with('success', "Guest checked out. A cleaning request has been sent to Housekeeping.");
    }
    /* ============================================================
     |  ROOMS
     ============================================================ */
    public function rooms(Request $request)
    {
        $query = Room::with('currentStay.guest');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('floor')->orderBy('room_number')->get();
        $roomsByFloor = $rooms->groupBy('floor');

        $roomCounts = [
            'available'   => Room::where('status', 'available')->count(),
            'occupied'    => Room::where('status', 'occupied')->count(),
            'cleaning'    => Room::where('status', 'cleaning')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        return view('dashboards.frontdesk.rooms', compact('roomsByFloor', 'roomCounts'));
    }

    public function updateRoomStatus(Request $request, Room $room)
    {
        $request->validate(['status' => 'required|in:available,occupied,cleaning,maintenance']);
        $room->update(['status' => $request->status]);
        return back()->with('success', "Room {$room->room_number} status updated.");
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'frontdesk')
            ->latest()->paginate(20);
        return view('dashboards.frontdesk.notifications', compact('notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllNotificationsRead()
    {
        Notification::where('target_role', 'frontdesk')
            ->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    /* ============================================================
     |  HISTORY
     ============================================================ */
    public function history(Request $request)
    {
        $query = Stay::with(['guest', 'room'])
            ->where('status', 'completed')
            ->latest('actual_check_out');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('guest', fn($g) =>
                    $g->where('first_name', 'like', "%{$s}%")
                      ->orWhere('last_name', 'like', "%{$s}%")
                )->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$s}%"));
            });
        }

        $history = $query->paginate(15)->withQueryString();
        return view('dashboards.frontdesk.history', compact('history'));
    }

    /* ============================================================
     |  REPORTS
     ============================================================ */
    public function reports()
    {
        $totalRooms    = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $totalStays    = Stay::count();
        $totalRequests = ServiceRequest::count();
        $avgRating     = round(\App\Models\Review::avg('rating') ?? 0, 1);

        $topServices = ServiceRequest::selectRaw('title, COUNT(*) as count')
            ->groupBy('title')->orderByDesc('count')->take(5)->get();

        $occupancyByFloor = Room::selectRaw('floor,
                COUNT(*) as total,
                SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied
            ')
            ->groupBy('floor')->orderBy('floor')->get();

        return view('dashboards.frontdesk.reports', compact(
            'occupancyRate', 'totalStays', 'totalRequests', 'avgRating',
            'topServices', 'occupancyByFloor', 'totalRooms'
        ));
    }

    /* ============================================================
     |  PROFILE
     ============================================================ */
    public function profile()
    {
        return view('dashboards.frontdesk.profile');
    }

    /* ============================================================
     |  QR CODE
     ============================================================ */
    public function showQr(Stay $stay)
    {
        $stay->load(['guest', 'room']);

        if ($stay->status !== 'active') {
            return redirect()->route('frontdesk.guests_stays')
                ->with('error', 'This stay is no longer active — QR code expired.');
        }

        $host = config('app.guest_access.host', '127.0.0.1');
        $port = config('app.guest_access.port', 8000);
        $accessUrl = "http://{$host}:{$port}/g/{$stay->access_token}";

        return view('dashboards.frontdesk.qr_code', compact('stay', 'accessUrl'));
    }
}