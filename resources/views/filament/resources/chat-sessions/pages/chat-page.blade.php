<x-filament-panels::page>

    {{-- ── Session closed banner ────────────────────────────────────────── --}}
    @if($this->session?->status->value === 'closed')
        <div class="flex items-center gap-3 rounded-xl border border-warning-300 bg-warning-50 px-4 py-3 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-950 dark:text-warning-200">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0" />
            <span>Sesi ini telah <strong>ditutup</strong>. Tidak dapat mengirim pesan baru.</span>
            <a href="{{ \App\Filament\Resources\ChatSessions\ChatSessionResource::getUrl('create') }}"
               class="ml-auto font-semibold underline underline-offset-2">
                Buka Sesi Baru →
            </a>
        </div>
    @endif

    {{-- ── Chat container ────────────────────────────────────────────────── --}}
    <div class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
         style="height: calc(100vh - 14rem);">

        {{-- ── Header bar ──────────────────────────────────────────────── --}}
        <div class="flex items-center gap-3 border-b border-gray-200 bg-gray-50 px-5 py-3 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <x-heroicon-o-cpu-chip class="h-5 w-5 text-primary-600 dark:text-primary-300" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $this->session?->title ?? 'Asisten Kesehatan Ayam' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Model: <span class="font-mono">{{ $this->session?->model_used }}</span>
                    @if($this->session?->farm)
                        &middot; Kandang: <strong>{{ $this->session->farm->name }}</strong>
                    @endif
                    @if($this->session?->chicken)
                        &middot; Ayam: <strong>{{ $this->session->chicken->code }}</strong>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <x-heroicon-o-bolt class="h-3.5 w-3.5 text-success-500" />
                <span>{{ number_format($this->session?->total_tokens_used ?? 0) }} token</span>
            </div>
        </div>

        {{-- ── Messages area ────────────────────────────────────────────── --}}
        <div
            id="chat-messages"
            class="flex-1 overflow-y-auto scroll-smooth px-5 py-4 space-y-4"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
            @chat-updated.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
        >
            @forelse($this->messages as $message)
                @php
                    $isUser = $message->role->value === 'user';
                @endphp

                <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }} items-end gap-2">

                    {{-- Avatar (assistant only) --}}
                    @unless($isUser)
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                            <x-heroicon-o-cpu-chip class="h-4 w-4 text-primary-600 dark:text-primary-300" />
                        </div>
                    @endunless

                    {{-- Bubble --}}
                    <div class="group max-w-[75%]">
                        <div @class([
                            'rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm',
                            'rounded-br-sm bg-primary-600 text-white'                                        => $isUser,
                            'rounded-bl-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100'   => !$isUser,
                        ])>
                            {!! nl2br(e($message->content)) !!}
                        </div>
                        <p @class([
                            'mt-1 text-[10px] text-gray-400',
                            'text-right pr-1' => $isUser,
                            'text-left pl-1'  => !$isUser,
                        ])>
                            {{ $message->created_at->translatedFormat('H:i') }}
                            @if($message->tokens_used)
                                &middot; {{ $message->tokens_used }} tok
                            @endif
                        </p>
                    </div>

                    {{-- Avatar (user only) --}}
                    @if($isUser)
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                            <x-heroicon-o-user class="h-4 w-4 text-gray-600 dark:text-gray-300" />
                        </div>
                    @endif

                </div>
            @empty
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center h-full py-16 text-center text-gray-400">
                    <x-heroicon-o-chat-bubble-left-right class="mb-3 h-12 w-12 opacity-40" />
                    <p class="text-sm font-medium">Belum ada percakapan</p>
                    <p class="text-xs mt-1">Ketik pertanyaan Anda di bawah untuk memulai</p>
                </div>
            @endforelse

            {{-- Typing indicator --}}
            @if($isLoading)
                <div class="flex justify-start items-end gap-2">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                        <x-heroicon-o-cpu-chip class="h-4 w-4 text-primary-600 dark:text-primary-300" />
                    </div>
                    <div class="rounded-2xl rounded-bl-sm bg-gray-100 px-4 py-3 dark:bg-gray-700">
                        <div class="flex gap-1.5 items-center">
                            <span class="h-2 w-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay:-0.3s"></span>
                            <span class="h-2 w-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay:-0.15s"></span>
                            <span class="h-2 w-2 rounded-full bg-gray-400 animate-bounce"></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Error message ────────────────────────────────────────────── --}}
        @if($errorMessage)
            <div class="mx-5 mb-2 flex items-center gap-2 rounded-lg border border-danger-200 bg-danger-50 px-3 py-2 text-xs text-danger-700 dark:border-danger-700 dark:bg-danger-950 dark:text-danger-300">
                <x-heroicon-o-exclamation-circle class="h-4 w-4 shrink-0" />
                {{ $errorMessage }}
            </div>
        @endif

        {{-- ── Input area ───────────────────────────────────────────────── --}}
        @php $isClosed = $this->session?->status->value === 'closed'; @endphp
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
            <div
                x-data="{ focused: false }"
                class="flex items-end gap-3 rounded-xl border bg-white px-3 py-2 transition dark:bg-gray-900 border-gray-200 dark:border-gray-600"
                :class="focused ? 'ring-2 ring-primary-500 border-primary-500' : ''"
            >
                <textarea
                    wire:model="userInput"
                    @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage() }"
                    @focus="focused = true"
                    @blur="focused = false"
                    placeholder="{{ $isClosed ? 'Sesi ditutup.' : 'Tanya tentang kesehatan ayam… (Enter kirim · Shift+Enter baris baru)' }}"
                    rows="1"
                    class="flex-1 resize-none bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none dark:text-gray-100 dark:placeholder-gray-500"
                    style="max-height: 120px; overflow-y: auto;"
                    x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                    @if($isClosed || $isLoading) disabled @endif
                    wire:loading.attr="disabled"
                ></textarea>

                <button
                    wire:click="sendMessage"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    @if($isClosed) disabled @endif
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-600 text-white transition hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed"
                    title="Kirim (Enter)"
                >
                    <span wire:loading wire:target="sendMessage">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="sendMessage">
                        <x-heroicon-o-paper-airplane class="h-4 w-4" />
                    </span>
                </button>
            </div>
            <p class="mt-1.5 text-center text-[10px] text-gray-400 dark:text-gray-600">
                AI dapat membuat kesalahan. Selalu verifikasi saran medis dengan dokter hewan.
            </p>
        </div>

    </div>

</x-filament-panels::page>
