<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Payment;
use App\Models\User;
use App\Models\Message;
use App\Models\IssueReport;
use App\Models\RoomInspection;
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

    public function issues()
    {
        $issues = IssueReport::with(['tenant','room','assignee'])
            ->where('assigned_to', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        $rooms = Room::orderBy('number')->get();
        return view('pages.staff.issues.index', compact('issues', 'rooms'));
    }

    public function submitIssue(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
        ]);
        IssueReport::create([
            'tenant_id' => null,
            'room_id' => $data['room_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'in_progress',
            'assigned_to' => auth()->id(),
            'reported_at' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Laporan berhasil dibuat.');
    }

    public function updateIssueStatus(Request $request, IssueReport $issue)
    {
        if ($issue->assigned_to !== auth()->id()) {
            abort(403);
        }
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);
        $issue->update(['status' => $data['status']]);
        return redirect()->back()->with('success', 'Status laporan diperbarui.');
    }

    public function destroyIssue(IssueReport $issue)
    {
        if ($issue->assigned_to !== auth()->id()) {
            abort(403);
        }
        $issue->delete();
        return redirect()->back()->with('success', 'Laporan dihapus.');
    }

    public function inspections()
    {
        $rooms = Room::orderBy('number')->get();
        $inspections = RoomInspection::with(['room','issue'])->orderByDesc('created_at')->get();
        return view('pages.staff.inspections.index', compact('rooms', 'inspections'));
    }

    public function storeInspection(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'type' => 'required|in:pre_move_in,post_move_out',
            'notes' => 'nullable|string',
            'has_damage' => 'nullable|boolean',
            'damage_title' => 'nullable|string|max:255',
            'damage_description' => 'nullable|string',
        ]);

        $issueId = null;
        if ($request->boolean('has_damage')) {
            $issue = IssueReport::create([
                'tenant_id' => null,
                'room_id' => $data['room_id'],
                'title' => $data['damage_title'] ?? 'Kerusakan kamar',
                'description' => $data['damage_description'] ?? null,
                'status' => 'pending',
                'assigned_to' => null,
                'reported_at' => Carbon::now(),
            ]);
            $issueId = $issue->id;
        }

        RoomInspection::create([
            'room_id' => $data['room_id'],
            'inspector_id' => auth()->id(),
            'type' => $data['type'],
            'notes' => $data['notes'] ?? null,
            'issue_report_id' => $issueId,
            'inspected_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Inspeksi dicatat.');
    }
}
