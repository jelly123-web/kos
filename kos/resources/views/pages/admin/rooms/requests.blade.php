@extends('layouts.app')

@section('content')
  <x-common.page-breadcrumb :pageTitle="'Permintaan Kamar'" :title="'Permintaan Kamar'" :subtitle="'Setujui atau tolak permintaan penghuni memilih kamar'" />

  <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Permintaan</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
        <thead class="bg-slate-50 dark:bg-slate-900/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Penghuni</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Kamar</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Status</th>
            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
          @foreach($requests as $req)
            <tr>
              <td class="px-6 py-4">{{ $req->tenant->name ?? '-' }}</td>
              <td class="px-6 py-4">{{ $req->room->number ?? '-' }} - {{ $req->room->name ?? '-' }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $req->status === 'pending' ? 'bg-orange-100 text-orange-700' : ($req->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                  {{ ucfirst($req->status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                @if($req->status === 'pending')
                  <form class="inline" method="POST" action="{{ route('admin.room-requests.approve', $req) }}">
                    @csrf
                    <button class="px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold">Setujui</button>
                  </form>
                  <form class="inline" method="POST" action="{{ route('admin.room-requests.reject', $req) }}">
                    @csrf
                    <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-bold">Tolak</button>
                  </form>
                @else
                  <span class="text-xs text-slate-500">Sudah diproses</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

