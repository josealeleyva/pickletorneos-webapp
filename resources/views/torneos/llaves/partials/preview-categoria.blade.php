<div class="space-y-6">
    <!-- Información de la categoría -->
    <div class="bg-blue-50 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 mb-2">{{ $categoria->nombre }}</h3>
        <p class="text-sm text-blue-700">
            <strong>{{ $total }}</strong> equipos.
            @if ($torneo->formato && $torneo->formato->esEliminacionDirecta())
            Puedes modificar los cruces tocando en la seccion de cada equipo.
            @else
            Puedes modificar los cruces tocando en la seccion de cada equipo o podes cambiarlos por otro equipo que haya quedado afuera.
            @endif
        </p>
    </div>

    @if($total === 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
        <p class="text-yellow-800">No hay equipos clasificados para esta categoría.</p>
        <p class="text-sm text-yellow-600 mt-1">Verifica la configuración de avance de grupos o completa los partidos de la fase de grupos.</p>
    </div>
    @else
    @php
    // Calcular potencia de 2
    $potencia = 1;
    while ($potencia < $total) {
        $potencia *=2;
        }
        $byes=$potencia - $total;

        // Obtener equipos NO clasificados para poder reemplazar
        $clasificadosIds=collect($clasificados)->pluck('equipo.id')->toArray();
        $equiposNoClasificados = $torneo->equipos()
        ->where('categoria_id', $categoria->id)
        ->whereNotIn('id', $clasificadosIds)
        ->with('jugadores')
        ->get();
        @endphp

        @php
        // Solo equipos impares reciben BYE
        $equiposConByeDirecto = $total % 2;
        $equiposQueJuegan = $total - $equiposConByeDirecto;
        @endphp

        @if($byes > 0)
        <div class="bg-blue-50 border border-brand-200 rounded p-3">
            <p class="text-sm text-blue-800">
                <strong>Nota:</strong> {{ $equiposConByeDirecto }} {{ $equiposConByeDirecto == 1 ? 'equipo recibe' : 'equipos reciben' }} BYE y {{ $equiposConByeDirecto == 1 ? 'avanza' : 'avanzan' }} automáticamente a la siguiente ronda ({{ $equiposQueJuegan }} equipos juegan en primera ronda).
            </p>
        </div>
        @endif

        <!-- Preview del bracket editable -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-gray-800">Cruces de Primera Ronda</h4>
                <div id="swap-indicator-{{ $categoria->id }}" class="hidden">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-brand-600 font-medium">Equipo seleccionado - Click en otro para intercambiar</span>
                        <button type="button"
                            onclick="cancelSwap({{ $categoria->id }})"
                            class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="bracket-preview-{{ $categoria->id }}">
                @php
                // Redistribuir equipos
                $equiposEnBracket = array_fill(0, $potencia, null);

                if ($equiposConByeDirecto == 1) {
                // El mejor seed recibe BYE (posición 0)
                $equiposEnBracket[0] = $clasificados[0];

                // Distribuir el resto alternando
                $equipoIdx = 1;
                for ($pos = 1; $pos < $potencia && $equipoIdx < $total; $pos++) {
                    // Saltar posiciones medias
                    if ($pos>= ($potencia / 2) - 1 && $pos <= ($potencia / 2)) {
                        continue;
                        }
                        $equiposEnBracket[$pos]=$clasificados[$equipoIdx];
                        $equipoIdx++;
                        }
                        } else {
                        // Todos se distribuyen normalmente
                        for ($idx=0; $idx < $total; $idx++) {
                        $equiposEnBracket[$idx]=$clasificados[$idx];
                        }
                        }
                        @endphp

                        @for($i=0; $i < $potencia / 2; $i++)
                        @php
                        // Emparejamiento estándar
                        $pos1=$i;
                        $pos2=$potencia - 1 - $i;

                        $equipo1=$equiposEnBracket[$pos1];
                        $equipo2=$equiposEnBracket[$pos2];
                        @endphp

                        @if($equipo1 || $equipo2)
                        <div class="bracket-match border-2 border-gray-300 rounded-lg overflow-hidden" data-match-index="{{ $i }}">
                        <!-- Header -->
                        <div class="bg-gray-100 px-3 py-2 border-b border-gray-300">
                            <span class="text-sm font-semibold text-gray-700">Llave {{ $i + 1 }}</span>
                        </div>

                        <!-- Equipo 1 -->
                        <div class="team-slot border-b border-gray-200 bg-white cursor-pointer hover:bg-blue-50 transition-colors"
                            data-slot="equipo1"
                            data-match="{{ $i }}"
                            data-categoria="{{ $categoria->id }}"
                            onclick="selectTeamForSwap({{ $categoria->id }}, {{ $i }}, 'equipo1', this)">
                            @if(isset($equipo1) && isset($equipo1['equipo']) && $equipo1['equipo'])
                            <div class="flex items-center justify-between p-3">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-800">{{ $equipo1['equipo']->nombre }}</span>
                                </div>
                                @if ($torneo->formato && !$torneo->formato->esEliminacionDirecta())
                                <button type="button"
                                    class="ml-2 px-2 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded z-10"
                                    onclick="event.stopPropagation(); showReplaceModal({{ $categoria->id }}, {{ $i }}, 'equipo1', {{ $equipo1['equipo']->id }})"
                                    title="Reemplazar este equipo">
                                    Cambiar
                                </button>
                                @endif
                            </div>
                            <input type="hidden"
                                class="team-input"
                                name="categorias[{{ $categoria->id }}][clasificados][]"
                                value="{{ $equipo1['equipo']->id }}"
                                data-original-index="{{ $i }}">
                            @else
                            <div class="p-3 text-sm text-gray-400 italic">BYE</div>
                            @endif
                        </div>

                        <!-- VS separator -->
                        <div class="bg-gray-50 text-center py-1">
                            <span class="text-xs font-bold text-gray-500">VS</span>
                        </div>

                        <!-- Equipo 2 -->
                        <div class="team-slot bg-white cursor-pointer hover:bg-blue-50 transition-colors"
                            data-slot="equipo2"
                            data-match="{{ $i }}"
                            data-categoria="{{ $categoria->id }}"
                            onclick="selectTeamForSwap({{ $categoria->id }}, {{ $i }}, 'equipo2', this)">
                            @if(isset($equipo2) && isset($equipo2['equipo']) && $equipo2['equipo'])
                            <div class="flex items-center justify-between p-3">
                                <div class="flex-1">
                                    <span class="font-medium text-gray-800">{{ $equipo2['equipo']->nombre }}</span>
                                </div>
                                @if ($torneo->formato && !$torneo->formato->esEliminacionDirecta())
                                <button type="button"
                                    class="ml-2 px-2 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded z-10"
                                    onclick="event.stopPropagation(); showReplaceModal({{ $categoria->id }}, {{ $i }}, 'equipo2', {{ $equipo2['equipo']->id }})"
                                    title="Reemplazar este equipo">
                                    Cambiar
                                </button>
                                @endif
                            </div>
                            <input type="hidden"
                                class="team-input"
                                name="categorias[{{ $categoria->id }}][clasificados][]"
                                value="{{ $equipo2['equipo']->id }}"
                                data-original-index="{{ $i }}">
                            @else
                            <div class="p-3 text-sm text-gray-400 italic">BYE</div>
                            @endif
                        </div>
            </div>
            @endif
            @endfor
        </div>
</div>

<!-- Input hidden para categoria_id -->
<input type="hidden" name="categorias[{{ $categoria->id }}][categoria_id]" value="{{ $categoria->id }}">

<!-- Modal para reemplazar equipo -->
<div id="replace-modal-{{ $categoria->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Reemplazar Equipo</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Selecciona un equipo de los que quedaron afuera para reemplazar al actual:</p>
            <div class="space-y-2 max-h-64 overflow-y-auto" id="replace-options-{{ $categoria->id }}">
                @foreach($equiposNoClasificados as $equipo)
                <button type="button"
                    class="w-full text-left p-3 border border-gray-200 rounded hover:bg-blue-50 hover:border-blue-300 transition"
                    onclick="replaceTeam({{ $categoria->id }}, {{ $equipo->id }})">
                    <div class="font-medium text-gray-800">{{ $equipo->nombre }}</div>
                    <div class="text-xs text-gray-500">{{ $equipo->jugadores->pluck('nombre_completo')->join(' / ') }}</div>
                </button>
                @endforeach
                @if($equiposNoClasificados->isEmpty())
                <p class="text-sm text-gray-500 text-center py-4">No hay equipos disponibles para reemplazo</p>
                @endif
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button type="button"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                onclick="closeReplaceModal({{ $categoria->id }})">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Datos para JavaScript -->
<script>
    window.clasificadosData = window.clasificadosData || {};
    window.clasificadosData[{
        {
            $categoria - > id
        }
    }] = @json($clasificados);

    window.equiposNoClasificados = window.equiposNoClasificados || {};
    window.equiposNoClasificados[{
        {
            $categoria - > id
        }
    }] = @json($equiposNoClasificados);
</script>
@endif
</div>