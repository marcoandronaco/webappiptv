@extends('layouts.iptv-screen', ['title' => 'Canali'])

@section('content')

@if($type === 'live')

@php
    $isLivePlayerPage = request()->filled('channel') && $playableChannel;
@endphp

@if(!$isLivePlayerPage)

{{-- PAGINA 1 LIVE TV: CATEGORIE + GRIGLIA CANALI --}}
<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">
    <div class="h-full w-full p-[clamp(4px,0.9vmin,12px)]">

        <div class="grid h-full grid-cols-[clamp(240px,28vw,460px)_1fr] gap-[clamp(4px,0.9vmin,14px)]">

            {{-- CATEGORIE --}}
            <aside class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.045] p-[clamp(5px,1vmin,14px)] shadow-2xl">

                {{-- BOTTONI HOME / LISTE --}}
                <div class="mb-[clamp(4px,0.8vmin,10px)] grid shrink-0 grid-cols-[1fr_auto] gap-2">
                    <a href="{{ url('/') }}"
                    class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-violet-700 px-[clamp(8px,1.5vmin,16px)] py-[clamp(6px,1.2vmin,12px)] text-[clamp(10px,1.45vmin,15px)] font-black hover:bg-violet-600"
                    data-preserve-scroll>
                        Home
                    </a>

                    <a href="{{ route('customer.playlists.index') }}"
                    class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-white/[0.08] px-[clamp(8px,1.5vmin,16px)] py-[clamp(6px,1.2vmin,12px)] text-[clamp(10px,1.45vmin,15px)] font-black hover:bg-white/[0.14]"
                    data-preserve-scroll>
                        Liste
                    </a>
                </div>

                {{-- RICERCA CATEGORIE --}}
                <form method="GET"
                      action="{{ route('customer.channels.index') }}"
                      class="mb-[clamp(4px,0.8vmin,9px)]"
                      data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">

                    <input type="text"
                           name="category_search"
                           value="{{ $categorySearch }}"
                           placeholder="Cerca categoria..."
                           class="w-full rounded-[clamp(9px,1.5vmin,15px)] border border-white/10 bg-black/25 px-[clamp(8px,1.4vmin,14px)] py-[clamp(5px,0.9vmin,9px)] text-[clamp(9px,1.25vmin,13px)] text-white placeholder:text-white/35 outline-none focus:border-orange-400">

                    @if($categorySearch)
                        <a href="{{ route('customer.channels.index', ['tipo' => $type]) }}"
                           data-preserve-scroll
                           class="mt-[clamp(3px,0.6vmin,6px)] block rounded-lg bg-white/10 px-3 py-1 text-center text-[clamp(8px,1vmin,10px)] font-black text-white/70 hover:bg-white/15">
                            Pulisci
                        </a>
                    @endif
                </form>

                {{-- LISTA CATEGORIE --}}
                <div id="categoriesScroll"
                     class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1 space-y-[clamp(3px,0.55vmin,6px)]">

                    <a href="{{ route('customer.channels.index', [
                        'tipo' => $type,
                        'category_search' => $categorySearch,
                    ]) }}"
                       data-preserve-scroll
                       class="js-scroll-item flex items-center justify-between rounded-[clamp(9px,1.5vmin,15px)] px-[clamp(8px,1.5vmin,14px)] py-[clamp(6px,1vmin,10px)] transition
                       {{ !$category ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                        <span class="truncate font-black text-[clamp(10px,1.35vmin,15px)]">Tutto</span>
                        <span class="font-bold text-[clamp(9px,1.15vmin,12px)] text-white/80">
                            {{ $totalChannels }}
                        </span>
                    </a>

                    @forelse($categories as $cat)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $cat->group_title,
                            'category_search' => $categorySearch,
                        ]) }}"
                           data-preserve-scroll
                           class="js-scroll-item flex items-center justify-between gap-2 rounded-[clamp(9px,1.5vmin,15px)] px-[clamp(8px,1.5vmin,14px)] py-[clamp(6px,1vmin,10px)] transition
                           {{ $category === $cat->group_title ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">

                            <span class="truncate font-black text-[clamp(10px,1.35vmin,15px)]">
                                {{ $cat->group_title }}
                            </span>

                            <span class="font-bold text-[clamp(9px,1.15vmin,12px)] text-white/80">
                                {{ $cat->total }}
                            </span>
                        </a>
                    @empty
                        <div class="rounded-xl bg-white/[0.07] p-3 text-[clamp(9px,1.2vmin,12px)] text-white/50">
                            Nessuna categoria trovata.
                        </div>
                    @endforelse
                </div>
            </aside>

            {{-- GRIGLIA CANALI --}}
            <main class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.035] p-[clamp(6px,1vmin,12px)] shadow-2xl">

                {{-- TESTATA CANALI --}}
                <div class="mb-[clamp(5px,0.9vmin,10px)] grid shrink-0 grid-cols-[1fr_auto] items-center gap-[clamp(5px,1vmin,12px)]">
                    <div class="flex items-center gap-[clamp(6px,1vmin,12px)] rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.8vmin,18px)] py-[clamp(6px,1vmin,10px)]">
                        <span class="text-[clamp(13px,2vmin,22px)]">🇮🇹</span>

                        <div class="min-w-0">
                            <div class="truncate text-[clamp(14px,2vmin,24px)] font-black">
                                {{ $category ?: 'TOP ITALIA' }}
                            </div>

                            <div class="truncate text-[clamp(8px,1.05vmin,11px)] text-white/40">
                                Seleziona un canale
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RICERCA CANALE --}}
                <form method="GET"
                      action="{{ route('customer.channels.index') }}"
                      class="mb-[clamp(5px,0.9vmin,10px)] shrink-0"
                      data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="category_search" value="{{ $categorySearch }}">

                    <input type="text"
                           name="channel_search"
                           value="{{ $channelSearch }}"
                           placeholder="Cerca canale..."
                           class="w-full rounded-[clamp(10px,1.6vmin,16px)] border border-white/10 bg-black/25 px-[clamp(10px,1.6vmin,16px)] py-[clamp(6px,1vmin,10px)] text-[clamp(10px,1.35vmin,14px)] text-white placeholder:text-white/35 outline-none focus:border-orange-400">

                    @if($channelSearch)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $category,
                            'category_search' => $categorySearch,
                        ]) }}"
                           data-preserve-scroll
                           class="mt-[clamp(3px,0.6vmin,6px)] block rounded-lg bg-white/10 px-3 py-1 text-center text-[clamp(8px,1vmin,10px)] font-black text-white/70 hover:bg-white/15">
                            Pulisci ricerca
                        </a>
                    @endif
                </form>

                {{-- RIQUADRI CANALI --}}
                <div id="channelsScroll" class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1">
                    <div class="grid grid-cols-4 gap-[clamp(10px,1.7vmin,22px)]">
                        @forelse($channels as $channel)
                            <a href="{{ route('customer.channels.index', [
                                'tipo' => $type,
                                'category' => $category,
                                'category_search' => $categorySearch,
                                'channel_search' => $channelSearch,
                                'channel' => $channel->id,
                                'page' => request('page'),
                            ]) }}"
                               data-preserve-scroll
                               class="js-scroll-item group overflow-hidden rounded-[clamp(16px,2.6vmin,28px)] border border-white/10 bg-white/[0.055] shadow-xl transition hover:scale-[1.025] hover:bg-white/[0.10]">

                                <div class="flex aspect-video items-center justify-center overflow-hidden bg-black/35">
                                    @if($channel->logo)
                                        <img src="{{ $channel->logo }}"
                                             class="h-full w-full object-contain p-[clamp(8px,1.6vmin,18px)] transition duration-300 group-hover:scale-105"
                                             alt="{{ $channel->name }}">
                                    @else
                                        <div class="px-3 text-center text-[clamp(16px,2.3vmin,26px)] font-black text-white/70">
                                            {{ $channel->name }}
                                        </div>
                                    @endif
                                </div>

                                <div class="px-[clamp(8px,1.5vmin,16px)] py-[clamp(7px,1.2vmin,14px)]">
                                    <div class="truncate text-center text-[clamp(12px,1.7vmin,18px)] font-black">
                                        {{ $channel->name }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-4 rounded-2xl bg-white/[0.07] p-6 text-white/50">
                                Nessun canale trovato.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- PAGINAZIONE --}}
                @if($channels->hasPages())
                    <div class="mt-[clamp(5px,0.9vmin,10px)] grid shrink-0 grid-cols-2 gap-[clamp(5px,0.9vmin,10px)]" data-preserve-scroll>

                        @if($channels->onFirstPage())
                            <span class="flex items-center justify-center rounded-[clamp(10px,1.6vmin,16px)] bg-white/[0.04] px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,10px)] text-[clamp(9px,1.15vmin,12px)] font-black text-white/30">
                                ← Previous
                            </span>
                        @else
                            <a href="{{ $channels->previousPageUrl() }}"
                               data-preserve-scroll
                               class="flex items-center justify-center rounded-[clamp(10px,1.6vmin,16px)] bg-white/[0.08] px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,10px)] text-[clamp(9px,1.15vmin,12px)] font-black text-white hover:bg-white/[0.14]">
                                ← Previous
                            </a>
                        @endif

                        @if($channels->hasMorePages())
                            <a href="{{ $channels->nextPageUrl() }}"
                               data-preserve-scroll
                               class="flex items-center justify-center rounded-[clamp(10px,1.6vmin,16px)] bg-violet-700 px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,10px)] text-[clamp(9px,1.15vmin,12px)] font-black text-white hover:bg-violet-600">
                                Next →
                            </a>
                        @else
                            <span class="flex items-center justify-center rounded-[clamp(10px,1.6vmin,16px)] bg-white/[0.04] px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,10px)] text-[clamp(9px,1.15vmin,12px)] font-black text-white/30">
                                Next →
                            </span>
                        @endif

                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

