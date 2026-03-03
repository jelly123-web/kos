@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan Pemasukan'" :title="'Laporan'" :subtitle="'Pemasukan total dan bulanan'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Ringkasan</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><span>Total Pemasukan</span><span class="font-bold">Rp {{ number_format($summary['revenue_total'],0,',','.') }}</span></div>
                <div class="flex justify-between"><span>Pemasukan Bulan Ini</span><span class="font-bold">Rp {{ number_format($summary['revenue_month'],0,',','.') }}</span></div>
            </div>
        </div>

        <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Laporan Bulanan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Bulan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Masuk (Lunas)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Belum Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($monthly as $row)
                        <tr>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::create($row->y, $row->m, 1)->isoFormat('MMMM YYYY') }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($row->paid ?? 0,0,',','.') }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($row->unpaid ?? 0,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
