<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['tenant', 'room'])->orderByDesc('due_date')->get();
        $tenants = Tenant::orderBy('name')->get();
        $rooms = Room::orderBy('number')->get();
        return view('pages.admin.payments.index', compact('payments', 'tenants', 'rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'room_id' => 'nullable|exists:rooms,id',
            'amount' => 'required|integer|min:0',
            'category' => 'nullable|in:rent,electricity,water',
            'due_date' => 'required|date',
        ]);
        $data['status'] = 'unpaid';
        $data['category'] = $data['category'] ?? 'rent';
        Payment::create($data);
        return redirect()->back()->with('success', 'Pembayaran dicatat.');
    }

    public function markPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Pembayaran ditandai lunas.');
    }

    public function generateMonthly(Request $request)
    {
        $month = now()->month;
        $year = now()->year;
        $electricityFee = (int) \App\Models\Setting::getValue('electricity_fee', 100000);
        $waterFee = (int) \App\Models\Setting::getValue('water_fee', 50000);
        $tenants = \App\Models\Tenant::where('status', 'active')->with('room')->get();
        $created = 0;
        foreach ($tenants as $t) {
            if (!$t->room) continue;
            $items = [
                ['category' => 'rent', 'amount' => (int) ($t->room->price ?? 0)],
                ['category' => 'electricity', 'amount' => $electricityFee],
                ['category' => 'water', 'amount' => $waterFee],
            ];
            foreach ($items as $it) {
                $exists = Payment::where('tenant_id', $t->id)
                    ->where('category', $it['category'])
                    ->whereMonth('due_date', $month)
                    ->whereYear('due_date', $year)
                    ->exists();
                if (!$exists) {
                    Payment::create([
                        'tenant_id' => $t->id,
                        'room_id' => $t->room->id,
                        'amount' => $it['amount'],
                        'category' => $it['category'],
                        'due_date' => now()->endOfMonth()->toDateString(),
                        'status' => 'unpaid',
                    ]);
                    $created++;
                }
            }
        }
        return redirect()->back()->with('success', "Generate tagihan selesai. Ditambahkan: $created item.");
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'electricity_fee' => 'required|integer|min:0',
            'water_fee' => 'required|integer|min:0',
        ]);
        \App\Models\Setting::setValue('electricity_fee', $data['electricity_fee']);
        \App\Models\Setting::setValue('water_fee', $data['water_fee']);
        return redirect()->back()->with('success', 'Pengaturan tagihan bulanan diperbarui.');
    }
}
