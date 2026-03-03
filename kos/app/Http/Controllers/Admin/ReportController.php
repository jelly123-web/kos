<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;

class ReportController extends Controller
{
    public function index()
    {
        $summary = [
            'rooms_total' => Room::count(),
            'rooms_empty' => Room::where('status', 'empty')->count(),
            'rooms_occupied' => Room::where('status', 'occupied')->count(),
            'tenants' => Tenant::count(),
            'payments_paid' => Payment::where('status', 'paid')->count(),
            'payments_unpaid' => Payment::where('status', 'unpaid')->count(),
        ];
        $recentPayments = Payment::with('tenant')->orderByDesc('created_at')->limit(10)->get();
        return view('pages.admin.reports.index', compact('summary', 'recentPayments'));
    }
}
