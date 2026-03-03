@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Riwayat Pembayaran'" :title="'Riwayat'" :subtitle="'Daftar pembayaran sebelumnya'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Riwayat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Tanggal Lunas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($payments as $p)
                    <tr>
                        <td class="px-6 py-4">Rp {{ number_format($p->amount,0,',','.') }}</td>
                        <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($p->paid_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @if($p->proof_path)
                                <a class="text-xs text-primary" href="{{ asset('storage/'.$p->proof_path) }}" target="_blank">Lihat</a>
                            @else
                                <span class="text-xs text-slate-600">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
