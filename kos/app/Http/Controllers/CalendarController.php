<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Operation;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = (int)($request->query('month', now()->month));
        $year = (int)($request->query('year', now()->year));
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $role = auth()->user()->role ?? 'guest';

        $paymentsQuery = Payment::whereBetween('due_date', [$start->toDateString(), $end->toDateString()]);
        if ($role === 'tenant') {
            $tenant = Tenant::where('user_id', auth()->id())->first();
            if ($tenant) {
                $paymentsQuery->where('tenant_id', $tenant->id);
            } else {
                $paymentsQuery->whereRaw('1=0');
            }
        }
        $payments = $paymentsQuery->get(['id','tenant_id','amount','status','due_date']);

        $operations = Operation::whereBetween('scheduled_at', [$start->copy()->startOfMonth(), $end->copy()->endOfMonth()])
            ->get(['id','title','scheduled_at','status']);

        $events = [];
        foreach ($payments as $p) {
            $d = Carbon::parse($p->due_date)->toDateString();
            $events[$d][] = [
                'type' => 'payment',
                'title' => 'Tagihan Rp '.number_format($p->amount,0,',','.'),
                'status' => $p->status,
            ];
        }
        foreach ($operations as $o) {
            $d = Carbon::parse($o->scheduled_at)->toDateString();
            $events[$d][] = [
                'type' => 'operation',
                'title' => $o->title,
                'status' => $o->status,
            ];
        }

        return view('pages.calendar.index', compact('month','year','events'));
    }
}
