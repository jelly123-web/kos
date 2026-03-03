@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Chat & Komunikasi'" :title="'Chat'" :subtitle="'Hubungi Admin, Owner, atau Staff'" />

    <div class="flex h-[calc(100vh-200px)] overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-white/[0.03] shadow-sm"
         x-data="{
            selectedUserId: {{ $selectedContact ? $selectedContact->id : 'null' }},
            messages: @js($messages),
            newMessage: '',
            
            async sendMessage() {
                if (!this.newMessage.trim() || !this.selectedUserId) return;
                
                const response = await fetch('{{ route('super-admin.chat.send') }}', {
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
                    this.$nextTick(() => this.scrollToBottom());
                }
            },
            
            async fetchMessages() {
                if (!this.selectedUserId) return;
                const response = await fetch(`/super-admin/chat/fetch/${this.selectedUserId}`);
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
        <!-- Sidebar Contacts -->
        <div class="w-1/3 border-r border-slate-100 dark:border-slate-800 flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                <div class="relative">
                    <input type="text" placeholder="Cari kontak..." class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 dark:bg-slate-900 dark:border-slate-800 dark:text-white">
                    <svg class="absolute left-3 top-2.5 text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @foreach($contacts as $contact)
                <a href="?user_id={{ $contact->id }}" 
                   class="w-full p-4 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-slate-50 dark:border-slate-800 last:border-0 text-left {{ $selectedContact && $selectedContact->id == $contact->id ? 'bg-slate-50 dark:bg-white/5' : '' }}">
                    <div class="relative">
                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            {{ substr($contact->name, 0, 1) }}
                        </div>
                        @php $c = $unread[$contact->id] ?? 0; @endphp
                        @if($c > 0)
                        <span class="absolute -top-1 -right-1 px-1.5 py-0.5 text-[10px] rounded-full bg-red-500 text-white font-bold">{{ $c }}</span>
                        @endif
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $contact->name }}</h4>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ ucfirst($contact->role) }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-slate-50/30 dark:bg-transparent">
            @if($selectedContact)
                <!-- Chat Header -->
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

                <!-- Messages List -->
                <div x-ref="messageContainer" class="flex-1 p-5 overflow-y-auto custom-scrollbar space-y-4">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id == {{ auth()->id() }} ? 'flex items-start gap-3 flex-row-reverse' : 'flex items-start gap-3'">
                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'h-8 w-8 rounded-full bg-primary flex items-center justify-center text-[10px] font-bold text-white' : 'h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold'"
                                 x-text="msg.sender_id == {{ auth()->id() }} ? 'SA' : '{{ substr($selectedContact->name, 0, 1) }}'">
                            </div>
                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'max-w-[70%] text-right' : 'max-w-[70%]'">
                                <div :class="msg.sender_id == {{ auth()->id() }} ? 'bg-primary p-3 rounded-2xl rounded-tr-none shadow-sm text-sm text-white' : 'bg-white dark:bg-slate-800 p-3 rounded-2xl rounded-tl-none shadow-sm text-sm text-slate-700 dark:text-slate-300'"
                                     x-text="msg.message">
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Message Input -->
                <div class="p-4 bg-white dark:bg-transparent border-t border-slate-100 dark:border-slate-800">
                    <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                        <input type="text" x-model="newMessage" placeholder="Tulis pesan..." class="flex-1 py-2 text-sm focus:outline-none dark:bg-transparent dark:text-white">
                        <button type="submit" class="h-10 w-10 bg-primary text-white rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 hover:bg-primary/90 transition-colors">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-slate-400">
                    <div class="text-center">
                        <svg class="mx-auto mb-4" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p>Pilih kontak untuk memulai percakapan</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
