<?php

namespace App\Support;

class PermissionRegistry
{
    public static function all(): array
    {
        return [
            'admin.dashboard.view' => 'Admin: Dashboard',
            'admin.rooms.view' => 'Admin: Kamar',
            'admin.tenants.view' => 'Admin: Penghuni',
            'admin.payments.view' => 'Admin: Pembayaran',
            'admin.reports.view' => 'Admin: Laporan',
            'admin.issues.view' => 'Admin: Laporan/Keluhan',
            'admin.exit.view' => 'Admin: Keluar Kos',
            'admin.inspections.view' => 'Admin: Inspeksi Kamar',

            'owner.rooms.view' => 'Owner: Kamar',
            'owner.tenants.view' => 'Owner: Penghuni',
            'owner.payments.view' => 'Owner: Pembayaran',
            'owner.reports.view' => 'Owner: Laporan',
            'owner.monitor.view' => 'Owner: Pantau Kos',
            'owner.chat.view' => 'Owner: Chat',

            'staff.dashboard.view' => 'Staff: Dashboard',
            'staff.tenants.view' => 'Staff: Penghuni',
            'staff.rooms.view' => 'Staff: Kamar',
            'staff.payments.view' => 'Staff: Pembayaran',
            'staff.issues.view' => 'Staff: Laporan/Keluhan',
            'staff.inspections.view' => 'Staff: Cek Kondisi',
            'staff.chat.view' => 'Staff: Chat',

            'manager.dashboard.view' => 'Manager: Dashboard',
            'manager.rooms.view' => 'Manager: Kamar',
            'manager.tenants.view' => 'Manager: Penghuni',
            'manager.payments.view' => 'Manager: Pembayaran',
            'manager.reports.view' => 'Manager: Laporan',
            'manager.operations.view' => 'Manager: Operasional',
            'manager.chat.view' => 'Manager: Chat',

            'tenant.dashboard.view' => 'Tenant: Dashboard',
            'tenant.room.view' => 'Tenant: Kamar Saya',
            'tenant.bills.view' => 'Tenant: Tagihan',
            'tenant.issues.view' => 'Tenant: Keluhan',
            'tenant.history.view' => 'Tenant: Riwayat',
            'tenant.exit.view' => 'Tenant: Ajukan Keluar',
            'tenant.chat.view' => 'Tenant: Chat',
        ];
    }

    public static function routeMap(): array
    {
        return [
            'admin.dashboard' => 'admin.dashboard.view',
            'admin.rooms' => 'admin.rooms.view',
            'admin.tenants' => 'admin.tenants.view',
            'admin.payments' => 'admin.payments.view',
            'admin.reports' => 'admin.reports.view',
            'admin.issues' => 'admin.issues.view',
            'admin.exit-requests' => 'admin.exit.view',
            'admin.room-inspections' => 'admin.inspections.view',
            'admin.chat' => 'admin.reports.view', // optional not used

            'owner.rooms' => 'owner.rooms.view',
            'owner.tenants' => 'owner.tenants.view',
            'owner.payments' => 'owner.payments.view',
            'owner.reports' => 'owner.reports.view',
            'owner.monitor' => 'owner.monitor.view',
            'owner.chat' => 'owner.chat.view',

            'staff.dashboard' => 'staff.dashboard.view',
            'staff.tenants' => 'staff.tenants.view',
            'staff.rooms' => 'staff.rooms.view',
            'staff.payments' => 'staff.payments.view',
            'staff.issues' => 'staff.issues.view',
            'staff.inspections' => 'staff.inspections.view',
            'staff.chat' => 'staff.chat.view',

            'manager.dashboard' => 'manager.dashboard.view',
            'manager.rooms' => 'manager.rooms.view',
            'manager.tenants' => 'manager.tenants.view',
            'manager.payments' => 'manager.payments.view',
            'manager.reports' => 'manager.reports.view',
            'manager.operations' => 'manager.operations.view',
            'manager.chat' => 'manager.chat.view',

            'tenant.dashboard' => 'tenant.dashboard.view',
            'tenant.room' => 'tenant.room.view',
            'tenant.bills' => 'tenant.bills.view',
            'tenant.issues' => 'tenant.issues.view',
            'tenant.history' => 'tenant.history.view',
            'tenant.exit' => 'tenant.exit.view',
            'tenant.chat' => 'tenant.chat.view',
        ];
    }
}
