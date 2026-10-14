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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class FrontDeskController extends Controller
{
    /* ============================================================
     |  DASHBOARD
     ============================================================ */
    public function dashboard()
    {
        $today = now()->toDateString();

        $checkInsToday = Stay::whereDate('check_in_date', $today)->count();
        $checkOutsToday = Stay::whereDate('check_out_date', $today)->count();
        $pendingRequests = ServiceRequest::whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
        $activeStays = Stay::where('status', 'active')->count();
        $totalRooms = Room::count();

        // Room status counts
        $roomCounts = [
            'available' => Room::where('status', 'available')->count(),
            'occupied' => Room::where('status', 'occupied')->count(),
            'cleaning' => Room::where('status', 'cleaning')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        // Sample floor 5 rooms for the mini grid
        $sampleRooms = Room::where('floor', 5)->orderBy('room_number')->get();

        // Recent notifications for activity feed
        $recentActivity = Notification::where('target_role', 'frontdesk')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.frontdesk.index', compact(
            'checkInsToday',
            'checkOutsToday',
            'pendingRequests',
            'activeStays',
            'totalRooms',
            'roomCounts',
            'sampleRooms',
            'recentActivity'
        ));
    }

    /* ============================================================
     |  GUESTS & STAYS
     ============================================================ */
    public function guestsStays(Request $request)
    {
        $query = Guest::with(['stays.room']);

        // Search
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

        // Filter by status
        if ($request->filled('filter') && $request->filter !== 'all') {
            $filter = $request->filter;
            $query->whereHas('stays', function ($q) use ($filter) {
                $q->where('status', $filter);
            });
        }

        $guests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboards.frontdesk.guests_stays', compact('guests'));
    }

    /* ============================================================
     |  GUEST DETAIL (modal content / standalone page)
     ============================================================ */
    public function guestDetail(Guest $guest)
    {
        $guest->load(['stays.room', 'stays.serviceRequests', 'reviews']);
        return view('dashboards.frontdesk.guest_detail', compact('guest'));
    }

    /* ============================================================
     |  CHECK-IN FORM
     ============================================================ */
    public function checkInForm()
    {
        $availableRooms = Room::where('status', 'available')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        $guests = Guest::orderBy('first_name')->get();

        return view('dashboards.frontdesk.check_in', compact('availableRooms', 'guests'));
    }

    /* ============================================================
     |  STORE CHECK-IN
     ============================================================ */
    public function checkInStore(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'room_id'        => 'required|exists:rooms,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_guests'     => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string',
        ]);

        // Create guest
        $lastGuest = Guest::orderByDesc('id')->first();
        $nextId = $lastGuest ? $lastGuest->id + 1 : 1;
        $guest = Guest::create([
            'guest_code' => 'G-' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ]);

        // Create stay
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

        // Update room status
        Room::find($validated['room_id'])->update(['status' => 'occupied']);

        // Create a notification
        Notification::create([
            'type'        => 'check_in',
            'title'       => 'Check-in',
            'message'     => "{$guest->full_name} has checked in to Room {$stay->room->room_number}",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        // ✅ THIS IS WHERE THE QR REDIRECT GOES
        // (replaces the old redirect to guests_stays)
        return redirect()
            ->route('frontdesk.qr', $stay)
            ->with('success', "Guest {$guest->full_name} checked in. Print the QR below.");
    }

    /* ============================================================
     |  CHECK-OUT
     ============================================================ */
    public function checkOut(Stay $stay)
    {
        if ($stay->status !== 'active') {
            return back()->with('error', 'This stay is not active.');
        }

        $stay->update([
            'status'            => 'completed',
            'actual_check_out'  => now(),
        ]);

        $stay->room->update(['status' => 'cleaning']);

        Notification::create([
            'type'        => 'check_out',
            'title'       => 'Check-out',
            'message'     => "{$stay->guest->full_name} has checked out of Room {$stay->room->room_number}",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        return back()->with('success', "Guest checked out of Room {$stay->room->room_number}.");
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

        // Group by floor
        $roomsByFloor = $rooms->groupBy('floor');

        $roomCounts = [
            'available'   => Room::where('status', 'available')->count(),
            'occupied'    => Room::where('status', 'occupied')->count(),
            'cleaning'    => Room::where('status', 'cleaning')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        return view('dashboards.frontdesk.rooms', compact('roomsByFloor', 'roomCounts'));
    }

    /* ============================================================
     |  UPDATE ROOM STATUS
     ============================================================ */
    public function updateRoomStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,cleaning,maintenance',
        ]);

        $room->update(['status' => $request->status]);

        return back()->with('success', "Room {$room->room_number} status updated to {$request->status}.");
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'frontdesk')
            ->latest()
            ->paginate(20);

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
            ->where('is_read', false)
            ->update(['is_read' => true]);

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
            $query->whereHas('guest', function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%");
            })->orWhereHas('room', function ($q) use ($s) {
                $q->where('room_number', 'like', "%{$s}%");
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
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $totalStays = Stay::count();
        $totalRequests = ServiceRequest::count();

        $avgRating = round(\App\Models\Review::avg('rating') ?? 0, 1);

        // Top services
        $topServices = ServiceRequest::selectRaw('title, COUNT(*) as count')
            ->groupBy('title')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Occupancy by floor
        $occupancyByFloor = Room::selectRaw('floor, 
                COUNT(*) as total,
                SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied
            ')
            ->groupBy('floor')
            ->orderBy('floor')
            ->get();

        return view('dashboards.frontdesk.reports', compact(
            'occupancyRate',
            'totalStays',
            'totalRequests',
            'avgRating',
            'topServices',
            'occupancyByFloor',
            'totalRooms'
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
     |  QR CODE PAGE (shown after check-in)
     ============================================================ */
         public function showQr(Stay $stay)
    {
        $stay->load(['guest', 'room']);

        // Prevent showing QR for completed/inactive stays
        if ($stay->status !== 'active') {
            return redirect()
                ->route('frontdesk.guests_stays')
                ->with('error', 'This stay is no longer active — QR code expired.');
        }

        $accessUrl = $this->buildGuestAccessUrl($stay);

        return view('dashboards.frontdesk.qr_code', compact('stay', 'accessUrl'));
    }
    //     public function showQr(Stay $stay)
    // {
    //     $stay->load(['guest', 'room']);
    //     $accessUrl = route('guest.access', $stay->access_token);

    //     $qrCode = new QrCode($accessUrl);
    //     $writer = new SvgWriter();
    //     $result = $writer->write($qrCode);
    //     $qrSvg = $result->getString();

    //     return view('dashboards.frontdesk.qr_code', compact('stay', 'accessUrl', 'qrSvg'));
    // }

        /* ============================================================
     |  HELPER: Build the guest access URL
     ============================================================ */
    private function buildGuestAccessUrl(Stay $stay): string
    {
        $host = config('app.guest_access.host');
        $port = config('app.guest_access.port');

        return "http://{$host}:{$port}/g/{$stay->access_token}";
    }
}

