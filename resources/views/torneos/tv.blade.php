<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $torneo->nombre }} — Modo TV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#eef9fa',
              100: '#d5f0f3',
              200: '#aee2e8',
              300: '#78ccd6',
              400: '#42b0bf',
              500: '#1f95a6',
              600: '#147a8a',
              700: '#0F6B78',
              800: '#0d5764',
              900: '#0d4855',
              950: '#093038',
            },
            accent: {
              50:  '#fff4ec',
              100: '#ffe8d5',
              200: '#ffd0aa',
              300: '#ffb47a',
              400: '#ff9240',
              500: '#ff7a1a',
              600: '#FF6A00',
              700: '#d95800',
              800: '#b54800',
              900: '#8f3900',
              950: '#5a2200',
            }
          }
        }
      }
    }
    </script>
    <style>
        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Fondo: mismo que el sidebar de la app */
            background: #1e1b4b; /* brand-950 aprox */
            color: #e0e7ff;      /* brand-100 */
            font-family: system-ui, -apple-system, sans-serif;
        }

        #tv-root {
            display: flex;
            flex-direction: column;
            height: 100vh;
            height: 100dvh;
        }

        /* ── HEADER — idéntico al gradiente del sidebar/hero de la app ── */
        #tv-header {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.65rem 1.5rem;
            background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%); /* brand-900 → violet-900 */
            border-bottom: 2px solid #4f46e5; /* brand-600 */
        }

        #tv-header .tournament-info { flex: 1; min-width: 0; }

        #tv-header h1 {
            font-size: clamp(1.1rem, 2.5vw, 2rem);
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        #tv-header .subtitle {
            font-size: clamp(0.65rem, 1.1vw, 0.85rem);
            color: #a5b4fc; /* brand-300 */
            margin-top: 0.1rem;
        }

        #tv-header .right-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
        }

        #tv-clock {
            font-size: clamp(1rem, 2vw, 1.6rem);
            font-weight: 700;
            color: #c7d2fe; /* brand-200 */
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }

        #fullscreen-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.25);
            color: #e0e7ff;
            border-radius: 0.5rem;
            padding: 0.4rem 0.7rem;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        #fullscreen-btn:hover { background: rgba(255,255,255,0.2); }

        /* ── COLUMNS ── */
        #tv-columns {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            min-height: 0;
        }

        .tv-panel {
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .tv-panel-header {
            flex-shrink: 0;
            padding: 0.5rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: clamp(0.7rem, 1.2vw, 0.9rem);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            color: #c7d2fe; /* brand-200 */
        }

        .panel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #818cf8; /* brand-400 */
            animation: pulse-dot 2s infinite;
        }

        #panel-resultados .panel-dot { background: #6ee7b7; } /* emerald-300 */
        #panel-proximos  .panel-dot { background: #818cf8; } /* brand-400 */

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── SCROLL ── */
        .scroll-wrapper {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .scroll-track {
            will-change: transform;
            padding: 0.4rem 0;
        }

        /* Divider */
        #tv-divider { background: rgba(255,255,255,0.08); }

        /* ── MATCH CARDS — misma forma que las cards de la app ── */
        .match-card {
            margin: 0.3rem 0.85rem;
            border-radius: 0.6rem;
            padding: 0.55rem 0.85rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            transition: background 0.15s;
        }

        .match-meta {
            font-size: clamp(0.55rem, 0.85vw, 0.7rem);
            color: #6366f1; /* brand-500 */
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .match-meta .sep { color: #312e81; }

        /* Resultado */
        .result-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 0.5rem;
        }

        .result-team-name {
            font-size: clamp(0.75rem, 1.4vw, 1.05rem);
            font-weight: 700;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Ganador en verde, igual que el badge "activo" de la app */
        .result-team-name.winner { color: #6ee7b7; } /* emerald-300 */
        .result-team-name.loser  { color: #4338ca; } /* brand-700 — atenuado */

        .result-team-left  { text-align: right; }
        .result-team-right { text-align: left; }

        .result-score {
            font-size: clamp(1rem, 2vw, 1.6rem);
            font-weight: 900;
            color: #ffffff;
            text-align: center;
            letter-spacing: 0.05em;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            line-height: 1;
        }

        /* Próximo */
        .upcoming-teams {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 0.5rem;
        }

        .upcoming-team-name {
            font-size: clamp(0.75rem, 1.4vw, 1.05rem);
            font-weight: 700;
            color: #e0e7ff; /* brand-100 */
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upcoming-team-left  { text-align: right; }
        .upcoming-team-right { text-align: left; }

        .upcoming-vs {
            font-size: clamp(0.65rem, 1.1vw, 0.85rem);
            font-weight: 800;
            color: #4338ca; /* brand-700 */
            text-align: center;
            letter-spacing: 0.05em;
        }

        .upcoming-time {
            font-size: clamp(0.85rem, 1.6vw, 1.2rem);
            font-weight: 800;
            color: #a5b4fc; /* brand-300 */
            text-align: center;
            margin-top: 0.3rem;
            letter-spacing: 0.03em;
        }

        .day-badge {
            display: inline-block;
            background: rgba(99,102,241,0.2); /* brand-500/20 */
            border: 1px solid rgba(99,102,241,0.4);
            border-radius: 0.25rem;
            padding: 0.05rem 0.4rem;
            font-size: clamp(0.5rem, 0.85vw, 0.68rem);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #818cf8; /* brand-400 */
            margin-right: 0.3rem;
        }

        /* Empty state */
        .tv-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #3730a3; /* brand-800 */
            font-size: clamp(0.75rem, 1.1vw, 0.85rem);
            font-weight: 500;
            text-align: center;
            padding: 2rem;
            gap: 0.5rem;
        }

        /* Footer */
        #tv-footer {
            flex-shrink: 0;
            padding: 0.25rem 1.2rem;
            background: rgba(0,0,0,0.3);
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.65rem;
            color: #4338ca; /* brand-700 */
        }

        .refresh-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #6ee7b7;
            margin-right: 0.35rem;
            animation: pulse-dot 3s infinite;
        }

        .score-label {
            display: block;
            font-size: clamp(0.45rem, 0.7vw, 0.6rem);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #4338ca;
            text-align: center;
            margin-top: 0.15rem;
        }

        /* ── BANNER CAMPEÓN ── */
        #tv-campeon {
            flex-shrink: 0;
            background: linear-gradient(135deg, #78350f 0%, #92400e 40%, #78350f 100%);
            border-bottom: 2px solid #f59e0b;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            min-height: 0;
        }

        .campeon-slot {
            display: none;
            align-items: center;
            gap: 1.2rem;
            animation: fade-in 0.6s ease;
        }
        .campeon-slot.active { display: flex; }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .campeon-trophy {
            font-size: clamp(1.8rem, 3vw, 2.8rem);
            line-height: 1;
            filter: drop-shadow(0 0 8px #fbbf24);
            animation: trophy-pulse 2s ease-in-out infinite;
        }

        @keyframes trophy-pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.08); }
        }

        .campeon-info { text-align: left; }

        .campeon-label {
            font-size: clamp(0.55rem, 0.9vw, 0.72rem);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #fbbf24;
            margin-bottom: 0.1rem;
        }

        .campeon-nombre {
            font-size: clamp(1rem, 2.2vw, 1.8rem);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            letter-spacing: 0.01em;
        }

        .campeon-categoria {
            font-size: clamp(0.55rem, 0.85vw, 0.7rem);
            color: #fcd34d;
            margin-top: 0.1rem;
            font-weight: 500;
        }

        .campeon-avatars {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .campeon-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
        }

        .campeon-avatar img,
        .campeon-avatar .avatar-placeholder {
            width: clamp(2.2rem, 3.5vw, 3.2rem);
            height: clamp(2.2rem, 3.5vw, 3.2rem);
            border-radius: 50%;
            border: 2px solid #fbbf24;
            object-fit: cover;
        }

        .campeon-avatar .avatar-placeholder {
            background: rgba(251,191,36,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.8rem, 1.4vw, 1.1rem);
            font-weight: 800;
            color: #fbbf24;
        }

        .campeon-avatar-name {
            font-size: clamp(0.45rem, 0.7vw, 0.6rem);
            color: #fcd34d;
            font-weight: 600;
            text-align: center;
            max-width: clamp(2.2rem, 3.5vw, 3.2rem);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .campeon-dots {
            display: flex;
            gap: 0.35rem;
            justify-content: center;
            margin-left: 1rem;
        }

        .campeon-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(251,191,36,0.35);
            cursor: pointer;
            transition: background 0.3s;
        }
        .campeon-dot.active { background: #fbbf24; }
    </style>
</head>
<body>
<div id="tv-root">

    {{-- HEADER --}}
    <div id="tv-header">
        <div class="tournament-info">
            <h1>{{ $torneo->nombre }}</h1>
            <div class="subtitle">{{ $torneo->deporte->nombre }} &bull; {{ $torneo->complejo->nombre }}</div>
        </div>
        <div class="right-controls">
            <div id="tv-clock">--:--:--</div>
            <button id="fullscreen-btn" title="Pantalla completa">
                <svg id="fs-icon-enter" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <svg id="fs-icon-exit" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4m0 5H4m5 0L3 3m12 6h5m-5 0V4m0 5l6-6M9 15v5m0-5H4m5 5l-6 6m12-6h5m-5 0v5m0-5l6 6"/>
                </svg>
                <span id="fs-label">Pantalla completa</span>
            </button>
        </div>
    </div>

    @if($campeones->isNotEmpty())
    {{-- BANNER CAMPEÓN --}}
    <div id="tv-campeon">
        @foreach($campeones as $i => $camp)
        <div class="campeon-slot {{ $i === 0 ? 'active' : '' }}" data-campeon="{{ $i }}">
            <div class="campeon-trophy">🏆</div>

            <div class="campeon-info">
                <div class="campeon-label">¡Campeón!</div>
                <div class="campeon-nombre">{{ $camp['equipo']->nombre }}</div>
                <div class="campeon-categoria">{{ $camp['categoria']->nombre }}</div>
            </div>

            @if($camp['equipo']->jugadores->isNotEmpty())
            <div class="campeon-avatars">
                @foreach($camp['equipo']->jugadores as $jugador)
                <div class="campeon-avatar">
                    @if($jugador->foto)
                        <img src="{{ asset('storage/' . $jugador->foto) }}"
                             alt="{{ $jugador->nombre_completo }}">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($jugador->apellido, 0, 1)) }}
                        </div>
                    @endif
                    <span class="campeon-avatar-name">{{ $jugador->nombre }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach

        @if($campeones->count() > 1)
        <div class="campeon-dots">
            @foreach($campeones as $i => $camp)
            <div class="campeon-dot {{ $i === 0 ? 'active' : '' }}" data-dot="{{ $i }}"></div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- COLUMNS --}}
    <div id="tv-columns">

        {{-- LEFT: Resultados --}}
        <div class="tv-panel" id="panel-resultados">
            <div class="tv-panel-header">
                <div class="panel-dot"></div>
                Resultados
                <span style="margin-left:auto; font-size:0.7em; font-weight:400; opacity:0.5;">
                    {{ $partidosFinalizados->count() }} partido{{ $partidosFinalizados->count() !== 1 ? 's' : '' }}
                </span>
            </div>
            <div class="scroll-wrapper" id="wrapper-resultados">
                <div class="scroll-track" id="track-resultados">
                    @forelse($partidosFinalizados as $partido)
                        @php
                            $esGanador1 = $partido->equipo_ganador_id === $partido->equipo1_id;
                            $esGanador2 = $partido->equipo_ganador_id === $partido->equipo2_id;
                        @endphp
                        <div class="match-card">
                            <div class="match-meta">
                                @if($partido->fecha_hora)
                                    <span>{{ $partido->fecha_hora->format('d/m H:i') }}</span>
                                    <span class="sep">|</span>
                                @endif
                                @if($partido->cancha)
                                    <span>{{ $partido->cancha->nombre }}</span>
                                    <span class="sep">|</span>
                                @endif
                                @if($partido->grupo)
                                    <span>{{ $partido->grupo->nombre }}</span>
                                @elseif($partido->llave)
                                    <span>{{ $partido->llave->ronda }}</span>
                                @endif
                            </div>
                            <div class="result-teams">
                                <div class="result-team-name result-team-left {{ $esGanador1 ? 'winner' : 'loser' }}">
                                    {{ $partido->equipo1->nombre ?? '—' }}
                                </div>
                                <div class="result-score">
                                    {{ $partido->sets_equipo1 }} &ndash; {{ $partido->sets_equipo2 }}
                                    <span class="score-label">{{ $esFutbol ? 'Goles' : 'Sets' }}</span>
                                </div>
                                <div class="result-team-name result-team-right {{ $esGanador2 ? 'winner' : 'loser' }}">
                                    {{ $partido->equipo2->nombre ?? '—' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="tv-empty">
                            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Sin resultados aún</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div id="tv-divider"></div>

        {{-- RIGHT: Próximos --}}
        <div class="tv-panel" id="panel-proximos">
            <div class="tv-panel-header">
                <div class="panel-dot"></div>
                Próximos Partidos
                <span style="margin-left:auto; font-size:0.7em; font-weight:400; opacity:0.5;">
                    {{ $proximosPartidos->count() }} partido{{ $proximosPartidos->count() !== 1 ? 's' : '' }}
                </span>
            </div>
            <div class="scroll-wrapper" id="wrapper-proximos">
                <div class="scroll-track" id="track-proximos">
                    @forelse($proximosPartidos as $partido)
                        @php
                            if ($partido->fecha_hora && $partido->fecha_hora->isToday())    $dayLabel = 'HOY';
                            elseif ($partido->fecha_hora && $partido->fecha_hora->isTomorrow()) $dayLabel = 'MAÑANA';
                            else $dayLabel = $partido->fecha_hora ? $partido->fecha_hora->format('d/m') : '';
                        @endphp
                        <div class="match-card">
                            <div class="match-meta">
                                @if($partido->cancha)
                                    <span>{{ $partido->cancha->nombre }}</span>
                                    <span class="sep">|</span>
                                @endif
                                @if($partido->grupo)
                                    <span>{{ $partido->grupo->nombre }}</span>
                                @elseif($partido->llave)
                                    <span>{{ $partido->llave->ronda }}</span>
                                @endif
                            </div>
                            <div class="upcoming-teams">
                                <div class="upcoming-team-name upcoming-team-left">
                                    {{ $partido->equipo1->nombre ?? '—' }}
                                </div>
                                <div class="upcoming-vs">VS</div>
                                <div class="upcoming-team-name upcoming-team-right">
                                    {{ $partido->equipo2->nombre ?? '—' }}
                                </div>
                            </div>
                            @if($partido->fecha_hora)
                                <div class="upcoming-time">
                                    <span class="day-badge">{{ $dayLabel }}</span>{{ $partido->fecha_hora->format('H:i') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="tv-empty">
                            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Sin partidos próximos programados</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div id="tv-footer">
        <span><span class="refresh-dot"></span>Actualizando cada minuto</span>
        <span>PickleTorneos &bull; {{ $torneo->nombre }}</span>
        <span id="footer-last-update"></span>
    </div>

</div>

<script>
    // ── RELOJ ────────────────────────────────────────────────────────
    function updateClock() {
        document.getElementById('tv-clock').textContent =
            new Date().toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── FULLSCREEN ───────────────────────────────────────────────────
    const fsBtn   = document.getElementById('fullscreen-btn');
    const fsEnter = document.getElementById('fs-icon-enter');
    const fsExit  = document.getElementById('fs-icon-exit');
    const fsLabel = document.getElementById('fs-label');

    fsBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            document.exitFullscreen().catch(() => {});
        }
    });

    document.addEventListener('fullscreenchange', () => {
        const isFs = !!document.fullscreenElement;
        fsEnter.style.display = isFs ? 'none' : '';
        fsExit.style.display  = isFs ? '' : 'none';
        fsLabel.textContent   = isFs ? 'Salir' : 'Pantalla completa';
    });

    // ── AUTO-SCROLL INFINITO ─────────────────────────────────────────
    const SCROLL_SPEED = 45; // px/segundo

    function setupScroll(wrapperId, trackId) {
        const wrapper = document.getElementById(wrapperId);
        const track   = document.getElementById(trackId);
        if (!wrapper || !track) return;

        requestAnimationFrame(() => {
            if (track.scrollHeight <= wrapper.clientHeight) return;

            // Duplicar para loop sin salto visible
            track.innerHTML += track.innerHTML;

            let pos      = 0;
            let lastTime = null;
            const halfH  = track.scrollHeight / 2;

            function step(ts) {
                if (lastTime === null) lastTime = ts;
                pos += SCROLL_SPEED * (ts - lastTime) / 1000;
                lastTime = ts;
                if (pos >= halfH) pos -= halfH;
                track.style.transform = `translateY(-${pos}px)`;
                requestAnimationFrame(step);
            }

            requestAnimationFrame(step);
        });
    }

    setupScroll('wrapper-resultados', 'track-resultados');
    setupScroll('wrapper-proximos',   'track-proximos');

    // ── AUTO-REFRESH CADA 60 s ───────────────────────────────────────
    document.getElementById('footer-last-update').textContent =
        'Última actualización: ' + new Date().toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });

    setTimeout(() => location.reload(), 60_000);

    // ── ROTACIÓN DE CAMPEONES ─────────────────────────────────────────
    (function () {
        const slots = document.querySelectorAll('.campeon-slot');
        const dots  = document.querySelectorAll('.campeon-dot');
        if (slots.length <= 1) return;

        let current = 0;

        function goTo(index) {
            slots[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = index % slots.length;
            slots[current].classList.add('active');
            dots[current].classList.add('active');
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', () => goTo(parseInt(dot.dataset.dot)));
        });

        setInterval(() => goTo(current + 1), 8000);
    })();
</script>
</body>
</html>
