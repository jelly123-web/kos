@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan / Keluhan'" :title="'Laporan / Keluhan'" :subtitle="'Kelola laporan kerusakan dan penugasan staff'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Laporan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penghuni</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($issues as $issue)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $issue->title }}</div>
                            <div class="text-xs text-slate-500">{{ Str::limit($issue->description, 80) }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $issue->tenant?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $issue->room?->number ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{
                                $issue->status === 'done' ? 'bg-green-100 text-green-700' :
                                ($issue->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700')
                            }}">
                                {{ str_replace('_',' ', ucfirst($issue->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $issue->assignee?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form class="inline" method="POST" action="{{ route('admin.issues.assign', $issue) }}">
                                @csrf
                                <select name="assigned_to" class="text-xs border rounded px-2 py-1">
                                    <option value="">- Pilih staff -</option>
                                    @foreach($staffs as $s)
                                        <option value="{{ $s->id }}" {{ $issue->assigned_to == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                <button class="px-2 py-1 text-xs rounded bg-primary text-white">Tugas</button>
                            </form>
                            <form class="inline ml-2" method="POST" action="{{ route('admin.issues.status', $issue) }}">
                                @csrf
                                <select name="status" class="text-xs border rounded px-2 py-1">
                                    <option value="pending" {{ $issue->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $issue->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                                    <option value="done" {{ $issue->status === 'done' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <button class="px-2 py-1 text-xs rounded bg-green-600 text-white">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($issues->isEmpty())
                    <tr><td class="px-6 py-6 text-center text-slate-500" colspan="6">Belum ada laporan.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

