@extends('layouts.iptv-screen', [
    'title' => $type === 'film' ? 'Film' : ($type === 'serie' ? 'Serie' : 'TV dal vivo')
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

        {{-- HEADER --}}
        <header class="shrink-0 flex items-center justify-between mb-[clamp(14px,3vmin,34px)]">
            <div>
                <h1 class="text-[clamp(28px,6.5vmin,62px)] font-extrabold tracking-wide uppercase leading-none">
                    @if($type === 'film')
                        Film
                    @elseif($type === 'serie')
                        Serie
                    @else
                        TV dal vivo
                    @endif
                </h1>

                <p class="mt-[clamp(4px,1vmin,10px)] text-[clamp(11px,2vmin,18px)] text-slate-400">
                    Seleziona un contenuto dalla playlist importata.
                </p>
            </div>

            <div class="flex items-center gap-[clamp(8px,2vmin,18px)]">
                <a href="{{ url('/') }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)]
                   bg-white/10 hover:bg-white/15 border border-white/10 flex items-center justify-center
                   text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                    Home
                </a>

                <a href="{{ route('customer.playlists.index') }}"
                   class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)]
                   bg-gradient-to-r from-violet-500 to-pink-500 hover:brightness-110 border border-white/10 flex items-center justify-center
                   text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                    Playlist
                </a>
            </div>
        </header>

        {{-- TABS --}}
        <div class="shrink-0 flex items-center gap-[clamp(8px,2vmin,18px)] mb-[clamp(10px,2vmin,22px)]">
            <a href="{{ route('customer.channels.index', ['tipo' => 'live']) }}"
               class="h-[clamp(38px,7vmin,58px)] px-[clamp(18px,4vmin,38px)] rounded-[clamp(12px,2vmin,20px)]
               {{ $type === 'live' ? 'bg-gradient-to-r from-cyan-400 to-blue-600 text-white' : 'bg-white/10 text-slate-200' }}
               flex items-center justify-center text-[clamp(12px,2.3vmin,20px)] font-extrabold">
                TV
            </a>

            <a href="{{ route('customer.channels.index', ['tipo' => 'film']) }}"
               class="h-[clamp(38px,7vmin,58px)] px-[clamp(18px,4vmin,38px)] rounded-[clamp(12px,2vmin,20px)]
               {{ $type === 'film' ? 'bg-gradient-to-r from-pink-500 to-orange-400 text-white' : 'bg-white/10 text-slate-200' }}
               flex items-center justify-center text-[clamp(12px,2.3vmin,20px)] font-extrabold">
                Film
            </a>

            <a href="{{ route('customer.channels.index', ['tipo' => 'serie']) }}"
               class="h-[clamp(38px,7vmin,58px)] px-[clamp(18px,4vmin,38px)] rounded-[clamp(12px,2vmin,20px)]
               {{ $type === 'serie' ? 'bg-gradient-to-r from-purple-500 to-sky-400 text-white' : 'bg-white/10 text-slate-200' }}
               flex items-center justify-center text-[clamp(12px,2.3vmin,20px)] font-extrabold">
                Serie
            </a>

            <form method="GET" action="{{ route('customer.channels.index') }}" class="ml-auto flex items-center gap-[clamp(8px,1.5vmin,14px)]">
                <input type="hidden" name="tipo" value="{{ $type }}">

                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cerca..."
                       class="h-[clamp(38px,7vmin,58px)] w-[clamp(180px,32vmin,340px)]
                       rounded-[clamp(12px,2vmin,20px)] bg-white/10 border border-white/10
                       px-[clamp(12px,2.5vmin,24px)] text-[clamp(12px,2vmin,18px)]
                       placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-fuchsia-400/40">

                <button type="submit"
                        class="h-[clamp(38px,7vmin,58px)] px-[clamp(16px,3vmin,28px)]
                        rounded-[clamp(12px,2vmin,20px)] bg-white/10 hover:bg-white/15
                        text-[clamp(12px,2vmin,18px)] font-bold">
                    Cerca
                </button>
            </form>
        </div>

        {{-- CONTENUTO --}}
        <main class="flex-1 min-h-0 grid grid-cols-[clamp(180px,25vmin,290px)_1fr] gap-[clamp(12px,3vmin,34px)]">

            {{-- GRUPPI --}}
            <aside class="min-h-0 rounded-[clamp(16px,3vmin,30px)] bg-white/[0.055] border border-white/10 p-[clamp(12px,2.5vmin,24px)] overflow-y-auto iptv-scroll">
                <div class="text-[clamp(13px,2.5vmin,22px)] font-extrabold mb-[clamp(10px,2vmin,18px)]">
                    Categorie
                </div>

                <div class="space-y-[clamp(6px,1.3vmin,12px)]">
                    <a href="{{ route('customer.channels.index', ['tipo' => $type]) }}"
                       class="block rounded-[clamp(10px,1.6vmin,16px)] px-[clamp(10px,2vmin,16px)] py-[clamp(8px,1.5vmin,12px)]
                       {{ !$group ? 'bg-white/20 text-white' : 'bg-white/5 text-slate-300' }}
                       text-[clamp(10px,1.8vmin,16px)] font-bold">
                        Tutte
                    </a>

                    @foreach($groups as $item)
                        <a href="{{ route('customer.channels.index', ['tipo' => $type, 'group' => $item->group_title]) }}"
                           class="block rounded-[clamp(10px,1.6vmin,16px)] px-[clamp(10px,2vmin,16px)] py-[clamp(8px,1.5vmin,12px)]
                           {{ $group === $item->group_title ? 'bg-white/20 text-white' : 'bg-white/5 text-slate-300' }}
                           text-[clamp(10px,1.8vmin,16px)] font-bold truncate">
                            {{ $item->group_title }}
                            <span class="text-white/50">({{ $item->total }})</span>
                        </a>
                    @endforeach
                </div>
            </aside>

            {{-- GRID CANALI --}}
            <section class="min-h-0 overflow-y-auto iptv-scroll pr-[clamp(4px,1vmin,10px)]">

                @if($channels->count())
                    <div class="grid grid-cols-5 gap-[clamp(10px,2vmin,22px)]">
                        @foreach($channels as $channel)
                            <a href="{{ route('customer.channels.show', $channel) }}"
                               class="group rounded-[clamp(14px,2.5vmin,26px)] bg-white/[0.07] border border-white/10
                               hover:bg-white/[0.12] hover:-translate-y-1 transition overflow-hidden shadow-xl">

                                <div class="aspect-video bg-black/30 flex items-center justify-center">
                                    @if($channel->logo)
                                        <img src="{{ $channel->logo }}"
                                             alt="{{ $channel->name }}"
                                             class="max-w-full max-h-full object-contain p-[clamp(8px,2vmin,18px)]"
                                             loading="lazy"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                        <div class="hidden w-full h-full items-center justify-center text-[clamp(20px,5vmin,42px)] font-extrabold text-white/60">
                                            ▶
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-[clamp(20px,5vmin,42px)] font-extrabold text-white/60">
                                            ▶
                                        </div>
                                    @endif
                                </div>

                                <div class="p-[clamp(8px,1.8vmin,16px)]">
                                    <div class="text-[clamp(10px,1.8vmin,16px)] font-extrabold truncate">
                                        {{ $channel->name }}
                                    </div>

                                    <div class="mt-[clamp(3px,0.7vmin,6px)] text-[clamp(8px,1.5vmin,13px)] text-slate-400 truncate">
                                        {{ $channel->group_title ?: 'Senza categoria' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-center gap-[clamp(12px,3vmin,26px)] mt-[clamp(14px,3vmin,30px)]">
                        @if($channels->previousPageUrl())
                            <a href="{{ $channels->previousPageUrl() }}"
                               class="h-[clamp(38px,7vmin,58px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)] bg-white/10 font-bold">
                                Indietro
                            </a>
                        @endif

                        @if($channels->nextPageUrl())
                            <a href="{{ $channels->nextPageUrl() }}"
                               class="h-[clamp(38px,7vmin,58px)] px-[clamp(18px,4vmin,36px)] rounded-[clamp(12px,2vmin,20px)] bg-white/10 font-bold">
                                Avanti
                            </a>
                        @endif
                    </div>
                @else
                    <div class="h-full rounded-[clamp(18px,3vmin,34px)] bg-white/[0.055] border border-white/10 flex flex-col items-center justify-center text-center p-[clamp(20px,5vmin,60px)]">
                        <div class="text-[clamp(40px,10vmin,90px)] mb-[clamp(10px,2vmin,20px)]">
                            📺
                        </div>

                        <h2 class="text-[clamp(24px,5vmin,44px)] font-extrabold">
                            Nessun contenuto trovato
                        </h2>

                        <p class="mt-[clamp(6px,1.5vmin,14px)] text-[clamp(12px,2vmin,18px)] text-slate-400">
                            Aggiungi o importa una playlist dalla sezione Lista.
                        </p>

                        <a href="{{ route('customer.playlists.index') }}"
                           class="mt-[clamp(16px,4vmin,36px)] h-[clamp(42px,8vmin,66px)] px-[clamp(22px,5vmin,44px)]
                           rounded-[clamp(12px,2vmin,20px)] bg-gradient-to-r from-violet-500 to-pink-500 font-extrabold">
                            Vai alle playlist
                        </a>
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>

@endsection