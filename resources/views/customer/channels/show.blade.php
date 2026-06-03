@extends('layouts.iptv-screen', ['title' => $channel->name])

@section('content')
<div class="relative h-full w-full overflow-hidden bg-black text-white select-none">

    <video
        id="iptv-video"
        class="absolute inset-0 h-full w-full bg-black object-contain"
        autoplay
        playsinline
        controls
        preload="auto">
    </video>

    <div id="player-overlay"
         class="absolute inset-0 z-20 bg-gradient-to-b from-black/70 via-slate-950/30 to-black/85 transition-opacity duration-300">

        <div class="absolute left-0 right-0 top-0 flex items-start justify-between px-[clamp(28px,5vmin,80px)] pt-[clamp(22px,4vmin,54px)]">
            <div class="flex items-center gap-[clamp(18px,3vmin,34px)]">
                <a href="{{ route('customer.channels.index', ['tipo' => $channel->type]) }}"
                   class="flex h-[clamp(46px,7vmin,82px)] w-[clamp(46px,7vmin,82px)] items-center justify-center rounded-full bg-violet-700 text-[clamp(28px,4vmin,48px)] font-black shadow-2xl hover:bg-violet-600">
                    ←
                </a>

                <div>
                    <div class="text-[clamp(11px,1.5vmin,16px)] font-bold uppercase tracking-[0.35em] text-white/45">
                        {{ strtoupper($channel->group_title ?: 'Canale IPTV') }}
                    </div>

                    <div class="mt-1 text-[clamp(22px,4vmin,44px)] font-black tracking-[0.18em] text-white drop-shadow-xl">
                        {{ strtoupper($channel->name) }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-[clamp(28px,5vmin,70px)]">
                <button type="button"
                        id="settings-btn"
                        class="flex h-[clamp(46px,7vmin,82px)] w-[clamp(46px,7vmin,82px)] items-center justify-center rounded-full bg-violet-700 text-[clamp(26px,4vmin,48px)] shadow-2xl hover:bg-violet-600">
                    ⚙
                </button>
            </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center">
            <div class="flex items-center gap-[clamp(70px,12vmin,170px)] text-white drop-shadow-2xl">
                <button type="button"
                        id="rewind-btn"
                        class="text-[clamp(58px,10vmin,130px)] opacity-95 hover:scale-110 transition">
                    ◀
                </button>

                <button type="button"
                        id="play-btn"
                        class="text-[clamp(74px,13vmin,160px)] opacity-95 hover:scale-110 transition">
                    ❚❚
                </button>

                <button type="button"
                        id="forward-btn"
                        class="text-[clamp(58px,10vmin,130px)] opacity-95 hover:scale-110 transition">
                    ▶
                </button>
            </div>
        </div>

        <div class="absolute right-[clamp(14px,3vmin,34px)] top-1/2 flex -translate-y-1/2 flex-col items-center gap-5">
            <button type="button"
                    id="mute-btn"
                    class="text-[clamp(30px,5vmin,60px)]">
                🔊
            </button>

            <div class="relative h-[clamp(180px,34vmin,340px)] w-[clamp(12px,2vmin,22px)] rounded-full bg-white/35 overflow-hidden">
                <div id="volume-level"
                     class="absolute bottom-0 left-0 right-0 h-[70%] rounded-full bg-white"></div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 px-[clamp(28px,5vmin,80px)] pb-[clamp(20px,4vmin,48px)]">

            <div class="grid grid-cols-[auto_1fr_auto] items-end gap-[clamp(18px,3vmin,36px)]">
                <div class="flex h-[clamp(58px,9vmin,100px)] w-[clamp(86px,13vmin,150px)] items-center justify-center rounded-xl bg-black/35">
                    @if($channel->logo)
                        <img src="{{ $channel->logo }}"
                             class="max-h-full max-w-full object-contain"
                             alt="{{ $channel->name }}">
                    @else
                        <div class="text-center">
                            <div class="text-[clamp(18px,3vmin,34px)] font-black text-orange-500">IPTV</div>
                            <div class="text-[clamp(9px,1.3vmin,14px)] tracking-[0.3em] text-white/50">LIVE</div>
                        </div>
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="flex items-center justify-between gap-4">
                        <div class="truncate text-[clamp(20px,3.5vmin,42px)] font-semibold">
                            Ora: {{ $channel->name }}
                        </div>

                        <div id="clock-now" class="shrink-0 text-[clamp(18px,3vmin,34px)] font-semibold">
                            --:--
                        </div>
                    </div>

                    <div class="mt-[clamp(8px,1.5vmin,18px)] h-[clamp(5px,0.9vmin,10px)] overflow-hidden rounded-full bg-white/30">
                        <div class="h-full w-[62%] rounded-full bg-yellow-400"></div>
                    </div>

                    <div class="mt-[clamp(12px,2vmin,24px)] flex items-center justify-between gap-4">
                        <div class="truncate text-[clamp(18px,3vmin,34px)] text-white/90">
                            Dopo: Programma successivo
                        </div>

                        <div id="format-label" class="shrink-0 text-[clamp(18px,3vmin,34px)] font-semibold">
                            -
                        </div>
                    </div>
                </div>

                <div class="hidden md:block min-w-[clamp(120px,18vmin,230px)] text-right text-[clamp(18px,3vmin,34px)] font-semibold">
                    <div>LIVE</div>
                    <div class="mt-[clamp(16px,2vmin,30px)] text-white/85">
                        {{ strtoupper($channel->type) }}
                    </div>
                </div>
            </div>

            <div class="mt-[clamp(22px,4vmin,46px)] flex items-center justify-center gap-[clamp(40px,8vmin,120px)] text-[clamp(18px,3vmin,34px)] font-semibold">
                <button type="button"
                        onclick="window.location.href='{{ route('customer.channels.index', ['tipo' => $channel->type]) }}'"
                        class="flex items-center gap-4 hover:text-orange-300">
                    <span class="text-[clamp(28px,5vmin,58px)]">▰</span>
                    Elenco canali
                </button>

                <button type="button"
                        id="aspect-btn"
                        class="flex items-center gap-4 hover:text-orange-300">
                    <span class="text-[clamp(28px,5vmin,58px)]">□</span>
                    Proporzioni
                </button>

                <button type="button"
                        id="fullscreen-btn"
                        class="flex items-center gap-4 hover:text-orange-300">
                    <span class="text-[clamp(28px,5vmin,58px)]">▦</span>
                    Schermo intero
                </button>
            </div>
        </div>

        <div id="player-loader"
             class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-black/70">
            <div class="h-16 w-16 animate-spin rounded-full border-4 border-white/20 border-t-white"></div>
            <div id="loader-text" class="mt-6 text-[clamp(20px,3vmin,38px)] font-black">
                Caricamento stream...
            </div>
        </div>

        <div id="player-error"
             class="hidden absolute inset-0 z-50 flex flex-col items-center justify-center bg-black/90 px-10 text-center">
            <div class="text-[clamp(42px,8vmin,90px)]">⚠️</div>

            <div class="mt-4 text-[clamp(26px,5vmin,60px)] font-black">
                Stream non compatibile
            </div>

            <div id="player-error-message" class="mt-4 max-w-3xl text-[clamp(15px,2vmin,24px)] text-white/60">
                Questo flusso non può essere riprodotto direttamente dal browser.
            </div>

            <div class="mt-8 flex gap-4">
                <button type="button"
                        onclick="window.location.reload()"
                        class="rounded-2xl bg-orange-500 px-7 py-4 text-sm font-black text-white hover:bg-orange-400">
                    Riprova
                </button>

                <a href="{{ route('customer.channels.index', ['tipo' => $channel->type]) }}"
                   class="rounded-2xl border border-white/15 bg-white/10 px-7 py-4 text-sm font-black text-white hover:bg-white/20">
                    Torna ai canali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/mpegts.js@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('iptv-video');
        const overlay = document.getElementById('player-overlay');
        const loader = document.getElementById('player-loader');
        const loaderText = document.getElementById('loader-text');
        const errorBox = document.getElementById('player-error');
        const errorMessage = document.getElementById('player-error-message');

        const playBtn = document.getElementById('play-btn');
        const muteBtn = document.getElementById('mute-btn');
        const volumeLevel = document.getElementById('volume-level');
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        const aspectBtn = document.getElementById('aspect-btn');
        const clockNow = document.getElementById('clock-now');
        const formatLabel = document.getElementById('format-label');

        const streamUrl = @json($channel->stream_url);

        let hls = null;
        let tsPlayer = null;
        let overlayTimer = null;
        let aspectMode = 0;

        function showLoader(text = 'Caricamento stream...') {
            loader.classList.remove('hidden');
            loaderText.textContent = text;
            errorBox.classList.add('hidden');
        }

        function hideLoader() {
            loader.classList.add('hidden');
        }

        function showError(text = 'Questo flusso non può essere riprodotto direttamente dal browser.') {
            loader.classList.add('hidden');
            errorBox.classList.remove('hidden');
            errorMessage.textContent = text;
        }

        function showOverlay() {
            overlay.classList.remove('opacity-0', 'pointer-events-none');

            clearTimeout(overlayTimer);

            overlayTimer = setTimeout(function () {
                if (!video.paused && errorBox.classList.contains('hidden')) {
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }, 4500);
        }

        function updatePlayIcon() {
            playBtn.textContent = video.paused ? '▶' : '❚❚';
        }

        function updateVolumeIcon() {
            muteBtn.textContent = video.muted || video.volume === 0 ? '🔇' : '🔊';
            volumeLevel.style.height = Math.round((video.muted ? 0 : video.volume) * 100) + '%';
        }

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

            video.removeAttribute('src');
            video.load();
        }

        function playHls(url) {
            formatLabel.textContent = 'HLS';

            if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                video.play().catch(() => showLoader('Premi Play per avviare'));
                return;
            }

            if (!window.Hls || !Hls.isSupported()) {
                showError('HLS non supportato da questo browser.');
                return;
            }

            hls = new Hls({
                lowLatencyMode: false,
                backBufferLength: 60,
                maxBufferLength: 30,
                maxMaxBufferLength: 60,
                liveSyncDurationCount: 4,
                liveMaxLatencyDurationCount: 8,
                manifestLoadingTimeOut: 60000,
                levelLoadingTimeOut: 60000,
                fragLoadingTimeOut: 60000,
                fragLoadingMaxRetry: 8,
                manifestLoadingMaxRetry: 8,
                levelLoadingMaxRetry: 8
            });

            hls.loadSource(url);
            hls.attachMedia(video);

            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                video.play().catch(() => showLoader('Premi Play per avviare'));
            });

            hls.on(Hls.Events.ERROR, function (event, data) {
                if (!data || !data.fatal) return;

                if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                    hls.startLoad();
                    return;
                }

                if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                    hls.recoverMediaError();
                    return;
                }

                showError('Stream HLS non riproducibile.');
            });
        }

        function playMpegTs(url) {
            formatLabel.textContent = 'MPEG-TS';

            if (!window.mpegts || !mpegts.isSupported()) {
                showError('MPEG-TS non supportato da questo browser.');
                return;
            }

            tsPlayer = mpegts.createPlayer({
                type: 'mpegts',
                isLive: true,
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

            video.play().catch(() => showLoader('Premi Play per avviare'));

            tsPlayer.on(mpegts.Events.ERROR, function () {
                showError('Stream TS non compatibile. Se il canale è HEVC/H.265 serve FFmpeg o una sorgente HLS compatibile.');
            });
        }

        function playNative(url) {
            formatLabel.textContent = 'Nativo';
            video.src = url;
            video.play().catch(() => showLoader('Premi Play per avviare'));
        }

        function startPlayer() {
            destroyPlayers();
            showLoader('Caricamento stream...');

            const format = detectFormat(streamUrl);

            if (format === 'hls') {
                playHls(streamUrl);
                return;
            }

            if (format === 'mpegts') {
                playMpegTs(streamUrl);
                return;
            }

            if (format === 'native') {
                playNative(streamUrl);
                return;
            }

            playHls(streamUrl);
        }

        playBtn.addEventListener('click', function () {
            if (video.paused) {
                video.play().catch(() => {});
            } else {
                video.pause();
            }

            updatePlayIcon();
            showOverlay();
        });

        muteBtn.addEventListener('click', function () {
            video.muted = !video.muted;
            updateVolumeIcon();
            showOverlay();
        });

        document.getElementById('rewind-btn').addEventListener('click', showOverlay);
        document.getElementById('forward-btn').addEventListener('click', showOverlay);

        fullscreenBtn.addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }

            showOverlay();
        });

        aspectBtn.addEventListener('click', function () {
            aspectMode = (aspectMode + 1) % 3;

            video.classList.remove('object-contain', 'object-cover', 'object-fill');

            if (aspectMode === 0) video.classList.add('object-contain');
            if (aspectMode === 1) video.classList.add('object-cover');
            if (aspectMode === 2) video.classList.add('object-fill');

            showOverlay();
        });

        video.addEventListener('playing', function () {
            hideLoader();
            updatePlayIcon();
            showOverlay();
        });

        video.addEventListener('pause', function () {
            updatePlayIcon();
            showOverlay();
        });

        video.addEventListener('waiting', function () {
            showLoader('Buffering...');
        });

        video.addEventListener('error', function () {
            showError('Errore video. Il codec potrebbe non essere supportato dal browser.');
        });

        function updateClock() {
            clockNow.textContent = new Date().toLocaleTimeString('it-IT', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        updateClock();
        setInterval(updateClock, 10000);

        video.volume = 0.7;
        updateVolumeIcon();
        updatePlayIcon();

        startPlayer();
    });
</script>
@endpush