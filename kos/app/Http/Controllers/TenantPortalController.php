<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use App\Models\RoomRequest;
use App\Models\ExitRequest;
use App\Models\IssueReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class TenantPortalController extends Controller
{
    protected function me(): ?Tenant
    {
        return Tenant::where('user_id', auth()->id())->with('room')->first();
    }

    public function dashboard()
    {
        $tenant = $this->me();
        $leaseStatus = 'Aktif';
        $daysLeft = null;
        if ($tenant && $tenant->end_date) {
            $now = Carbon::now();
            $end = Carbon::parse($tenant->end_date);
            if ($end->greaterThanOrEqualTo($now)) {
                $diff = $now->diffInDays($end, false);
                if ($diff >= 0 && $diff <= 10) {
                    $leaseStatus = 'Hampir Habis';
                }
                $daysLeft = $diff >= 0 ? $diff : null;
            }
        }
        $currentMonthDue = 0;
        $overdue = 0;
        if ($tenant) {
            $currentMonthDue = Payment::where('tenant_id', $tenant->id)
                ->where('status', 'unpaid')
                ->whereMonth('due_date', Carbon::now()->month)
                ->whereYear('due_date', Carbon::now()->year)
                ->sum('amount');
            $overdue = Payment::where('tenant_id', $tenant->id)
                ->where('status', 'unpaid')
                ->whereDate('due_date', '<', Carbon::now()->toDateString())
                ->sum('amount');
        }
        $labels = [];
        $paidSeries = [];
        $unpaidSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->copy()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();
            $labels[] = $start->format('M Y');
            $paid = Payment::where('tenant_id', $tenant?->id)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->sum('amount');
            $unpaid = Payment::where('tenant_id', $tenant?->id)
                ->where('status', 'unpaid')
                ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');
            $paidSeries[] = (int) $paid;
            $unpaidSeries[] = (int) $unpaid;
        }
        return view('pages.dashboard.ecommerce', compact(
            'tenant',
            'leaseStatus',
            'daysLeft',
            'currentMonthDue',
            'overdue',
            'labels',
            'paidSeries',
            'unpaidSeries'
        ));
    }

    public function room()
    {
        $tenant = $this->me();
        $availableRooms = Room::where('status', 'empty')->orderBy('number')->get();
        return view('pages.tenant.room.index', compact('tenant', 'availableRooms'));
    }

    public function requestRoom(Request $request)
    {
        $tenant = $this->me();
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);
        $exists = RoomRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Masih ada permintaan kamar yang diproses.');
        }
        RoomRequest::create([
            'tenant_id' => $tenant->id,
            'room_id' => $data['room_id'],
            'status' => 'pending',
        ]);
        return redirect()->back()->with('success', 'Permintaan kamar terkirim, menunggu konfirmasi admin.');
    }

    public function bills(Request $request)
    {
        $tenant = $this->me();
        if ($tenant && $tenant->room_id) {
            $room = Room::find($tenant->room_id);
            $now = Carbon::now();
            $existsThisMonth = Payment::where('tenant_id', $tenant->id)
                ->where('category', 'rent')
                ->whereMonth('due_date', $now->month)
                ->whereYear('due_date', $now->year)
                ->exists();
            if (!$existsThisMonth) {
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room?->id,
                    'amount' => $room?->price ?? 0,
                    'category' => 'rent',
                    'due_date' => $now->endOfMonth()->toDateString(),
                    'status' => 'unpaid',
                ]);
            }
            $elecExists = Payment::where('tenant_id', $tenant->id)
                ->where('category', 'electricity')
                ->whereMonth('due_date', $now->month)
                ->whereYear('due_date', $now->year)
                ->exists();
            if (!$elecExists) {
                $electricityFee = (int) \App\Models\Setting::getValue('electricity_fee', 100000);
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room?->id,
                    'amount' => $electricityFee,
                    'category' => 'electricity',
                    'due_date' => $now->endOfMonth()->toDateString(),
                    'status' => 'unpaid',
                ]);
            }
            $waterExists = Payment::where('tenant_id', $tenant->id)
                ->where('category', 'water')
                ->whereMonth('due_date', $now->month)
                ->whereYear('due_date', $now->year)
                ->exists();
            if (!$waterExists) {
                $waterFee = (int) \App\Models\Setting::getValue('water_fee', 50000);
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room?->id,
                    'amount' => $waterFee,
                    'category' => 'water',
                    'due_date' => $now->endOfMonth()->toDateString(),
                    'status' => 'unpaid',
                ]);
            }
        }
        $category = $request->query('category');
        $paymentsQuery = Payment::where('tenant_id', $tenant?->id);
        if (in_array($category, ['rent','electricity','water'], true)) {
            $paymentsQuery->where('category', $category);
        }
        $payments = $paymentsQuery->orderByDesc('due_date')->get();
        return view('pages.tenant.bills.index', compact('tenant', 'payments', 'category'));
    }

    public function uploadProof(Request $request, Payment $payment)
    {
        $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $path = $request->file('proof')->store('payment-proofs', 'public');
        $payment->update([
            'proof_path' => $path,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Bukti pembayaran diunggah.');
    }

    public function payBill(Request $request, Payment $payment)
    {
        $tenant = $this->me();
        if (!$tenant || $payment->tenant_id !== $tenant->id) {
            abort(403);
        }
        if ($payment->status === 'paid') {
            return redirect()->back()->with('info', 'Tagihan ini sudah lunas.');
        }
        $payment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
        if ($tenant->room_id) {
            $room = Room::find($tenant->room_id);
            if ($room) {
                if ($payment->category === 'electricity') {
                    $stillUnpaid = Payment::where('tenant_id', $tenant->id)
                        ->where('category', 'electricity')
                        ->where('status', 'unpaid')
                        ->exists();
                    if (!$stillUnpaid) {
                        $room->update(['electricity_status' => 'on']);
                    }
                }
                if ($payment->category === 'water') {
                    $stillUnpaid = Payment::where('tenant_id', $tenant->id)
                        ->where('category', 'water')
                        ->where('status', 'unpaid')
                        ->exists();
                    if (!$stillUnpaid) {
                        $room->update(['water_status' => 'on']);
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Pembayaran berhasil. Status tagihan sudah lunas.');
    }

    public function issues()
    {
        $tenant = $this->me();
        $issues = IssueReport::where('tenant_id', $tenant?->id)->orderByDesc('created_at')->get();
        return view('pages.tenant.issues.index', compact('tenant', 'issues'));
    }

    public function submitIssue(Request $request)
    {
        $tenant = $this->me();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        if (!$tenant || !$tenant->room_id) {
            return redirect()->back()->withErrors(['title' => 'Anda belum terhubung ke kamar. Ajukan kamar terlebih dahulu sebelum mengirim keluhan.']);
        }
        IssueReport::create([
            'tenant_id' => $tenant?->id,
            'room_id' => $tenant?->room_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            'reported_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Keluhan berhasil dikirim. Admin akan menindaklanjuti.');
    }

    public function history()
    {
        $tenant = $this->me();
        $payments = Payment::where('tenant_id', $tenant?->id)->whereNotNull('paid_at')->orderByDesc('paid_at')->get();
        return view('pages.tenant.history.index', compact('tenant', 'payments'));
    }

    public function chat(Request $request)
    {
        $contacts = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['admin', 'staff'])
            ->get();

        $unread = Message::select('sender_id', \DB::raw('COUNT(*) as c'))
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('c', 'sender_id');

        $selectedContact = null;
        $messages = collect();

        if ($request->has('user_id')) {
            $selectedContact = User::findOrFail($request->user_id);
            $messages = Message::where(function ($query) use ($selectedContact) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($query) use ($selectedContact) {
                $query->where('sender_id', $selectedContact->id)
                    ->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();

            Message::where('sender_id', $selectedContact->id)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        }

        return view('pages.tenant.chat', compact('contacts', 'selectedContact', 'messages', 'unread'));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message' => $data['message'],
            'is_read' => false,
        ]);

        return response()->json(['ok' => true]);
    }

    public function fetchMessages(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function exitIndex()
    {
        $tenant = $this->me();
        $latest = ExitRequest::where('tenant_id', $tenant?->id)->orderByDesc('created_at')->first();
        return view('pages.tenant.exit.index', compact('tenant', 'latest'));
    }

    public function submitExit(Request $request)
    {
        $tenant = $this->me();
        $data = $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);
        $hasPending = ExitRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->exists();
        if ($hasPending) {
            return redirect()->back()->with('info', 'Anda sudah memiliki pengajuan keluar yang sedang diproses.');
        }
        ExitRequest::create([
            'tenant_id' => $tenant->id,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);
        return redirect()->back()->with('success', 'Pengajuan keluar kos terkirim. Menunggu persetujuan admin.');
    }
}
