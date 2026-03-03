@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Chat & Komunikasi'" :title="'Chat'" :subtitle="'Hubungi Admin atau Staff'" />

    <div class="flex h-[calc(100vh-200px)] overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm"
         x-data="{
            selectedUserId: {{ $selectedContact ? $selectedContact->id : 'null' }},
            messages: @js($messages),
            newMessage: '',
            
            async sendMessage() {
                if (!this.newMessage.trim() || !this.selectedUserId) return;
                
                const response = await fetch('{{ route('tenant.chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        receiver_id: this.selectedUserId,
                        message: this.newMessage
                    })
                });
                
                if (response.ok) {
                    this.messages.push({
                        sender_id: {{ auth()->id() }},
                        message: this.newMessage,
                        created_at: new Date().toISOString()
                    });
                    this.newMessage = '';
                }
            },
            
            async fetchMessages() {
                if (!this.selectedUserId) return;
                const response = await fetch(`/tenant/chat/fetch/${this.selectedUserId}`);
                if (response.ok) {
                    this.messages = await response.json();
                }
            },
            
            scrollToBottom() {
                const container = this.$refs.messageContainer;
                container.scrollTop = container.scrollHeight;
            },
            
            init() {
                if (this.selectedUserId) {
                    this.scrollToBottom();
                    setInterval(() => this.fetchMessages(), 5000);
                }
            }
         }">
        <div class="w-1/3 border-r border-slate-100 dark:border-slate-800 flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                <div class="relative">
                    <input type="text" placeholder="Cari kontak..." class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white">
                    <span class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @foreach($contacts as $user)
                    <a href="{{ route('tenant.chat', ['user_id' => $user->id]) }}" class="flex items-center gap-3 p-4 hover:bg-slate-50 dark:hover:bg-white/5 border-b border-slate-100 dark:border-slate-800">
                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ ucfirst($user->role) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-slate-50/30 dark:bg-transparent">
            @if($selectedContact)
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-transparent flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                            {{ substr($selectedContact->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $selectedContact->name }}</h4>
                            <span class="text-[11px] text-slate-500 font-medium">{{ ucfirst($selectedContact->role) }}</span>
                        </div>
                    </div>
                </div>

                <div x-ref="messageContainer" class="flex-1 p-5 overflow-y-auto custom-scrollbar space-y-4">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id == {{ auth()->id() }} ? 'flex items-start gap-3 flex-row-reverse' : 'flex items-start gap-3'">
                            <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">
                                <span x-text="msg.sender_id == {{ auth()->id() }} ? '{{ substr(auth()->user()->name, 0, 1) }}' : '{{ substr($selectedContact->name, 0, 1) }}'"></span>
                            </div>
                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300'" class="max-w-[70%] rounded-2xl px-4 py-2 text-sm">
                                <p x-text="msg.message"></p>
                                <span class="block text-[10px] mt-1 opacity-75" x-text="new Date(msg.created_at).toLocaleString()"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-transparent">
                    <div class="flex gap-2">
                        <input x-model="newMessage" type="text" placeholder="Tulis pesan..." class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white">
                        <button @click="sendMessage" class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">Kirim</button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-slate-500">
                    Pilih kontak untuk mulai chat.
                </div>
            @endif
        </div>
    </div>
@endsection