@else

{{-- PAGINA 2 LIVE TV: ELENCO CANALI + PLAYER + EPG SENZA CATEGORIE --}}
<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">
    <div class="h-full w-full p-[clamp(4px,0.9vmin,14px)]">

        <div class="grid h-full grid-cols-[clamp(240px,28vw,460px)_1fr] gap-[clamp(4px,0.9vmin,14px)]">

            {{-- ELENCO CANALI --}}
            <section class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.045] p-[clamp(5px,1vmin,14px)] shadow-2xl">

                <div class="mb-[clamp(4px,0.8vmin,10px)] grid shrink-0 grid-cols-[1fr_auto] gap-2">
                    <a href="{{ route('customer.channels.index', [
                        'tipo' => 'live',
                        'category' => $category,
                        'category_search' => $categorySearch,
                        'channel_search' => $channelSearch,
                        'page' => request('page'),
                    ]) }}"
                       class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-violet-700 px-[clamp(8px,1.5vmin,16px)] py-[clamp(6px,1.2vmin,12px)] text-[clamp(10px,1.45vmin,15px)] font-black hover:bg-violet-600"
                       data-preserve-scroll>
                        ← Categorie
                    </a>

                    <a href="{{ url('/') }}"
                       class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-white/[0.08] px-[clamp(8px,1.5vmin,16px)] py-[clamp(6px,1.2vmin,12px)] text-[clamp(10px,1.45vmin,15px)] font-black hover:bg-white/[0.14]"
                       data-preserve-scroll>
                        Home
                    </a>
                </div>

            
                <form method="GET"
                        action="{{ route('customer.channels.index') }}"
                        class="mb-[clamp(4px,0.8vmin,10px)] shrink-0"
                        data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="category_search" value="{{ $categorySearch }}">

                    <input type="text"
                           name="channel_search"
                           value="{{ $channelSearch }}"
                           placeholder="Cerca canale..."
                           class="w-full rounded-[clamp(10px,1.8vmin,18px)] border border-white/10 bg-black/25 px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1.1vmin,12px)] text-[clamp(10px,1.45vmin,15px)] text-white placeholder:text-white/35 outline-none focus:border-orange-400">

                           @if($channelSearch)
                                <a href="{{ route('customer.channels.index', [
                                    'tipo' => $type,
                                    'category' => $category,
                                    'category_search' => $categorySearch,
                                    'channel' => optional($selectedChannel)->id,
                                    'page' => request('page'),
                                ]) }}"
                                data-preserve-scroll
                                class="mt-1 block rounded-xl bg-white/10 px-3 py-1.5 text-center text-[clamp(8px,1.1vmin,11px)] font-black text-white/70 hover:bg-white/15">
                                    Pulisci ricerca canali
                                </a>
                            @endif
                </form>

                <div id="channelsScroll"
                        class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1 space-y-[clamp(4px,0.7vmin,8px)]">

                    @forelse($channels as $index => $channel)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $category,
                            'category_search' => $categorySearch,
                            'channel_search' => $channelSearch,
                            'channel' => $channel->id,
                            'page' => request('page'),
                        ]) }}"
                           data-preserve-scroll
                           class="js-scroll-item grid grid-cols-[clamp(28px,4vmin,48px)_clamp(30px,4.4vmin,42px)_1fr_auto] items-center gap-[clamp(5px,0.9vmin,12px)] rounded-[clamp(10px,1.8vmin,18px)] px-[clamp(6px,1vmin,12px)] py-[clamp(5px,0.9vmin,10px)] transition
                           {{ optional($selectedChannel)->id === $channel->id ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">

                            <div class="text-center text-[clamp(10px,1.55vmin,19px)] font-black">
                                {{ method_exists($channels, 'firstItem') && $channels->firstItem() ? $channels->firstItem() + $index : $loop->iteration }}
                            </div>

                            <div class="flex h-[clamp(28px,4.5vmin,42px)] w-[clamp(28px,4.5vmin,42px)] items-center justify-center overflow-hidden rounded-lg bg-black/25">
                                @if($channel->logo)
                                    <img src="{{ $channel->logo }}" class="max-h-full max-w-full object-contain" alt="">
                                @else
                                    <span class="text-[clamp(8px,1vmin,11px)] text-white/35">TV</span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="truncate text-[clamp(10px,1.55vmin,17px)] font-black">
                                    {{ $channel->name }}
                                </div>

                                <div class="truncate text-[clamp(8px,1.05vmin,12px)] text-white/45">
                                    {{ $channel->group_title ?: 'Senza categoria' }}
                                </div>
                            </div>

                            @if(str_contains(strtolower($channel->name), 'hd'))
                                <div class="rounded bg-red-500 px-2 py-1 text-[clamp(7px,0.9vmin,10px)] font-black">
                                    HD
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/[0.07] p-4 text-[clamp(10px,1.4vmin,14px)] text-white/50">
                            Nessun canale trovato.
                        </div>
                    @endforelse
                </div>

                @if($channels->hasPages())
                    <div class="mt-[clamp(4px,0.8vmin,10px)] grid shrink-0 grid-cols-2 gap-[clamp(4px,0.8vmin,10px)]" data-preserve-scroll>

                        @if($channels->onFirstPage())
                            <span class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-white/[0.04] px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1.1vmin,12px)] text-[clamp(9px,1.2vmin,13px)] font-black text-white/30">
                                ← Previous
                            </span>
                        @else
                            <a href="{{ $channels->previousPageUrl() }}"
                            data-preserve-scroll
                            class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-white/[0.08] px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1.1vmin,12px)] text-[clamp(9px,1.2vmin,13px)] font-black text-white hover:bg-white/[0.14]">
                                ← Previous
                            </a>
                        @endif

                        @if($channels->hasMorePages())
                            <a href="{{ $channels->nextPageUrl() }}"
                            data-preserve-scroll
                            class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-violet-700 px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1.1vmin,12px)] text-[clamp(9px,1.2vmin,13px)] font-black text-white hover:bg-violet-600">
                                Next →
                            </a>
                        @else
                            <span class="flex items-center justify-center rounded-[clamp(10px,1.8vmin,18px)] bg-white/[0.04] px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1.1vmin,12px)] text-[clamp(9px,1.2vmin,13px)] font-black text-white/30">
                                Next →
                            </span>
                        @endif

                    </div>
                @endif
            </section>

            {{-- PLAYER + EPG --}}
            <main class="min-h-0 grid grid-rows-[minmax(0,7fr)_minmax(0,3fr)] gap-[clamp(4px,0.9vmin,14px)] overflow-hidden">

                {{-- PLAYER 70% --}}
                <section class="min-h-0 overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.045] p-[clamp(5px,1vmin,14px)] shadow-2xl">
                    <div class="grid h-full grid-rows-[auto_minmax(0,1fr)_auto] gap-[clamp(4px,0.8vmin,10px)]">

                        <div class="flex items-center justify-between rounded-[clamp(12px,2vmin,20px)] bg-white/[0.07] px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,10px)]">
                            <div class="flex items-center gap-[clamp(5px,0.9vmin,12px)]">
                                <span class="h-[clamp(6px,1vmin,12px)] w-[clamp(18px,3vmin,34px)] rounded-full bg-red-500"></span>
                                <span class="text-[clamp(10px,1.45vmin,16px)] font-black">Ordina</span>

                                <span class="ml-[clamp(4px,0.8vmin,12px)] h-[clamp(6px,1vmin,12px)] w-[clamp(18px,3vmin,34px)] rounded-full bg-green-500"></span>
                                <span class="text-[clamp(10px,1.45vmin,16px)] font-black">Categoria</span>

                                <span class="ml-[clamp(4px,0.8vmin,12px)] h-[clamp(6px,1vmin,12px)] w-[clamp(18px,3vmin,34px)] rounded-full bg-yellow-400"></span>
                                <span class="text-[clamp(10px,1.45vmin,16px)] font-black">Preferiti</span>
                            </div>
                        </div>

                        <div class="grid min-h-0 grid-cols-[1fr_clamp(72px,9vw,118px)] gap-[clamp(5px,1vmin,14px)]">
                            <div class="min-h-0 overflow-hidden rounded-[clamp(12px,2vmin,20px)] border border-white/10 bg-black">
                                <video id="iptv-video"
                                       class="h-full w-full bg-black object-contain"
                                       controls
                                       autoplay
                                       playsinline
                                       preload="auto"></video>
                            </div>

                            <div class="grid min-h-0 grid-rows-3 gap-[clamp(4px,0.8vmin,10px)]">
                                <div class="flex flex-col items-center justify-center rounded-[clamp(12px,2vmin,18px)] border border-white/10 bg-gradient-to-br from-white/[0.13] to-white/[0.045] p-[clamp(5px,0.9vmin,10px)] text-center shadow-inner">
                                    <div id="liveClockTime"
                                        class="leading-none text-[clamp(18px,3.4vmin,42px)] font-black tracking-tight text-white">
                                        {{ now()->format('H:i') }}
                                    </div>

                                    <div id="liveClockDate"
                                        class="mt-[clamp(3px,0.6vmin,7px)] max-w-full truncate px-1 text-[clamp(7px,1.05vmin,12px)] font-bold uppercase tracking-wide text-white/55">
                                        {{ now()->translatedFormat('D d M') }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-center rounded-[clamp(12px,2vmin,18px)] bg-gradient-to-br from-violet-600 to-red-400 p-[clamp(5px,0.9vmin,10px)] text-center text-[clamp(10px,1.35vmin,14px)] font-black">
                                    LIVE
                                </div>

                                <div class="flex items-center justify-center rounded-[clamp(12px,2vmin,18px)] bg-white/[0.08] p-[clamp(5px,0.9vmin,10px)] text-center text-[clamp(9px,1.2vmin,13px)] text-white/70">
                                    TV
                                </div>
                            </div>
                        </div>

                        @if($playableChannel)
                            <div class="grid grid-cols-[clamp(42px,6vmin,72px)_1fr_auto] items-center gap-[clamp(6px,1vmin,14px)]">
                                <div class="flex h-[clamp(40px,6vmin,68px)] w-[clamp(40px,6vmin,68px)] items-center justify-center rounded-xl bg-black/25">
                                    @if($playableChannel->logo)
                                        <img src="{{ $playableChannel->logo }}" class="max-h-full max-w-full object-contain" alt="">
                                    @else
                                        <span class="text-[clamp(8px,1vmin,12px)] font-black text-orange-400">IPTV</span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="truncate text-[clamp(14px,2.5vmin,30px)] font-black">
                                        {{ $playableChannel->name }}
                                    </div>

                                    <div class="truncate text-[clamp(8px,1.15vmin,13px)] text-white/50">
                                        {{ $playableChannel->group_title ?: 'Senza categoria' }}
                                    </div>
                                </div>

                                <div id="player-format" class="rounded-xl bg-white/10 px-[clamp(8px,1.4vmin,16px)] py-[clamp(5px,0.9vmin,9px)] text-[clamp(9px,1.2vmin,13px)] font-black">
                                    Auto
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- EPG 30% --}}
                <section class="min-h-0 overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.045] p-[clamp(5px,1vmin,14px)] shadow-2xl">
                    <div class="mb-[clamp(4px,0.8vmin,10px)] flex items-center justify-between">
                        <div class="text-[clamp(14px,2.3vmin,26px)] font-black">
                            EPG
                        </div>

                        <div class="truncate text-[clamp(9px,1.2vmin,13px)] text-white/45">
                            {{ optional($playableChannel)->name ?: 'Nessun canale' }}
                        </div>
                    </div>

                    <div id="epgScroll"
                         class="iptv-panel-scroll overflow-y-auto pr-1 space-y-[clamp(4px,0.7vmin,8px)]"
                         style="height: calc(100% - clamp(28px, 4.5vmin, 46px));">
                        @forelse($epgProgrammes as $programme)
                            @php
                                $start = \Carbon\Carbon::parse($programme->start_at);
                                $end = \Carbon\Carbon::parse($programme->end_at);
                                $isCurrent = now()->between($start, $end);
                            @endphp

                            <div class="grid grid-cols-[1fr_auto_clamp(10px,1.4vmin,16px)] items-center gap-[clamp(6px,1vmin,14px)] rounded-[clamp(10px,1.7vmin,16px)] px-[clamp(8px,1.4vmin,16px)] py-[clamp(6px,1vmin,12px)]
                                {{ $isCurrent ? 'bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07]' }}">
                                <div class="min-w-0">
                                    <div class="truncate text-[clamp(10px,1.45vmin,15px)] font-black">
                                        {{ $programme->title }}
                                    </div>

                                    <div class="truncate text-[clamp(8px,1.1vmin,12px)] text-white/50">
                                        {{ $programme->description ?: 'Nessuna descrizione' }}
                                    </div>
                                </div>

                                <div class="text-[clamp(9px,1.2vmin,13px)] font-bold">
                                    {{ $start->format('H:i') }} · {{ $end->format('H:i') }}
                                </div>

                                <div class="h-[clamp(10px,1.4vmin,16px)] w-[clamp(10px,1.4vmin,16px)] rounded-full {{ $isCurrent ? 'bg-red-500' : 'bg-white/80' }}"></div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-white/[0.07] p-4 text-[clamp(10px,1.4vmin,14px)] text-white/50">
                                Nessun EPG disponibile per questo canale.
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

