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

        return view('pages.owner.reports.index', compact('summary', 'monthly'));
    }

    public function monitor()
    {
        $property = Property::first();
        $roomsTotal = Room::count();
        $roomsEmpty = Room::where('status', 'empty')->count();
        $roomsOccupied = Room::where('status', 'occupied')->count();
        $admins = User::where('role', 'admin')->get();
        $staffs = User::where('role', 'staff')->get();

        return view('pages.owner.monitor.index', compact(
            'property','roomsTotal','roomsEmpty','roomsOccupied','admins','staffs'
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
}
