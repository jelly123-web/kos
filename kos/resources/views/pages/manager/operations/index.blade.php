@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Operasional Kos'" :title="'Kegiatan Kos'" :subtitle="'Atur kegiatan dan laporkan ke Owner'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Ringkasan Penanganan Laporan</h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-slate-500">Pending</div>
                    <div class="text-2xl font-bold">{{ $issueCounts['pending'] ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-slate-500">Diproses</div>
                    <div class="text-2xl font-bold">{{ $issueCounts['in_progress'] ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-slate-500">Selesai</div>
                    <div class="text-2xl font-bold">{{ $issueCounts['done'] ?? 0 }}</div>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h4 class="text-sm font-bold text-slate-700 dark:text-white">Keluhan Terbaru</h4>
            </div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penghuni</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($latestIssues as $issue)
                        <tr>
                            <td class="px-6 py-3">
                                <div class="font-bold text-slate-800">{{ $issue->title }}</div>
                                <div class="text-xs text-slate-500">{{ $issue->description ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-3">{{ $issue->tenant?->name ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $issue->room?->number ?? '-' }}</td>
                            <td class="px-6 py-3">
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
                            <td class="px-6 py-3">{{ $issue->assignee?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                        @if(($latestIssues ?? collect())->isEmpty())
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada data.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Kegiatan</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('manager.operations.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="text-xs text-slate-500">Judul</label>
                        <input name="title" class="mt-1 w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Jadwal</label>
                        <input type="datetime-local" name="scheduled_at" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div class="md:col-span-4">
                        <label class="text-xs text-slate-500">Deskripsi</label>
                        <textarea name="description" rows="3" class="mt-1 w-full border rounded px-3 py-2"></textarea>
                    </div>
                    <div class="md:col-span-4">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kegiatan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Judul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Jadwal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Laporkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($ops as $op)
                        <tr>
                            <td class="px-6 py-4">{{ $op->title }}</td>
                            <td class="px-6 py-4">{{ $op->scheduled_at ? $op->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-6 py-4">
                                <form class="inline" method="POST" action="{{ route('manager.operations.update', $op) }}">
                                    @csrf
                                    <select name="status" class="border rounded px-2 py-1 text-xs">
                                        <option value="planned" @selected($op->status==='planned')>Direncanakan</option>
                                        <option value="in_progress" @selected($op->status==='in_progress')>Berjalan</option>
                                        <option value="done" @selected($op->status==='done')>Selesai</option>
                                    </select>
                                    <label class="ml-3 text-xs">
                                        <input type="checkbox" name="reported" value="1" @checked($op->reported) />
                                        Dilaporkan ke Owner
                                    </label>
                                    <button class="ml-2 px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white">Simpan</button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $op->reported ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $op->reported ? 'Sudah' : 'Belum' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
