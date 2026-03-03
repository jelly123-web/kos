@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Log Aktivitas Sistem'" :title="'Activity Log'" :subtitle="'Pantau aktivitas seluruh pengguna sistem'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Jejak Aktivitas (Audit Trail)</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-lg hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400">Export PDF</button>
                <button class="px-3 py-1.5 border border-slate-200 text-xs font-bold rounded-lg hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400 text-red-500">Hapus Log Lama</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400 w-48">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aktivitas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Modul</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">2026-02-28 12:45:10</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] text-primary font-bold">A</div>
                                <span class="text-sm font-bold text-slate-800 dark:text-white">Admin Utama</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-400">Mengubah status pembayaran <span class="font-bold text-primary">TRX-001</span> menjadi Lunas</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase dark:bg-blue-500/10 dark:text-blue-400">Pembayaran</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">2026-02-28 11:20:05</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center text-[10px] text-purple-600 font-bold">O</div>
                                <span class="text-sm font-bold text-slate-800 dark:text-white">Owner Melati</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-400">Menambahkan kamar baru <span class="font-bold text-primary">Room 105</span></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-bold rounded uppercase dark:bg-purple-500/10 dark:text-purple-400">Kamar</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
