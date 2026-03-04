<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Support\Carbon;
use App\Models\Setting;

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
            'revenue_total' => (int) Payment::where('status', 'paid')->sum('amount'),
            'revenue_month' => (int) Payment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ];
        $recentPayments = Payment::with('tenant')->orderByDesc('created_at')->limit(10)->get();
        return view('pages.admin.reports.index', compact('summary', 'recentPayments'));
    }

    public function chartPayments()
    {
        $labels = [];
        $paid = [];
        $unpaid = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->copy()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();
            $labels[] = $start->format('M Y');
            $paid[] = (int) Payment::whereNotNull('tenant_id')->where('status', 'paid')
                ->whereBetween('paid_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->sum('amount');
            $unpaid[] = (int) Payment::whereNotNull('tenant_id')->where('status', 'unpaid')
                ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');
        }
        return response()->json([
            'labels' => $labels,
            'paid' => $paid,
            'unpaid' => $unpaid,
        ]);
    }

    public function metricsSummary()
    {
        $totalRevenue = (int) Payment::whereNotNull('tenant_id')->where('status', 'paid')->sum('amount');
        $monthRevenue = (int) Payment::whereNotNull('tenant_id')
            ->where('status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');
        $todayRevenue = (int) Payment::whereNotNull('tenant_id')
            ->where('status', 'paid')
            ->whereDate('paid_at', Carbon::now()->toDateString())
            ->sum('amount');
        $monthTarget = (int) (Setting::getValue('monthly_target', 0));
        $progress = $monthTarget > 0 ? round(($monthRevenue / $monthTarget) * 100, 2) : 0;
        $totalUnpaid = (int) Payment::whereNotNull('tenant_id')->where('status', 'unpaid')->sum('amount');
        $activeTenants = (int) Tenant::where('status', 'active')->count();
        return response()->json([
            'total_revenue' => $totalRevenue,
            'month_revenue' => $monthRevenue,
            'today_revenue' => $todayRevenue,
            'month_target' => $monthTarget,
            'progress_percent' => $progress,
            'total_unpaid' => $totalUnpaid,
            'active_tenants' => $activeTenants,
        ]);
    }
}
