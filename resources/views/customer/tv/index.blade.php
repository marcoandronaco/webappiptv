@extends('layouts.iptv-screen', ['title' => 'TV Player'])

@section('content')
<div class="min-h-screen bg-[#060b1a] text-white overflow-hidden">
    <div class="h-screen p-4 md:p-5">
        <div class="grid h-full grid-cols-12 gap-4">

            {{-- CATEGORIE --}}
            <div class="col-span-12 lg:col-span-2 rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-md overflow-hidden">
                <div class="px-4 py-4 border-b border-white/10 text-xl font-bold">
                    Categorie
                </div>

                <div class="h-[calc(100%-72px)] overflow-y-auto p-3 space-y-2">
                    @foreach($categories as $category)
                        <a href="{{ route('customer.tv-player', [
                            'type' => $type,
                            'category' => $category,
                            'subcategory' => null,
                        ]) }}"
                           class="flex items-center justify-between rounded-2xl px-4 py-3 transition
                           {{ $selectedCategory === $category ? 'bg-gradient-to-r from-fuchsia-500 to-orange-400 text-white' : 'bg-white/5 hover:bg-white/10 text-white/85' }}">
                            <span class="font-semibold truncate">{{ $category }}</span>
                            <span class="text-sm opacity-70">›</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- SOTTOCATEGORIE --}}
            <div class="col-span-12 lg:col-span-2 rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-md overflow-hidden">
                <div class="px-4 py-4 border-b border-white/10 text-xl font-bold">
                    Sottocategorie
                </div>

                <div class="h-[calc(100%-72px)] overflow-y-auto p-3 space-y-2">
                    @forelse($subcategories as $subcategory)
                        <a href="{{ route('customer.tv-player', [
                            'type' => $type,
                            'category' => $selectedCategory,
                            'subcategory' => $subcategory,
                        ]) }}"
                           class="flex items-center justify-between rounded-2xl px-4 py-3 transition
                           {{ $selectedSubcategory === $subcategory ? 'bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white' : 'bg-white/5 hover:bg-white/10 text-white/85' }}">
                            <span class="font-semibold truncate">{{ $subcategory }}</span>
                            <span class="text-sm opacity-70">›</span>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/5 px-4 py-3 text-white/50">
                            Nessuna sottocategoria
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- LISTA CANALI --}}
            <div class="col-span-12 lg:col-span-3 rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-md overflow-hidden">
                <div class="px-4 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="text-xl font-bold">Canali</div>
                    <div class="text-sm text-white/50">{{ $channels->count() }} elementi</div>
                </div>

                <div class="h-[calc(100%-72px)] overflow-y-auto p-3 space-y-2">
                    @forelse($channels as $channel)
                        <a href="{{ route('customer.tv-player', [
                            'type' => $type,
                            'category' => $selectedCategory,
                            'subcategory' => $selectedSubcategory,
                            'channel' => $channel->id,
                        ]) }}"
                           class="flex items-center gap-3 rounded-2xl px-3 py-3 transition border
                           {{ optional($selectedChannel)->id === $channel->id
                               ? 'bg-gradient-to-r from-fuchsia-500 to-orange-400 border-transparent'
                               : 'bg-white/5 hover:bg-white/10 border-white/5' }}">

                            <div class="w-10 text-center text-lg font-bold text-white/90">
                                {{ $channel->channel_number ?? '-' }}
                            </div>

                            <div class="h-10 w-10 rounded-lg bg-black/20 flex items-center justify-center overflow-hidden shrink-0">
                                @if($channel->logo)
                                    <img src="{{ $channel->logo }}" alt="{{ $channel->name }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <span class="text-xs text-white/40">LOGO</span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="truncate font-semibold text-lg">{{ $channel->name }}</div>
                                <div class="truncate text-xs text-white/50">
                                    {{ $channel->subcategory_name ?: $channel->group_title ?: 'Canale TV' }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/5 px-4 py-3 text-white/50">
                            Nessun canale trovato
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- PLAYER + EPG --}}
            <div class="col-span-12 lg:col-span-5 space-y-4">
                <div class="rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-md p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4">
                            @if($selectedChannel?->logo)
                                <div class="h-16 w-16 rounded-xl bg-black/20 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $selectedChannel->logo }}" alt="{{ $selectedChannel->name }}" class="max-h-full max-w-full object-contain">
                                </div>
                            @endif

                            <div>
                                <div class="text-sm uppercase tracking-[0.25em] text-white/45">
                                    {{ strtoupper($selectedChannel?->subcategory_name ?: $selectedChannel?->group_title ?: 'LIVE TV') }}
                                </div>
                                <div class="text-3xl font-black">
                                    {{ $selectedChannel?->channel_number ? $selectedChannel->channel_number.' - ' : '' }}{{ $selectedChannel?->name }}
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-white/50">{{ now()->format('d/m/Y') }}</div>
                            <div class="text-4xl font-black">{{ now()->format('H:i') }}</div>
                        </div>
                    </div>

                    <div class="rounded-[20px] overflow-hidden border border-white/10 bg-black">
                        @if($selectedChannel)
                            <video id="iptv-video"
                                   class="w-full aspect-video bg-black object-contain"
                                   controls
                                   autoplay
                                   playsinline></video>
                        @else
                            <div class="aspect-video flex items-center justify-center text-white/40">
                                Nessun canale selezionato
                            </div>
                        @endif
                    </div>

                    @if($selectedChannel)
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <div class="text-sm text-white/50">Stream</div>
                                <div class="font-semibold">{{ $selectedChannel->name }}</div>
                            </div>

                            <div class="text-right">
                                <div class="text-sm text-white/50">Formato</div>
                                <div id="player-format" class="font-semibold">Auto</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- EPG --}}
                <div class="rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-md overflow-hidden">
                    <div class="px-4 py-4 border-b border-white/10 flex items-center justify-between">
                        <div class="text-xl font-bold">EPG</div>
                        <div class="text-sm text-white/50">{{ $selectedChannel?->name }}</div>
                    </div>

                    <div class="max-h-[320px] overflow-y-auto p-3 space-y-2">
                        @php
                            $programmes = $selectedChannel?->epgProgrammes ?? collect();
                        @endphp

                        @forelse($programmes as $programme)
                            @php
                                $isCurrent = now()->between($programme->start_at, $programme->end_at);
                            @endphp

                            <div class="rounded-2xl px-4 py-3 flex items-center justify-between gap-4
                                {{ $isCurrent ? 'bg-gradient-to-r from-fuchsia-500/80 to-orange-400/80' : 'bg-white/5' }}">
                                <div class="min-w-0">
                                    <div class="font-semibold truncate">{{ $programme->title }}</div>
                                    <div class="text-xs text-white/60 truncate">
                                        {{ $programme->description ?: 'Nessuna descrizione' }}
                                    </div>
                                </div>

                                <div class="shrink-0 text-right">
                                    <div class="font-semibold">
                                        {{ $programme->start_at->format('H:i') }} - {{ $programme->end_at->format('H:i') }}
                                    </div>
                                    @if($isCurrent)
                                        <div class="text-xs font-bold">IN ONDA</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-white/5 px-4 py-3 text-white/50">
                                Nessun EPG disponibile
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/mpegts.js@latest"></script>

