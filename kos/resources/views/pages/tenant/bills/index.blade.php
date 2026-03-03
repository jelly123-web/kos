@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Tagihan Kos'" :title="'Tagihan'" :subtitle="'Tagihan dan jatuh tempo'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Tagihan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jatuh Tempo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4">Rp {{ number_format($payment->amount,0,',','.') }}</td>
                        <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($payment->due_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($payment->status === 'unpaid')
                            <form method="POST" action="{{ route('tenant.bills.upload', $payment) }}" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                                @csrf
                                <input type="file" name="proof" class="text-xs" required>
                                <button class="px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white">Upload Bukti & Konfirmasi</button>
                            </form>
                            @else
                                <a class="text-xs text-slate-600" href="{{ $payment->proof_path ? asset('storage/'.$payment->proof_path) : '#' }}" target="_blank">Lihat Bukti</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
