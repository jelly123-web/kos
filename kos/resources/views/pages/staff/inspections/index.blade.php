@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Cek Kondisi Kamar'" :title="'Cek Kondisi Kamar'" :subtitle="'Mengecek sebelum dihuni, saat keluar, dan laporkan kerusakan ke admin'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Catat Inspeksi</h3>
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
                <form method="POST" action="{{ route('staff.inspections.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Kamar</label>
                        <select name="room_id" class="mt-1 w-full border rounded px-3 py-2" required>
                            <option value="">-</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id')==$room->id)>{{ $room->number }} - {{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Jenis Inspeksi</label>
                        <select name="type" class="mt-1 w-full border rounded px-3 py-2" required>
                            <option value="pre_move_in" @selected(old('type')==='pre_move_in')>Sebelum dihuni</option>
                            <option value="post_move_out" @selected(old('type')==='post_move_out')>Saat penghuni keluar</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-xs text-slate-500">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="md:col-span-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_damage" value="1" class="mr-2" x-model="hasDamage">
                            <span class="text-sm">Ada kerusakan yang perlu dilaporkan ke admin</span>
                        </label>
                    </div>
                    <div class="md:col-span-3" x-data="{ hasDamage: false }" x-init="hasDamage = false">
                        <div x-show="hasDamage" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label class="text-xs text-slate-500">Judul Kerusakan</label>
                                <input name="damage_title" class="mt-1 w-full border rounded px-3 py-2" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-slate-500">Deskripsi Kerusakan</label>
                                <textarea name="damage_description" rows="3" class="mt-1 w-full border rounded px-3 py-2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Inspeksi</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Riwayat Inspeksi</h3>
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
@endsection