@if($selectedChannel)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('iptv-video');
    const formatLabel = document.getElementById('player-format');
    const streamUrl = @json($selectedChannel->stream_url);

    let hls = null;
    let tsPlayer = null;

    function detectFormat(url) {
        const clean = url.split('?')[0].toLowerCase();

        if (clean.endsWith('.m3u8')) return 'hls';
        if (clean.endsWith('.ts') || clean.includes('/live/')) return 'mpegts';
        if (clean.endsWith('.mp4') || clean.endsWith('.m4v') || clean.endsWith('.webm')) return 'native';

        return 'auto';
    }

    function destroyPlayers() {
        if (hls) {
            hls.destroy();
            hls = null;
        }

        if (tsPlayer) {
            try {
                tsPlayer.unload();
                tsPlayer.detachMediaElement();
                tsPlayer.destroy();
            } catch (e) {}

            tsPlayer = null;
        }
    }

    function playHls(url) {
        formatLabel.textContent = 'HLS';

        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = url;
            video.play().catch(() => {});
            return;
        }

        if (!window.Hls || !Hls.isSupported()) {
            return;
        }

        hls = new Hls({
            lowLatencyMode: false,
            backBufferLength: 60,
            maxBufferLength: 30,
            maxMaxBufferLength: 60,
            liveSyncDurationCount: 4,
            liveMaxLatencyDurationCount: 8
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
            return;
        }

        tsPlayer = mpegts.createPlayer({
            type: 'mpegts',
            isLive: true,
            url: url
        }, {
            enableWorker: true,
            enableStashBuffer: true,
            lazyLoad: false,
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
    destroyPlayers();

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
@endpush