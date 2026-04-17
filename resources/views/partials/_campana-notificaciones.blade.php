<div
    x-data="{
        open: false,
        notificaciones: [],
        noLeidas: 0,
        cargando: false,
        async cargar() {
            if (this.cargando) return;
            this.cargando = true;
            try {
                const res = await fetch('{{ route('notificaciones.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.notificaciones = data.notificaciones;
                this.noLeidas = data.no_leidas;
            } catch (e) {
                console.error('Error cargando notificaciones', e);
            } finally {
                this.cargando = false;
            }
        },
        async abrir() {
            this.open = !this.open;
            if (this.open) await this.cargar();
        },
        async marcarLeida(id) {
            await fetch(`/notificaciones/${id}/leer`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            const n = this.notificaciones.find(n => n.id === id);
            if (n) {
                n.leida = true;
                this.noLeidas = Math.max(0, this.noLeidas - 1);
            }
        },
        async marcarTodas() {
            await fetch('/notificaciones/leer-todas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            this.notificaciones.forEach(n => n.leida = true);
            this.noLeidas = 0;
        },
        async handleClick(n) {
            if (!n.leida) {
                await this.marcarLeida(n.id);
            }
            if (n.tipo === 'invitacion_torneo') {
                window.location.href = '{{ route('jugador.inscripciones') }}';
            } else if (n.tipo === 'resultado_tentativo' || n.tipo === 'resultado_confirmado') {
                window.location.href = '{{ route('jugador.partidos') }}';
            }
        }
    }"
    x-init="cargar()"
    class="relative"
>
    <!-- Botón campana -->
    <button
        @click="abrir()"
        class="relative p-2 rounded-lg text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
        aria-label="Notificaciones"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <!-- Badge -->
        <span
            x-show="noLeidas > 0"
            x-text="noLeidas > 9 ? '9+' : noLeidas"
            class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs font-bold rounded-full min-w-[1.1rem] h-[1.1rem] flex items-center justify-center px-0.5 leading-none"
        ></span>
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden"
        style="display:none;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Notificaciones</h3>
            <button
                x-show="noLeidas > 0"
                @click="marcarTodas()"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
            >
                Marcar todas como leídas
            </button>
        </div>

        <!-- Lista -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            <template x-if="cargando">
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </template>

            <template x-if="!cargando && notificaciones.length === 0">
                <div class="text-center py-8 text-gray-400 text-sm">
                    No tenés notificaciones
                </div>
            </template>

            <template x-for="n in notificaciones" :key="n.id">
                <div
                    @click="handleClick(n)"
                    :class="n.leida && !['invitacion_torneo','resultado_tentativo','resultado_confirmado'].includes(n.tipo) ? 'bg-white' : 'bg-indigo-50 cursor-pointer hover:bg-indigo-100'"
                    class="px-4 py-3 transition"
                >
                    <div class="flex items-start gap-3">
                        <!-- Ícono según tipo -->
                        <div :class="{
                            'bg-green-100 text-green-600': n.tipo === 'inscripcion_confirmada',
                            'bg-blue-100 text-blue-600': n.tipo === 'invitacion_torneo',
                            'bg-red-100 text-red-600': n.tipo === 'inscripcion_cancelada',
                            'bg-yellow-100 text-yellow-600': n.tipo === 'nuevo_equipo',
                            'bg-indigo-100 text-indigo-600': !['inscripcion_confirmada','invitacion_torneo','inscripcion_cancelada','nuevo_equipo'].includes(n.tipo)
                        }" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 leading-snug" x-text="n.mensaje"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="n.fecha"></p>
                        </div>
                        <!-- Punto no leída -->
                        <div x-show="!n.leida" class="flex-shrink-0 w-2 h-2 bg-indigo-500 rounded-full mt-1.5"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
