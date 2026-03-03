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
}
