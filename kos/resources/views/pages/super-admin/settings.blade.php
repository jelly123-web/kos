@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Pengaturan Aplikasi'" :title="'Settings'" :subtitle="'Konfigurasi sistem, biaya, dan aturan kos'" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ activeTab: 'general' }">
        <!-- Sidebar Settings -->
        <div class="md:col-span-1 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Kategori Pengaturan</h3>
                </div>
                <div class="p-2 space-y-1">
                    <button @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'text-primary bg-primary/5' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Informasi Umum
                    </button>
                    <button @click="activeTab = 'billing'" 
                        :class="activeTab === 'billing' ? 'text-primary bg-primary/5' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Biaya & Tarif
                    </button>
                    <button @click="activeTab = 'security'" 
                        :class="activeTab === 'security' ? 'text-primary bg-primary/5' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Aturan & Keamanan
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="md:col-span-2 space-y-6">
            <form action="{{ route('super-admin.settings.update') }}" method="POST">
                @csrf
                
                @if(session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-500/10 dark:text-green-400 font-bold border border-green-100 dark:border-green-500/20" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- General Settings -->
                <div x-show="activeTab === 'general'" x-cloak class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Informasi Umum</h3>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Simpan Perubahan</button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Nama Aplikasi / Kos</label>
                                <input type="text" name="app_name" value="{{ $settings['app_name'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Slogan</label>
                                <input type="text" name="app_slogan" value="{{ $settings['app_slogan'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Settings -->
                <div x-show="activeTab === 'billing'" x-cloak class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Biaya & Tarif</h3>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Simpan Perubahan</button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Deposit Awal (Rp)</label>
                                <input type="number" name="deposit_fee" value="{{ $settings['deposit_fee'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Denda Keterlambatan (%)</label>
                                <input type="number" name="late_fee_percent" value="{{ $settings['late_fee_percent'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Biaya Listrik Default (Rp)</label>
                                <input type="number" name="electricity_fee" value="{{ $settings['electricity_fee'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Biaya Air Default (Rp)</label>
                                <input type="number" name="water_fee" value="{{ $settings['water_fee'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div x-show="activeTab === 'security'" x-cloak class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Aturan & Keamanan</h3>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Simpan Perubahan</button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Aturan Umum Kos</label>
                            <textarea name="app_rules" rows="6" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white">{{ $settings['app_rules'] }}</textarea>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Parameter Keamanan</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Session Timeout (Menit)</label>
                                    <input type="number" name="security_session_timeout" value="{{ $settings['security_session_timeout'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Maksimal Gagal Login</label>
                                    <input type="number" name="security_max_attempts" value="{{ $settings['security_max_attempts'] }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden Fields to keep current values if not in active tab --}}
                <div class="hidden">
                    <input type="hidden" name="app_name" :value="activeTab !== 'general' ? '{{ $settings['app_name'] }}' : null" x-bind:disabled="activeTab === 'general'">
                    {{-- Note: Standard Laravel validation might fail if hidden fields are empty, 
                         but here we are using a single form for all tabs which is simpler. 
                         I'll just put all inputs in the form and they will all be sent regardless of activeTab visibility. --}}
                </div>
            </form>
        </div>
    </div>
@endsection
