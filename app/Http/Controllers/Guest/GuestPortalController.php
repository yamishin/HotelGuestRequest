<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\Stay;
use App\Models\Review;
use Illuminate\Http\Request;

class GuestPortalController extends Controller
{
    /* ============================================================
     |  PUBLIC: Access via QR token
     ============================================================ */
    public function access(string $token)
    {
        $stay = Stay::with(['guest', 'room'])->where('access_token', $token)->first();

        if (!$stay) {
            return redirect('/')->with('error', 'Invalid access link.');
        }

        if ($stay->status !== 'active') {
            return redirect('/')->with('error', 'This access link is no longer active.');
        }

        session(['guest_stay_id' => $stay->id]);

        return redirect()->route('guest.dashboard')
            ->with('success', "Welcome, {$stay->guest->full_name}!");
    }

    /* ============================================================
     |  GUEST DASHBOARD
     ============================================================ */
    public function dashboard()
    {
        $stay = Stay::with(['guest', 'room', 'serviceRequests'])->find(session('guest_stay_id'));

        $recentRequests = $stay->serviceRequests()->latest()->take(5)->get();
        $notifications = Notification::where('target_role', 'guest')
            ->latest()
            ->take(5)
            ->get();

        return view('guest.dashboard', compact('stay', 'recentRequests', 'notifications'));
    }

    /* ============================================================
     |  REQUEST SERVICE (form)
     ============================================================ */
    public function requestForm()
    {
        return view('guest.request');
    }

    public function requestStore(Request $request)
    {
        $stay = Stay::find(session('guest_stay_id'));

        // 1. Validate the form (Department removed)
        $validated = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        // 2. Generate a UNIQUE request code
        $lastCode = ServiceRequest::selectRaw("MAX(CAST(SUBSTR(request_code, 5) AS INTEGER)) as max_num")
            ->value('max_num');
        $nextNumber = ($lastCode ?? 0) + 1;

        do {
            $newCode = 'REQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = ServiceRequest::where('request_code', $newCode)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        // 3. Create the request (Department is "Unassigned")
        $serviceRequest = ServiceRequest::create([
            'request_code' => $newCode,
            'stay_id'      => $stay->id,
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'department'   => 'Unassigned', 
            'status'       => 'pending',
        ]);

        // 4. Notify Front Desk only (Supervisors don't need to see it until Front Desk routes it)
        Notification::create([
            'type'        => 'request',
            'title'       => 'New Guest Request',
            'message'     => "Room {$stay->room->room_number} requested: {$validated['title']}",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        // 5. Notify the guest that their request was received
        Notification::create([
            'type'        => 'request',
            'title'       => 'Request Received',
            'message'     => "Your request \"{$validated['title']}\" has been received and is being reviewed by the Front Desk.",
            'target_role' => 'guest',
            'is_read'     => false,
        ]);

        return redirect()->route('guest.dashboard')
            ->with('success', "Request submitted! The Front Desk will review it shortly.");
    }

    /* ============================================================
     |  MY REQUESTS
     ============================================================ */
    public function myRequests()
    {
        $stay = Stay::find(session('guest_stay_id'));
        $requests = $stay->serviceRequests()->latest()->paginate(10);

        return view('guest.my_requests', compact('requests'));
    }

    /* ============================================================
     |  MY STAY
     ============================================================ */
    public function myStay()
    {
        $stay = Stay::with(['guest', 'room', 'serviceRequests'])
            ->find(session('guest_stay_id'));

        $totalRequests = $stay->serviceRequests->count();
        $completedRequests = $stay->serviceRequests->whereIn('status', ['completed', 'resolved'])->count();

        $daysRemaining = max(0, now()->startOfDay()->diffInDays(
            $stay->check_out_date->startOfDay(), false
        ));

        return view('guest.my_stay', compact('stay', 'totalRequests', 'completedRequests', 'daysRemaining'));
    }

    /* ============================================================
     |  REQUEST DETAIL
     ============================================================ */
    public function requestDetail(\App\Models\ServiceRequest $serviceRequest)
    {
        $stay = Stay::find(session('guest_stay_id'));

        if ($serviceRequest->stay_id !== $stay->id) {
            abort(403, 'This request does not belong to you.');
        }

        return view('guest.request_detail', compact('serviceRequest'));
    }

    /* ============================================================
     |  HOTEL INFO
     ============================================================ */
    public function hotelInfo()
    {
        return view('guest.hotel_info');
    }

    /* ============================================================
     |  NOTIFICATIONS
     ============================================================ */
    public function notifications()
    {
        $notifications = Notification::where('target_role', 'guest')
            ->latest()
            ->paginate(15);

        return view('guest.notifications', compact('notifications'));
    }

    /* ============================================================
     |  SUBMIT REVIEW
     ============================================================ */
    public function reviewForm()
    {
        $stay = Stay::find(session('guest_stay_id'));

        if ($stay->review) {
            return redirect()->route('guest.dashboard')
                ->with('error', 'You have already submitted a review.');
        }

        return view('guest.review');
    }

    public function reviewStore(Request $request)
    {
        $stay = Stay::find(session('guest_stay_id'));

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'stay_id'  => $stay->id,
            'guest_id' => $stay->guest_id,
            'rating'   => $validated['rating'],
            'comment'  => $validated['comment'] ?? null,
        ]);

        Notification::create([
            'type'        => 'review',
            'title'       => 'New Guest Review',
            'message'     => "Room {$stay->room->room_number} left a {$validated['rating']}-star review",
            'target_role' => 'frontdesk',
            'is_read'     => false,
        ]);

        return redirect()->route('guest.dashboard')
            ->with('success', 'Thank you for your feedback!');
    }

    /* ============================================================
     |  LOGOUT
     ============================================================ */
    public function logout()
    {
        session()->forget('guest_stay_id');
        return redirect('/')->with('success', 'You have been logged out.');
    }
}