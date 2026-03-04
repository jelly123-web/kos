@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Pantau Kos'" :title="'Pantau Kos'" :subtitle="'Kondisi kos dan tim admin/staff'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Data Kos</h3>
            <div class="mt-4 space-y-2 text-sm">
                <div><span class="font-bold">{{ $property->name ?? '-' }}</span></div>
                <div class="text-slate-600">{{ $property->address ?? '-' }}</div>
                <div class="text-slate-600">Harga default kamar: {{ $property?->default_room_price ? 'Rp '.number_format($property->default_room_price,0,',','.') : '-' }}</div>
                <div class="text-slate-600">Fasilitas: {{ $property->facilities ?? '-' }}</div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Kondisi Kamar</h3>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Total Kamar</span><span class="font-bold">{{ $roomsTotal }}</span></div>
                <div class="flex justify-between"><span>Kamar Kosong</span><span class="font-bold">{{ $roomsEmpty }}</span></div>
                <div class="flex justify-between"><span>Kamar Terisi</span><span class="font-bold">{{ $roomsOccupied }}</span></div>
                <div class="flex justify-between"><span>Maintenance</span><span class="font-bold">{{ $roomsMaintenance }}</span></div>
                <div class="flex justify-between"><span>Penghuni Aktif</span><span class="font-bold">{{ $activeTenants }}</span></div>
                <div class="flex justify-between"><span>Laporan Perbaikan Aktif</span><span class="font-bold">{{ $issuesOpen }}</span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Tim Admin & Staff</h3>
            <div class="mt-4">
                <p class="text-sm font-bold mb-2">Admin ({{ count($admins) }})</p>
                <ul class="text-sm space-y-1">
                    @foreach($admins as $u)
                    <li>{{ $u->name }} ({{ $u->username }})</li>
                    @endforeach
                </ul>
                <p class="text-sm font-bold mt-4 mb-2">Staff ({{ count($staffs) }})</p>
                <ul class="text-sm space-y-1">
                    @foreach($staffs as $u)
                    <li>{{ $u->name }} ({{ $u->username }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="xl:col-span-3 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Inspeksi Terbaru dari Staff</h3>
            <div class="overflow-x-auto mt-3">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kerusakan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($recentInspections as $ins)
                        <tr>
                            <td class="px-6 py-3">{{ $ins->room?->number ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $ins->type === 'pre_move_in' ? 'Sebelum dihuni' : 'Saat keluar' }}</td>
                            <td class="px-6 py-3">{{ $ins->notes ?: '-' }}</td>
                            <td class="px-6 py-3">
                                @if($ins->issue)
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Dilaporkan</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $ins->inspected_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                        @if($recentInspections->isEmpty())
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada data.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
