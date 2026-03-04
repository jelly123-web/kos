@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Manajemen Pembayaran'" :title="'Pembayaran'" :subtitle="'Catat dan cek pembayaran kos'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Riwayat Pembayaran</h3>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.payments.generate') }}">
                        @csrf
                        <button class="px-3 py-1 rounded-lg bg-primary text-white text-xs font-bold">Generate Tagihan Bulanan</button>
                    </form>
                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Total: {{ count($payments) }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Penghuni</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Kamar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($payments as $payment)
                        <tr>
                            <td class="px-6 py-4">{{ $payment->tenant?->name }}</td>
                            <td class="px-6 py-4">{{ $payment->room?->number }}</td>
                            <td class="px-6 py-4">{{ ucfirst($payment->category ?? 'rent') }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($payment->amount,0,',','.') }}</td>
                            <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($payment->due_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($payment->status === 'unpaid')
                                <form class="inline" method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">
                                    @csrf
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white">Tandai Lunas</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Catat Pembayaran</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.payments.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Penghuni</label>
                        <select name="tenant_id" class="mt-1 w-full border rounded px-3 py-2" required>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Kamar</label>
                        <select name="room_id" class="mt-1 w-full border rounded px-3 py-2">
                            <option value="">-</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Jenis</label>
                        <select name="category" class="mt-1 w-full border rounded px-3 py-2">
                            <option value="rent">Sewa</option>
                            <option value="electricity">Listrik</option>
                            <option value="water">Air</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Jumlah</label>
                        <input type="number" name="amount" class="mt-1 w-full border rounded px-3 py-2" placeholder="1000000" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="mt-1 w-full border rounded px-3 py-2" required />
                    </div>
                    <div class="md:col-span-4">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pengaturan Tagihan Bulanan</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.payments.settings') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Biaya Listrik / Bulan</label>
                        <input type="number" name="electricity_fee" class="mt-1 w-full border rounded px-3 py-2" value="{{ \App\Models\Setting::getValue('electricity_fee', 100000) }}" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Biaya Air / Bulan</label>
                        <input type="number" name="water_fee" class="mt-1 w-full border rounded px-3 py-2" value="{{ \App\Models\Setting::getValue('water_fee', 50000) }}" required />
                    </div>
                    <div class="md:col-span-3">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
