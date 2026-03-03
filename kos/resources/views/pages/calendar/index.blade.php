@extends('layouts.app')

@php
    $carbon = \Illuminate\Support\Carbon::create($year, $month, 1);
    $startWeekDay = $carbon->copy()->startOfMonth()->dayOfWeekIso; // 1..7
    $daysInMonth = $carbon->daysInMonth;
    $prev = $carbon->copy()->subMonth();
    $next = $carbon->copy()->addMonth();
    $today = \Illuminate\Support\Carbon::today()->toDateString();
@endphp

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Kalender'" :title="'Kalender'" :subtitle="'Jadwal tagihan dan kegiatan kos'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <a href="{{ route('calendar', ['month' => $prev->month, 'year' => $prev->year]) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/20">Prev</a>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $carbon->isoFormat('MMMM YYYY') }}</h3>
                <a href="{{ route('calendar', ['month' => $next->month, 'year' => $next->year]) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/20">Next</a>
            </div>
            <a href="{{ route('calendar') }}" class="text-sm text-primary font-bold">Today</a>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-500">
                <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
            </div>
            <div class="grid grid-cols-7 gap-2 mt-2">
                @for ($i = 1; $i < $startWeekDay; $i++)
                    <div class="h-28 rounded-xl bg-slate-50 dark:bg-slate-900/40"></div>
                @endfor
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $date = \Illuminate\Support\Carbon::create($year, $month, $d)->toDateString();
                        $items = $events[$date] ?? [];
                        $isToday = $date === $today;
                    @endphp
                    <div class="h-28 rounded-xl border border-slate-100 dark:border-slate-800 p-2 {{ $isToday ? 'ring-2 ring-primary/40' : '' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $d }}</span>
                            @if($isToday)
                                <span class="text-[10px] px-2 py-0.5 rounded bg-primary/10 text-primary font-bold">Today</span>
                            @endif
                        </div>
                        <div class="mt-2 space-y-1 overflow-y-auto max-h-20 custom-scrollbar">
                            @foreach($items as $it)
                                <div class="text-[11px] px-2 py-1 rounded {{ $it['type']==='payment' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $it['title'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endsection
