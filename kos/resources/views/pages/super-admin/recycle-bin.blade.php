@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Recycle Bin'" :title="'Recycle Bin'" :subtitle="'Pulihkan data yang terhapus (soft delete)'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Terhapus</h3>
        </div>
        <div class="p-6">
            @if(($entries ?? collect())->isEmpty())
                <div class="text-sm text-slate-600 dark:text-slate-400">Tidak ada data terhapus.</div>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Label</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Deleted By</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">IP</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Deleted At</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($entries as $e)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ strtoupper(str_replace('_',' ',$e['model'])) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $e['id'] }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $e['label'] }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $e['deleted_by'] ? 'User #'.$e['deleted_by'] : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $e['deleted_ip'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ optional($e['deleted_at'])->format('d M Y H:i (l)') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('super-admin.recycle-bin.restore', ['model' => $e['model'], 'id' => $e['id']]) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90">Restore</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
