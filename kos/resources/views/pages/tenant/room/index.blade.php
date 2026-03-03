@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Kamar Saya'" :title="'Kamar Saya'" :subtitle="'Informasi kamar dan harga'" />

    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
        @if($tenant && $tenant->room)
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>Nomor</span><span class="font-bold">{{ $tenant->room->number }}</span></div>
                <div class="flex justify-between"><span>Nama</span><span class="font-bold">{{ $tenant->room->name }}</span></div>
                <div class="flex justify-between"><span>Harga</span><span class="font-bold">{{ $tenant->room->price ? 'Rp '.number_format($tenant->room->price,0,',','.') : '-' }}</span></div>
                <div class="flex justify-between"><span>Status</span><span class="font-bold">{{ $tenant->room->status }}</span></div>
            </div>
        @else
            <div class="space-y-4">
                <p class="text-slate-700 dark:text-slate-300">Anda belum memiliki kamar terhubung. Silakan ajukan kamar kosong di bawah ini.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nomor</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Harga</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @forelse($availableRooms as $r)
                                <tr>
                                    <td class="px-4 py-3">{{ $r->number }}</td>
                                    <td class="px-4 py-3">{{ $r->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $r->price ? 'Rp '.number_format($r->price,0,',','.') : '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('tenant.request-room') }}">
                                            @csrf
                                            <input type="hidden" name="room_id" value="{{ $r->id }}">
                                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold">Ajukan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 text-slate-500" colspan="4">Tidak ada kamar kosong.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
