@extends('layouts.iptv-screen', ['title' => 'Lista playlist'])

@section('content')

<style>
    .iptv-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.35) transparent;
        -webkit-overflow-scrolling: touch;
    }

    .iptv-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .iptv-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .iptv-scroll::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.30);
        border-radius: 999px;
    }
</style>

@php
    $hasActiveImport = $playlists->contains(function ($playlist) {
        return in_array($playlist->import_status, ['queued', 'running'], true);
    });
@endphp

<div class="relative h-full w-full overflow-hidden px-[clamp(16px,4vmin,80px)] py-[clamp(12px,3vmin,48px)]">

    {{-- DECORAZIONI SFONDO --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[12%] left-[30%] w-[clamp(20px,5vmin,58px)] h-[clamp(20px,5vmin,58px)] rounded-lg bg-indigo-400/10"></div>
        <div class="absolute top-[28%] right-[18%] w-[clamp(22px,6vmin,70px)] h-[clamp(22px,6vmin,70px)] rounded-lg bg-indigo-400/10"></div>
        <div class="absolute bottom-[18%] left-[42%] w-[clamp(18px,5vmin,60px)] h-[clamp(18px,5vmin,60px)] rounded-lg bg-indigo-400/10"></div>
        <div class="absolute bottom-[8%] right-[8%] w-[clamp(16px,4vmin,48px)] h-[clamp(16px,4vmin,48px)] rounded-lg bg-indigo-400/10"></div>
    </div>

    <div class="relative h-full flex flex-col">

        {{-- HEADER --}}
        <header class="flex items-center justify-between shrink-0 mb-[clamp(12px,3vmin,38px)]">
            <div>
                <h1 class="text-[clamp(28px,7vmin,58px)] font-extrabold tracking-tight lowercase leading-none">
                    lista
                </h1>

                <p class="mt-[clamp(4px,1vmin,10px)] text-[clamp(10px,2vmin,18px)] text-slate-400">
                    Gestisci le tue playlist IPTV personali
                </p>
            </div>

            <div class="flex items-center gap-[clamp(8px,2vmin,18px)]">
                <a href="{{ url('/') }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)]
                   rounded-[clamp(12px,2vmin,20px)]
                   bg-white/10 hover:bg-white/15 border border-white/10
                   flex items-center justify-center
                   text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                    Home
                </a>

                <a href="{{ route('customer.playlists.create') }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,38px)]
                   rounded-[clamp(12px,2vmin,20px)]
                   bg-gradient-to-r from-violet-500 to-pink-500
                   hover:brightness-110 border border-white/10
                   flex items-center justify-center gap-[clamp(8px,1.6vmin,16px)]
                   text-white font-extrabold shadow-2xl transition">

                    <span class="text-[clamp(24px,5vmin,42px)] leading-none font-light">
                        +
                    </span>

                    <span class="text-[clamp(12px,2.3vmin,22px)] whitespace-nowrap">
                        Aggiungi playlist
                    </span>
                </a>
            </div>
        </header>

        {{-- MESSAGGI --}}
        @if(session('success'))
            <div class="mb-[clamp(8px,2vmin,18px)] rounded-[clamp(12px,2vmin,20px)]
                border border-emerald-400/30 bg-emerald-500/10
                px-[clamp(12px,3vmin,22px)] py-[clamp(8px,2vmin,15px)]
                text-[clamp(11px,2vmin,17px)] text-emerald-200 shrink-0">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-[clamp(8px,2vmin,18px)] rounded-[clamp(12px,2vmin,20px)]
                border border-red-400/30 bg-red-500/10
                px-[clamp(12px,3vmin,22px)] py-[clamp(8px,2vmin,15px)]
                text-[clamp(11px,2vmin,17px)] text-red-200 shrink-0">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- LISTA PLAYLIST --}}
        <main class="flex-1 min-h-0 overflow-y-auto pr-[clamp(4px,1vmin,10px)] space-y-[clamp(8px,2vmin,18px)] iptv-scroll">

            @forelse($playlists as $playlist)

                @php
                    $status = $playlist->import_status ?? 'pending';

                    $statusLabel = match ($status) {
                        'queued' => 'In coda',
                        'running' => 'Importazione',
                        'completed' => 'Completata',
                        'failed' => 'Errore',
                        default => 'In attesa',
                    };

                    $statusClass = match ($status) {
                        'queued' => 'bg-blue-500/25 text-blue-100',
                        'running' => 'bg-yellow-500/25 text-yellow-100',
                        'completed' => 'bg-emerald-500/25 text-emerald-100',
                        'failed' => 'bg-red-500/25 text-red-100',
                        default => 'bg-white/15 text-white/85',
                    };
                @endphp

                <div class="rounded-[clamp(14px,2.6vmin,26px)] overflow-hidden
                    bg-gradient-to-r from-violet-500 via-fuchsia-500 to-rose-500
                    shadow-[0_20px_60px_rgba(0,0,0,0.35)]">

                    <div class="flex items-center justify-between gap-[clamp(10px,2vmin,22px)]
                        px-[clamp(18px,4vmin,38px)] py-[clamp(12px,2.5vmin,24px)]">

                        {{-- INFO PLAYLIST --}}
                        <div class="flex items-center gap-[clamp(16px,4vmin,42px)] min-w-0">

                            <div class="text-[clamp(16px,3.5vmin,28px)] font-bold text-white/90 shrink-0">
                                {{ $loop->iteration }}
                            </div>

                            <div class="min-w-0">

                                <div class="text-[clamp(18px,4vmin,34px)] font-extrabold truncate">
                                    {{ $playlist->name }}
                                </div>

                                <div class="mt-[clamp(4px,1vmin,8px)]
                                    flex flex-wrap items-center gap-[clamp(6px,1.5vmin,12px)]
                                    text-[clamp(9px,1.8vmin,14px)] text-white/85">

                                    <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full bg-white/15">
                                        {{ $playlist->type_label }}
                                    </span>

                                    <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full bg-white/15">
                                        {{ $playlist->channels_count ?? 0 }} canali
                                    </span>

                                    @if($playlist->imported_channels_count)
                                        <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full bg-white/15">
                                            Importati: {{ $playlist->imported_channels_count }}
                                        </span>
                                    @endif

                                    <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if($playlist->is_active)
                                        <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full bg-emerald-500/25">
                                            Attiva
                                        </span>
                                    @else
                                        <span class="px-[clamp(8px,2vmin,14px)] py-[clamp(3px,0.8vmin,6px)] rounded-full bg-red-500/25">
                                            Disattivata
                                        </span>
                                    @endif
                                </div>

                                @if($playlist->import_message)
                                    <div class="mt-[clamp(4px,1vmin,8px)]
                                        text-[clamp(8px,1.5vmin,13px)] text-white/75 truncate max-w-[720px]">
                                        {{ $playlist->import_message }}
                                    </div>
                                @endif

                                @if($playlist->import_started_at || $playlist->import_finished_at)
                                    <div class="mt-[clamp(3px,0.8vmin,6px)]
                                        text-[clamp(8px,1.4vmin,12px)] text-white/55 truncate">
                                        @if($playlist->import_started_at)
                                            Avvio: {{ $playlist->import_started_at->format('d/m/Y H:i') }}
                                        @endif

                                        @if($playlist->import_finished_at)
                                            · Fine: {{ $playlist->import_finished_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- AZIONI --}}
                        <div class="flex items-center gap-[clamp(8px,1.8vmin,16px)] shrink-0">

                            {{-- IMPORTA / RICARICA --}}
                            <form method="POST" action="{{ route('customer.playlists.import', $playlist) }}">
                                @csrf

                                <button type="submit"
                                        class="w-[clamp(42px,8vmin,62px)] h-[clamp(42px,8vmin,62px)]
                                        rounded-full bg-emerald-500 hover:bg-emerald-400
                                        flex items-center justify-center transition shadow-xl
                                        disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Importa canali"
                                        @disabled(in_array($status, ['queued', 'running'], true))>

                                    @if(in_array($status, ['queued', 'running'], true))
                                        <svg class="w-[52%] h-[52%] text-white animate-spin"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 12a9 9 0 0 1-9 9"></path>
                                            <path d="M3 12a9 9 0 0 1 9-9"></path>
                                        </svg>
                                    @else
                                        <svg class="w-[52%] h-[52%] text-white"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 12a9 9 0 0 0-15.5-6.3"></path>
                                            <path d="M3 5v6h6"></path>
                                            <path d="M3 12a9 9 0 0 0 15.5 6.3"></path>
                                            <path d="M21 19v-6h-6"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>

                            {{-- MODIFICA --}}
                            <a href="{{ route('customer.playlists.edit', $playlist) }}"
                               class="w-[clamp(42px,8vmin,62px)] h-[clamp(42px,8vmin,62px)]
                               rounded-full bg-white/15 hover:bg-white/25
                               flex items-center justify-center transition"
                               title="Modifica">

                                <svg class="w-[52%] h-[52%] text-white"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path>
                                </svg>
                            </a>

                            {{-- ELIMINA --}}
                            <form method="POST"
                                  action="{{ route('customer.playlists.destroy', $playlist) }}"
                                  onsubmit="return confirm('Vuoi eliminare questa playlist?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="w-[clamp(46px,9vmin,70px)] h-[clamp(46px,9vmin,70px)]
                                        rounded-full bg-rose-500 hover:bg-rose-400
                                        flex items-center justify-center transition shadow-xl"
                                        title="Elimina">

                                    <svg class="w-[55%] h-[55%] text-white"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4h8v2"></path>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                        <path d="M10 11v5"></path>
                                        <path d="M14 11v5"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @empty

                <div class="h-full rounded-[clamp(16px,3vmin,32px)]
                    border border-white/10 bg-white/[0.055]
                    p-[clamp(20px,5vmin,54px)] text-center shadow-2xl backdrop-blur-xl
                    flex flex-col items-center justify-center">

                    <div class="w-[clamp(58px,12vmin,100px)] h-[clamp(58px,12vmin,100px)]
                        rounded-[clamp(14px,3vmin,28px)] bg-white/10
                        flex items-center justify-center mb-[clamp(12px,3vmin,26px)]">

                        <svg class="w-[55%] h-[55%] text-slate-300"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                            <path d="M8 9h8"></path>
                            <path d="M8 13h6"></path>
                        </svg>
                    </div>

                    <h2 class="text-[clamp(22px,5vmin,42px)] font-extrabold">
                        Nessuna playlist presente
                    </h2>

                    <p class="mt-[clamp(6px,1.5vmin,14px)] text-[clamp(12px,2vmin,18px)] text-slate-400">
                        Aggiungi una lista IPTV tramite URL M3U oppure API Xtream.
                    </p>

                    <a href="{{ route('customer.playlists.create') }}"
                       class="inline-flex mt-[clamp(14px,3vmin,32px)]
                       px-[clamp(22px,5vmin,44px)] py-[clamp(10px,2vmin,18px)]
                       rounded-[clamp(12px,2vmin,20px)]
                       bg-gradient-to-r from-violet-500 to-pink-500
                       font-extrabold text-[clamp(13px,2.4vmin,21px)]">
                        Aggiungi playlist
                    </a>
                </div>

            @endforelse

        </main>

        {{-- FOOTER --}}
        <footer class="grid grid-cols-2 gap-[clamp(8px,2vmin,30px)] shrink-0 mt-[clamp(10px,2vmin,24px)]">

            <div class="space-y-[clamp(5px,1vmin,10px)] min-w-0">
                <div class="h-[clamp(34px,6vmin,54px)]
                    px-[clamp(10px,2.5vmin,22px)]
                    rounded-[clamp(10px,1.5vmin,16px)]
                    bg-white/10 border border-white/10
                    flex items-center gap-[clamp(8px,1.5vmin,16px)] min-w-0">

                    <span class="w-[clamp(22px,4vmin,34px)] h-[clamp(22px,4vmin,34px)]
                        rounded-full bg-blue-400 flex items-center justify-center shrink-0">
                        👤
                    </span>

                    <span class="text-[clamp(10px,2vmin,20px)] truncate">
                        {{ auth()->check() ? auth()->user()->name : 'salvoiptv' }}
                    </span>
                </div>

                <div class="h-[clamp(34px,6vmin,54px)]
                    px-[clamp(10px,2.5vmin,22px)]
                    rounded-[clamp(10px,1.5vmin,16px)]
                    bg-white/10 border border-white/10
                    flex items-center gap-[clamp(8px,1.5vmin,16px)] min-w-0">

                    <span class="w-[clamp(22px,4vmin,34px)] h-[clamp(22px,4vmin,34px)]
                        rounded-full bg-rose-500 flex items-center justify-center shrink-0">
                        ×
                    </span>

                    <span class="text-[clamp(10px,2vmin,20px)] truncate">
                        Playlist cliente
                    </span>
                </div>
            </div>

            <div class="space-y-[clamp(5px,1vmin,10px)] min-w-0">
                <div class="h-[clamp(34px,6vmin,54px)]
                    px-[clamp(10px,2.5vmin,22px)]
                    rounded-[clamp(10px,1.5vmin,16px)]
                    bg-white/10 border border-white/10
                    flex items-center gap-[clamp(8px,1.5vmin,16px)] min-w-0">

                    <span class="shrink-0">▣</span>

                    <span class="text-[clamp(10px,2vmin,20px)] truncate">
                        Codice dispositivo: {{ $deviceCode ?? 'DEVICE-XXXX-XXXX' }}
                    </span>
                </div>

                <div class="h-[clamp(34px,6vmin,54px)]
                    px-[clamp(10px,2.5vmin,22px)]
                    rounded-[clamp(10px,1.5vmin,16px)]
                    bg-white/10 border border-white/10
                    flex items-center gap-[clamp(8px,1.5vmin,16px)] min-w-0">

                    <span class="shrink-0">🌐</span>

                    <span class="text-[clamp(10px,2vmin,20px)] truncate">
                        Sito web: {{ request()->getHost() }}
                    </span>
                </div>
            </div>
        </footer>
    </div>
</div>

@if($hasActiveImport)
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 6000);
    </script>
@endif

@endsection