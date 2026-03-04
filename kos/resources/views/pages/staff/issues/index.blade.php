@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan / Keluhan'" :title="'Laporan / Keluhan'" :subtitle="'Catat laporan dan pantau progres penanganan'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Buat Laporan</h3>
            </div>
            <div class="p-5">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('staff.issues.submit') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Kamar (opsional)</label>
                        <select name="room_id" class="mt-1 w-full border rounded px-3 py-2">
                            <option value="">-</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id')==$room->id)>{{ $room->number }} - {{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-slate-500">Judul</label>
                        <input name="title" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('title') }}" required />
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-xs text-slate-500">Deskripsi (opsional)</label>
                        <textarea name="description" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="md:col-span-3">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Laporan Ditugaskan ke Saya</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($issues as $issue)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $issue->title }}</div>
                                <div class="text-xs text-slate-500">{{ $issue->description ?: '-' }}</div>
                            </td>
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
                            <td class="px-6 py-4 text-right">
                                <form class="inline" method="POST" action="{{ route('staff.issues.status', $issue) }}">
                                    @csrf
                                    <select name="status" class="text-xs border rounded px-2 py-1">
                                        <option value="pending" {{ $issue->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $issue->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                                        <option value="done" {{ $issue->status === 'done' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    <button class="px-2 py-1 text-xs rounded bg-green-600 text-white">Simpan</button>
                                </form>
                                <form class="inline ml-2" method="POST" action="{{ route('staff.issues.destroy', $issue) }}" onsubmit="return confirm('Hapus laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 text-xs rounded bg-red-600 text-white">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($issues->isEmpty())
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada laporan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
