<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use App\Models\Setting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        return;
    }
    $targetDate = Carbon::now()->addDays(10)->toDateString();
    $payments = Payment::where('status', 'unpaid')->whereDate('due_date', $targetDate)->get();
    foreach ($payments as $p) {
        $tenant = Tenant::find($p->tenant_id);
        if ($tenant && $tenant->user_id) {
            $text = 'Pengingat: Tagihan '.$p->category.' sebesar Rp '.number_format($p->amount,0,',','.')
                .' jatuh tempo pada '.$p->due_date.'. Mohon segera dibayar.';
            Message::create([
                'sender_id' => $admin->id,
                'receiver_id' => $tenant->user_id,
                'message' => $text,
                'is_read' => false,
            ]);
        }
    }
})->dailyAt('08:00')->name('reminder-10-days');

Schedule::call(function () {
    $electricityFee = (int) (Setting::getValue('electricity_fee', 100000));
    $waterFee = (int) (Setting::getValue('water_fee', 50000));
    $today = Carbon::now()->toDateString();

    $tenants = Tenant::where('status', 'active')->whereNotNull('room_id')->get();
    foreach ($tenants as $tenant) {
        $room = Room::find($tenant->room_id);
        if (!$room) {
            continue;
        }
        $firstNextMonth = Carbon::now()->copy()->startOfMonth()->addMonth()->toDateString();
        $rentExists = Payment::where('tenant_id', $tenant->id)
            ->where('category', 'rent')
            ->whereDate('due_date', $firstNextMonth)
            ->exists();
        if (!$rentExists) {
            Payment::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'amount' => $room->price ?? (int) Setting::getValue('default_room_price', 0),
                'category' => 'rent',
                'due_date' => $firstNextMonth,
                'status' => 'unpaid',
            ]);
        }
        $elecExists = Payment::where('tenant_id', $tenant->id)
            ->where('category', 'electricity')
            ->whereDate('due_date', $firstNextMonth)
            ->exists();
        if (!$elecExists) {
            Payment::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'amount' => $electricityFee,
                'category' => 'electricity',
                'due_date' => $firstNextMonth,
                'status' => 'unpaid',
            ]);
        }
        $waterExists = Payment::where('tenant_id', $tenant->id)
            ->where('category', 'water')
            ->whereDate('due_date', $firstNextMonth)
            ->exists();
        if (!$waterExists) {
            Payment::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'amount' => $waterFee,
                'category' => 'water',
                'due_date' => $firstNextMonth,
                'status' => 'unpaid',
            ]);
        }
    }
})->dailyAt('03:00')->name('generate-monthly-bills');

Schedule::call(function () {
    $today = Carbon::now()->toDateString();
    $rooms = Room::where('status', 'occupied')->get();
    foreach ($rooms as $room) {
        $tenant = Tenant::where('room_id', $room->id)->where('status', 'active')->first();
        if (!$tenant) {
            $room->update(['electricity_status' => 'off', 'water_status' => 'off']);
            continue;
        }
        $electricityOverdue = Payment::where('tenant_id', $tenant->id)
            ->where('category', 'electricity')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', $today)
            ->exists();
        $waterOverdue = Payment::where('tenant_id', $tenant->id)
            ->where('category', 'water')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', $today)
            ->exists();
        $room->update([
            'electricity_status' => $electricityOverdue ? 'off' : 'on',
            'water_status' => $waterOverdue ? 'off' : 'on',
        ]);
    }
})->hourly()->name('utilities-cutoff');
