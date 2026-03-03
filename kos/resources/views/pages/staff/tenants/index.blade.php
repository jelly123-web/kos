@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Kelola Penghuni'" :title="'Penghuni'" :subtitle="'Catat penghuni baru & update data'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Penghuni</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Telepon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Kamar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($tenants as $tenant)
                        <tr>
                            <td class="px-6 py-4">{{ $tenant->name }}</td>
                            <td class="px-6 py-4">{{ $tenant->phone }}</td>
                            <td class="px-6 py-4">{{ $tenant->room?->number }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form class="inline" method="POST" action="{{ route('staff.tenants.update', $tenant) }}">
                                    @csrf
                                    <select name="room_id" class="border rounded px-2 py-1 text-xs">
                                        <option value="">-</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" @selected($tenant->room_id === $room->id)>{{ $room->number }}</option>
                                        @endforeach
                                    </select>
                                    <select name="status" class="border rounded px-2 py-1 text-xs">
                                        <option value="active" @selected($tenant->status==='active')>Aktif</option>
                                        <option value="inactive" @selected($tenant->status==='inactive')>Nonaktif</option>
                                    </select>
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white">Simpan</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Catat Penghuni Baru</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('staff.tenants.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Nama</label>
                        <input name="name" class="mt-1 w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Telepon</label>
                        <input name="phone" class="mt-1 w-full border rounded px-3 py-2" />
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
                        <label class="text-xs text-slate-500">Tgl Masuk</label>
                        <input type="date" name="start_date" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div class="md:col-span-4">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
