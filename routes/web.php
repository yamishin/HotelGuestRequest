<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontDesk\FrontDeskController;
use App\Http\Controllers\Guest\GuestPortalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Staff\StaffController;

/* ============================================================
 |  PUBLIC
 ============================================================ */

Route::get('/', fn() => view('homepage'));
Route::get('/about', fn() => view('about'));

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


/* ============================================================
 |  GUEST ACCESS (public token)
 ============================================================ */

Route::get('/g/{token}', [GuestPortalController::class, 'access'])->name('guest.access');


/* ============================================================
 |  GUEST PORTAL
 ============================================================ */

Route::prefix('guest')->name('guest.')->middleware('guest.stay')->group(function () {
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
});


/* ============================================================
 |  FRONT DESK
 ============================================================ */

Route::prefix('dashboard/frontdesk')->name('frontdesk.')->middleware('auth')->group(function () {
    Route::get('/', [FrontDeskController::class, 'dashboard'])->name('dashboard');

    // NEW: Request Inbox (central hub)
    Route::get('/inbox', [FrontDeskController::class, 'inbox'])->name('inbox');
    Route::post('/requests/{serviceRequest}/accept', [FrontDeskController::class, 'acceptRequest'])->name('request.accept');
    Route::post('/requests/{serviceRequest}/reject', [FrontDeskController::class, 'rejectRequest'])->name('request.reject');
    Route::post('/requests/{serviceRequest}/unavailable', [FrontDeskController::class, 'markUnavailable'])->name('request.unavailable');

    // NEW: Monitor
    Route::get('/monitor', [FrontDeskController::class, 'monitor'])->name('monitor');

    // Guests & Stays
    Route::get('/guests-stays', [FrontDeskController::class, 'guestsStays'])->name('guests_stays');
    Route::get('/guests-stays/{guest}', [FrontDeskController::class, 'guestDetail'])->name('guest_detail');

    // Check-in / Check-out
    Route::get('/check-in', [FrontDeskController::class, 'checkInForm'])->name('check_in.form');
    Route::post('/check-in', [FrontDeskController::class, 'checkInStore'])->name('check_in.store');
    Route::post('/check-out/{stay}', [FrontDeskController::class, 'checkOut'])->name('check_out');

    // Rooms
    Route::get('/rooms', [FrontDeskController::class, 'rooms'])->name('rooms');
    Route::patch('/rooms/{room}/status', [FrontDeskController::class, 'updateRoomStatus'])->name('rooms.updateStatus');

    // Notifications
    Route::get('/notifications', [FrontDeskController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [FrontDeskController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [FrontDeskController::class, 'markAllNotificationsRead'])->name('notifications.readAll');

    // QR
    Route::get('/qr/{stay}', [FrontDeskController::class, 'showQr'])->name('qr');

    // History & Reports
    Route::get('/history', [FrontDeskController::class, 'history'])->name('history');
    Route::get('/reports', [FrontDeskController::class, 'reports'])->name('reports');

    // Profile
    Route::get('/profile', [FrontDeskController::class, 'profile'])->name('profile');
});


/* ============================================================
 |  SUPERVISOR
 ============================================================ */

Route::prefix('dashboard/supervisor')->name('supervisor.')->middleware('auth')->group(function () {
    Route::get('/', [SupervisorController::class, 'dashboard'])->name('dashboard');

    // Queue
    Route::get('/queue', [SupervisorController::class, 'requestQueue'])->name('queue');
    Route::post('/assign/{serviceRequest}', [SupervisorController::class, 'assign'])->name('assign');
    Route::post('/reassign/{serviceRequest}', [SupervisorController::class, 'reassign'])->name('reassign');
    Route::post('/unassign/{serviceRequest}', [SupervisorController::class, 'unassign'])->name('unassign');

    // Awaiting Review
    Route::get('/awaiting-review', [SupervisorController::class, 'awaitingReview'])->name('awaiting_review');
    Route::post('/requests/{serviceRequest}/confirm', [SupervisorController::class, 'confirmResolved'])->name('request.confirm');
    Route::post('/requests/{serviceRequest}/unresolved', [SupervisorController::class, 'markUnresolved'])->name('request.unresolved');

    // Staff
    Route::get('/staff', [SupervisorController::class, 'staff'])->name('staff');
    Route::get('/staff/create', [SupervisorController::class, 'staffCreate'])->name('staff.create');
    Route::post('/staff', [SupervisorController::class, 'staffStore'])->name('staff.store');
    Route::get('/staff/{user}', [SupervisorController::class, 'staffShow'])->name('staff.show');
    Route::get('/staff/{user}/edit', [SupervisorController::class, 'staffEdit'])->name('staff.edit');
    Route::patch('/staff/{user}', [SupervisorController::class, 'staffUpdate'])->name('staff.update');
    Route::patch('/staff/{user}/toggle-status', [SupervisorController::class, 'staffToggleStatus'])->name('staff.toggleStatus');
    Route::patch('/staff/{user}/reset-password', [SupervisorController::class, 'staffResetPassword'])->name('staff.resetPassword');

    // History
    Route::get('/history', [SupervisorController::class, 'history'])->name('history');

    // Notifications
    Route::get('/notifications', [SupervisorController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [SupervisorController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [SupervisorController::class, 'markAllNotificationsRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [SupervisorController::class, 'profile'])->name('profile');
    Route::patch('/profile', [SupervisorController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [SupervisorController::class, 'updatePassword'])->name('profile.password');
});


/* ============================================================
 |  STAFF
 ============================================================ */

Route::prefix('dashboard/staff')->name('staff.')->middleware('auth')->group(function () {
    Route::get('/', [StaffController::class, 'dashboard'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [StaffController::class, 'tasks'])->name('tasks');
    Route::post('/tasks/{serviceRequest}/start', [StaffController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{serviceRequest}/complete', [StaffController::class, 'completeTask'])->name('tasks.complete');
    Route::post('/tasks/{serviceRequest}/unresolved', [StaffController::class, 'markUnresolved'])->name('tasks.unresolved');

    // All department requests
    Route::get('/all-requests', [StaffController::class, 'allRequests'])->name('all_requests');

    // Notifications
    Route::get('/notifications', [StaffController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [StaffController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [StaffController::class, 'markAllNotificationsRead'])->name('notifications.readAll');

    // History
    Route::get('/history', [StaffController::class, 'history'])->name('history');

    // Profile
    Route::get('/profile', [StaffController::class, 'profile'])->name('profile');
    Route::patch('/profile', [StaffController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [StaffController::class, 'updatePassword'])->name('profile.password');
});