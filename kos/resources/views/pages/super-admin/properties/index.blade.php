@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Data Kos Global'" :title="'Data Kos Global'" :subtitle="'Tambah, edit, hapus data kos (multi-kos)'" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Data Kos</h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('super-admin.properties.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div class="md:col-span-1">
                        <label class="text-xs text-slate-500">Nama</label>
                        <input name="name" class="mt-1 w-full border rounded px-3 py-2" required />
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-xs text-slate-500">Harga Default</label>
                        <input type="number" name="default_room_price" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-slate-500">Alamat</label>
                        <input name="address" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div class="md:col-span-4">
                        <label class="text-xs text-slate-500">Fasilitas</label>
                        <textarea name="facilities" rows="2" class="mt-1 w-full border rounded px-3 py-2"></textarea>
                    </div>
                    <div class="md:col-span-4">
                        <button class="px-4 py-2 rounded-lg bg-primary text-white">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kos</h3>
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Total: {{ count($properties) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Default</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fasilitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($properties as $p)
                        <tr>
                            <td class="px-6 py-4 font-bold">{{ $p->name }}</td>
                            <td class="px-6 py-4">{{ $p->default_room_price ? 'Rp '.number_format($p->default_room_price,0,',','.') : '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $p->address ?: '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $p->facilities ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('super-admin.properties.update', $p) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $p->name }}" />
                                    <input type="hidden" name="default_room_price" value="{{ $p->default_room_price }}" />
                                    <input type="hidden" name="address" value="{{ $p->address }}" />
                                    <input type="hidden" name="facilities" value="{{ $p->facilities }}" />
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-blue-600 text-white">Simpan (Quick)</button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.properties.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus data kos ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs font-bold rounded-lg bg-red-600 text-white">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($properties->isEmpty())
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada data kos.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