@endif

@else

@php
    $detailItem = $selectedSeries ?: $selectedChannel;
    $isDetail = (bool) $detailItem;

    $title = $detailItem?->name ?? '';
    preg_match('/\((\d{4})\)/', $title, $yearMatch);
    $year = $yearMatch[1] ?? null;

    $background = $detailItem?->logo;

    $seasonKeys = collect($episodesBySeason ?? collect())->keys()->sort()->values();
    $activeSeason = request('season');

    if (($activeSeason === null || $activeSeason === '') && $seasonKeys->count()) {
        $activeSeason = $seasonKeys->first();
    }

    $seasonEpisodes = collect();

    if ($activeSeason !== null && isset($episodesBySeason[$activeSeason])) {
        $seasonEpisodes = collect($episodesBySeason[$activeSeason]);
    }

    $seriesIdForLinks = $selectedSeries?->id ?: $detailItem?->id;

    $castItems = collect();

    if (!empty($selectedSeries)) {
        $rawCast = $selectedSeries->cast_json ?? $selectedSeries->cast ?? null;

        if (is_string($rawCast) && str_starts_with(trim($rawCast), '[')) {
            $decoded = json_decode($rawCast, true);

            if (is_array($decoded)) {
                $castItems = collect($decoded);
            }
        } elseif (is_string($rawCast) && trim($rawCast) !== '') {
            $castItems = collect(explode(',', $rawCast))
                ->map(fn ($name) => ['name' => trim($name), 'photo' => null])
                ->filter(fn ($item) => !empty($item['name']));
        } elseif (is_array($rawCast)) {
            $castItems = collect($rawCast);
        }
    }
