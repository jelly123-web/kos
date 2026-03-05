@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Log Aktivitas Sistem'" :title="'Activity Log'" :subtitle="'Pantau aktivitas seluruh pengguna sistem'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Jejak Aktivitas (Audit Trail)</h3>
            @if(session('success'))<span class="text-xs px-3 py-1 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</span>@endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400 w-48">Kapan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Ngapain</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Dimana (Lat,Lng)</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">{{ $log->created_at?->format('d M Y H:i (l)') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] text-primary font-bold">
                                        {{ strtoupper(substr($log->user?->name ?? '-',0,1)) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $log->user?->name ?? 'Guest' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $log->action }}</p>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $log->method }} {{ $log->url }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $log->lat && $log->lng ? ($log->lat.', '.$log->lng) : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $log->ip }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-6 text-center text-slate-500" colspan="5">Belum ada log.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
