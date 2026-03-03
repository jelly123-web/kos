@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan Global Kos'" :title="'Reports'" :subtitle="'Analisis performa seluruh properti kos'" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <span class="text-xs font-bold text-green-500 bg-green-50 dark:bg-green-500/10 px-2 py-1 rounded-lg">+12.5%</span>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $reports['total_revenue'] }}</h3>
        </div>

        <!-- Occupancy Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-50 dark:bg-white/5 px-2 py-1 rounded-lg">Target: 90%</span>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tingkat Hunian (Okupansi)</p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $reports['occupancy_rate'] }}</h3>
        </div>

        <!-- Monthly Revenue -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center text-green-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pendapatan Bulan Ini</p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $reports['revenue_this_month'] }}</h3>
        </div>

        <!-- Properties Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Properti Aktif</p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $reports['active_properties'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Table -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Performa Properti Teratas</h3>
                <button class="text-primary text-xs font-bold hover:underline">Lihat Semua</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Nama Kos</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Pendapatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Okupansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($reports['top_properties'] as $prop)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $prop['name'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $prop['revenue'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-full max-w-[60px] h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full">
                                        <div class="h-full bg-primary rounded-full" style="width: {{ $prop['occupancy'] }}"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $prop['occupancy'] }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Target Summary -->
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-white/[0.03] shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Analisis Pertumbuhan</h3>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Target Tahunan</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">75%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full">
                            <div class="h-full bg-blue-500 rounded-full shadow-sm" style="width: 75%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Kamar Terisi</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">412 / 500</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full">
                            <div class="h-full bg-purple-500 rounded-full shadow-sm" style="width: 82%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Tagihan Terbayar</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">92%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full">
                            <div class="h-full bg-green-500 rounded-full shadow-sm" style="width: 92%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-slate-800">
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        <span class="font-bold text-primary">Tips:</span> Pertumbuhan pendapatan bulan ini meningkat sebesar 12.5% dibandingkan bulan lalu. Fokus pada pengisian unit kosong di Kos Anggrek untuk meningkatkan okupansi global.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
