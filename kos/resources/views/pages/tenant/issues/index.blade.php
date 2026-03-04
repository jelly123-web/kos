@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Keluhan / Laporan'" :title="'Keluhan / Laporan'" :subtitle="'Kirim keluhan dan pantau statusnya'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kirim Keluhan</h3>
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
                <form method="POST" action="{{ route('tenant.issues.submit') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Kamar</label>
                        <input class="mt-1 w-full border rounded px-3 py-2 bg-slate-50" value="{{ $tenant?->room?->number ?? '-' }}" readonly />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Judul</label>
                        <input name="title" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('title') }}" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-slate-500">Deskripsi (opsional)</label>
                        <textarea name="description" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white" {{ $tenant?->room_id ? '' : 'disabled' }}>Kirim Keluhan</button>
                        @if(!$tenant?->room_id)
                            <p class="text-xs text-red-600 mt-2">Anda belum terhubung ke kamar. Ajukan kamar terlebih dahulu.</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Riwayat Keluhan Saya</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Petugas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat</th>
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
                            <td class="px-6 py-4">{{ $issue->assignee?->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($issue->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                        @if($issues->isEmpty())
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada keluhan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
