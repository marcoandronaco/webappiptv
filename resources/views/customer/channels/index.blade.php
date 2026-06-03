@extends('layouts.iptv-screen', ['title' => 'Canali'])

@section('content')

@if($type === 'live')

<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">
    <div class="h-full w-full p-[clamp(8px,1.6vmin,20px)]">

        <div class="grid h-full grid-cols-[clamp(270px,22vw,410px)_clamp(360px,30vw,560px)_1fr] gap-[clamp(8px,1.4vmin,18px)]">

            {{-- CATEGORIE LIVE --}}
            <aside class="min-h-0 rounded-[clamp(18px,3vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(8px,1.4vmin,16px)] shadow-2xl overflow-hidden">

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

                <div class="mb-[clamp(8px,1.5vmin,16px)] flex items-center gap-3 rounded-[clamp(14px,2vmin,22px)] bg-white/[0.07] px-4 py-3">
                    <div class="text-[clamp(22px,3vmin,34px)]">▦</div>

                    <div class="min-w-0">
                        <div class="text-[clamp(14px,2vmin,22px)] font-black">
                            TV dal vivo
                        </div>

                        <div class="text-[clamp(10px,1.4vmin,14px)] text-white/45">
                            {{ $totalChannels }} canali
                        </div>
                    </div>
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

                <div id="categoriesScroll" class="iptv-panel-scroll h-[calc(100%-208px)] overflow-y-auto pr-1 space-y-2">

                    <a href="{{ route('customer.channels.index', [
                        'tipo' => $type,
                        'category_search' => $categorySearch,
                    ]) }}"
                       data-preserve-scroll
                       class="js-scroll-item flex items-center justify-between rounded-[clamp(12px,2vmin,20px)] px-4 py-3 transition
                       {{ !$category ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                        <span class="font-black text-[clamp(13px,1.9vmin,19px)]">Tutto</span>
                        <span class="font-bold text-white/80">{{ $totalChannels }}</span>
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

            {{-- ELENCO CANALI LIVE --}}
            <section class="min-h-0 rounded-[clamp(18px,3vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(8px,1.4vmin,16px)] shadow-2xl overflow-hidden">

                <div class="mb-[clamp(8px,1.5vmin,16px)] flex items-center justify-between gap-3 rounded-[clamp(14px,2vmin,22px)] bg-white/[0.07] px-4 py-3">
                    <div>
                        <div class="text-[clamp(14px,2vmin,22px)] font-black">
                            Elenco canali
                        </div>

                        <div class="max-w-[320px] truncate text-[clamp(10px,1.4vmin,14px)] text-white/45">
                            {{ $category ?: 'Tutti i canali' }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('customer.channels.index', ['tipo' => 'live']) }}"
                           data-preserve-scroll
                           class="rounded-xl px-3 py-2 text-xs font-black {{ $type === 'live' ? 'bg-orange-500' : 'bg-white/10' }}">
                            TV
                        </a>

                        <a href="{{ route('customer.channels.index', ['tipo' => 'film']) }}"
                           data-preserve-scroll
                           class="rounded-xl px-3 py-2 text-xs font-black {{ $type === 'film' ? 'bg-orange-500' : 'bg-white/10' }}">
                            Film
                        </a>

                        <a href="{{ route('customer.channels.index', ['tipo' => 'serie']) }}"
                           data-preserve-scroll
                           class="rounded-xl px-3 py-2 text-xs font-black {{ $type === 'serie' ? 'bg-orange-500' : 'bg-white/10' }}">
                            Serie
                        </a>
                    </div>
                </div>

                <form method="GET"
                      action="{{ route('customer.channels.index') }}"
                      class="mb-[clamp(8px,1.5vmin,16px)]"
                      data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="category_search" value="{{ $categorySearch }}">

                    <input type="text"
                           name="channel_search"
                           value="{{ $channelSearch }}"
                           placeholder="{{ $category ? 'Cerca canale in questa categoria...' : 'Seleziona prima una categoria' }}"
                           @disabled(!$category)
                           class="w-full rounded-[clamp(12px,2vmin,20px)] border border-white/10 bg-black/25 px-4 py-3 text-[clamp(12px,1.7vmin,16px)] text-white placeholder:text-white/35 outline-none focus:border-orange-400 disabled:cursor-not-allowed disabled:opacity-45">

                    @if($category && $channelSearch)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $category,
                            'category_search' => $categorySearch,
                        ]) }}"
                           data-preserve-scroll
                           class="mt-2 block rounded-xl bg-white/10 px-4 py-2 text-center text-xs font-black text-white/70 hover:bg-white/15">
                            Pulisci ricerca canali
                        </a>
                    @endif
                </form>

                <div id="channelsScroll" class="iptv-panel-scroll h-[calc(100%-184px)] overflow-y-auto pr-1 space-y-2">
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
                           class="js-scroll-item grid grid-cols-[52px_44px_1fr_auto] items-center gap-3 rounded-[clamp(12px,2vmin,20px)] px-3 py-3 transition
                           {{ optional($selectedChannel)->id === $channel->id ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">

                            <div class="text-center text-[clamp(16px,2.4vmin,25px)] font-black">
                                {{ method_exists($channels, 'firstItem') && $channels->firstItem() ? $channels->firstItem() + $index : $loop->iteration }}
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-black/25">
                                @if($channel->logo)
                                    <img src="{{ $channel->logo }}" class="max-h-full max-w-full object-contain" alt="">
                                @else
                                    <span class="text-xs text-white/35">TV</span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="truncate text-[clamp(14px,2.1vmin,22px)] font-black">
                                    {{ $channel->name }}
                                </div>

                                <div class="truncate text-[clamp(10px,1.5vmin,14px)] text-white/45">
                                    {{ $channel->group_title ?: 'Senza categoria' }}
                                </div>
                            </div>

                            @if(str_contains(strtolower($channel->name), 'hd'))
                                <div class="rounded bg-red-500 px-2 py-1 text-[10px] font-black">
                                    HD
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/[0.07] p-6 text-white/50">
                            Nessun canale trovato.
                        </div>
                    @endforelse
                </div>

                <div class="mt-3" data-preserve-scroll>
                    {{ $channels->links() }}
                </div>
            </section>

            {{-- PLAYER + EPG LIVE --}}
            <main class="min-h-0 grid grid-rows-[auto_1fr] gap-[clamp(10px,1.6vmin,18px)] overflow-hidden">

                <section class="rounded-[clamp(18px,3vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(10px,1.5vmin,18px)] shadow-2xl">

                    <div class="mb-3 flex items-center justify-between rounded-[clamp(14px,2vmin,22px)] bg-white/[0.07] px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-8 rounded-full bg-red-500"></span>
                            <span class="text-[clamp(14px,2vmin,22px)] font-black">Ordina</span>

                            <span class="ml-4 h-3 w-8 rounded-full bg-green-500"></span>
                            <span class="text-[clamp(14px,2vmin,22px)] font-black">Categoria</span>

                            <span class="ml-4 h-3 w-8 rounded-full bg-yellow-400"></span>
                            <span class="text-[clamp(14px,2vmin,22px)] font-black">Preferiti</span>
                        </div>

                    </div>

                    <div class="grid grid-cols-[1fr_120px] gap-4">
                        <div class="overflow-hidden rounded-[clamp(14px,2vmin,22px)] border border-white/10 bg-black">
                            @if($playableChannel)
                                <video id="iptv-video"
                                       class="aspect-video w-full bg-black object-contain"
                                       controls
                                       autoplay
                                       playsinline
                                       preload="auto"></video>
                            @else
                                <div class="flex aspect-video items-center justify-center text-white/40">
                                    Seleziona un canale
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-[18px] bg-white/[0.08] p-3 text-center">
                                <div class="text-xs text-white/50">{{ now()->format('d/m/Y') }}</div>
                                <div class="text-[clamp(24px,4vmin,42px)] font-black">{{ now()->format('H:i') }}</div>
                            </div>

                            <div class="rounded-[18px] bg-gradient-to-br from-violet-600 to-red-400 p-3 text-center font-black">
                                LIVE
                            </div>

                            <div class="rounded-[18px] bg-white/[0.08] p-3 text-center text-sm text-white/70">
                                TV
                            </div>
                        </div>
                    </div>

                    @if($playableChannel)
                        <div class="mt-4 grid grid-cols-[90px_1fr_auto] items-center gap-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-black/25">
                                @if($playableChannel->logo)
                                    <img src="{{ $playableChannel->logo }}" class="max-h-full max-w-full object-contain" alt="">
                                @else
                                    <span class="font-black text-orange-400">IPTV</span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="text-[clamp(22px,4vmin,44px)] font-black">
                                    {{ $playableChannel->name }}
                                </div>

                                <div class="text-white/50">
                                    {{ $playableChannel->group_title ?: 'Senza categoria' }}
                                </div>
                            </div>

                            <div id="player-format" class="rounded-xl bg-white/10 px-4 py-2 font-black">
                                Auto
                            </div>
                        </div>
                    @endif
                </section>

                <section class="min-h-0 rounded-[clamp(18px,3vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(10px,1.5vmin,18px)] shadow-2xl overflow-hidden">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="text-[clamp(18px,2.7vmin,30px)] font-black">
                            EPG
                        </div>

                        <div class="text-white/45">
                            {{ optional($playableChannel)->name ?: 'Nessun canale' }}
                        </div>
                    </div>

                    <div id="epgScroll" class="iptv-panel-scroll h-[calc(100%-48px)] overflow-y-auto space-y-2">
                        @forelse($epgProgrammes as $programme)
                            @php
                                $start = \Carbon\Carbon::parse($programme->start_at);
                                $end = \Carbon\Carbon::parse($programme->end_at);
                                $isCurrent = now()->between($start, $end);
                            @endphp

                            <div class="grid grid-cols-[1fr_auto_18px] items-center gap-4 rounded-[clamp(10px,1.7vmin,16px)] px-4 py-3
                                {{ $isCurrent ? 'bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07]' }}">
                                <div class="min-w-0">
                                    <div class="truncate font-black">{{ $programme->title }}</div>

                                    <div class="truncate text-sm text-white/50">
                                        {{ $programme->description ?: 'Nessuna descrizione' }}
                                    </div>
                                </div>

                                <div class="font-bold">
                                    {{ $start->format('H:i') }} · {{ $end->format('H:i') }}
                                </div>

                                <div class="h-4 w-4 rounded-full {{ $isCurrent ? 'bg-red-500' : 'bg-white/80' }}"></div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-white/[0.07] p-5 text-white/50">
                                Nessun EPG disponibile per questo canale.
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

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

            {{-- TOP BAR: pointer-events-none per non bloccare i controlli video --}}
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

            {{-- BOTTOM BAR: pointer-events-none per lasciare cliccabile volume/fullscreen nativo --}}
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

            <aside class="min-h-0 overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.045] p-3">

                <div class="mb-3 grid grid-cols-[1fr_auto] gap-2">
                    <a href="{{ url('/') }}"
                       class="rounded-2xl bg-violet-700 px-4 py-3 text-center font-black hover:bg-violet-600"
                       data-preserve-scroll>
                        ← Home
                    </a>

                    <a href="{{ route('customer.playlists.index') }}"
                       class="rounded-2xl bg-white/10 px-4 py-3 text-center font-black hover:bg-white/15"
                       data-preserve-scroll>
                        Liste
                    </a>
                </div>

                <form method="GET" action="{{ route('customer.channels.index') }}" class="mb-3" data-preserve-form>
                    <input type="hidden" name="tipo" value="{{ $type }}">

                    <div class="flex items-center gap-3 rounded-2xl bg-yellow-400 px-4 py-3 text-black">
                        <span class="text-2xl">🔍</span>

                        <input type="text"
                               name="category_search"
                               value="{{ $categorySearch }}"
                               placeholder="Cerca"
                               class="w-full bg-transparent text-lg font-black outline-none placeholder:text-black/60">
                    </div>
                </form>

                <div id="categoriesScroll" class="iptv-panel-scroll h-[calc(100%-126px)] space-y-2 overflow-y-auto pr-1">

                    <a href="{{ route('customer.channels.index', ['tipo' => $type]) }}"
                       data-preserve-scroll
                       class="js-scroll-item flex items-center justify-between rounded-2xl px-5 py-4 font-black
                       {{ !$category ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                        <span>Aggiunti di recente</span>
                        <span>{{ $totalChannels }}</span>
                    </a>

                    @forelse($categories as $cat)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $cat->group_title,
                            'category_search' => $categorySearch,
                        ]) }}"
                           data-preserve-scroll
                           class="js-scroll-item flex items-center justify-between rounded-2xl px-5 py-4 font-black
                           {{ $category === $cat->group_title ? 'is-active bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                            <span class="truncate">◇ {{ $cat->group_title }} ◇</span>
                            <span>{{ $cat->total }}</span>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/[0.07] p-5 text-white/50">
                            Nessuna categoria trovata.
                        </div>
                    @endforelse
                </div>
            </aside>

            <main class="min-h-0 overflow-hidden">
                <div class="mb-4 grid grid-cols-[1fr_auto] gap-4">
                    <form method="GET" action="{{ route('customer.channels.index') }}" data-preserve-form>
                        <input type="hidden" name="tipo" value="{{ $type }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="category_search" value="{{ $categorySearch }}">

                        <input type="text"
                               name="channel_search"
                               value="{{ $channelSearch }}"
                               placeholder="{{ $type === 'film' ? 'Cerca film...' : 'Cerca serie...' }}"
                               class="w-full rounded-2xl border border-white/10 bg-white/[0.07] px-6 py-4 text-lg font-black outline-none placeholder:text-white/45 focus:border-orange-400">
                    </form>

                    <div class="rounded-2xl bg-white/[0.07] px-8 py-4 text-center text-lg font-black">
                        {{ $category ?: 'Aggiunti di recente' }}
                    </div>
                </div>

                <div id="vodGridScroll" class="iptv-panel-scroll h-[calc(100%-76px)] overflow-y-auto pr-2">
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

                    <div class="mt-5" data-preserve-scroll>
                        {{ $channels->links() }}
                    </div>
                </div>
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

                <div class="relative z-10 h-full w-full p-[clamp(26px,5vmin,70px)]">

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

                    <div class="grid h-[calc(100%-80px)] grid-cols-[minmax(430px,42vw)_1fr] gap-8">

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

            {{-- DETTAGLIO SERIE SENZA RIQUADRO PLAYER --}}
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

                    {{-- STAGIONI --}}
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

                    {{-- EPISODI --}}
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

                    {{-- CAST & CREW --}}
                    @if($castItems->count())
                        <div class="mt-12">
                            <div class="mb-5 text-[clamp(22px,2.8vmin,36px)] font-black">
                                Cast &amp; Crew
                            </div>

                            <div class="iptv-panel-scroll overflow-x-auto pb-3">
                                <div class="flex gap-6">
                                    @foreach($castItems as $person)
                                        @php
                                            $personName = is_array($person) ? ($person['name'] ?? 'Cast') : $person;
                                            $personPhoto = is_array($person) ? ($person['photo'] ?? $person['image'] ?? null) : null;
                                            $initials = collect(explode(' ', $personName))
                                                ->filter()
                                                ->take(2)
                                                ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                                                ->implode('');
                                        @endphp

                                        <div class="min-w-[150px] text-center">
                                            <div class="mx-auto flex h-[150px] w-[150px] items-center justify-center overflow-hidden rounded-full border-2 border-white/25 bg-black/45 shadow-2xl">
                                                @if($personPhoto)
                                                    <img src="{{ $personPhoto }}"
                                                         alt="{{ $personName }}"
                                                         class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-4xl font-black text-white/85">{{ $initials }}</span>
                                                @endif
                                            </div>

                                            <div class="mt-3 text-xl font-medium">
                                                {{ $personName }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

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

        if (clean.endsWith('.m3u8')) return 'hls';
        if (clean.endsWith('.ts') || clean.includes('/live/') || clean.includes('/series/')) return 'mpegts';
        if (clean.endsWith('.mp4') || clean.endsWith('.m4v') || clean.endsWith('.webm') || clean.endsWith('.mkv')) return 'native';

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
            formatLabel.textContent = 'Nativo';
        }

        video.src = url;
        video.play().catch(() => {});
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
        episodesScroll: 'iptv-scroll-episodes:' + selectedId
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
@endpush