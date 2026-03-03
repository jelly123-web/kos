<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($role === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        return redirect()->route('login');
    }
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('super-admin.users');
    Route::post('/users', [SuperAdminController::class, 'createUser'])->name('super-admin.users.create');
    Route::post('/users/{user}/toggle', [SuperAdminController::class, 'toggleUserStatus'])->name('super-admin.users.toggle');
    Route::post('/users/{user}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('super-admin.users.reset-password');
    
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('super-admin.reports');
    Route::get('/charts/line', [SuperAdminController::class, 'lineChart'])->name('super-admin.charts.line');
    Route::get('/charts/bar', [SuperAdminController::class, 'barChart'])->name('super-admin.charts.bar');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('super-admin.settings');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('super-admin.settings.update');
    Route::get('/activity-log', [SuperAdminController::class, 'activityLog'])->name('super-admin.activity-log');
    Route::get('/chat', [SuperAdminController::class, 'chat'])->name('super-admin.chat');
    Route::post('/chat/send', [SuperAdminController::class, 'sendMessage'])->name('super-admin.chat.send');
    Route::get('/chat/fetch/{user}', [SuperAdminController::class, 'fetchMessages'])->name('super-admin.chat.fetch');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.ecommerce');
    })->name('admin.dashboard');
    
    Route::get('/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'index'])->name('admin.rooms');
    Route::post('/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'store'])->name('admin.rooms.store');
    Route::post('/rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'update'])->name('admin.rooms.update');
    Route::post('/rooms/{room}/toggle-status', [\App\Http\Controllers\Admin\RoomController::class, 'toggleStatus'])->name('admin.rooms.toggle');
    Route::delete('/rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'destroy'])->name('admin.rooms.destroy');

    Route::get('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'index'])->name('admin.tenants');
    Route::post('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'store'])->name('admin.tenants.store');
    Route::post('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'update'])->name('admin.tenants.update');
    Route::delete('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('admin.tenants.destroy');

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments');
    Route::post('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('admin.payments.store');
    Route::post('/payments/{payment}/mark-paid', [\App\Http\Controllers\Admin\PaymentController::class, 'markPaid'])->name('admin.payments.mark-paid');

    Route::get('/property', [\App\Http\Controllers\Admin\PropertyController::class, 'show'])->name('admin.property');
    Route::post('/property', [\App\Http\Controllers\Admin\PropertyController::class, 'update'])->name('admin.property.update');

    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');
    Route::get('/room-requests', [\App\Http\Controllers\Admin\RoomController::class, 'requests'])->name('admin.room-requests');
    Route::post('/room-requests/{requestModel}/approve', [\App\Http\Controllers\Admin\RoomController::class, 'approveRequest'])->name('admin.room-requests.approve');
    Route::post('/room-requests/{requestModel}/reject', [\App\Http\Controllers\Admin\RoomController::class, 'rejectRequest'])->name('admin.room-requests.reject');
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.ecommerce');
    })->name('owner.dashboard');
    Route::get('/rooms', [\App\Http\Controllers\OwnerController::class, 'rooms'])->name('owner.rooms');
    Route::get('/tenants', [\App\Http\Controllers\OwnerController::class, 'tenants'])->name('owner.tenants');
    Route::get('/payments', [\App\Http\Controllers\OwnerController::class, 'payments'])->name('owner.payments');
    Route::get('/reports', [\App\Http\Controllers\OwnerController::class, 'reports'])->name('owner.reports');
    Route::get('/monitor', [\App\Http\Controllers\OwnerController::class, 'monitor'])->name('owner.monitor');
    Route::post('/evict/{tenant}', [\App\Http\Controllers\OwnerController::class, 'evict'])->name('owner.tenants.evict');
});

Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\TenantPortalController::class, 'dashboard'])->name('tenant.dashboard');
    Route::get('/room', [\App\Http\Controllers\TenantPortalController::class, 'room'])->name('tenant.room');
    Route::post('/request-room', [\App\Http\Controllers\TenantPortalController::class, 'requestRoom'])->name('tenant.request-room');
    Route::get('/bills', [\App\Http\Controllers\TenantPortalController::class, 'bills'])->name('tenant.bills');
    Route::post('/bills/{payment}/upload', [\App\Http\Controllers\TenantPortalController::class, 'uploadProof'])->name('tenant.bills.upload');
    Route::get('/history', [\App\Http\Controllers\TenantPortalController::class, 'history'])->name('tenant.history');
    Route::get('/chat', [\App\Http\Controllers\TenantPortalController::class, 'chat'])->name('tenant.chat');
    Route::post('/chat/send', [\App\Http\Controllers\TenantPortalController::class, 'sendMessage'])->name('tenant.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\TenantPortalController::class, 'fetchMessages'])->name('tenant.chat.fetch');
});
Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\StaffPortalController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/tenants', [\App\Http\Controllers\StaffPortalController::class, 'tenants'])->name('staff.tenants');
    Route::post('/tenants', [\App\Http\Controllers\StaffPortalController::class, 'storeTenant'])->name('staff.tenants.store');
    Route::post('/tenants/{tenant}', [\App\Http\Controllers\StaffPortalController::class, 'updateTenant'])->name('staff.tenants.update');
    Route::get('/rooms', [\App\Http\Controllers\StaffPortalController::class, 'rooms'])->name('staff.rooms');
    Route::get('/payments', [\App\Http\Controllers\StaffPortalController::class, 'payments'])->name('staff.payments');
    Route::post('/payments', [\App\Http\Controllers\StaffPortalController::class, 'storePayment'])->name('staff.payments.store');
    Route::get('/chat', [\App\Http\Controllers\StaffPortalController::class, 'chat'])->name('staff.chat');
    Route::post('/chat/send', [\App\Http\Controllers\StaffPortalController::class, 'sendMessage'])->name('staff.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\StaffPortalController::class, 'fetchMessages'])->name('staff.chat.fetch');
});
Route::middleware(['auth', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ManagerPortalController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/rooms', [\App\Http\Controllers\ManagerPortalController::class, 'rooms'])->name('manager.rooms');
    Route::get('/tenants', [\App\Http\Controllers\ManagerPortalController::class, 'tenants'])->name('manager.tenants');
    Route::get('/payments', [\App\Http\Controllers\ManagerPortalController::class, 'payments'])->name('manager.payments');
    Route::get('/reports', [\App\Http\Controllers\ManagerPortalController::class, 'reports'])->name('manager.reports');
    Route::get('/operations', [\App\Http\Controllers\ManagerPortalController::class, 'operations'])->name('manager.operations');
    Route::post('/operations', [\App\Http\Controllers\ManagerPortalController::class, 'storeOperation'])->name('manager.operations.store');
    Route::post('/operations/{operation}', [\App\Http\Controllers\ManagerPortalController::class, 'updateOperation'])->name('manager.operations.update');
    Route::get('/chat', [\App\Http\Controllers\ManagerPortalController::class, 'chat'])->name('manager.chat');
    Route::post('/chat/send', [\App\Http\Controllers\ManagerPortalController::class, 'sendMessage'])->name('manager.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\ManagerPortalController::class, 'fetchMessages'])->name('manager.chat.fetch');
});
Route::middleware(['auth'])->get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar');
Route::get('/dashboard', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($role === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        if ($role === 'tenant') {
            return redirect()->route('tenant.dashboard');
        }
        if ($role === 'staff') {
            return redirect()->route('staff.dashboard');
        }
        if ($role === 'manager') {
            return redirect()->route('manager.dashboard');
        }
    }
    return redirect('/login');
});
