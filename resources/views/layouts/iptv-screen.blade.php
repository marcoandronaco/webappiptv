<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WebApp IPTV' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #070b18;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            position: fixed;
            inset: 0;
            overscroll-behavior: none;
        }

        .iptv-viewport {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            background: #070b18;
            margin: 0;
            padding: 0;
        }

        .iptv-screen {
            position: absolute;
            inset: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            background: #070b18;
            margin: 0;
            padding: 0;
        }

        /*
        * Telefono in verticale:
        * la schermata viene forzata in orizzontale senza lasciare spazio nero.
        */
        @media screen and (orientation: portrait) {
            .iptv-screen {
                width: 100vh;
                width: 100dvh;
                height: 100vw;
                transform-origin: top left;
                transform: rotate(90deg) translateY(-100vw);
                overflow: hidden;
            }
        }
    </style>
</head>

<body class="text-white font-sans">

<div class="iptv-viewport">
    <div class="iptv-screen">
        <div class="h-full w-full overflow-hidden
            bg-[radial-gradient(circle_at_top_left,rgba(124,58,237,0.22),transparent_32%),radial-gradient(circle_at_center,rgba(37,99,235,0.14),transparent_34%),linear-gradient(135deg,#020617_0%,#080d3f_48%,#020617_100%)]">

            @yield('content')

        </div>
    </div>
</div>

{{-- <button id="forceLandscapeBtn"
        type="button"
        style="
            position: fixed;
            z-index: 999999;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            background: #7c3aed;
            color: white;
            font-weight: 900;
            font-size: 14px;
            box-shadow: 0 12px 30px rgba(0,0,0,.35);
            display: none;
        ">
    ↔ Schermo orizzontale
</button>

<script>
(function () {
    const btn = document.getElementById('forceLandscapeBtn');

    if (!btn) {
        return;
    }

    function isPortrait() {
        return window.innerHeight > window.innerWidth;
    }

    function refreshButton() {
        btn.style.display = isPortrait() ? 'block' : 'none';
    }

    async function forceLandscape() {
        try {
            if (!document.fullscreenElement && document.documentElement.requestFullscreen) {
                await document.documentElement.requestFullscreen();
            }

            if (screen.orientation && screen.orientation.lock) {
                await screen.orientation.lock('landscape');
            }

            btn.style.display = 'none';
        } catch (error) {
            console.warn('Blocco orientamento non riuscito:', error);
            btn.innerText = 'Ruota il telefono in orizzontale';
        }
    }

    btn.addEventListener('click', forceLandscape);

    window.addEventListener('resize', refreshButton);
    window.addEventListener('orientationchange', function () {
        setTimeout(refreshButton, 300);
    });

    refreshButton();
})();
</script> --}}



@stack('scripts')

</body>
</html>