@endphp

<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">

    @if(in_array($type, ['film', 'serie'], true) && $play && $playableChannel)

        {{-- PLAYER FILM / EPISODIO A SCHERMO INTERO --}}
        @php
            if ($type === 'film') {
                $backUrl = route('customer.channels.index', [
                    'tipo' => 'film',
                    'category' => $category,
                    'category_search' => $categorySearch,
                    'channel_search' => $channelSearch,
                    'channel' => $selectedChannel?->id ?: $playableChannel->id,
                    'page' => request('page'),
                ]);

                $backLabel = '← Torna al film';
                $subtitle = $playableChannel->group_title ?: 'Film';
                $bottomTitle = $playableChannel->name;
                $bottomSubtitle = 'Film';
            } else {
                $backUrl = route('customer.channels.index', [
                    'tipo' => 'serie',
                    'category' => $category,
                    'category_search' => $categorySearch,
                    'channel_search' => $channelSearch,
                    'channel' => $selectedSeries?->id ?: $selectedChannel?->id,
                    'season' => $activeSeason,
                    'page' => request('page'),
                ]);

                $backLabel = '← Torna alla serie';
                $subtitle = $selectedSeries?->name ?: 'Serie';
                $bottomTitle = $selectedSeries?->name ?: 'Serie';
                $bottomSubtitle = 'Stagione ' . ($activeSeason ?: 1);
            }
        @endphp

        <div class="relative h-full w-full overflow-hidden bg-black text-white">

            <video id="iptv-video"
                   class="h-full w-full bg-black object-contain"
                   controls
                   autoplay
                   playsinline
                   preload="auto"></video>

            {{-- TOP BAR --}}
            <div id="fullscreen-top-bar"
                 class="pointer-events-none absolute left-0 right-0 top-0 z-20 bg-gradient-to-b from-black/85 to-transparent p-6 transition-opacity duration-300">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="truncate text-2xl font-black md:text-3xl">
                            {{ $playableChannel->name }}
                        </div>

                        <div class="truncate text-sm text-white/70 md:text-base">
                            {{ $subtitle }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div id="player-format" class="rounded-xl bg-white/10 px-4 py-2 font-black">
                            Auto
                        </div>

                        <a href="{{ $backUrl }}"
                           class="pointer-events-auto rounded-2xl bg-black/60 px-5 py-3 text-sm font-black hover:bg-white/20 md:text-base">
                            {{ $backLabel }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- BOTTOM BAR --}}
            <div id="fullscreen-bottom-bar"
                 class="pointer-events-none absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/85 to-transparent p-6 transition-opacity duration-300">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="truncate text-lg font-black">
                            {{ $bottomTitle }}
                        </div>

                        <div class="truncate text-sm text-white/70">
                            {{ $bottomSubtitle }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    @elseif(!$isDetail)

        {{-- GRIGLIA FILM / SERIE --}}
        <div class="grid h-full grid-cols-[clamp(280px,23vw,430px)_1fr] gap-[clamp(10px,1.5vmin,22px)] p-[clamp(10px,1.6vmin,22px)]">

            <aside class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(18px,3vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(8px,1.4vmin,16px)] shadow-2xl">

                <div class="mb-3 grid grid-cols-[1fr_auto] gap-2">
                    <a href="{{ url('/') }}"
                    class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-violet-700 px-4 py-3 text-[clamp(13px,1.8vmin,18px)] font-black hover:bg-violet-600"
                    data-preserve-scroll>
                        ← Home
                    </a>

                    <a href="{{ route('customer.playlists.index') }}"
                    class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-white/[0.08] px-4 py-3 text-[clamp(13px,1.8vmin,18px)] font-black hover:bg-white/[0.14]"
                    data-preserve-scroll>
                        Liste
                    </a>
                </div>

            
                <form method="GET"
                    action="{{ route('customer.channels.index') }}"
                    class="mb-[clamp(8px,1.5vmin,16px)]"
                    data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">

                    <input type="text"
                        name="category_search"
                        value="{{ $categorySearch }}"
                        placeholder="Cerca categoria..."
                        class="w-full rounded-[clamp(12px,2vmin,20px)] border border-white/10 bg-black/25 px-4 py-3 text-[clamp(12px,1.7vmin,16px)] text-white placeholder:text-white/35 outline-none focus:border-orange-400">

                    @if($categorySearch)
                        <a href="{{ route('customer.channels.index', ['tipo' => $type]) }}"
                        data-preserve-scroll
                        class="mt-2 block rounded-xl bg-white/10 px-4 py-2 text-center text-xs font-black text-white/70 hover:bg-white/15">
                            Pulisci ricerca categorie
                        </a>
                    @endif
                </form>

                <div id="categoriesScroll" class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1 space-y-2">

                    <a href="{{ route('customer.channels.index', [
                        'tipo' => $type,
                        'category_search' => $categorySearch,
                    ]) }}"
                    data-preserve-scroll
                    class="js-scroll-item flex items-center justify-between rounded-[clamp(12px,2vmin,20px)] px-4 py-3 transition
                    {{ !$category ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                        <span class="font-black text-[clamp(13px,1.9vmin,19px)]">
                            Aggiunti di recente
                        </span>

                        <span class="font-bold text-white/80">
                            {{ $totalChannels }}
                        </span>
                    </a>

                    @forelse($categories as $cat)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $cat->group_title,
                            'category_search' => $categorySearch,
                        ]) }}"
                        data-preserve-scroll
                        class="js-scroll-item flex items-center justify-between gap-3 rounded-[clamp(12px,2vmin,20px)] px-4 py-3 transition
                        {{ $category === $cat->group_title ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">

                            <span class="truncate font-black text-[clamp(13px,1.9vmin,19px)]">
                                {{ $cat->group_title }}
                            </span>

                            <span class="font-bold text-white/80">
                                {{ $cat->total }}
                            </span>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/[0.07] p-5 text-white/50">
                            Nessuna categoria trovata.
                        </div>
                    @endforelse
                </div>
            </aside>

            <main class="flex min-h-0 flex-col overflow-hidden">
                <div class="mb-4 grid shrink-0 grid-cols-[1fr_auto] gap-4">
                    <form method="GET" action="{{ route('customer.channels.index') }}" data-preserve-form>
                        <input type="hidden" name="tipo" value="{{ $type }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="category_search" value="{{ $categorySearch }}">

                        <input type="text"
                               name="channel_search"
                               value="{{ $channelSearch }}"
                               placeholder="{{ $type === 'film' ? 'Cerca film...' : 'Cerca serie...' }}"
                               class="w-full rounded-2xl border border-white/10 bg-white/[0.07] px-6 py-4 text-lg font-black outline-none placeholder:text-white/45 focus:border-orange-400">

                               @if($channelSearch)
                                    <a href="{{ route('customer.channels.index', [
                                        'tipo' => $type,
                                        'category' => $category,
                                        'category_search' => $categorySearch,
                                    ]) }}"
                                    data-preserve-scroll
                                    class="mt-2 block rounded-xl bg-white/10 px-4 py-2 text-center text-xs font-black text-white/70 hover:bg-white/15">
                                        {{ $type === 'film' ? 'Pulisci ricerca film' : 'Pulisci ricerca serie' }}
                                    </a>
                                @endif
                    </form>

                    <div class="rounded-2xl bg-white/[0.07] px-8 py-4 text-center text-lg font-black">
                        {{ $category ?: 'Aggiunti di recente' }}
                    </div>
                </div>

                <div id="vodGridScroll" class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-2">
                    <div class="grid grid-cols-5 gap-[clamp(14px,2vmin,26px)]">
                        @forelse($channels as $item)
                            <a href="{{ route('customer.channels.index', [
                                'tipo' => $type,
                                'category' => $category,
                                'category_search' => $categorySearch,
                                'channel_search' => $channelSearch,
                                'channel' => $item->id,
                                'page' => request('page'),
                            ]) }}"
                               data-preserve-scroll
                               class="js-scroll-item group overflow-hidden rounded-2xl bg-white/[0.06] transition hover:scale-[1.035] hover:bg-white/[0.10]">

                                <div class="aspect-[2/3] overflow-hidden rounded-2xl bg-black/40">
                                    @if($item->logo)
                                        <img src="{{ $item->logo }}"
                                             class="h-full w-full object-cover transition duration-300 group-hover:scale-110"
                                             alt="{{ $item->name }}">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-center">
                                            <div>
                                                <div class="text-5xl">{{ $type === 'film' ? '🎬' : '📺' }}</div>
                                                <div class="mt-3 px-3 text-sm font-black text-white/50">
                                                    {{ $item->name }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="px-3 py-3">
                                    <div class="truncate text-lg font-black">
                                        {{ $item->name }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-5 rounded-2xl bg-white/[0.07] p-8 text-white/50">
                                Nessun contenuto trovato.
                            </div>
                        @endforelse
                    </div>

                </div>
                @if($channels->hasPages())
                    <div class="mt-[clamp(6px,1vmin,12px)] grid shrink-0 grid-cols-2 gap-[clamp(6px,1vmin,12px)]" data-preserve-scroll>

                        @if($channels->onFirstPage())
                            <span class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-white/[0.04] px-[clamp(10px,1.6vmin,18px)] py-[clamp(7px,1.2vmin,13px)] text-[clamp(10px,1.4vmin,14px)] font-black text-white/30">
                                ← Previous
                            </span>
                        @else
                            <a href="{{ $channels->previousPageUrl() }}"
                            data-preserve-scroll
                            class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-white/[0.08] px-[clamp(10px,1.6vmin,18px)] py-[clamp(7px,1.2vmin,13px)] text-[clamp(10px,1.4vmin,14px)] font-black text-white hover:bg-white/[0.14]">
                                ← Previous
                            </a>
                        @endif

                        @if($channels->hasMorePages())
                            <a href="{{ $channels->nextPageUrl() }}"
                            data-preserve-scroll
                            class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-violet-700 px-[clamp(10px,1.6vmin,18px)] py-[clamp(7px,1.2vmin,13px)] text-[clamp(10px,1.4vmin,14px)] font-black text-white hover:bg-violet-600">
                                Next →
                            </a>
                        @else
                            <span class="flex items-center justify-center rounded-[clamp(12px,2vmin,20px)] bg-white/[0.04] px-[clamp(10px,1.6vmin,18px)] py-[clamp(7px,1.2vmin,13px)] text-[clamp(10px,1.4vmin,14px)] font-black text-white/30">
                                Next →
                            </span>
                        @endif

                    </div>
                @endif
            </main>
        </div>

    @else

        @if($type === 'film')

            {{-- DETTAGLIO FILM --}}
            <div class="relative h-full w-full overflow-hidden">

                @if($background)
                    <div class="absolute inset-0 bg-cover bg-center"
                         style="background-image: url('{{ $background }}')"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-violet-950 to-black"></div>
                @endif

                <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/55 to-black/15"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-black/20"></div>

                <div id="filmDetailScroll" class="iptv-panel-scroll relative z-10 h-full w-full overflow-y-auto p-[clamp(26px,5vmin,70px)]">

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $category,
                            'category_search' => $categorySearch,
                            'channel_search' => $channelSearch,
                            'page' => request('page'),
                        ]) }}"
                           class="rounded-2xl bg-black/45 px-6 py-3 text-lg font-black hover:bg-white/20"
                           data-preserve-scroll>
                            ← Indietro
                        </a>

                        <a href="{{ url('/') }}"
                           class="rounded-2xl bg-violet-700 px-6 py-3 text-lg font-black hover:bg-violet-600">
                            Home
                        </a>
                    </div>

                    <div class="grid min-h-[calc(100%-80px)] grid-cols-[minmax(430px,42vw)_1fr] gap-8 pb-[clamp(30px,5vmin,80px)]">

                        <section class="flex flex-col justify-center">
                            <div class="text-[clamp(34px,6vmin,82px)] font-black leading-tight drop-shadow-2xl">
                                {{ $title }}
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-4 text-[clamp(18px,2.5vmin,30px)] font-black">
                                <span class="text-yellow-400">★ ★ ★ ★ ☆</span>

                                @if($year)
                                    <span class="rounded-lg bg-white/90 px-4 py-1 text-black">{{ $year }}</span>
                                @endif

                                <span>{{ $detailItem->group_title ?: 'Film' }}</span>
                            </div>

                            <p class="mt-7 max-w-3xl text-[clamp(18px,2.4vmin,30px)] leading-relaxed text-white/90 drop-shadow">
                                Seleziona Gioca per avviare la riproduzione del film.
                            </p>

                            <div class="mt-10 flex max-w-[520px] flex-col gap-4">
                                <a href="{{ route('customer.channels.index', [
                                    'tipo' => 'film',
                                    'category' => $category,
                                    'category_search' => $categorySearch,
                                    'channel_search' => $channelSearch,
                                    'channel' => $detailItem->id,
                                    'play' => 1,
                                    'page' => request('page'),
                                ]) }}"
                                   class="flex items-center justify-center gap-4 rounded-2xl bg-black/45 px-8 py-5 text-2xl font-black hover:bg-white/20">
                                    ▶ GIOCA
                                </a>

                                <button type="button"
                                        class="flex items-center justify-center gap-4 rounded-2xl bg-white/85 px-8 py-5 text-2xl font-black text-black">
                                    ♥ PREFERITI
                                </button>
                            </div>
                        </section>

                        <section class="flex items-center justify-center">
                            @if($detailItem->logo)
                                <div class="hidden xl:flex aspect-[2/3] max-h-[70vh] overflow-hidden rounded-[34px] border border-white/15 bg-black/40 shadow-2xl">
                                    <img src="{{ $detailItem->logo }}"
                                         class="h-full w-full object-cover"
                                         alt="{{ $detailItem->name }}">
                                </div>
                            @else
                                <div class="hidden xl:flex aspect-[2/3] max-h-[70vh] items-center justify-center rounded-[34px] border border-white/15 bg-black/40 p-10 text-center shadow-2xl">
                                    <div>
                                        <div class="text-7xl">🎬</div>
                                        <div class="mt-4 text-3xl font-black">{{ $title }}</div>
                                    </div>
                                </div>
                            @endif
                        </section>
                    </div>
                </div>
            </div>

        @else

            {{-- DETTAGLIO SERIE --}}
            <div class="relative h-full w-full overflow-hidden">

                @if($background)
                    <div class="absolute inset-0 bg-cover bg-center"
                         style="background-image: url('{{ $background }}')"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-violet-950 to-black"></div>
                @endif

                <div class="absolute inset-0 bg-gradient-to-r from-black/82 via-black/35 to-black/18"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/10"></div>

                <div class="relative z-10 h-full w-full overflow-y-auto p-[clamp(26px,5vmin,70px)]">

                    <div class="mb-6 flex items-center gap-3">
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => 'serie',
                            'category' => $category,
                            'category_search' => $categorySearch,
                            'channel_search' => $channelSearch,
                            'page' => request('page'),
                        ]) }}"
                           class="rounded-2xl bg-black/45 px-6 py-3 text-lg font-black hover:bg-white/20"
                           data-preserve-scroll>
                            ← Indietro
                        </a>

                        <a href="{{ url('/') }}"
                           class="rounded-2xl bg-violet-700 px-6 py-3 text-lg font-black hover:bg-violet-600">
                            Home
                        </a>
                    </div>

                    <div class="max-w-[980px]">
                        <div class="text-[clamp(34px,6vmin,72px)] font-black leading-tight drop-shadow-2xl">
                            {{ $title }}
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-4 text-[clamp(18px,2.5vmin,28px)] font-black">
                            <span class="text-yellow-400">★ ★ ★ ★ ☆</span>

                            @if($year)
                                <span class="rounded-lg bg-white/90 px-4 py-1 text-black">{{ $year }}</span>
                            @endif

                            <span>{{ $detailItem->group_title ?: 'Serie TV' }}</span>
                        </div>

                        <p class="mt-7 max-w-4xl text-[clamp(18px,2.4vmin,28px)] leading-relaxed text-white/90 drop-shadow">
                            Seleziona una stagione e poi un episodio. Quando clicchi un episodio si apre a schermo intero.
                        </p>

                        <div class="mt-8 flex max-w-[520px] flex-col gap-4">
                            <button type="button"
                                    class="flex items-center justify-center gap-4 rounded-2xl bg-white/85 px-8 py-5 text-2xl font-black text-black">
                                ♥ PREFERITI
                            </button>
                        </div>
                    </div>

                    <div class="mt-12">
                        <div class="mb-4 text-[clamp(24px,3vmin,40px)] font-black">
                            Stagioni
                        </div>

                        <div class="flex flex-wrap gap-5">
                            @forelse($seasonKeys as $seasonNumber)
                                <a href="{{ route('customer.channels.index', [
                                    'tipo' => 'serie',
                                    'category' => $category,
                                    'category_search' => $categorySearch,
                                    'channel_search' => $channelSearch,
                                    'channel' => $seriesIdForLinks,
                                    'season' => $seasonNumber,
                                    'page' => request('page'),
                                ]) }}"
                                   data-preserve-scroll
                                   class="rounded-2xl px-10 py-4 text-2xl font-black transition
                                   {{ (string) $activeSeason === (string) $seasonNumber ? 'bg-indigo-600' : 'bg-black/55 hover:bg-black/70' }}">
                                    Stagione {{ $seasonNumber }}
                                </a>
                            @empty
                                <div class="rounded-2xl bg-black/45 px-6 py-4 text-lg font-black text-white/70">
                                    Nessuna stagione disponibile
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-10">
                        <div class="mb-4 text-[clamp(22px,2.8vmin,36px)] font-black">
                            Episodi
                        </div>

                        @if($seriesImportError)
                            <div class="mb-5 rounded-2xl bg-red-500/20 p-4 text-red-100">
                                Errore import episodi: {{ $seriesImportError }}
                            </div>
                        @endif

                        <div id="episodesScroll" class="iptv-panel-scroll overflow-x-auto pb-3">
                            <div class="flex gap-8">
                                @forelse($seasonEpisodes as $episode)
                                    <a href="{{ route('customer.channels.index', [
                                        'tipo' => 'serie',
                                        'category' => $category,
                                        'category_search' => $categorySearch,
                                        'channel_search' => $channelSearch,
                                        'channel' => $seriesIdForLinks,
                                        'episode' => $episode->id,
                                        'season' => $activeSeason,
                                        'play' => 1,
                                        'page' => request('page'),
                                    ]) }}"
                                       data-preserve-scroll
                                       class="js-scroll-item block min-w-[340px] max-w-[340px] overflow-hidden rounded-[24px] bg-black/45 shadow-2xl transition hover:scale-[1.02] hover:bg-black/60">

                                        <div class="aspect-video overflow-hidden bg-black/40">
                                            @if($episode->logo)
                                                <img src="{{ $episode->logo }}"
                                                     alt="{{ $episode->name }}"
                                                     class="h-full w-full object-cover">
                                            @elseif($selectedSeries?->logo)
                                                <img src="{{ $selectedSeries->logo }}"
                                                     alt="{{ $episode->name }}"
                                                     class="h-full w-full object-cover opacity-80">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-5xl">
                                                    ▶
                                                </div>
                                            @endif
                                        </div>

                                        <div class="px-4 py-4">
                                            <div class="truncate text-2xl font-black">
                                                S{{ str_pad((string) ($episode->season_number ?: $activeSeason ?: 1), 1, '0', STR_PAD_LEFT) }}
                                                Episode{{ $episode->episode_number ?: $loop->iteration }}
                                            </div>

                                            <div class="mt-1 truncate text-lg text-white/85">
                                                {{ $episode->name }}
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="rounded-2xl bg-black/45 p-5 text-white/70">
                                        Nessun episodio disponibile per questa stagione.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        @endif

    @endif
