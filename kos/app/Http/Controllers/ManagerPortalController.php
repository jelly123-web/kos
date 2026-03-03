<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerPortalController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard.ecommerce');
    }

    public function rooms()
    {
        $rooms = Room::orderBy('number')->get();
        return view('pages.manager.rooms.index', compact('rooms'));
    }

    public function tenants()
    {
        $tenants = Tenant::with('room')->orderBy('name')->get();
        return view('pages.owner.tenants.index', compact('tenants'));
    }

    public function payments()
    {
        $payments = Payment::with(['tenant', 'room'])->orderByDesc('due_date')->get();
        $unpaidCount = Payment::where('status', 'unpaid')->count();
        return view('pages.manager.payments.index', compact('payments', 'unpaidCount'));
    }

    public function reports()
    {
        $summary = [
            'revenue_total' => Payment::where('status', 'paid')->sum('amount'),
            'revenue_month' => Payment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ];
        $monthly = Payment::select(
                DB::raw('YEAR(due_date) as y'),
                DB::raw('MONTH(due_date) as m'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid"),
                DB::raw("SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as unpaid")
            )
            ->groupBy('y','m')
            ->orderBy('y','desc')->orderBy('m','desc')
            ->limit(12)
            ->get();

        return view('pages.manager.reports.index', compact('summary', 'monthly'));
    }

    public function operations()
    {
        $ops = Operation::orderByDesc('created_at')->get();
        return view('pages.manager.operations.index', compact('ops'));
    }

    public function chat(Request $request)
    {
        $contacts = \App\Models\User::where('id', '!=', auth()->id())
            ->whereIn('role', ['admin', 'super_admin', 'staff'])
            ->get();

        $unread = \App\Models\Message::select('sender_id', \DB::raw('COUNT(*) as c'))
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('c', 'sender_id');

        $selectedContact = null;
        $messages = collect();

        if ($request->has('user_id')) {
            $selectedContact = \App\Models\User::findOrFail($request->user_id);
            $messages = \App\Models\Message::where(function ($query) use ($selectedContact) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($query) use ($selectedContact) {
                $query->where('sender_id', $selectedContact->id)
                    ->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();

            \App\Models\Message::where('sender_id', $selectedContact->id)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        }

        return view('pages.manager.chat', compact('contacts', 'selectedContact', 'messages', 'unread'));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message' => $data['message'],
            'is_read' => false,
        ]);

        return response()->json(['ok' => true]);
    }

    public function fetchMessages(\App\Models\User $user)
    {
        $messages = \App\Models\Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
    public function storeOperation(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);
        $data['status'] = 'planned';
        $data['reported'] = false;
        $data['created_by'] = auth()->id();
        Operation::create($data);
        return redirect()->back()->with('success', 'Kegiatan ditambahkan.');
    }

    public function updateOperation(Request $request, Operation $operation)
    {
        $data = $request->validate([
            'status' => 'required|in:planned,in_progress,done',
            'reported' => 'nullable|boolean',
        ]);
        $operation->update([
            'status' => $data['status'],
            'reported' => $request->boolean('reported'),
        ]);
        return redirect()->back()->with('success', 'Kegiatan diperbarui.');
    }
}
