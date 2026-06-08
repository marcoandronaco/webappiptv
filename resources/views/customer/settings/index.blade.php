@extends('layouts.iptv-screen', ['title' => 'Impostazioni'])

@section('content')

<div class="h-full w-full overflow-hidden bg-[#070b18] text-white">
    <div class="h-full w-full p-[clamp(4px,0.9vmin,14px)]">

        <div class="grid h-full grid-cols-[clamp(270px,30vw,520px)_1fr] gap-[clamp(5px,1vmin,16px)]">

            {{-- SINISTRA: MENU IMPOSTAZIONI --}}
            <aside class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(16px,2.6vmin,30px)] border border-white/10 bg-white/[0.045] p-[clamp(7px,1.25vmin,18px)] shadow-2xl">

                {{-- BOTTONI HOME / TITOLO --}}
                <div class="mb-[clamp(6px,1vmin,14px)] grid shrink-0 grid-cols-[auto_1fr] gap-[clamp(6px,1vmin,12px)]">
                    <a href="{{ url('/') }}"
                       class="flex items-center justify-center rounded-[clamp(12px,2vmin,22px)] bg-violet-700 px-[clamp(12px,1.9vmin,22px)] py-[clamp(8px,1.45vmin,16px)] text-[clamp(12px,1.65vmin,18px)] font-black hover:bg-violet-600">
                        Home
                    </a>

                    <div class="flex items-center justify-center rounded-[clamp(12px,2vmin,22px)] bg-white/[0.08] px-[clamp(12px,1.9vmin,22px)] py-[clamp(8px,1.45vmin,16px)] text-[clamp(12px,1.65vmin,18px)] font-black">
                        IMPOSTAZIONI
                    </div>
                </div>

                {{-- LISTA VOCI CON SCROLL NATIVO --}}
                <div id="settingsMenuScroll"
                     class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1 space-y-[clamp(3px,0.55vmin,6px)]">

                    <button type="button"
                            data-settings-tab="general"
                            class="js-settings-item js-scroll-item is-active flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-gradient-to-r from-violet-600 via-fuchsia-500 to-red-400 px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">ⓘ</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Informazioni Generali</span>
                    </button>

                    <button type="button"
                            data-settings-tab="player"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">▶</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Impostazioni Del Lettore</span>
                    </button>

                    <button type="button"
                            data-settings-tab="categories"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">▦</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Gestisci Categorie</span>
                    </button>

                    <button type="button"
                            data-settings-tab="language"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">◉</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Cambia Lingua</span>
                    </button>

                    <button type="button"
                            data-settings-tab="live-layout"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">▣</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Layout TV In Diretta</span>
                    </button>

                    <button type="button"
                            data-settings-tab="archive"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">▥</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Pulisci Archivio</span>
                    </button>

                    <button type="button"
                            data-settings-tab="pin"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">🔒</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Cambia PIN</span>
                    </button>

                    <button type="button"
                            data-settings-tab="time"
                            class="js-settings-item js-scroll-item flex w-full items-center gap-2 rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.7vmin,18px)] py-[clamp(8px,1.25vmin,14px)] text-left transition hover:bg-white/[0.12]">
                        <span class="w-[clamp(22px,3vmin,34px)] shrink-0 text-center">◷</span>
                        <span class="truncate text-[clamp(11px,1.55vmin,17px)] font-black">Impostazioni Del Tempo</span>
                    </button>

                </div>
            </aside>

            {{-- DESTRA: CONTENUTO --}}
            <main class="flex min-h-0 flex-col overflow-hidden rounded-[clamp(14px,2.4vmin,26px)] border border-white/10 bg-white/[0.035] p-[clamp(6px,1vmin,12px)] shadow-2xl">

                <div id="settingsContentScroll"
                     class="iptv-panel-scroll min-h-0 flex-1 overflow-y-auto pr-1">

                    {{-- GENERALI --}}
                    <section data-settings-panel="general">
                        <div class="mb-[clamp(10px,1.8vmin,22px)] rounded-[clamp(11px,1.8vmin,18px)] bg-white/[0.07] px-[clamp(10px,1.8vmin,18px)] py-[clamp(8px,1.3vmin,14px)]">
                            <div class="text-[clamp(18px,2.8vmin,34px)] font-black tracking-[0.14em]">
                                IMPOSTAZIONI
                            </div>
                        </div>

                        <div class="mb-[clamp(8px,1.5vmin,18px)] text-[clamp(16px,2.2vmin,28px)] font-black tracking-[0.12em]">
                            Informazioni account
                        </div>

                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Stato <strong>Prova gratuita</strong></div>
                            <div class="settings-row">Data di registrazione <strong>{{ now()->format('d.m.Y') }}</strong></div>
                            <div class="settings-row">Prova gratuita scaduta <strong>{{ now()->addDays(7)->format('d.m.Y') }}</strong></div>
                        </div>

                        <div class="mb-[clamp(8px,1.5vmin,18px)] mt-[clamp(18px,3vmin,36px)] text-[clamp(16px,2.2vmin,28px)] font-black tracking-[0.12em]">
                            Informazioni dispositivo
                        </div>

                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Indirizzo MAC <strong>PC:91:OV:R4:BT:N1</strong></div>
                            <div class="settings-row">Chiave del dispositivo <strong>581269</strong></div>
                            <div class="settings-row">Versione dell'app <strong>3.5.3</strong></div>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="player">
                        <div class="settings-title">Impostazioni del lettore</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Lettore predefinito <strong>Automatico</strong></div>
                            <div class="settings-row">Riproduzione automatica <strong>Attiva</strong></div>
                            <div class="settings-row">Formato Live TV <strong>HLS</strong></div>
                            <div class="settings-row">Buffer video <strong>Normale</strong></div>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="categories">
                        <div class="settings-title">Gestisci categorie</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <a href="{{ route('customer.channels.index', ['tipo' => 'live']) }}" class="settings-row">Categorie Live TV <strong>Apri</strong></a>
                            <a href="{{ route('customer.channels.index', ['tipo' => 'film']) }}" class="settings-row">Categorie Film <strong>Apri</strong></a>
                            <a href="{{ route('customer.channels.index', ['tipo' => 'serie']) }}" class="settings-row">Categorie Serie <strong>Apri</strong></a>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="language">
                        <div class="settings-title">Cambia lingua</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Lingua attuale <strong>Italiano</strong></div>
                            <div class="settings-row">English <strong>Disponibile</strong></div>
                            <div class="settings-row">Français <strong>Disponibile</strong></div>
                            <div class="settings-row">العربية <strong>Disponibile</strong></div>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="live-layout">
                        <div class="settings-title">Layout TV In Diretta</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Layout attuale <strong>Lista + Player</strong></div>
                            <div class="settings-row">Colonna categorie <strong>Grande</strong></div>
                            <div class="settings-row">Griglia canali <strong>4 colonne</strong></div>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="archive">
                        <div class="settings-title">Pulisci Archivio</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Cache applicazione <strong>Disponibile</strong></div>
                            <div class="settings-row">Archivio HLS <strong>storage/app/hls</strong></div>
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="pin">
                        <div class="settings-title">Cambia PIN</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">PIN attuale <strong>••••</strong></div>
                            <input type="password" placeholder="Nuovo PIN" class="w-full rounded-[clamp(11px,1.8vmin,18px)] border border-white/10 bg-white/[0.07] px-[clamp(12px,2vmin,20px)] py-[clamp(9px,1.5vmin,16px)] text-[clamp(12px,1.8vmin,20px)] text-white outline-none">
                        </div>
                    </section>

                    <section class="hidden" data-settings-panel="time">
                        <div class="settings-title">Impostazioni Del Tempo</div>
                        <div class="space-y-[clamp(5px,0.9vmin,10px)]">
                            <div class="settings-row">Fuso orario <strong>Europe/Rome</strong></div>
                            <div class="settings-row">Formato ora <strong>24 ore</strong></div>
                            <div class="settings-row">Ora corrente <strong>{{ now()->format('H:i') }}</strong></div>
                        </div>
                    </section>

                </div>
            </main>

        </div>
    </div>
