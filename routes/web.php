<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontDesk\FrontDeskController;
use App\Http\Controllers\Guest\GuestPortalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Supervisor\SupervisorController;

/* ============================================================
 |  PUBLIC ROUTES (no auth needed)
 ============================================================ */

Route::get('/', function () {
    return view('homepage');
});

Route::get('/about', function () {
    return view('about');
});

// Login / Logout (staff-side auth)
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Staff Management
Route::get('/staff/create', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffCreate'])->name('staff.create');
Route::post('/staff', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffStore'])->name('staff.store');
Route::get('/staff/{user}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffShow'])->name('staff.show');
Route::get('/staff/{user}/edit', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffEdit'])->name('staff.edit');
Route::patch('/staff/{user}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffUpdate'])->name('staff.update');
Route::patch('/staff/{user}/toggle-status', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffToggleStatus'])->name('staff.toggleStatus');
Route::patch('/staff/{user}/reset-password', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffResetPassword'])->name('staff.resetPassword');


/* ============================================================
 |  GUEST QR ACCESS (public — the token IS the auth)
 ============================================================ */

Route::get('/g/{token}', [GuestPortalController::class, 'access'])->name('guest.access');


/* ============================================================
 |  GUEST PORTAL (protected by guest.stay middleware)
 ============================================================ */

Route::prefix('guest')
    ->name('guest.')
    ->middleware('guest.stay')
    ->group(function () {

    Route::get('/dashboard',     [GuestPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-stay',       [GuestPortalController::class, 'myStay'])->name('my_stay');
    Route::get('/request',       [GuestPortalController::class, 'requestForm'])->name('request.form');
    Route::post('/request',      [GuestPortalController::class, 'requestStore'])->name('request.store');
    Route::get('/my-requests',   [GuestPortalController::class, 'myRequests'])->name('my_requests');
    Route::get('/requests/{serviceRequest}', [GuestPortalController::class, 'requestDetail'])->name('request.detail');
    Route::get('/hotel-info',    [GuestPortalController::class, 'hotelInfo'])->name('hotel_info');
    Route::get('/notifications', [GuestPortalController::class, 'notifications'])->name('notifications');
    Route::get('/review',        [GuestPortalController::class, 'reviewForm'])->name('review.form');
    Route::post('/review',       [GuestPortalController::class, 'reviewStore'])->name('review.store');
    Route::post('/logout',       [GuestPortalController::class, 'logout'])->name('logout');

    Route::post('/requests/{serviceRequest}/confirm', [GuestPortalController::class, 'confirmRequest'])->name('request.confirm');
    Route::post('/requests/{serviceRequest}/reject', [GuestPortalController::class, 'rejectRequest'])->name('request.reject');
});


/* ============================================================
 |  FRONT DESK DASHBOARD (protected by auth)
 ============================================================ */

Route::prefix('dashboard/frontdesk')
    ->name('frontdesk.')
    ->middleware('auth')
    ->group(function () {

    Route::get('/', [FrontDeskController::class, 'dashboard'])->name('dashboard');
    Route::get('/guests-stays', [FrontDeskController::class, 'guestsStays'])->name('guests_stays');
    Route::get('/guests-stays/{guest}', [FrontDeskController::class, 'guestDetail'])->name('guest_detail');
    Route::get('/check-in', [FrontDeskController::class, 'checkInForm'])->name('check_in.form');
    Route::post('/check-in', [FrontDeskController::class, 'checkInStore'])->name('check_in.store');
    Route::post('/check-out/{stay}', [FrontDeskController::class, 'checkOut'])->name('check_out');
    Route::get('/rooms', [FrontDeskController::class, 'rooms'])->name('rooms');
    Route::patch('/rooms/{room}/status', [FrontDeskController::class, 'updateRoomStatus'])->name('rooms.updateStatus');
    Route::get('/notifications', [FrontDeskController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [FrontDeskController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [FrontDeskController::class, 'markAllNotificationsRead'])->name('notifications.readAll');
    Route::get('/qr/{stay}', [FrontDeskController::class, 'showQr'])->name('qr');
    Route::get('/history', [FrontDeskController::class, 'history'])->name('history');
    Route::get('/reports', [FrontDeskController::class, 'reports'])->name('reports');
    Route::get('/profile', [FrontDeskController::class, 'profile'])->name('profile');
});


/* ============================================================
 |  SUPERVISOR DASHBOARD
 ============================================================ */

Route::prefix('dashboard/supervisor')
    ->name('supervisor.')
    ->middleware('auth')
    ->group(function () {

    // Dashboard
    Route::get('/', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'dashboard'])->name('dashboard');

    // Request Queue
    Route::get('/queue', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'requestQueue'])->name('queue');
    Route::post('/assign/{serviceRequest}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'assign'])->name('assign');
    Route::post('/unassign/{serviceRequest}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'unassign'])->name('unassign');

    // Staff — list route
    Route::get('/staff', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staff'])->name('staff');

    // Staff — CRUD routes (order matters!)
    Route::get('/staff/create', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffCreate'])->name('staff.create');
    Route::post('/staff', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffStore'])->name('staff.store');
    Route::get('/staff/{user}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffShow'])->name('staff.show');
    Route::get('/staff/{user}/edit', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffEdit'])->name('staff.edit');
    Route::patch('/staff/{user}', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffUpdate'])->name('staff.update');
    Route::patch('/staff/{user}/toggle-status', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffToggleStatus'])->name('staff.toggleStatus');
    Route::patch('/staff/{user}/reset-password', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'staffResetPassword'])->name('staff.resetPassword');

    // History
    Route::get('/history', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'history'])->name('history');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'markAllNotificationsRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'profile'])->name('profile');
    Route::patch('/profile', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [\App\Http\Controllers\Supervisor\SupervisorController::class, 'updatePassword'])->name('profile.password');
});

/* ============================================================
 |  STAFF DASHBOARD (stub — full build later)
 ============================================================ */

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/staff', function () {
        return view('dashboards.staff');
    })->name('staff.dashboard');
});

/* ============================================================
 |  SERVICE STAFF DASHBOARD
 ============================================================ */

Route::prefix('dashboard/staff')
    ->name('staff.')
    ->middleware('auth')
    ->group(function () {

    // Dashboard
    Route::get('/', [\App\Http\Controllers\Staff\StaffController::class, 'dashboard'])->name('dashboard');

    // My Tasks
    Route::get('/tasks', [\App\Http\Controllers\Staff\StaffController::class, 'tasks'])->name('tasks');
    Route::patch('/tasks/{serviceRequest}/status', [\App\Http\Controllers\Staff\StaffController::class, 'updateTaskStatus'])->name('tasks.updateStatus');

    // All Department Requests
    Route::get('/all-requests', [\App\Http\Controllers\Staff\StaffController::class, 'allRequests'])->name('all_requests');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Staff\StaffController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\Staff\StaffController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [\App\Http\Controllers\Staff\StaffController::class, 'markAllNotificationsRead'])->name('notifications.readAll');

    // History
    Route::get('/history', [\App\Http\Controllers\Staff\StaffController::class, 'history'])->name('history');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Staff\StaffController::class, 'profile'])->name('profile');
    Route::patch('/profile', [\App\Http\Controllers\Staff\StaffController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [\App\Http\Controllers\Staff\StaffController::class, 'updatePassword'])->name('profile.password');
});