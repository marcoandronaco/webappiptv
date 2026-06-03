@php
    $detailItem = $selectedSeries ?: $selectedChannel;
    $isDetail = (bool) $detailItem;

    $title = $detailItem?->name ?? '';
    preg_match('/\((\d{4})\)/', $title, $yearMatch);
    $year = $yearMatch[1] ?? null;

    $background = $detailItem?->logo;
@endphp

<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">

    @if(!$isDetail)
        {{-- GRIGLIA FILM / SERIE COME PRIMA FOTO --}}
        <div class="grid h-full grid-cols-[clamp(280px,23vw,430px)_1fr] gap-[clamp(10px,1.5vmin,22px)] p-[clamp(10px,1.6vmin,22px)]">

            {{-- CATEGORIE --}}
            <aside class="min-h-0 rounded-[28px] border border-white/10 bg-white/[0.045] p-3 overflow-hidden">

                <div class="mb-3 grid grid-cols-[1fr_auto] gap-2">
                    <a href="{{ url('/') }}"
                       class="rounded-2xl bg-violet-700 px-4 py-3 text-center font-black hover:bg-violet-600">
                        ← Home
                    </a>

                    <a href="{{ route('customer.playlists.index') }}"
                       class="rounded-2xl bg-white/10 px-4 py-3 text-center font-black hover:bg-white/15">
                        Liste
                    </a>
                </div>

                <form method="GET" action="{{ route('customer.channels.index') }}" class="mb-3">
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

                <div id="categoriesScroll" class="h-[calc(100%-126px)] overflow-y-auto space-y-2 pr-1">

                    <a href="{{ route('customer.channels.index', ['tipo' => $type]) }}"
                       class="flex items-center justify-between rounded-2xl px-5 py-4 font-black
                       {{ !$category ? 'bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                        <span>Aggiunti di recente</span>
                        <span>{{ $totalChannels }}</span>
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('customer.channels.index', [
                            'tipo' => $type,
                            'category' => $cat->group_title,
                            'category_search' => $categorySearch,
                        ]) }}"
                           class="flex items-center justify-between rounded-2xl px-5 py-4 font-black
                           {{ $category === $cat->group_title ? 'bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400' : 'bg-white/[0.07] hover:bg-white/[0.12]' }}">
                            <span class="truncate">◇ {{ $cat->group_title }} ◇</span>
                            <span>{{ $cat->total }}</span>
                        </a>
                    @endforeach
                </div>
            </aside>

            {{-- GRIGLIA POSTER --}}
            <main class="min-h-0 overflow-hidden">
                <div class="mb-4 grid grid-cols-[1fr_auto] gap-4">
                    <form method="GET" action="{{ route('customer.channels.index') }}">
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

                <div id="vodGridScroll" class="h-[calc(100%-76px)] overflow-y-auto pr-2">
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
                               class="group overflow-hidden rounded-2xl bg-white/[0.06] transition hover:scale-[1.035] hover:bg-white/[0.10]">

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

                    <div class="mt-5">
                        {{ $channels->links() }}
                    </div>
                </div>
            </main>
        </div>
    @else
        {{-- DETTAGLIO FILM / SERIE COME SECONDA FOTO --}}
        <div class="relative h-full w-full overflow-hidden">

            @if($background)
                <div class="absolute inset-0 bg-cover bg-center"
                     style="background-image: url('{{ $background }}')"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-violet-950 to-black"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/45 to-black/10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>

            <div class="relative z-10 h-full w-full p-[clamp(26px,5vmin,70px)]">

                <div class="mb-6 flex items-center gap-3">
                    <a href="{{ route('customer.channels.index', [
                        'tipo' => $type,
                        'category' => $category,
                        'category_search' => $categorySearch,
                        'channel_search' => $channelSearch,
                        'page' => request('page'),
                    ]) }}"
                       class="rounded-2xl bg-black/45 px-6 py-3 text-lg font-black hover:bg-white/20">
                        ← Indietro
                    </a>

                    <a href="{{ url('/') }}"
                       class="rounded-2xl bg-violet-700 px-6 py-3 text-lg font-black hover:bg-violet-600">
                        Home
                    </a>
                </div>

                <div class="grid h-[calc(100%-80px)] grid-cols-[minmax(430px,42vw)_1fr] gap-8">

                    {{-- INFO --}}
                    <section class="flex flex-col justify-center">
                        <div class="text-[clamp(34px,6vmin,82px)] font-black leading-tight drop-shadow-2xl">
                            {{ $title }}
                        </div>

                        <div class="mt-6 flex items-center gap-4 text-[clamp(18px,2.5vmin,30px)] font-black">
                            <span class="text-yellow-400">★ ★ ★ ★ ☆</span>

                            @if($year)
                                <span class="rounded-lg bg-white/90 px-4 py-1 text-black">{{ $year }}</span>
                            @endif

                            <span>{{ $detailItem->group_title ?: ($type === 'film' ? 'Film' : 'Serie TV') }}</span>
                        </div>

                        <p class="mt-7 max-w-3xl text-[clamp(18px,2.4vmin,30px)] leading-relaxed text-white/90 drop-shadow">
                            {{ $type === 'film'
                                ? 'Seleziona Gioca per avviare la riproduzione del film.'
                                : 'Seleziona una stagione e un episodio per avviare la riproduzione della serie.' }}
                        </p>

                        <div class="mt-10 flex max-w-[520px] flex-col gap-4">
                            @if($type === 'film')
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
                            @else
                                @php
                                    $firstEpisode = $episodesBySeason->flatten(1)->first();
                                @endphp

                                @if($firstEpisode)
                                    <a href="{{ route('customer.channels.index', [
                                        'tipo' => 'serie',
                                        'category' => $category,
                                        'category_search' => $categorySearch,
                                        'channel_search' => $channelSearch,
                                        'channel' => $selectedSeries->id,
                                        'episode' => $firstEpisode->id,
                                        'play' => 1,
                                        'page' => request('page'),
                                    ]) }}"
                                       class="flex items-center justify-center gap-4 rounded-2xl bg-black/45 px-8 py-5 text-2xl font-black hover:bg-white/20">
                                        ▶ GIOCA
                                    </a>
                                @endif
                            @endif

                            <button type="button"
                                    class="flex items-center justify-center gap-4 rounded-2xl bg-white/85 px-8 py-5 text-2xl font-black text-black">
                                ♥ PREFERITI
                            </button>
                        </div>
                    </section>

                    {{-- PLAYER SE PLAY --}}
                    <section class="flex items-center justify-center">
                        @if($playableChannel)
                            <div class="w-full overflow-hidden rounded-[34px] border border-white/15 bg-black shadow-2xl">
                                <video id="iptv-video"
                                       class="aspect-video w-full bg-black object-contain"
                                       controls
                                       autoplay
                                       playsinline
                                       preload="auto"></video>

                                <div class="flex items-center justify-between px-5 py-4">
                                    <div class="min-w-0">
                                        <div class="truncate text-xl font-black">
                                            {{ $playableChannel->name }}
                                        </div>
                                        <div class="text-sm text-white/45">
                                            In riproduzione
                                        </div>
                                    </div>

                                    <div id="player-format" class="rounded-xl bg-white/10 px-4 py-2 font-black">
                                        Auto
                                    </div>
                                </div>
                            </div>
                        @elseif($detailItem->logo)
                            <div class="hidden xl:flex aspect-[2/3] max-h-[70vh] overflow-hidden rounded-[34px] border border-white/15 bg-black/40 shadow-2xl">
                                <img src="{{ $detailItem->logo }}"
                                     class="h-full w-full object-cover"
                                     alt="{{ $detailItem->name }}">
                            </div>
                        @endif
                    </section>
                </div>

                {{-- STAGIONI / EPISODI --}}
                @if($type === 'serie' && $selectedSeries)
                    <div class="absolute bottom-[clamp(22px,4vmin,50px)] left-[clamp(26px,5vmin,70px)] right-[clamp(26px,5vmin,70px)]">
                        <div class="mb-4 text-[clamp(22px,3vmin,38px)] font-black">
                            Stagioni
                        </div>

                        @if($seriesImportError)
                            <div class="mb-4 rounded-2xl bg-red-500/20 p-4 text-red-100">
                                Errore import episodi: {{ $seriesImportError }}
                            </div>
                        @endif

                        <div class="flex gap-5 overflow-x-auto pb-3">
                            @forelse($episodesBySeason as $seasonNumber => $episodes)
                                <div class="min-w-[320px] rounded-2xl bg-black/45 p-4 backdrop-blur">
                                    <div class="mb-3 text-xl font-black">
                                        Stagione {{ $seasonNumber }}
                                    </div>

                                    <div class="max-h-[210px] space-y-2 overflow-y-auto pr-1">
                                        @foreach($episodes as $episode)
                                            <a href="{{ route('customer.channels.index', [
                                                'tipo' => 'serie',
                                                'category' => $category,
                                                'category_search' => $categorySearch,
                                                'channel_search' => $channelSearch,
                                                'channel' => $selectedSeries->id,
                                                'episode' => $episode->id,
                                                'play' => 1,
                                                'page' => request('page'),
                                            ]) }}"
                                               class="flex items-center gap-3 rounded-xl px-3 py-3 hover:bg-white/15
                                               {{ optional($playableChannel)->id === $episode->id ? 'bg-violet-700' : 'bg-white/10' }}">
                                                <span class="font-black">
                                                    E{{ str_pad((string) ($episode->episode_number ?: $loop->iteration), 2, '0', STR_PAD_LEFT) }}
                                                </span>

                                                <span class="truncate">
                                                    {{ $episode->name }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl bg-black/45 p-5 text-white/70">
                                    Nessun episodio disponibile.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

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
        formatLabel.textContent = 'HLS';

        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = url;
            video.play().catch(() => {});
            return;
        }

        if (!window.Hls || !Hls.isSupported()) {
            formatLabel.textContent = 'HLS non supportato';
            return;
        }

        const hls = new Hls({
            lowLatencyMode: false,
            backBufferLength: 60,
            maxBufferLength: 30,
            maxMaxBufferLength: 60
        });

        hls.loadSource(url);
        hls.attachMedia(video);

        hls.on(Hls.Events.MANIFEST_PARSED, function () {
            video.play().catch(() => {});
        });
    }

    function playTs(url) {
        formatLabel.textContent = 'MPEG-TS';

        if (!window.mpegts || !mpegts.isSupported()) {
            formatLabel.textContent = 'TS non supportato';
            return;
        }

        const tsPlayer = mpegts.createPlayer({
            type: 'mpegts',
            isLive: false,
            url: url
        }, {
            enableWorker: true,
            enableStashBuffer: true,
            stashInitialSize: 1024 * 1024 * 3,
            lazyLoad: false
        });

        tsPlayer.attachMediaElement(video);
        tsPlayer.load();
        video.play().catch(() => {});
    }

    function playNative(url) {
        formatLabel.textContent = 'Nativo';
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