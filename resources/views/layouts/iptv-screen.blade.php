<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WebApp IPTV' }}</title>

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
        <div class="h-full w-full overflow-hidden
            bg-[radial-gradient(circle_at_top_left,rgba(124,58,237,0.22),transparent_32%),radial-gradient(circle_at_center,rgba(37,99,235,0.14),transparent_34%),linear-gradient(135deg,#020617_0%,#080d3f_48%,#020617_100%)]">

            @yield('content')

        </div>
    </div>
</div>

@stack('scripts')

</body>
</html>