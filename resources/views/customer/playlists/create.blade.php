@extends('layouts.iptv-screen', [
    'title' => $mode === 'edit' ? 'Modifica playlist' : 'Aggiungi playlist'
])

@section('content')

<style>
    .iptv-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.35) transparent;
        -webkit-overflow-scrolling: touch;
    }

    .iptv-touch-scroll {
        height: 100%;
        min-height: 0;
        overflow-y: scroll;
        overflow-x: hidden;
        touch-action: none;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
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

<div class="relative h-full w-full overflow-hidden
    px-[clamp(18px,4vmin,80px)]
    py-[clamp(14px,3vmin,46px)]">

    {{-- DECORAZIONI SFONDO --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[12%] left-[25%] w-[clamp(24px,5vmin,60px)] h-[clamp(24px,5vmin,60px)] rounded-xl bg-indigo-400/10"></div>
        <div class="absolute top-[30%] right-[14%] w-[clamp(28px,6vmin,76px)] h-[clamp(28px,6vmin,76px)] rounded-xl bg-indigo-400/10"></div>
        <div class="absolute bottom-[18%] left-[42%] w-[clamp(22px,5vmin,64px)] h-[clamp(22px,5vmin,64px)] rounded-xl bg-indigo-400/10"></div>
        <div class="absolute bottom-[8%] right-[8%] w-[clamp(20px,4vmin,50px)] h-[clamp(20px,4vmin,50px)] rounded-xl bg-indigo-400/10"></div>
    </div>

    <div class="relative z-10 h-full min-h-0 flex flex-col overflow-hidden">

        {{-- HEADER --}}
        <header class="shrink-0 flex items-center justify-between mb-[clamp(14px,3vmin,34px)]">
            <div>
                <h1 class="text-[clamp(28px,6.5vmin,60px)] font-extrabold tracking-wide uppercase leading-none">
                    {{ $mode === 'edit' ? 'MODIFICA PLAYLIST' : 'AGGIUNGI PLAYLIST' }}
                </h1>

                <p class="mt-[clamp(4px,1vmin,10px)] text-[clamp(11px,2vmin,18px)] text-slate-400">
                    Inserisci una lista URL M3U oppure un accesso API Xtream.
                </p>
            </div>

            <a href="{{ route('customer.playlists.index') }}"
               class="h-[clamp(42px,8vmin,66px)] px-[clamp(18px,4vmin,36px)]
               rounded-[clamp(12px,2vmin,20px)] bg-white/10 hover:bg-white/15
               border border-white/10 flex items-center justify-center
               text-[clamp(13px,2.5vmin,22px)] font-bold transition">
                Indietro
            </a>
        </header>

        {{-- CONTENITORE SCROLLABILE TOUCH --}}
        <main id="playlistFormScroller"
              class="flex-1 min-h-0 iptv-scroll iptv-touch-scroll pr-[clamp(4px,1vmin,10px)] pb-[clamp(40px,10vmin,120px)]">

            {{-- ERRORI --}}
            @if($errors->any())
                <div class="shrink-0 mb-[clamp(10px,2vmin,18px)] rounded-[clamp(12px,2vmin,20px)]
                    border border-red-400/30 bg-red-500/10 px-[clamp(14px,3vmin,24px)]
                    py-[clamp(8px,1.6vmin,14px)] text-[clamp(10px,1.8vmin,16px)] text-red-200">
                    <ul class="list-disc pl-5 leading-snug">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CARD FORM --}}
            <form method="POST"
                  action="{{ $mode === 'edit' ? route('customer.playlists.update', $playlist) : route('customer.playlists.store') }}"
                  class="rounded-[clamp(18px,3vmin,34px)] border border-white/10
                  bg-white/[0.055] backdrop-blur-xl shadow-2xl
                  p-[clamp(18px,4vmin,46px)]
                  flex flex-col justify-between
                  min-h-[clamp(400px,72vmin,760px)]">

                @csrf

                @if($mode === 'edit')
                    @method('PUT')
                @endif

                <div class="w-full">

                    {{-- TABS --}}
                    <div class="flex items-center gap-[clamp(10px,2vmin,24px)] mb-[clamp(16px,3vmin,34px)]">
                        <button type="button"
                                id="tabXtream"
                                onclick="setType('xtream')"
                                class="h-[clamp(44px,8vmin,68px)] w-[clamp(180px,32vmin,320px)]
                                rounded-[clamp(12px,2vmin,20px)] font-extrabold
                                text-[clamp(13px,2.5vmin,23px)] transition">
                            Conto Xtream
                        </button>

                        <button type="button"
                                id="tabM3u"
                                onclick="setType('m3u')"
                                class="h-[clamp(44px,8vmin,68px)] w-[clamp(180px,32vmin,320px)]
                                rounded-[clamp(12px,2vmin,20px)] font-extrabold
                                text-[clamp(13px,2.5vmin,23px)] transition">
                            Lista URL
                        </button>
                    </div>

                    <input type="hidden"
                           name="type"
                           id="playlistType"
                           value="{{ old('type', $playlist->type ?: 'xtream') }}">

                    {{-- NOME PLAYLIST --}}
                    <div class="mb-[clamp(10px,2vmin,22px)]">
                        <input type="text"
                               name="name"
                               value="{{ old('name', $playlist->name) }}"
                               placeholder="Nome playlist"
                               class="w-full h-[clamp(44px,8vmin,68px)]
                               rounded-[clamp(12px,2vmin,20px)] bg-slate-100 text-slate-900
                               px-[clamp(18px,3vmin,34px)]
                               text-[clamp(14px,2.6vmin,24px)] font-semibold placeholder:text-slate-500
                               focus:outline-none focus:ring-4 focus:ring-fuchsia-400/50">
                    </div>

                    {{-- CAMPI XTREAM --}}
                    <div id="xtreamFields" class="space-y-[clamp(10px,2vmin,22px)]">
                        <input type="url"
                               name="xtream_host"
                               value="{{ old('xtream_host', $playlist->xtream_host) }}"
                               placeholder="Host"
                               class="w-full h-[clamp(44px,8vmin,68px)]
                               rounded-[clamp(12px,2vmin,20px)] bg-slate-100 text-slate-900
                               px-[clamp(18px,3vmin,34px)]
                               text-[clamp(14px,2.6vmin,24px)] font-semibold placeholder:text-slate-500
                               focus:outline-none focus:ring-4 focus:ring-fuchsia-400/50">

                        <div class="grid grid-cols-2 gap-[clamp(10px,2vmin,22px)]">
                            <input type="text"
                                   name="xtream_username"
                                   value="{{ old('xtream_username', $playlist->xtream_username) }}"
                                   placeholder="Nome utente"
                                   class="w-full h-[clamp(44px,8vmin,68px)]
                                   rounded-[clamp(12px,2vmin,20px)] bg-slate-100 text-slate-900
                                   px-[clamp(18px,3vmin,34px)]
                                   text-[clamp(14px,2.6vmin,24px)] font-semibold placeholder:text-slate-500
                                   focus:outline-none focus:ring-4 focus:ring-fuchsia-400/50">

                            <input type="password"
                                   name="xtream_password"
                                   value=""
                                   placeholder="{{ $mode === 'edit' ? 'Password - lascia vuoto per non modificarla' : 'Password' }}"
                                   class="w-full h-[clamp(44px,8vmin,68px)]
                                   rounded-[clamp(12px,2vmin,20px)] bg-slate-100 text-slate-900
                                   px-[clamp(18px,3vmin,34px)]
                                   text-[clamp(14px,2.6vmin,24px)] font-semibold placeholder:text-slate-500
                                   focus:outline-none focus:ring-4 focus:ring-fuchsia-400/50">
                        </div>
                    </div>

                    {{-- CAMPO M3U --}}
                    <div id="m3uFields" class="hidden">
                        <input type="url"
                               name="m3u_url"
                               value="{{ old('m3u_url', $playlist->m3u_url) }}"
                               placeholder="URL lista M3U"
                               class="w-full h-[clamp(44px,8vmin,68px)]
                               rounded-[clamp(12px,2vmin,20px)] bg-slate-100 text-slate-900
                               px-[clamp(18px,3vmin,34px)]
                               text-[clamp(14px,2.6vmin,24px)] font-semibold placeholder:text-slate-500
                               focus:outline-none focus:ring-4 focus:ring-fuchsia-400/50">
                    </div>

                    {{-- ATTIVA --}}
                    <div class="mt-[clamp(12px,2.4vmin,26px)] flex items-center justify-between gap-6">
                        <label class="flex items-center gap-[clamp(8px,1.8vmin,16px)] text-[clamp(12px,2.2vmin,20px)] text-slate-200">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $playlist->exists ? $playlist->is_active : true))
                                   class="w-[clamp(18px,3vmin,26px)] h-[clamp(18px,3vmin,26px)] rounded border-white/20">
                            Playlist attiva
                        </label>

                        <div class="hidden xl:block text-[clamp(10px,1.7vmin,16px)] text-slate-400 text-right">
                            Usa solo playlist personali o contenuti autorizzati.
                        </div>
                    </div>
                </div>

                {{-- BARRA INFERIORE --}}
                <div class="shrink-0 mt-[clamp(20px,4vmin,42px)] flex items-end justify-between gap-[clamp(18px,4vmin,44px)]">

                    <div class="min-w-0 text-[clamp(11px,2.2vmin,22px)] leading-relaxed font-semibold text-slate-200">
                        <p>
                            Puoi aggiungere una playlist tramite
                            <span class="text-yellow-300">URL M3U</span>
                            oppure tramite
                            <span class="text-yellow-300">API Xtream</span>.
                        </p>

                        <p class="text-slate-400 text-[clamp(9px,1.7vmin,16px)] mt-[clamp(2px,0.7vmin,8px)]">
                            Sito web: https://{{ request()->getHost() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-[clamp(12px,3vmin,28px)] shrink-0">
                        <a href="{{ route('customer.playlists.index') }}"
                           class="w-[clamp(120px,22vmin,190px)] h-[clamp(42px,8vmin,66px)]
                           rounded-[clamp(12px,2vmin,20px)]
                           bg-slate-100 hover:bg-white text-slate-700 flex items-center justify-center
                           text-[clamp(13px,2.5vmin,22px)] font-extrabold transition shadow-2xl">
                            Annulla
                        </a>

                        <button type="submit"
                                class="w-[clamp(120px,22vmin,190px)] h-[clamp(42px,8vmin,66px)]
                                rounded-[clamp(12px,2vmin,20px)]
                                bg-slate-100 hover:bg-white text-slate-700 flex items-center justify-center
                                text-[clamp(13px,2.5vmin,22px)] font-extrabold transition shadow-2xl">
                            Ok
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const typeInput = document.getElementById('playlistType');
    const tabXtream = document.getElementById('tabXtream');
    const tabM3u = document.getElementById('tabM3u');
    const xtreamFields = document.getElementById('xtreamFields');
    const m3uFields = document.getElementById('m3uFields');

    function setType(type) {
        typeInput.value = type;

        const activeClass =
            'h-[clamp(44px,8vmin,68px)] w-[clamp(180px,32vmin,320px)] rounded-[clamp(12px,2vmin,20px)] font-extrabold text-[clamp(13px,2.5vmin,23px)] transition bg-gradient-to-r from-violet-500 to-pink-500 text-white shadow-xl';

        const inactiveClass =
            'h-[clamp(44px,8vmin,68px)] w-[clamp(180px,32vmin,320px)] rounded-[clamp(12px,2vmin,20px)] font-extrabold text-[clamp(13px,2.5vmin,23px)] transition bg-slate-100 text-slate-700';

        if (type === 'xtream') {
            xtreamFields.classList.remove('hidden');
            m3uFields.classList.add('hidden');

            tabXtream.className = activeClass;
            tabM3u.className = inactiveClass;
        } else {
            xtreamFields.classList.add('hidden');
            m3uFields.classList.remove('hidden');

            tabXtream.className = inactiveClass;
            tabM3u.className = activeClass;
        }
    }

    setType(typeInput.value || 'xtream');

    /*
     * Fix scroll touch mobile.
     * Serve perché il layout IPTV forza lo schermo in landscape con transform:
     * su alcuni cellulari la rotellina funziona, ma il trascinamento con il dito no.
     */
    (function () {
        const scroller = document.getElementById('playlistFormScroller');

        if (!scroller) {
            return;
        }

        let lastX = 0;
        let lastY = 0;
        let startX = 0;
        let startY = 0;

        scroller.addEventListener('touchstart', function (event) {
            if (event.touches.length !== 1) {
                return;
            }

            const touch = event.touches[0];

            startX = lastX = touch.clientX;
            startY = lastY = touch.clientY;
        }, { passive: true });

        scroller.addEventListener('touchmove', function (event) {
            if (event.touches.length !== 1) {
                return;
            }

            const touch = event.touches[0];

            const currentX = touch.clientX;
            const currentY = touch.clientY;

            const diffX = currentX - lastX;
            const diffY = currentY - lastY;

            const totalX = currentX - startX;
            const totalY = currentY - startY;

            if (Math.abs(totalX) < 3 && Math.abs(totalY) < 3) {
                return;
            }

            const delta = Math.abs(diffX) > Math.abs(diffY)
                ? diffX
                : diffY;

            scroller.scrollTop += delta;

            lastX = currentX;
            lastY = currentY;

            event.preventDefault();
            event.stopPropagation();
        }, { passive: false });
    })();
</script>
@endpush