</div>

<style>
    .settings-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: clamp(8px, 1.5vmin, 18px);
        border-radius: clamp(11px, 1.8vmin, 18px);
        background: rgba(255,255,255,0.07);
        padding: clamp(9px,1.5vmin,16px) clamp(12px,2vmin,20px);
        color: white;
        text-decoration: none;
        font-size: clamp(12px,1.8vmin,20px);
        font-weight: 700;
    }

    .settings-row strong {
        font-weight: 800;
        white-space: nowrap;
    }

    .settings-title {
        margin-bottom: clamp(10px,1.8vmin,22px);
        border-radius: clamp(11px,1.8vmin,18px);
        background: rgba(255,255,255,0.07);
        padding: clamp(10px,1.8vmin,18px);
        font-size: clamp(18px,2.8vmin,34px);
        font-weight: 900;
        letter-spacing: .12em;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = Array.from(document.querySelectorAll('[data-settings-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-settings-panel]'));

    function activate(tabName) {
        tabs.forEach(function (tab) {
            const active = tab.dataset.settingsTab === tabName;

            tab.classList.toggle('is-active', active);

            tab.classList.toggle('bg-gradient-to-r', active);
            tab.classList.toggle('from-violet-600', active);
            tab.classList.toggle('via-fuchsia-500', active);
            tab.classList.toggle('to-red-400', active);

            tab.classList.toggle('bg-white/[0.07]', !active);
            tab.classList.toggle('hover:bg-white/[0.12]', !active);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.dataset.settingsPanel !== tabName);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activate(tab.dataset.settingsTab);
        });
    });
});
</script>

@endsection