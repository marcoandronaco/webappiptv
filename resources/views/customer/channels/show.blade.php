@extends('layouts.iptv-screen', [
    'title' => $channel->name
])

@section('content')

<div class="relative h-full w-full overflow-hidden px-[clamp(18px,4vmin,80px)] py-[clamp(14px,3vmin,46px)]">

    <div class="h-full flex flex-col">

        {{-- HEADER --}}
        <header class="shrink-0 flex items-center justify-between mb-[clamp(14px,3vmin,34px)]">
            <div class="min-w-0">
                <h1 class="text-[clamp(24px,5.5vmin,56px)] font-extrabold tracking-wide uppercase leading-none truncate">
                    {{ $channel->name }}
                </h1>

                <p class="mt-[clamp(4px,1vmin,10px)] text-[clamp(11px,2vmin,18px)] text-slate-400 truncate">
                    {{ $channel->group_title ?: 'Contenuto IPTV' }}
                </p>
            </div>

            <div class="flex items-center gap-[clamp(8px,2vmin,18px)] shrink-0">
                <a href="{{ route('customer.channels.index', ['tipo' => $channel->type]) }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)]
                   bg-white/10 hover:bg-white/15 border border-white/10 flex items-center justify-center
                   text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                    Indietro
                </a>

                <a href="{{ url('/') }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)]
                   bg-gradient-to-r from-violet-500 to-pink-500 hover:brightness-110 border border-white/10 flex items-center justify-center
                   text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                    Home
                </a>
            </div>
        </header>

        {{-- PLAYER --}}
        <main class="flex-1 min-h-0 grid grid-cols-[1fr_clamp(210px,30vmin,360px)] gap-[clamp(14px,3vmin,34px)]">

            <section class="min-h-0 rounded-[clamp(18px,3vmin,34px)] overflow-hidden bg-black border border-white/10 shadow-2xl relative">
                <video id="iptvPlayer"
                       class="w-full h-full bg-black"
                       controls
                       autoplay
                       playsinline
                       preload="auto">
                </video>

                <div id="playerMessage"
                     class="absolute inset-0 hidden items-center justify-center bg-black/80 text-center p-8">
                    <div>
                        <div class="text-[clamp(28px,7vmin,70px)] mb-4">⚠️</div>
                        <div class="text-[clamp(16px,3vmin,28px)] font-extrabold">
                            Stream non riproducibile dal browser
                        </div>
                        <div class="mt-3 text-[clamp(11px,2vmin,18px)] text-slate-300">
                            Alcuni flussi IPTV in formato TS non vengono letti da Chrome/Edge.
                            Prova una sorgente M3U8 oppure usa un player compatibile.
                        </div>
                    </div>
                </div>
            </section>

            <aside class="min-h-0 rounded-[clamp(18px,3vmin,34px)] bg-white/[0.055] border border-white/10 p-[clamp(14px,3vmin,28px)] shadow-2xl">
                <div class="aspect-video rounded-[clamp(14px,2.5vmin,26px)] bg-black/30 flex items-center justify-center overflow-hidden mb-[clamp(12px,2.5vmin,24px)]">
                    @if($channel->logo)
                        <img src="{{ $channel->logo }}" alt="{{ $channel->name }}" class="max-w-full max-h-full object-contain p-4">
                    @else
                        <div class="text-[clamp(36px,8vmin,80px)]">▶</div>
                    @endif
                </div>

                <div class="space-y-[clamp(8px,1.5vmin,14px)]">
                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Nome</div>
                        <div class="text-[clamp(13px,2.4vmin,22px)] font-extrabold line-clamp-2">
                            {{ $channel->name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Categoria</div>
                        <div class="text-[clamp(12px,2.2vmin,20px)] font-bold">
                            {{ $channel->group_title ?: 'Senza categoria' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Tipo</div>
                        <div class="text-[clamp(12px,2.2vmin,20px)] font-bold uppercase">
                            {{ $channel->type }}
                        </div>
                    </div>
                </div>
            </aside>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<script>
    const video = document.getElementById('iptvPlayer');
    const message = document.getElementById('playerMessage');

    const streamUrl = @json($channel->stream_url);

    function showError() {
        message.classList.remove('hidden');
        message.classList.add('flex');
    }

    if (streamUrl.includes('.m3u8')) {
        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = streamUrl;
            video.play().catch(() => {});
        } else if (window.Hls && Hls.isSupported()) {
            const hls = new Hls({
                maxBufferLength: 30,
                liveSyncDurationCount: 3
            });

            hls.loadSource(streamUrl);
            hls.attachMedia(video);

            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                video.play().catch(() => {});
            });

            hls.on(Hls.Events.ERROR, function () {
                showError();
            });
        } else {
            showError();
        }
    } else {
        video.src = streamUrl;
        video.play().catch(() => {});
    }

    video.addEventListener('error', showError);
</script>

@endsection