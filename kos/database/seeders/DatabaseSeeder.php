<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@kos.com',
                'password' => 'superadmin',
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@kos.com',
                'password' => 'admin',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['username' => 'pemilik'],
            [
                'name' => 'Pemilik Kos',
                'email' => 'pemilik@kos.com',
                'password' => 'pemilik',
                'role' => 'owner',
                'status' => 'active',
            ]
        );

        $tenantUser = User::updateOrCreate(
            ['username' => 'penghuni'],
            [
                'name' => 'Penghuni',
                'email' => 'penghuni@kos.com',
                'password' => 'penghuni',
                'role' => 'tenant',
                'status' => 'active',
            ]
        );
        \App\Models\Tenant::updateOrCreate(
            ['user_id' => $tenantUser->id],
            [
                'name' => 'Penghuni',
                'phone' => '081234567890',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff',
                'email' => 'staff@kos.com',
                'password' => 'staff',
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager',
                'email' => 'manager@kos.com',
                'password' => 'manager',
                'role' => 'manager',
                'status' => 'active',
            ]
        );
    }
}
