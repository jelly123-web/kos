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

    // Global properties management
    Route::get('/properties', [\App\Http\Controllers\SuperAdmin\PropertyController::class, 'index'])->name('super-admin.properties');
    Route::post('/properties', [\App\Http\Controllers\SuperAdmin\PropertyController::class, 'store'])->name('super-admin.properties.store');
    Route::post('/properties/{property}', [\App\Http\Controllers\SuperAdmin\PropertyController::class, 'update'])->name('super-admin.properties.update');
    Route::delete('/properties/{property}', [\App\Http\Controllers\SuperAdmin\PropertyController::class, 'destroy'])->name('super-admin.properties.destroy');

    // Maintenance
    Route::get('/maintenance', [SuperAdminController::class, 'maintenance'])->name('super-admin.maintenance');
    Route::get('/maintenance/backup', [SuperAdminController::class, 'backup'])->name('super-admin.maintenance.backup');
    Route::post('/maintenance/restore', [SuperAdminController::class, 'restore'])->name('super-admin.maintenance.restore');
    Route::post('/maintenance/update', [SuperAdminController::class, 'systemUpdate'])->name('super-admin.maintenance.update');
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
    Route::get('/rooms/inspections', [\App\Http\Controllers\Admin\RoomController::class, 'inspections'])->name('admin.room-inspections');
    Route::post('/rooms/{room}/condition', [\App\Http\Controllers\Admin\RoomController::class, 'setCondition'])->name('admin.rooms.condition');
    Route::post('/rooms/{room}/set-status', [\App\Http\Controllers\Admin\RoomController::class, 'setStatus'])->name('admin.rooms.set-status');
    Route::post('/rooms/{room}/mark-ready', [\App\Http\Controllers\Admin\RoomController::class, 'markReady'])->name('admin.rooms.mark-ready');
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat');
    Route::post('/chat/send', [\App\Http\Controllers\Admin\ChatController::class, 'send'])->name('admin.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\Admin\ChatController::class, 'fetch'])->name('admin.chat.fetch');

    Route::get('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'index'])->name('admin.tenants');
    Route::post('/tenants', [\App\Http\Controllers\Admin\TenantController::class, 'store'])->name('admin.tenants.store');
    Route::post('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'update'])->name('admin.tenants.update');
    Route::delete('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('admin.tenants.destroy');

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments');
    Route::post('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('admin.payments.store');
    Route::post('/payments/{payment}/mark-paid', [\App\Http\Controllers\Admin\PaymentController::class, 'markPaid'])->name('admin.payments.mark-paid');
    Route::post('/payments/generate-monthly', [\App\Http\Controllers\Admin\PaymentController::class, 'generateMonthly'])->name('admin.payments.generate');
    Route::post('/payments/settings', [\App\Http\Controllers\Admin\PaymentController::class, 'updateSettings'])->name('admin.payments.settings');

    Route::get('/property', [\App\Http\Controllers\Admin\PropertyController::class, 'show'])->name('admin.property');
    Route::post('/property', [\App\Http\Controllers\Admin\PropertyController::class, 'update'])->name('admin.property.update');

    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');
    Route::get('/charts/payments', [\App\Http\Controllers\Admin\ReportController::class, 'chartPayments'])->name('admin.charts.payments');
    Route::get('/metrics/summary', [\App\Http\Controllers\Admin\ReportController::class, 'metricsSummary'])->name('admin.metrics.summary');
    Route::get('/exit-requests', [\App\Http\Controllers\Admin\ExitRequestController::class, 'index'])->name('admin.exit-requests');
    Route::post('/exit-requests/{exitRequest}/approve', [\App\Http\Controllers\Admin\ExitRequestController::class, 'approve'])->name('admin.exit-requests.approve');
    Route::post('/exit-requests/{exitRequest}/reject', [\App\Http\Controllers\Admin\ExitRequestController::class, 'reject'])->name('admin.exit-requests.reject');
    Route::get('/issues', [\App\Http\Controllers\Admin\IssueController::class, 'index'])->name('admin.issues');
    Route::post('/issues/{issue}/assign', [\App\Http\Controllers\Admin\IssueController::class, 'assign'])->name('admin.issues.assign');
    Route::post('/issues/{issue}/status', [\App\Http\Controllers\Admin\IssueController::class, 'setStatus'])->name('admin.issues.status');
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
    Route::get('/reports/export', [\App\Http\Controllers\OwnerController::class, 'exportReport'])->name('owner.reports.export');
    Route::get('/monitor', [\App\Http\Controllers\OwnerController::class, 'monitor'])->name('owner.monitor');
    Route::post('/evict/{tenant}', [\App\Http\Controllers\OwnerController::class, 'evict'])->name('owner.tenants.evict');
    Route::get('/chat', [\App\Http\Controllers\OwnerController::class, 'chat'])->name('owner.chat');
    Route::post('/chat/send', [\App\Http\Controllers\OwnerController::class, 'sendMessage'])->name('owner.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\OwnerController::class, 'fetchMessages'])->name('owner.chat.fetch');
});

Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\TenantPortalController::class, 'dashboard'])->name('tenant.dashboard');
    Route::get('/room', [\App\Http\Controllers\TenantPortalController::class, 'room'])->name('tenant.room');
    Route::post('/request-room', [\App\Http\Controllers\TenantPortalController::class, 'requestRoom'])->name('tenant.request-room');
    Route::get('/bills', [\App\Http\Controllers\TenantPortalController::class, 'bills'])->name('tenant.bills');
    Route::post('/bills/{payment}/upload', [\App\Http\Controllers\TenantPortalController::class, 'uploadProof'])->name('tenant.bills.upload');
    Route::post('/bills/{payment}/pay', [\App\Http\Controllers\TenantPortalController::class, 'payBill'])->name('tenant.bills.pay');
    Route::get('/history', [\App\Http\Controllers\TenantPortalController::class, 'history'])->name('tenant.history');
    Route::get('/chat', [\App\Http\Controllers\TenantPortalController::class, 'chat'])->name('tenant.chat');
    Route::post('/chat/send', [\App\Http\Controllers\TenantPortalController::class, 'sendMessage'])->name('tenant.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\TenantPortalController::class, 'fetchMessages'])->name('tenant.chat.fetch');
    Route::get('/exit', [\App\Http\Controllers\TenantPortalController::class, 'exitIndex'])->name('tenant.exit');
    Route::post('/exit', [\App\Http\Controllers\TenantPortalController::class, 'submitExit'])->name('tenant.exit.submit');
    Route::get('/issues', [\App\Http\Controllers\TenantPortalController::class, 'issues'])->name('tenant.issues');
    Route::post('/issues', [\App\Http\Controllers\TenantPortalController::class, 'submitIssue'])->name('tenant.issues.submit');
});
Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\StaffPortalController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/tenants', [\App\Http\Controllers\StaffPortalController::class, 'tenants'])->name('staff.tenants');
    Route::post('/tenants', [\App\Http\Controllers\StaffPortalController::class, 'storeTenant'])->name('staff.tenants.store');
    Route::post('/tenants/{tenant}', [\App\Http\Controllers\StaffPortalController::class, 'updateTenant'])->name('staff.tenants.update');
    Route::get('/rooms', [\App\Http\Controllers\StaffPortalController::class, 'rooms'])->name('staff.rooms');
    Route::get('/payments', [\App\Http\Controllers\StaffPortalController::class, 'payments'])->name('staff.payments');
    Route::post('/payments', [\App\Http\Controllers\StaffPortalController::class, 'storePayment'])->name('staff.payments.store');
    Route::get('/issues', [\App\Http\Controllers\StaffPortalController::class, 'issues'])->name('staff.issues');
    Route::post('/issues', [\App\Http\Controllers\StaffPortalController::class, 'submitIssue'])->name('staff.issues.submit');
    Route::post('/issues/{issue}/status', [\App\Http\Controllers\StaffPortalController::class, 'updateIssueStatus'])->name('staff.issues.status');
    Route::delete('/issues/{issue}', [\App\Http\Controllers\StaffPortalController::class, 'destroyIssue'])->name('staff.issues.destroy');
    Route::get('/inspections', [\App\Http\Controllers\StaffPortalController::class, 'inspections'])->name('staff.inspections');
    Route::post('/inspections', [\App\Http\Controllers\StaffPortalController::class, 'storeInspection'])->name('staff.inspections.store');
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
    Route::get('/reports/export', [\App\Http\Controllers\ManagerPortalController::class, 'exportReport'])->name('manager.reports.export');
    Route::get('/operations', [\App\Http\Controllers\ManagerPortalController::class, 'operations'])->name('manager.operations');
    Route::post('/operations', [\App\Http\Controllers\ManagerPortalController::class, 'storeOperation'])->name('manager.operations.store');
    Route::post('/operations/{operation}', [\App\Http\Controllers\ManagerPortalController::class, 'updateOperation'])->name('manager.operations.update');
    Route::get('/chat', [\App\Http\Controllers\ManagerPortalController::class, 'chat'])->name('manager.chat');
    Route::post('/chat/send', [\App\Http\Controllers\ManagerPortalController::class, 'sendMessage'])->name('manager.chat.send');
    Route::get('/chat/fetch/{user}', [\App\Http\Controllers\ManagerPortalController::class, 'fetchMessages'])->name('manager.chat.fetch');
});
Route::middleware(['auth'])->prefix('dashboard-api')->group(function () {
    Route::get('/metrics-summary', [\App\Http\Controllers\DashboardController::class, 'metricsSummary'])->name('dashboard.metrics.summary');
    Route::get('/charts-payments', [\App\Http\Controllers\DashboardController::class, 'chartPayments'])->name('dashboard.charts.payments');
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
