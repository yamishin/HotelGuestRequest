<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guest\GuestPortalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Supervisor\SupervisorController;

Route::get('/', fn() => view('homepage'));
Route::get('/about', fn() => view('about'));

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/g/{token}', [GuestPortalController::class, 'access'])->name('guest.access');

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
    Route::post('/requests/{serviceRequest}/cancel', [GuestPortalController::class, 'cancelRequest'])->name('request.cancel');
});

Route::prefix('dashboard/supervisor')->name('supervisor.')->middleware('auth')->group(function () {
    Route::get('/', [SupervisorController::class, 'dashboard'])->name('dashboard');

    // Front Desk Guest Management (Only accessible to Front Desk Supervisor)
    Route::get('/check-in', [SupervisorController::class, 'checkInForm'])->name('check_in.form');
    Route::post('/check-in', [SupervisorController::class, 'checkInStore'])->name('check_in.store');
    Route::get('/qr/{stay}', [SupervisorController::class, 'showQr'])->name('check_in.qr');
    Route::get('/guests-stays', [SupervisorController::class, 'guestsStays'])->name('guests_stays');
    Route::get('/rooms', [SupervisorController::class, 'rooms'])->name('rooms');
    Route::get('/guests-stays/{guest}', [SupervisorController::class, 'guestDetail'])->name('guest_detail');
    Route::post('/check-out/{stay}', [SupervisorController::class, 'checkOut'])->name('check_out');
    Route::get('/requests/{serviceRequest}/print', [SupervisorController::class, 'printTicket'])->name('request.print');

    // Request Queue & Actions
    Route::get('/queue', [SupervisorController::class, 'requestQueue'])->name('queue');
    Route::post('/requests/{serviceRequest}/accept', [SupervisorController::class, 'acceptRequest'])->name('request.accept');
    Route::post('/requests/{serviceRequest}/reject', [SupervisorController::class, 'rejectRequest'])->name('request.reject');
    Route::post('/requests/{serviceRequest}/unavailable', [SupervisorController::class, 'markUnavailable'])->name('request.unavailable');

    Route::post('/assign/{serviceRequest}', [SupervisorController::class, 'assign'])->name('assign');
    Route::post('/reassign/{serviceRequest}', [SupervisorController::class, 'reassign'])->name('reassign');
    Route::patch('/requests/{serviceRequest}/proxy-status', [SupervisorController::class, 'proxyUpdateStatus'])->name('request.proxy_status');

    // Awaiting Review
    Route::get('/awaiting-review', [SupervisorController::class, 'awaitingReview'])->name('awaiting_review');
    Route::post('/requests/{serviceRequest}/confirm', [SupervisorController::class, 'confirmResolved'])->name('request.confirm');
    Route::post('/requests/{serviceRequest}/unresolved', [SupervisorController::class, 'markUnresolved'])->name('request.unresolved');

    // Staff Management
    Route::get('/staff', [SupervisorController::class, 'staff'])->name('staff');
    Route::get('/staff/create', [SupervisorController::class, 'staffCreate'])->name('staff.create');
    Route::post('/staff', [SupervisorController::class, 'staffStore'])->name('staff.store');
    Route::get('/staff/{user}', [SupervisorController::class, 'staffShow'])->name('staff.show');
    Route::get('/staff/{user}/edit', [SupervisorController::class, 'staffEdit'])->name('staff.edit');
    Route::patch('/staff/{user}', [SupervisorController::class, 'staffUpdate'])->name('staff.update');
    Route::patch('/staff/{user}/toggle-status', [SupervisorController::class, 'staffToggleStatus'])->name('staff.toggleStatus');
    Route::patch('/staff/{user}/reset-password', [SupervisorController::class, 'staffResetPassword'])->name('staff.resetPassword');

    // Reports & History
    Route::get('/reports', [SupervisorController::class, 'reports'])->name('reports');
    Route::get('/history', [SupervisorController::class, 'history'])->name('history');

    // Notifications & Profile
    Route::get('/notifications', [SupervisorController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [SupervisorController::class, 'markNotificationRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [SupervisorController::class, 'markAllNotificationsRead'])->name('notifications.readAll');
    Route::get('/profile', [SupervisorController::class, 'profile'])->name('profile');
    Route::patch('/profile', [SupervisorController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [SupervisorController::class, 'updatePassword'])->name('profile.password');
});

/* ============================================================
 |  ADMIN DASHBOARD
 ============================================================ */
Route::prefix('dashboard/admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/supervisor/create', [\App\Http\Controllers\Admin\AdminController::class, 'createSupervisor'])->name('supervisor.create');
    Route::post('/supervisor', [\App\Http\Controllers\Admin\AdminController::class, 'storeSupervisor'])->name('supervisor.store');
    Route::patch('/user/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminController::class, 'toggleStatus'])->name('user.toggleStatus');
    Route::get('/history', [\App\Http\Controllers\Admin\AdminController::class, 'history'])->name('history');
    Route::get('/reports', [\App\Http\Controllers\Admin\AdminController::class, 'reports'])->name('reports');
});
