@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Data Kos'" :title="'Kos'" :subtitle="'Atur data kos, harga, fasilitas'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Kos</h3>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('admin.property.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-xs text-slate-500">Nama Kos</label>
                    <input name="name" class="mt-1 w-full border rounded px-3 py-2" value="{{ $property->name ?? '' }}" required />
                </div>
                <div>
                    <label class="text-xs text-slate-500">Harga Default Kamar</label>
                    <input type="number" name="default_room_price" class="mt-1 w-full border rounded px-3 py-2" value="{{ $property->default_room_price ?? '' }}" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-slate-500">Alamat</label>
                    <textarea name="address" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ $property->address ?? '' }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-slate-500">Fasilitas</label>
                    <textarea name="facilities" class="mt-1 w-full border rounded px-3 py-2" rows="3">{{ $property->facilities ?? '' }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
