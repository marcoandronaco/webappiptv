<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebApp IPTV</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #020617;
        }

        .iptv-viewport {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100dvh;
            overflow: hidden;
            background: #020617;
        }

        .iptv-screen {
            width: 100vw;
            height: 100dvh;
            overflow: hidden;
        }

        @media screen and (orientation: portrait) {
            .iptv-screen {
                width: 100dvh;
                height: 100vw;
                transform-origin: top left;
                transform: rotate(90deg) translateY(-100%);
            }
        }
    </style>
</head>

<body class="text-white font-sans">

<div class="iptv-viewport">
    <div class="iptv-screen">

        <div class="h-full w-full overflow-hidden flex flex-col justify-between
            px-[clamp(10px,3vmin,70px)] py-[clamp(8px,2vmin,38px)]
            gap-[clamp(8px,2vmin,28px)]
            bg-[radial-gradient(circle_at_top_left,rgba(0,153,255,0.20),transparent_32%),radial-gradient(circle_at_top_right,rgba(155,44,255,0.18),transparent_34%),linear-gradient(135deg,#020617_0%,#050b2f_45%,#020617_100%)]">

            {{-- HEADER --}}
            <header class="flex items-center justify-between gap-[clamp(8px,2vmin,30px)] shrink-0">

                {{-- LOGO / BRAND --}}
                <div class="flex items-center gap-[clamp(8px,2vmin,24px)] min-w-0">
                    <div class="relative shrink-0 flex items-center justify-center
                        w-[clamp(46px,12vmin,112px)] h-[clamp(34px,8vmin,80px)]
                        rounded-[clamp(8px,1.7vmin,18px)]
                        border-[clamp(2px,0.45vmin,4px)] border-cyan-400
                        shadow-[0_0_35px_rgba(34,211,238,0.35)]">

                        <div class="absolute top-[clamp(-20px,-2vmin,-10px)] left-[22%] w-[32%] h-[4px] bg-cyan-400 rounded-full rotate-45"></div>
                        <div class="absolute top-[clamp(-20px,-2vmin,-10px)] right-[22%] w-[32%] h-[4px] bg-cyan-400 rounded-full -rotate-45"></div>

                        <div class="ml-[3px] w-0 h-0
                            border-y-[clamp(8px,1.8vmin,18px)]
                            border-y-transparent
                            border-l-[clamp(14px,3vmin,30px)]
                            border-l-white drop-shadow-lg"></div>
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-[clamp(16px,4vmin,38px)] leading-none font-medium tracking-wide whitespace-nowrap">
                            WebApp <span class="text-blue-400 font-bold">IPTV</span>
                        </h1>

                        <p class="mt-[clamp(2px,0.7vmin,8px)] text-[clamp(9px,1.8vmin,18px)] text-slate-300 tracking-wide whitespace-nowrap">
                            La tua TV, sempre con te
                        </p>
                    </div>
                </div>

                {{-- DATA / ORA --}}
                <div class="flex items-center justify-end gap-[clamp(8px,2vmin,24px)] shrink-0">

                    <div class="text-right pr-[clamp(8px,1.5vmin,24px)] border-r border-white/20">
                        <div id="dayName" class="text-[clamp(9px,1.8vmin,18px)] text-slate-100 leading-tight">
                            Lunedì
                        </div>
                        <div id="dateFull" class="text-[clamp(9px,1.8vmin,18px)] text-slate-200 leading-tight">
                            01 Giugno
                        </div>
                    </div>

                    <div id="clock" class="text-[clamp(20px,5.5vmin,54px)] leading-none font-extrabold tracking-widest">
                        12:18
                    </div>

                    <button type="button"
                            class="w-[clamp(34px,8vmin,80px)] h-[clamp(34px,8vmin,80px)] rounded-full border border-white/15 bg-white/5 backdrop-blur-xl flex items-center justify-center shadow-2xl">
                        <svg class="w-[52%] h-[52%] text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 22c1.5-5.5 5-8 8-8s6.5 2.5 8 8"></path>
                        </svg>
                    </button>

                    <button type="button"
                            onclick="window.location.reload()"
                            class="w-[clamp(34px,8vmin,80px)] h-[clamp(34px,8vmin,80px)] rounded-full border border-white/15 bg-white/5 backdrop-blur-xl flex items-center justify-center shadow-2xl">
                        <svg class="w-[52%] h-[52%] text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-15.5-6.3"></path>
                            <path d="M3 5v6h6"></path>
                            <path d="M3 12a9 9 0 0 0 15.5 6.3"></path>
                            <path d="M21 19v-6h-6"></path>
                        </svg>
                    </button>
                </div>
            </header>

            {{-- MENU PRINCIPALE --}}
            <main class="grid flex-1 min-h-0 gap-[clamp(8px,2.2vmin,34px)]"
                  style="grid-template-columns: 1.25fr 0.95fr 0.95fr; grid-template-rows: 1fr clamp(42px, 8vmin, 86px);">

                {{-- TV DAL VIVO --}}
                <a href="{{ url('/cliente/player?tipo=live') }}"
                   class="group relative row-span-2 overflow-hidden flex flex-col items-center justify-center
                   rounded-[clamp(12px,2.5vmin,28px)]
                   bg-gradient-to-br from-cyan-300 via-blue-500 to-blue-800
                   border border-white/15 shadow-[0_25px_60px_rgba(0,0,0,0.35)]
                   transition duration-300 hover:-translate-y-1 hover:scale-[1.01] hover:brightness-110">

                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.28),transparent_34%)]"></div>

                    <svg class="relative w-[clamp(70px,18vmin,176px)] h-[clamp(70px,18vmin,176px)]
                        mb-[clamp(10px,4vmin,42px)] text-white drop-shadow-xl"
                         viewBox="0 0 180 180" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="32" y="48" width="116" height="78" rx="12"></rect>
                        <path d="M68 144h44"></path>
                        <path d="M76 126l-8 18"></path>
                        <path d="M104 126l8 18"></path>
                        <path d="M72 48l-28-28"></path>
                        <path d="M108 48l28-28"></path>
                        <text x="90" y="99" text-anchor="middle" fill="currentColor" stroke="none" font-size="40" font-weight="800">TV</text>
                    </svg>

                    <span class="relative text-[clamp(18px,5.5vmin,54px)] leading-none font-extrabold tracking-wide whitespace-nowrap">
                        TV dal vivo
                    </span>
                </a>

                {{-- FILM --}}
                <a href="{{ url('/cliente/player?tipo=film') }}"
                   class="group relative overflow-hidden flex flex-col items-center justify-center
                   rounded-[clamp(12px,2.5vmin,28px)]
                   bg-gradient-to-br from-pink-500 via-red-500 to-orange-400
                   border border-white/15 shadow-[0_25px_60px_rgba(0,0,0,0.35)]
                   transition duration-300 hover:-translate-y-1 hover:scale-[1.01] hover:brightness-110">

                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.25),transparent_34%)]"></div>

                    <svg class="relative w-[clamp(54px,14vmin,144px)] h-[clamp(54px,14vmin,144px)]
                        mb-[clamp(8px,3vmin,36px)] text-white drop-shadow-xl"
                         viewBox="0 0 180 180" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="70" cy="46" r="18"></circle>
                        <circle cx="110" cy="46" r="22"></circle>
                        <rect x="48" y="78" width="88" height="58" rx="10"></rect>
                        <path d="M136 94l30-18v62l-30-18z"></path>
                        <path d="M86 94l28 18-28 18z" fill="currentColor" stroke="none"></path>
                    </svg>

                    <span class="relative text-[clamp(18px,4.3vmin,42px)] leading-none font-extrabold tracking-wide whitespace-nowrap">
                        Film
                    </span>
                </a>

                {{-- SERIE --}}
                <a href="{{ url('/cliente/player?tipo=serie') }}"
                   class="group relative overflow-hidden flex flex-col items-center justify-center
                   rounded-[clamp(12px,2.5vmin,28px)]
                   bg-gradient-to-br from-purple-500 via-indigo-500 to-sky-400
                   border border-white/15 shadow-[0_25px_60px_rgba(0,0,0,0.35)]
                   transition duration-300 hover:-translate-y-1 hover:scale-[1.01] hover:brightness-110">

                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.25),transparent_34%)]"></div>

                    <svg class="relative w-[clamp(54px,14vmin,144px)] h-[clamp(54px,14vmin,144px)]
                        mb-[clamp(8px,3vmin,36px)] text-white drop-shadow-xl"
                         viewBox="0 0 180 180" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="34" y="42" width="112" height="76" rx="10"></rect>
                        <path d="M78 64l34 18-34 20z" fill="currentColor" stroke="none"></path>
                        <path d="M64 138h52"></path>
                        <path d="M78 118l-12 20"></path>
                        <path d="M102 118l12 20"></path>
                        <path d="M128 58h1"></path>
                        <path d="M128 76h1"></path>
                        <path d="M128 94h1"></path>
                    </svg>

                    <span class="relative text-[clamp(18px,4.3vmin,42px)] leading-none font-extrabold tracking-wide whitespace-nowrap">
                        Serie
                    </span>
                </a>

                {{-- IMPOSTAZIONI --}}
                <a href="{{ url('/cliente/dispositivo') }}"
                   class="rounded-[clamp(10px,2vmin,22px)]
                   px-[clamp(10px,3vmin,48px)]
                   flex items-center justify-center gap-[clamp(8px,2vmin,28px)]
                   bg-gradient-to-r from-teal-500/55 to-cyan-900/45 border border-cyan-300/25
                   shadow-[0_20px_45px_rgba(0,0,0,0.25)]
                   transition duration-300 hover:-translate-y-1 hover:brightness-110">

                    <svg class="w-[clamp(22px,5vmin,50px)] h-[clamp(22px,5vmin,50px)] text-white shrink-0"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3.5"></circle>
                        <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-.4-1.1 1.7 1.7 0 0 0-1-.6 1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 0 1 0-4h.1A1.7 1.7 0 0 0 4.2 9a1.7 1.7 0 0 0 .6-1 1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 .4 1.1 1.7 1.7 0 0 0 1 .6 1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.2.4.5.8.9 1 .3.2.7.3 1.1.3h.1a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.1.4c-.4.2-.7.6-.9 1z"></path>
                    </svg>

                    <span class="text-[clamp(12px,3.2vmin,30px)] leading-none font-bold tracking-wide whitespace-nowrap">
                        Impostazioni
                    </span>
                </a>

                {{-- LISTA --}}
                <a href="{{ route('customer.playlists.index') }}"
                   class="rounded-[clamp(10px,2vmin,22px)]
                   px-[clamp(10px,3vmin,48px)]
                   flex items-center justify-center gap-[clamp(8px,2vmin,28px)]
                   bg-gradient-to-r from-teal-500/55 to-cyan-900/45 border border-cyan-300/25
                   shadow-[0_20px_45px_rgba(0,0,0,0.25)]
                   transition duration-300 hover:-translate-y-1 hover:brightness-110">

                    <svg class="w-[clamp(22px,5vmin,50px)] h-[clamp(22px,5vmin,50px)] text-white shrink-0"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h6"></path>
                        <path d="M6 9h.01"></path>
                        <path d="M6 13h.01"></path>
                        <path d="M9 17l3-2 3 2"></path>
                    </svg>

                    <span class="text-[clamp(12px,3.2vmin,30px)] leading-none font-bold tracking-wide whitespace-nowrap">
                        Lista
                    </span>
                </a>
            </main>

            {{-- FOOTER --}}
            <footer class="grid grid-cols-2 gap-[clamp(8px,2vmin,32px)] shrink-0">

                {{-- SINISTRA --}}
                <div class="flex flex-col gap-[clamp(6px,1.2vmin,12px)] min-w-0">

                    <div class="h-[clamp(34px,7vmin,70px)]
                        px-[clamp(10px,3vmin,32px)]
                        rounded-[clamp(10px,2vmin,22px)]
                        bg-white/[0.055] border border-white/10 backdrop-blur-xl
                        flex items-center gap-[clamp(8px,2vmin,24px)] shadow-xl min-w-0">

                        <div class="w-[clamp(26px,5vmin,48px)] h-[clamp(26px,5vmin,48px)] rounded-full bg-gradient-to-br from-sky-400 to-violet-600 flex items-center justify-center shadow-[0_0_22px_rgba(139,92,246,0.45)] shrink-0">
                            <svg class="w-[60%] h-[60%] text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 21c1.5-5 5-8 8-8s6.5 3 8 8"></path>
                            </svg>
                        </div>

                        <span class="text-[clamp(10px,2.5vmin,26px)] tracking-widest truncate">
                            {{ auth()->check() ? auth()->user()->name : 'salvoiptv' }}
                        </span>
                    </div>

                    <div class="h-[clamp(34px,7vmin,70px)]
                        px-[clamp(10px,3vmin,32px)]
                        rounded-[clamp(10px,2vmin,22px)]
                        bg-white/[0.055] border border-white/10 backdrop-blur-xl
                        flex items-center gap-[clamp(8px,2vmin,24px)] shadow-xl min-w-0">

                        <div class="w-[clamp(26px,5vmin,48px)] h-[clamp(26px,5vmin,48px)] rounded-full bg-gradient-to-br from-fuchsia-500 to-violet-700 flex items-center justify-center shadow-[0_0_22px_rgba(217,70,239,0.45)] shrink-0">
                            <svg class="w-[58%] h-[58%] text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.9 6.2 6.8.8-5 4.7 1.3 6.7L12 17l-6 3.4 1.3-6.7-5-4.7 6.8-.8z"></path>
                            </svg>
                        </div>

                        <span class="text-[clamp(10px,2.5vmin,26px)] tracking-wide truncate">
                            Benvenuto
                        </span>
                    </div>
                </div>

                {{-- DESTRA --}}
                <div class="flex flex-col gap-[clamp(6px,1.2vmin,12px)] min-w-0">

                    <div class="h-[clamp(34px,7vmin,70px)]
                        px-[clamp(10px,3vmin,32px)]
                        rounded-[clamp(10px,2vmin,22px)]
                        bg-white/[0.055] border border-white/10 backdrop-blur-xl
                        flex items-center gap-[clamp(8px,2vmin,24px)] shadow-xl min-w-0">

                        <svg class="w-[clamp(20px,4vmin,36px)] h-[clamp(20px,4vmin,36px)] text-white shrink-0"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="M8 21h8"></path>
                        </svg>

                        <span class="text-[clamp(9px,2.2vmin,24px)] tracking-wide truncate">
                            Codice dispositivo: {{ $deviceCode ?? 'DEVICE-XXXX-XXXX' }}
                        </span>
                    </div>

                    <div class="h-[clamp(34px,7vmin,70px)]
                        px-[clamp(10px,3vmin,32px)]
                        rounded-[clamp(10px,2vmin,22px)]
                        bg-white/[0.055] border border-white/10 backdrop-blur-xl
                        flex items-center gap-[clamp(8px,2vmin,24px)] shadow-xl min-w-0">

                        <svg class="w-[clamp(20px,4vmin,36px)] h-[clamp(20px,4vmin,36px)] text-white shrink-0"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M2 12h20"></path>
                            <path d="M12 2a15 15 0 0 1 0 20"></path>
                            <path d="M12 2a15 15 0 0 0 0 20"></path>
                        </svg>

                        <span class="text-[clamp(9px,2.2vmin,24px)] tracking-wide truncate">
                            Sito web: {{ request()->getHost() ?: 'webappiptv.local' }}
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();

        const giorni = [
            'Domenica',
            'Lunedì',
            'Martedì',
            'Mercoledì',
            'Giovedì',
            'Venerdì',
            'Sabato'
        ];

        const mesi = [
            'Gennaio',
            'Febbraio',
            'Marzo',
            'Aprile',
            'Maggio',
            'Giugno',
            'Luglio',
            'Agosto',
            'Settembre',
            'Ottobre',
            'Novembre',
            'Dicembre'
        ];

        document.getElementById('dayName').textContent = giorni[now.getDay()];

        document.getElementById('dateFull').textContent =
            String(now.getDate()).padStart(2, '0') + ' ' + mesi[now.getMonth()];

        document.getElementById('clock').textContent =
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0');
    }

    updateClock();
    setInterval(updateClock, 30000);
</script>

</body>
</html>
