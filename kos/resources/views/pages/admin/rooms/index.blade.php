@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Manajemen Kamar'" :title="'Kamar'" :subtitle="'Kelola data kamar kos'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kamar</h3>
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Total: {{ count($rooms) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Harga</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($rooms as $room)
                        <tr>
                            <td class="px-6 py-4">{{ $room->number }}</td>
                            <td class="px-6 py-4">{{ $room->name }}</td>
                            <td class="px-6 py-4">{{ $room->price ? 'Rp '.number_format($room->price,0,',','.') : '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $room->status === 'empty' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $room->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form class="inline" method="POST" action="{{ route('admin.rooms.toggle', $room) }}">
                                    @csrf
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/20">
                                        Toggle Status
                                    </button>
                                </form>
                                <form class="inline" method="POST" action="{{ route('admin.rooms.update', $room) }}">
                                    @csrf
                                    <input type="text" name="name" value="{{ $room->name }}" class="border rounded px-2 py-1 text-xs w-32" />
                                    <input type="number" name="price" value="{{ $room->price }}" class="border rounded px-2 py-1 text-xs w-28" />
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white">Simpan</button>
                                </form>
                                <form class="inline" method="POST" action="{{ route('admin.rooms.destroy', $room) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-red-100 text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Kamar Baru</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.rooms.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-500">Nomor</label>
                        <input name="number" class="mt-1 w-full border rounded px-3 py-2" placeholder="A1" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Nama</label>
                        <input name="name" class="mt-1 w-full border rounded px-3 py-2" placeholder="Kamar A1" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Harga</label>
                        <input type="number" name="price" class="mt-1 w-full border rounded px-3 py-2" placeholder="1000000" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Fasilitas</label>
                        <input name="facilities" class="mt-1 w-full border rounded px-3 py-2" placeholder="AC, WiFi" />
                    </div>
                    <div class="md:col-span-4">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
