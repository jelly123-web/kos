<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function rooms()
    {
        $rooms = Room::orderBy('number')->get();
        return view('pages.owner.rooms.index', compact('rooms'));
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
        return view('pages.owner.payments.index', compact('payments', 'unpaidCount'));
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

        $arrears = Payment::with(['tenant','room'])
            ->where('status', 'unpaid')
            ->orderByDesc('due_date')
            ->limit(20)
            ->get();

        return view('pages.owner.reports.index', compact('summary', 'monthly', 'arrears'));
    }

    public function exportReport()
    {
        $rows = Payment::select(
                DB::raw('YEAR(due_date) as year'),
                DB::raw('MONTH(due_date) as month'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid"),
                DB::raw("SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as unpaid")
            )
            ->groupBy('year','month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $csv = implode(',', ['Year','Month','Paid','Unpaid'])."\n";
        foreach ($rows as $r) {
            $csv .= implode(',', [$r->year, $r->month, $r->paid ?? 0, $r->unpaid ?? 0])."\n";
        }
        $filename = 'owner_report_'.now()->format('Ymd_His').'.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function monitor()
    {
        $property = Property::first();
        $roomsTotal = Room::count();
        $roomsEmpty = Room::where('status', 'empty')->count();
        $roomsOccupied = Room::where('status', 'occupied')->count();
        $roomsMaintenance = Room::where('status', 'maintenance')->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $issuesOpen = \App\Models\IssueReport::whereIn('status', ['pending','in_progress'])->count();
        $recentInspections = \App\Models\RoomInspection::with('room')->orderByDesc('created_at')->limit(5)->get();
        $admins = User::where('role', 'admin')->get();
        $staffs = User::where('role', 'staff')->get();

        return view('pages.owner.monitor.index', compact(
            'property','roomsTotal','roomsEmpty','roomsOccupied','roomsMaintenance',
            'activeTenants','issuesOpen','recentInspections','admins','staffs'
        ));
    }

    public function evict(Request $request, Tenant $tenant)
    {
        $room = $tenant->room;
        $tenant->update(['status' => 'inactive', 'room_id' => null]);
        if ($room) {
            $room->update(['status' => 'empty']);
        }
        return redirect()->back()->with('success', 'Penghuni berhasil dikeluarkan dari kamar.');
    }

    public function chat(Request $request)
    {
        $contacts = \App\Models\User::where('id', '!=', auth()->id())
            ->whereIn('role', ['admin', 'super_admin', 'staff', 'manager', 'tenant', 'owner'])
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
            $messages = \App\Models\Message::where(function ($q) use ($selectedContact) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($q) use ($selectedContact) {
                $q->where('sender_id', $selectedContact->id)->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();
            \App\Models\Message::where('sender_id', $selectedContact->id)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        }
        return view('pages.owner.chat', compact('contacts', 'selectedContact', 'messages', 'unread'));
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
        $messages = \App\Models\Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}
