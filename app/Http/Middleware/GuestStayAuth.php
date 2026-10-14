<?php

namespace App\Http\Middleware;

use App\Models\Stay;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestStayAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $stayId = session('guest_stay_id');

        if (!$stayId) {
            return redirect('/')->with('error', 'Please scan your room QR code to access your guest portal.');
        }

        $stay = Stay::with(['guest', 'room'])->find($stayId);

        if (!$stay || $stay->status !== 'active') {
            session()->forget('guest_stay_id');
            return redirect('/')->with('error', 'Your stay is no longer active. Please contact the front desk.');
        }

        // Share the current stay with all guest views
        view()->share('currentStay', $stay);

        return $next($request);
    }
}