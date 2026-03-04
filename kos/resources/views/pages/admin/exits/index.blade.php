@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Pengajuan Keluar Kos'" :title="'Keluar Kos'" :subtitle="'Daftar pengajuan berhenti sewa dari penghuni'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Pengajuan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penghuni</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Alasan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Diajukan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($requests as $req)
                    <tr>
                        <td class="px-6 py-4">{{ $req->tenant?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $req->tenant?->room?->number ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600 text-sm">{{ $req->reason ?: '-' }}</td>
                        <td class="px-6 py-4">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $req->status === 'approved' ? 'bg-green-100 text-green-700' : ($req->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($req->status === 'pending')
                            <form class="inline" method="POST" action="{{ route('admin.exit-requests.approve', $req) }}">
                                @csrf
                                <button class="px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold">Setujui</button>
                            </form>
                            <form class="inline" method="POST" action="{{ route('admin.exit-requests.reject', $req) }}">
                                @csrf
                                <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-bold">Tolak</button>
                            </form>
                            @else
                            <span class="text-xs text-slate-500">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($requests->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-slate-500">Belum ada pengajuan keluar kos.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