</div>

@endif

@endsection

@push('scripts')
@if($playableChannel)
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/mpegts.js@latest"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('iptv-video');
    const formatLabel = document.getElementById('player-format');
    const streamUrl = @json($playableChannel->stream_url);
    
    if (!video || !streamUrl) {
        return;
    }

    function detectFormat(url) {
        const clean = url.split('?')[0].toLowerCase();

        if (clean.endsWith('.m3u8')) {
            return 'hls';
        }

        /*
         * Film e serie Xtream sono quasi sempre file VOD.
         * Non devono diventare MPEG-TS solo perché l'URL contiene /series/ o /movie/.
         */
        if (
            clean.endsWith('.mp4') ||
            clean.endsWith('.m4v') ||
            clean.endsWith('.mov') ||
            clean.endsWith('.webm') ||
            clean.endsWith('.mkv') ||
            clean.includes('/movie/') ||
            clean.includes('/series/')
        ) {
            return 'native';
        }

        /*
         * MPEG-TS solo per live .ts oppure URL live.
         */
        if (clean.endsWith('.ts') || clean.includes('/live/')) {
            return 'mpegts';
        }

        return 'native';
    }

    function playHls(url) {
        if (formatLabel) {
            formatLabel.textContent = 'HLS';
        }

        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = url;
            video.play().catch(() => {});
            return;
        }

        if (!window.Hls || !Hls.isSupported()) {
            if (formatLabel) {
                formatLabel.textContent = 'HLS non supportato';
            }

            return;
        }

        const hls = new Hls({
            lowLatencyMode: false,
            backBufferLength: 60,
            maxBufferLength: 30,
            maxMaxBufferLength: 60,
            liveSyncDurationCount: 4,
            liveMaxLatencyDurationCount: 8,
            fragLoadingMaxRetry: 8,
            manifestLoadingMaxRetry: 8,
            levelLoadingMaxRetry: 8
        });

        hls.loadSource(url);
        hls.attachMedia(video);

        hls.on(Hls.Events.MANIFEST_PARSED, function () {
            video.play().catch(() => {});
        });
    }

    function playTs(url) {
        if (formatLabel) {
            formatLabel.textContent = 'MPEG-TS';
        }

        if (!window.mpegts || !mpegts.isSupported()) {
            if (formatLabel) {
                formatLabel.textContent = 'TS non supportato';
            }

            return;
        }

        const tsPlayer = mpegts.createPlayer({
            type: 'mpegts',
            isLive: @json($playableChannel->type === 'live'),
            url: url
        }, {
            enableWorker: true,
            enableStashBuffer: true,
            stashInitialSize: 1024 * 1024 * 3,
            lazyLoad: false,
            liveBufferLatencyChasing: false,
            autoCleanupSourceBuffer: true
        });

        tsPlayer.attachMediaElement(video);
        tsPlayer.load();
        video.play().catch(() => {});
    }

    function playNative(url) {
        if (formatLabel) {
            formatLabel.textContent = 'VOD';
        }

        video.src = url;
        video.load();

        video.addEventListener('loadedmetadata', function () {
            video.play().catch(() => {});
        }, { once: true });

        video.addEventListener('canplay', function () {
            video.play().catch(() => {});
        }, { once: true });

        video.addEventListener('error', function () {
            if (formatLabel) {
                formatLabel.textContent = 'Formato non supportato';
            }

            console.error('Errore riproduzione VOD:', {
                url: url,
                error: video.error
            });
        }, { once: true });
    }

    const format = detectFormat(streamUrl);

    if (format === 'hls') {
        playHls(streamUrl);
    } else if (format === 'mpegts') {
        playTs(streamUrl);
    } else {
        playNative(streamUrl);
    }
});
</script>
@endif

