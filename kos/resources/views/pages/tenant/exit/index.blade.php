@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Ajukan Keluar Kos'" :title="'Ajukan Keluar Kos'" :subtitle="'Ajukan permintaan berhenti sewa & lihat statusnya'" />

    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-sm">
                {{ session('info') }}
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

        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Status Pengajuan</h3>
                <p class="text-sm text-slate-500">Lihat status persetujuan berhenti sewa</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4">
                @if($latest)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-600">Terakhir diajukan: {{ $latest->created_at->format('d M Y H:i') }}</p>
                            <p class="text-sm text-slate-600">Alasan: {{ $latest->reason ?: '-' }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $latest->status === 'approved' ? 'bg-green-100 text-green-700' : ($latest->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($latest->status) }}
                        </span>
                    </div>
                @else
                    <p class="text-slate-600">Belum ada pengajuan keluar.</p>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Ajukan Permintaan Berhenti Sewa</h3>
            <form method="POST" action="{{ route('tenant.exit.submit') }}" class="space-y-3">
                @csrf
                <label class="text-xs text-slate-500">Alasan (opsional)</label>
                <textarea name="reason" rows="3" class="w-full border rounded px-3 py-2">{{ old('reason') }}</textarea>
                <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-bold">Kirim Pengajuan</button>
            </form>
            <p class="text-xs text-slate-500 mt-2">Pengajuan akan ditinjau oleh admin. Anda dapat melihat statusnya di atas.</p>
        </div>
    </div>
@endsection

