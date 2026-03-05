@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Hak Akses Menu'" :title="'Hak Akses Menu'" :subtitle="'Checklist apa saja yang bisa diakses setiap role'" />

    <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Atur Akses</h3>
            @if(session('success'))<span class="text-xs px-3 py-1 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</span>@endif
        </div>
        <div class="p-5 overflow-x-auto">
            <form method="POST" action="{{ route('super-admin.access.save') }}">
                @csrf
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Fitur/Menu</th>
                            @foreach($roles as $r)
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">{{ ucfirst($r) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($matrix as $key => $row)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $row['label'] }}</td>
                            @foreach($roles as $r)
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="perm[{{ $r }}][{{ $key }}]" value="1" @checked($row[$r]) class="w-4 h-4" />
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-5">
                    <button class="px-4 py-2 rounded-lg bg-primary text-white">Simpan Akses</button>
                </div>
            </form>
            <p class="text-xs text-slate-500 mt-4">Catatan: Super Admin selalu memiliki akses penuh.</p>
        </div>
    </div>
@endsection