@if(in_array($type, ['film', 'serie'], true) && $play && $playableChannel)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('iptv-video');
    const topBar = document.getElementById('fullscreen-top-bar');
    const bottomBar = document.getElementById('fullscreen-bottom-bar');

    const tryFullscreen = async () => {
        try {
            if (!document.fullscreenElement) {
                await document.documentElement.requestFullscreen();
            }
        } catch (e) {
            // Alcuni browser bloccano il fullscreen automatico.
        }
    };

    setTimeout(tryFullscreen, 500);

    document.addEventListener('click', tryFullscreen, { once: true });

    if (!video || !topBar || !bottomBar) {
        return;
    }

    let hideTimer = null;

    function showBars() {
        topBar.classList.remove('opacity-0');
        bottomBar.classList.remove('opacity-0');

        clearTimeout(hideTimer);

        hideTimer = setTimeout(function () {
            if (!video.paused) {
                topBar.classList.add('opacity-0');
                bottomBar.classList.add('opacity-0');
            }
        }, 3000);
    }

    function hideBars() {
        if (!video.paused) {
            topBar.classList.add('opacity-0');
            bottomBar.classList.add('opacity-0');
        }
    }

    video.addEventListener('play', function () {
        setTimeout(hideBars, 1500);
    });

    video.addEventListener('pause', showBars);
    video.addEventListener('mousemove', showBars);
    video.addEventListener('touchstart', showBars);

    document.addEventListener('mousemove', showBars);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            showBars();
        }
    });

    showBars();
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = @json($type);
    const category = @json($category ?? 'all');
    const categorySearch = @json($categorySearch ?? '');
    const channelSearch = @json($channelSearch ?? '');
    const selectedId = @json(optional($selectedChannel)->id ?? optional($selectedSeries)->id ?? optional($playableChannel)->id ?? 'none');

    const panels = {
        categoriesScroll: 'iptv-scroll-categories:' + type + ':' + categorySearch,
        channelsScroll: 'iptv-scroll-channels:' + type + ':' + category + ':' + channelSearch,
        vodGridScroll: 'iptv-scroll-vod-grid:' + type + ':' + category + ':' + channelSearch,
        epgScroll: 'iptv-scroll-epg:' + @json(optional($playableChannel)->id ?? 'none'),
        episodesScroll: 'iptv-scroll-episodes:' + selectedId,
        filmDetailScroll: 'iptv-scroll-film-detail:' + selectedId
    };

    function saveScrollPositions() {
        Object.keys(panels).forEach(function (id) {
            const el = document.getElementById(id);

            if (el) {
                const value = id === 'episodesScroll'
                    ? String(el.scrollLeft || 0)
                    : String(el.scrollTop || 0);

                sessionStorage.setItem(panels[id], value);
            }
        });
    }

    function restoreScrollPositions() {
        Object.keys(panels).forEach(function (id) {
            const el = document.getElementById(id);

            if (!el) {
                return;
            }

            const value = sessionStorage.getItem(panels[id]);

            if (value !== null) {
                if (id === 'episodesScroll') {
                    el.scrollLeft = parseInt(value, 10) || 0;
                } else {
                    el.scrollTop = parseInt(value, 10) || 0;
                }
            }
        });

        setTimeout(scrollActiveItemsIntoView, 80);
    }

    function scrollActiveItemsIntoView() {
        document.querySelectorAll('.iptv-panel-scroll').forEach(function (panel) {
            const active = panel.querySelector('.is-active');

            if (!active) {
                return;
            }

            active.scrollIntoView({
                block: 'center',
                inline: 'center'
            });
        });
    }

    document.querySelectorAll('a[data-preserve-scroll]').forEach(function (link) {
        link.addEventListener('click', saveScrollPositions);
    });

    document.querySelectorAll('[data-preserve-form]').forEach(function (form) {
        form.addEventListener('submit', saveScrollPositions);
    });

    window.addEventListener('beforeunload', saveScrollPositions);

    setTimeout(restoreScrollPositions, 120);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const timeEl = document.getElementById('liveClockTime');
    const dateEl = document.getElementById('liveClockDate');

    if (!timeEl || !dateEl) {
        return;
    }

    function updateLiveClock() {
        const now = new Date();

        timeEl.textContent = now.toLocaleTimeString('it-IT', {
            hour: '2-digit',
            minute: '2-digit'
        });

        dateEl.textContent = now.toLocaleDateString('it-IT', {
            weekday: 'short',
            day: '2-digit',
            month: 'short'
        }).replace('.', '');
    }

    updateLiveClock();
    setInterval(updateLiveClock, 1000);
});
</script>
@endpush