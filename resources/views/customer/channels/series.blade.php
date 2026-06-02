@extends('layouts.iptv-screen', [
    'title' => $series->name
])

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

<div class="relative h-full w-full overflow-hidden px-[clamp(18px,4vmin,80px)] py-[clamp(14px,3vmin,46px)]">

    <div class="h-full flex flex-col">

        <header class="shrink-0 flex items-center justify-between mb-[clamp(14px,3vmin,34px)]">
            <div class="min-w-0">
                <h1 class="text-[clamp(24px,5.5vmin,56px)] font-extrabold tracking-wide uppercase leading-none truncate">
                    {{ $series->name }}
                </h1>

                <p class="mt-[clamp(4px,1vmin,10px)] text-[clamp(11px,2vmin,18px)] text-slate-400 truncate">
                    {{ $series->group_title ?: 'Serie' }}
                </p>
            </div>

            <div class="flex items-center gap-[clamp(8px,2vmin,18px)] shrink-0">
                <a href="{{ route('customer.channels.index', ['tipo' => 'serie']) }}"
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

        @if($importedCount > 0)
            <div class="mb-[clamp(8px,2vmin,18px)] rounded-[clamp(12px,2vmin,20px)] border border-emerald-400/30 bg-emerald-500/10 px-[clamp(12px,3vmin,22px)] py-[clamp(8px,2vmin,15px)] text-[clamp(11px,2vmin,17px)] text-emerald-200 shrink-0">
                Episodi importati: {{ $importedCount }}
            </div>
        @endif

        @if($importError)
            <div class="mb-[clamp(8px,2vmin,18px)] rounded-[clamp(12px,2vmin,20px)] border border-red-400/30 bg-red-500/10 px-[clamp(12px,3vmin,22px)] py-[clamp(8px,2vmin,15px)] text-[clamp(11px,2vmin,17px)] text-red-200 shrink-0">
                {{ $importError }}
            </div>
        @endif

        <main class="flex-1 min-h-0 grid grid-cols-[clamp(210px,30vmin,360px)_1fr] gap-[clamp(14px,3vmin,34px)]">

            <aside class="min-h-0 rounded-[clamp(18px,3vmin,34px)] bg-white/[0.055] border border-white/10 p-[clamp(14px,3vmin,28px)] shadow-2xl">
                <div class="aspect-[2/3] rounded-[clamp(14px,2.5vmin,26px)] bg-black/30 flex items-center justify-center overflow-hidden mb-[clamp(12px,2.5vmin,24px)]">
                    @if($series->logo)
                        <img src="{{ $series->logo }}" alt="{{ $series->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-[clamp(36px,8vmin,80px)]">▶</div>
                    @endif
                </div>

                <div class="space-y-[clamp(8px,1.5vmin,14px)]">
                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Serie</div>
                        <div class="text-[clamp(13px,2.4vmin,22px)] font-extrabold line-clamp-2">
                            {{ $series->name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Categoria</div>
                        <div class="text-[clamp(12px,2.2vmin,20px)] font-bold">
                            {{ $series->group_title ?: 'Serie' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[clamp(10px,1.8vmin,15px)] text-slate-400">Episodi</div>
                        <div class="text-[clamp(12px,2.2vmin,20px)] font-bold">
                            {{ $episodes->flatten()->count() }}
                        </div>
                    </div>
                </div>
            </aside>

            <section class="min-h-0 overflow-y-auto iptv-scroll pr-[clamp(4px,1vmin,10px)]">
                @if($episodes->count())
                    <div class="space-y-[clamp(16px,3vmin,32px)]">
                        @foreach($episodes as $season => $items)
                            <div>
                                <h2 class="text-[clamp(18px,4vmin,34px)] font-extrabold mb-[clamp(8px,2vmin,18px)]">
                                    Stagione {{ $season ?: 1 }}
                                </h2>

                                <div class="grid grid-cols-3 gap-[clamp(10px,2vmin,22px)]">
                                    @foreach($items as $episode)
                                        <a href="{{ route('customer.channels.show', $episode) }}"
                                           class="rounded-[clamp(14px,2.5vmin,26px)] bg-white/[0.07] border border-white/10
                                           hover:bg-white/[0.12] hover:-translate-y-1 transition overflow-hidden shadow-xl">

                                            <div class="aspect-video bg-black/30 flex items-center justify-center">
                                                @if($episode->logo)
                                                    <img src="{{ $episode->logo }}"
                                                         alt="{{ $episode->name }}"
                                                         class="max-w-full max-h-full object-contain p-[clamp(8px,2vmin,18px)]"
                                                         loading="lazy">
                                                @else
                                                    <div class="text-[clamp(20px,5vmin,42px)] font-extrabold text-white/60">
                                                        ▶
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="p-[clamp(8px,1.8vmin,16px)]">
                                                <div class="text-[clamp(10px,1.8vmin,16px)] font-extrabold line-clamp-2">
                                                    {{ $episode->name }}
                                                </div>

                                                <div class="mt-[clamp(3px,0.7vmin,6px)] text-[clamp(8px,1.5vmin,13px)] text-slate-400">
                                                    S{{ str_pad($episode->season_number ?: 1, 2, '0', STR_PAD_LEFT) }}
                                                    E{{ str_pad($episode->episode_number ?: 1, 2, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full rounded-[clamp(18px,3vmin,34px)] bg-white/[0.055] border border-white/10 flex flex-col items-center justify-center text-center p-[clamp(20px,5vmin,60px)]">
                        <div class="text-[clamp(40px,10vmin,90px)] mb-[clamp(10px,2vmin,20px)]">
                            🎬
                        </div>

                        <h2 class="text-[clamp(24px,5vmin,44px)] font-extrabold">
                            Nessun episodio disponibile
                        </h2>

                        <p class="mt-[clamp(6px,1.5vmin,14px)] text-[clamp(12px,2vmin,18px)] text-slate-400">
                            Il server non ha restituito episodi per questa serie.
                        </p>
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>

@endsection