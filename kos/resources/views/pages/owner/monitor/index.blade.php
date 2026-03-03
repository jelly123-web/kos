@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Pantau Kos'" :title="'Pantau Kos'" :subtitle="'Kondisi kos dan tim admin/staff'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Data Kos</h3>
            <div class="mt-4 space-y-2 text-sm">
                <div><span class="font-bold">{{ $property->name ?? '-' }}</span></div>
                <div class="text-slate-600">{{ $property->address ?? '-' }}</div>
                <div class="text-slate-600">Harga default kamar: {{ $property?->default_room_price ? 'Rp '.number_format($property->default_room_price,0,',','.') : '-' }}</div>
                <div class="text-slate-600">Fasilitas: {{ $property->facilities ?? '-' }}</div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Kondisi Kamar</h3>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Total Kamar</span><span class="font-bold">{{ $roomsTotal }}</span></div>
                <div class="flex justify-between"><span>Kamar Kosong</span><span class="font-bold">{{ $roomsEmpty }}</span></div>
                <div class="flex justify-between"><span>Kamar Terisi</span><span class="font-bold">{{ $roomsOccupied }}</span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white/90">Tim Admin & Staff</h3>
            <div class="mt-4">
                <p class="text-sm font-bold mb-2">Admin ({{ count($admins) }})</p>
                <ul class="text-sm space-y-1">
                    @foreach($admins as $u)
                    <li>{{ $u->name }} ({{ $u->username }})</li>
                    @endforeach
                </ul>
                <p class="text-sm font-bold mt-4 mb-2">Staff ({{ count($staffs) }})</p>
                <ul class="text-sm space-y-1">
                    @foreach($staffs as $u)
                    <li>{{ $u->name }} ({{ $u->username }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
