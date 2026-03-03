<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Payment;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffPortalController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard.ecommerce');
    }

    public function tenants()
    {
        $tenants = Tenant::with('room')->orderBy('name')->get();
        $rooms = Room::orderBy('number')->get();
        return view('pages.staff.tenants.index', compact('tenants', 'rooms'));
    }

    public function storeTenant(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'room_id' => 'nullable|exists:rooms,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
        $data['status'] = 'active';
        $tenant = Tenant::create($data);
        if ($tenant->room_id) {
            $tenant->room->update(['status' => 'occupied']);
        }
        return redirect()->back()->with('success', 'Penghuni dicatat.');
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'phone' => 'nullable|string|max:50',
            'room_id' => 'nullable|exists:rooms,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);
        $tenant->update($data);
        if ($tenant->room_id) {
            $tenant->room->update(['status' => 'occupied']);
        }
        return redirect()->back()->with('success', 'Data penghuni diupdate.');
    }

    public function rooms()
    {
        $rooms = Room::orderBy('number')->get();
        return view('pages.staff.rooms.index', compact('rooms'));
    }

    public function payments()
    {
        $payments = Payment::with(['tenant', 'room'])->orderByDesc('due_date')->get();
        $tenants = Tenant::orderBy('name')->get();
        $rooms = Room::orderBy('number')->get();
        return view('pages.staff.payments.index', compact('payments', 'tenants', 'rooms'));
    }

    public function storePayment(Request $request)
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

    public function chat(Request $request)
    {
        $contacts = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['tenant', 'admin'])
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

        return view('pages.staff.chat', compact('contacts', 'selectedContact', 'messages', 'unread'));
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
}
