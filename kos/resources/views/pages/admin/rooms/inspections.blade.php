@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Inspeksi & Kondisi Kamar'" :title="'Inspeksi Kamar'" :subtitle="'Lihat laporan penghuni, update staff, dan kelola status kamar'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kelola Status & Kondisi</h3>
            </div>
            <div class="p-5 space-y-6">
                <form method="POST" action="{{ route('admin.rooms.condition', $rooms->first()) }}" x-data="{ roomId: '{{ $rooms->first()->id ?? '' }}' }" x-on:change.window="
                    if($event.detail?.roomId){ roomId = $event.detail.roomId; $el.action = '/admin/rooms/' + roomId + '/condition'; }
                ">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Pilih Kamar</label>
                        <select class="mt-1 w-full border rounded px-3 py-2" x-on:change="$dispatch('change', { roomId: $event.target.value })">
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">{{ $r->number }} - {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Kondisi</label>
                        <select name="condition" class="mt-1 w-full border rounded px-3 py-2">
                            <option value="good">Baik</option>
                            <option value="damaged">Rusak</option>
                            <option value="needs_repair">Perlu Perbaikan</option>
                        </select>
                    </div>
                    <button class="px-4 py-2 rounded-lg bg-primary text-white mt-2">Simpan Kondisi</button>
                </form>

                <form method="POST" action="{{ route('admin.rooms.set-status', $rooms->first()) }}" x-data="{ roomId2: '{{ $rooms->first()->id ?? '' }}' }" x-on:change.window="
                    if($event.detail?.roomId){ roomId2 = $event.detail.roomId; $el.action = '/admin/rooms/' + roomId2 + '/set-status'; }
                ">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Status Kamar</label>
                        <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                            <option value="empty">Kosong</option>
                            <option value="occupied">Terisi</option>
                            <option value="maintenance">Maintenance / Perbaikan</option>
                        </select>
                    </div>
                    <button class="px-4 py-2 rounded-lg bg-primary text-white mt-2">Simpan Status</button>
                </form>

                <form method="POST" action="{{ route('admin.rooms.mark-ready', $rooms->first()) }}" x-data="{ roomId3: '{{ $rooms->first()->id ?? '' }}' }" x-on:change.window="
                    if($event.detail?.roomId){ roomId3 = $event.detail.roomId; $el.action = '/admin/rooms/' + roomId3 + '/mark-ready'; }
                ">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-green-600 text-white w-full">Setujui Siap Dipakai (Set Kosong + Baik)</button>
                </form>
            </div>
        </div>

        <div class="xl:col-span-2 grid grid-cols-1 gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Laporan Kerusakan dari Penghuni</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penghuni</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($tenantReports as $issue)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $issue->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $issue->description ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $issue->tenant?->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $issue->room?->number ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $labels = ['pending' => 'Pending', 'in_progress' => 'Diproses', 'done' => 'Selesai'];
                                        $classes = [
                                            'pending' => 'bg-orange-100 text-orange-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'done' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $classes[$issue->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $labels[$issue->status] ?? ucfirst($issue->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($issue->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                            @if($tenantReports->isEmpty())
                            <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada laporan.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Update Inspeksi dari Staff</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kerusakan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($inspections as $ins)
                            <tr>
                                <td class="px-6 py-4">{{ $ins->room?->number ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $ins->type === 'pre_move_in' ? 'Sebelum dihuni' : 'Saat keluar' }}</td>
                                <td class="px-6 py-4">{{ $ins->notes ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($ins->issue)
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">Dilaporkan ke admin</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $ins->inspected_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                            @if($inspections->isEmpty())
                            <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada inspeksi.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
