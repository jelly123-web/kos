@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Manajemen User'" :title="'Users'" :subtitle="'Kelola hak akses Admin, Owner, dan Staff'" />

    <div class="grid grid-cols-1 gap-6">
        <!-- Table Section -->
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Pengguna Sistem</h3>
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Total: {{ count($users) }}</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Pengguna</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Role & Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5">
                                    <span class="w-fit px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase tracking-wider
                                        {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' : 
                                           ($user->role === 'admin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 
                                           'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-400') }}">
                                        {{ $user->role }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs font-medium {{ $user->status === 'active' ? 'text-green-600' : 'text-slate-400' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $user->status === 'active' ? 'bg-green-500' : 'bg-slate-300' }}"></span>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($user->role !== 'super_admin')
                                <div class="flex justify-end gap-2" x-data="{ openReset: false }">
                                    <!-- Toggle Status -->
                                    <form action="{{ route('super-admin.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 {{ $user->status === 'active' ? 'text-green-500 hover:text-red-500' : 'text-slate-400 hover:text-green-500' }} transition-colors" title="{{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($user->status === 'active')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0M12 2v10"/></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.38 12a13 13 0 0 1 21.24 0M8.59 13.51a5 5 0 0 1 6.82 0M12 15.57a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Reset Password -->
                                    <button @click="openReset = !openReset" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Reset Password">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3L15.5 7.5z"/></svg>
                                    </button>

                                    <!-- Form Reset Password (Hidden) -->
                                    <div x-show="openReset" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl border border-slate-100 dark:border-slate-800">
                                            <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Reset Password: {{ $user->name }}</h4>
                                            <form action="{{ route('super-admin.users.reset-password', $user->id) }}" method="POST">
                                                @csrf
                                                <input type="password" name="password" placeholder="Password Baru" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm mb-4 dark:bg-slate-950 dark:border-slate-800 dark:text-white" required>
                                                <div class="flex gap-2">
                                                    <button type="button" @click="openReset = false" class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-bold">Batal</button>
                                                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Section -->
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah User Baru</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('super-admin.users.create') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <input type="text" name="name" placeholder="Masukkan nama" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-slate-800 dark:bg-slate-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Email</label>
                            <input type="email" name="email" placeholder="email@contoh.com" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-slate-800 dark:bg-slate-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Password</label>
                            <input type="password" name="password" placeholder="Min. 8 karakter" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-slate-800 dark:bg-slate-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Hak Akses (Role)</label>
                            <select name="role" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-slate-800 dark:bg-slate-900 dark:text-white appearance-none">
                                <option value="admin">Admin System</option>
                                <option value="owner">Owner Kos</option>
                                <option value="staff">Staff Operasional</option>
                                <option value="tenant">Penyewa (Tenant)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                            Simpan Data Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
