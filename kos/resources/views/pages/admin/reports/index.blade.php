@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan'" :title="'Laporan'" :subtitle="'Pembayaran, Penghuni, Kamar'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Ringkasan</h3>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Total Kamar</span><span class="font-bold">{{ $summary['rooms_total'] }}</span></div>
                <div class="flex justify-between"><span>Kamar Kosong</span><span class="font-bold">{{ $summary['rooms_empty'] }}</span></div>
                <div class="flex justify-between"><span>Kamar Terisi</span><span class="font-bold">{{ $summary['rooms_occupied'] }}</span></div>
                <div class="flex justify-between"><span>Total Penghuni</span><span class="font-bold">{{ $summary['tenants'] }}</span></div>
                <div class="flex justify-between"><span>Bayar Lunas</span><span class="font-bold">{{ $summary['payments_paid'] }}</span></div>
                <div class="flex justify-between"><span>Belum Bayar</span><span class="font-bold">{{ $summary['payments_unpaid'] }}</span></div>
            </div>
        </div>

        <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pembayaran Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Penghuni</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($recentPayments as $p)
                        <tr>
                            <td class="px-6 py-4">{{ $p->tenant?->name }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($p->amount,0,',','.') }}</td>
                            <td class="px-6 py-4">{{ $p->status }}</td>
                            <td class="px-6 py-4">{{ $p->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
