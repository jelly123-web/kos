<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function metricsSummary(Request $request)
    {
        $totalRevenue = (int) Payment::where('status', 'paid')->sum('amount');
        $monthRevenue = (int) Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
        $todayRevenue = (int) Payment::where('status', 'paid')
            ->whereDate('paid_at', now()->toDateString())
            ->sum('amount');
        $totalUnpaid = (int) Payment::where('status', 'unpaid')->sum('amount');
        $activeTenants = (int) Tenant::where('status', 'active')->count();

        $target = (int) (config('app.month_target') ?? 0);
        $progressPercent = $target > 0 ? round(($monthRevenue / $target) * 100) : 0;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'month_revenue' => $monthRevenue,
            'total_unpaid' => $totalUnpaid,
            'active_tenants' => $activeTenants,
            'month_target' => $target,
            'progress_percent' => $progressPercent,
            'today_revenue' => $todayRevenue,
        ]);
    }

    public function chartPayments(Request $request)
    {
        $rows = Payment::select(
                DB::raw('YEAR(due_date) as y'),
                DB::raw('MONTH(due_date) as m'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid")
            )
            ->groupBy('y','m')
            ->orderBy('y')->orderBy('m')
            ->limit(12)
            ->get();

        $labels = [];
        $paid = [];
        foreach ($rows as $r) {
            $labels[] = \Carbon\Carbon::create($r->y, $r->m, 1)->isoFormat('MMM YYYY');
            $paid[] = (int) ($r->paid ?? 0);
        }
        return response()->json([
            'labels' => $labels,
            'paid' => $paid,
        ]);
    }
}
