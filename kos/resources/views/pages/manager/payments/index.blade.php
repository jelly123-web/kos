@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Mengecek Pembayaran'" :title="'Pembayaran'" :subtitle="'Lihat pembayaran & yang belum bayar'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Riwayat Pembayaran</h3>
            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">Belum bayar: {{ $unpaidCount }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Penghuni</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Kamar</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jatuh Tempo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4">{{ $payment->tenant?->name }}</td>
                        <td class="px-6 py-4">{{ $payment->room?->number }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($payment->amount,0,',','.') }}</td>
                        <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($payment->due_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
