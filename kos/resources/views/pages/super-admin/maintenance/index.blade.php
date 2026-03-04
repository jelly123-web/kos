@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Maintenance Sistem'" :title="'Maintenance Sistem'" :subtitle="'Backup/Restore database, update sistem, dan cek log'" />

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Backup / Restore</h3>
            </div>
            <div class="p-5 space-y-4">
                <a href="{{ route('super-admin.maintenance.backup') }}" class="px-4 py-2 rounded-lg bg-primary text-white inline-block">Download Backup (JSON)</a>
                <form method="POST" action="{{ route('super-admin.maintenance.restore') }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="backup_file" accept=".json" class="w-full border rounded px-3 py-2" required />
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Restore</button>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pengaturan Sistem</h3>
            </div>
            <div class="p-5 space-y-4">
                <form method="POST" action="{{ route('super-admin.maintenance.update') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg bg-green-600 text-white">Update Sistem</button>
                </form>
                <p class="text-xs text-slate-500">Tombol ini placeholder, bisa dihubungkan ke pipeline update.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden xl:col-span-1">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Log Sistem (ringkas)</h3>
            </div>
            <div class="p-5">
                <pre class="text-xs whitespace-pre-wrap max-h-80 overflow-auto">{{ $logPreview }}</pre>
            </div>
        </div>
    </div>
@endsection
