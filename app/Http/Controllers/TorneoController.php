<?php

namespace App\Http\Controllers;

use App\Exports\ResultadosPartidosExport;
use App\Models\AvanceGrupo;
use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\TamanioGrupo;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TorneoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Torneo::class, 'torneo');
    }

    /**
     * Listado de torneos del organizador
     */
    public function index()
    {
        $torneos = Auth::user()->torneos()
            ->with(['deporte', 'complejo', 'formato', 'pago'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('torneos.index', compact('torneos'));
    }

    /**
     * Paso 1: Mostrar formulario de información general
     */
    public function create()
    {
        $deportes = Deporte::all();
        $complejos = Auth::user()->complejos;

        // Cargar solo las categorías del organizador autenticado agrupadas por deporte
        $categoriasPorDeporte = Categoria::where('organizador_id', Auth::id())
            ->get()
            ->groupBy('deporte_id');

        return view('torneos.create-step1', compact('deportes', 'complejos', 'categoriasPorDeporte'));
    }

    /**
     * Paso 1: Guardar información general (crear torneo en borrador)
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'deporte_id' => 'required|exists:deportes,id',
            'categorias' => 'required|array|min:1|max:10',
            'categorias.*' => 'required|exists:categorias,id',
            'complejo_id' => 'required|exists:complejos_deportivos,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_inscripcion' => 'nullable|date|before_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
            'precio_inscripcion' => 'nullable|numeric|min:0',
            'premios' => 'nullable|string',
            'imagen_banner' => 'nullable|image|max:10240', // 10MB max
            'reglamento_texto' => 'nullable|string',
            'reglamento_pdf' => 'nullable|file|mimes:pdf|max:20480', // 20MB max
        ]);

        // Verificar que el complejo pertenece al organizador
        $complejo = ComplejoDeportivo::findOrFail($validated['complejo_id']);
        if ($complejo->organizador_id !== Auth::id()) {
            abort(403, 'No puedes usar un complejo que no te pertenece.');
        }

        // Verificar que las categorías pertenecen al deporte seleccionado y al organizador
        $categoriasValidas = Categoria::where('deporte_id', $validated['deporte_id'])
            ->where('organizador_id', Auth::id())
            ->whereIn('id', $validated['categorias'])
            ->count();

        if ($categoriasValidas !== count($validated['categorias'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Las categorías seleccionadas no corresponden al deporte elegido o no te pertenecen.');
        }

        // Manejar la imagen si existe
        if ($request->hasFile('imagen_banner')) {
            $validated['imagen_banner'] = $request->file('imagen_banner')->store('torneos/banners', 'public');
        }

        // Manejar el PDF del reglamento si existe
        if ($request->hasFile('reglamento_pdf')) {
            $validated['reglamento_pdf'] = $request->file('reglamento_pdf')->store('torneos/reglamentos', 'public');
        }

        // Crear torneo en estado borrador
        $validated['organizador_id'] = Auth::id();
        $validated['estado'] = 'borrador';

        // Extraer categorías antes de crear el torneo
        $categorias = $validated['categorias'];
        unset($validated['categorias']);

        $torneo = Torneo::create($validated);

        // Asociar categorías al torneo
        $torneo->categorias()->attach($categorias);

        return redirect()
            ->route('torneos.create-step2', $torneo)
            ->with('success', 'Información básica guardada. Continúa con el formato del torneo.');
    }

    /**
     * Paso 2: Mostrar formulario de formato
     */
    public function createStep2(Torneo $torneo)
    {
        // Verificar que el torneo pertenece al organizador y está en borrador
        if ($torneo->organizador_id !== Auth::id()) {
            abort(403);
        }

        if ($torneo->estado !== 'borrador') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes editar torneos en estado borrador.');
        }

        $formatos = FormatoTorneo::all();
        $tamanios = TamanioGrupo::all();
        $avances = AvanceGrupo::all();

        // Cargar categorías del torneo con su configuración
        $torneo->load('categorias');

        return view('torneos.create-step2', compact('torneo', 'formatos', 'tamanios', 'avances'));
    }

    /**
     * Paso 2: Guardar formato del torneo
     */
    public function storeStep2(Request $request, Torneo $torneo)
    {
        // Verificar que el torneo pertenece al organizador y está en borrador
        if ($torneo->organizador_id !== Auth::id()) {
            abort(403);
        }

        if ($torneo->estado !== 'borrador') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes editar torneos en estado borrador.');
        }

        $validated = $request->validate([
            'formato_id' => 'required|exists:formatos_torneos,id',
            'categorias' => 'required|array',
            'categorias.*.categoria_id' => 'required|exists:categorias,id',
            'categorias.*.numero_grupos' => 'nullable|integer|min:2|max:8',
            'categorias.*.tamanio_grupo_id' => 'nullable|exists:tamanios_grupos,id',
            'categorias.*.avance_grupos_id' => 'nullable|exists:avances_grupos,id',
            'categorias.*.cupos_categoria' => 'nullable|integer|min:2|max:32',
            'categorias.*.edad_minima' => 'nullable|integer|min:1|max:99',
            'categorias.*.edad_maxima' => 'nullable|integer|min:1|max:99',
            'categorias.*.genero_permitido' => 'nullable|in:masculino,femenino,mixto',
        ]);

        // Verificar si el formato tiene grupos
        $formato = FormatoTorneo::find($validated['formato_id']);

        if ($formato->tiene_grupos) {
            // Si tiene grupos, validar que cada categoría tenga configuración
            $request->validate([
                'categorias.*.numero_grupos' => 'required|integer|min:2|max:8',
                'categorias.*.tamanio_grupo_id' => 'required|exists:tamanios_grupos,id',
                'categorias.*.avance_grupos_id' => 'required|exists:avances_grupos,id',
            ]);
        } else {
            // Liga o Eliminación Directa: validar cupos_categoria
            $request->validate([
                'categorias.*.cupos_categoria' => 'required|integer|min:2|max:32',
            ]);
        }

        // Actualizar formato del torneo
        $torneo->update(['formato_id' => $validated['formato_id']]);

        // Sincronizar categorías con su configuración
        $syncData = [];
        foreach ($validated['categorias'] as $categoriaData) {
            $categoriaId = $categoriaData['categoria_id'];

            if ($formato->tiene_grupos) {
                $syncData[$categoriaId] = [
                    'numero_grupos' => $categoriaData['numero_grupos'],
                    'tamanio_grupo_id' => $categoriaData['tamanio_grupo_id'],
                    'avance_grupos_id' => $categoriaData['avance_grupos_id'],
                    'cupos_categoria' => null,
                    'edad_minima' => $categoriaData['edad_minima'] ?? null,
                    'edad_maxima' => $categoriaData['edad_maxima'] ?? null,
                    'genero_permitido' => $categoriaData['genero_permitido'] ?? null,
                ];
            } else {
                $syncData[$categoriaId] = [
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                    'cupos_categoria' => $categoriaData['cupos_categoria'],
                    'edad_minima' => $categoriaData['edad_minima'] ?? null,
                    'edad_maxima' => $categoriaData['edad_maxima'] ?? null,
                    'genero_permitido' => $categoriaData['genero_permitido'] ?? null,
                ];
            }
        }

        $torneo->categorias()->sync($syncData);

        // Verificar si necesita crear pago
        $organizador = Auth::user();

        // Verificar si ya existe un pago para este torneo
        $pagoExistente = \App\Models\PagoTorneo::where('torneo_id', $torneo->id)->first();

        if (! $pagoExistente) {
            // Determinar si es el primer torneo (gratis) o requiere pago
            $esPrimerTorneo = $organizador->torneos_creados == 0;

            if ($esPrimerTorneo) {
                // Crear registro de pago gratuito
                \App\Models\PagoTorneo::create([
                    'torneo_id' => $torneo->id,
                    'organizador_id' => $organizador->id,
                    'monto' => 0,
                    'estado' => 'gratuito',
                    'es_primer_torneo_gratis' => true,
                    'pagado_en' => now(),
                ]);

                // Incrementar contador de torneos
                $organizador->increment('torneos_creados');

                // Pasar a activo automáticamente
                $torneo->update(['estado' => 'activo']);

                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('success', '¡Torneo creado exitosamente! Este es tu primer torneo gratis. Ahora puedes agregar participantes.');
            } else {
                // Requiere pago - obtener precio del sistema
                $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);

                // Crear registro de pago pendiente
                \App\Models\PagoTorneo::create([
                    'torneo_id' => $torneo->id,
                    'organizador_id' => $organizador->id,
                    'monto' => $precioTorneo,
                    'estado' => 'pendiente',
                    'es_primer_torneo_gratis' => false,
                ]);

                // Redirigir a checkout de MercadoPago
                return redirect()
                    ->route('pagos.checkout', $torneo)
                    ->with('info', 'Para continuar con la configuración del torneo, debes completar el pago.');
            }
        }

        // Si ya existe un pago, redirigir según su estado
        if ($pagoExistente->estado === 'pendiente') {
            return redirect()
                ->route('pagos.checkout', $torneo)
                ->with('info', 'Debes completar el pago para continuar con la configuración del torneo.');
        }

        // Pago ya acreditado — pasar a activo si todavía está en borrador
        if ($torneo->estado === 'borrador') {
            $torneo->update(['estado' => 'activo']);
        }

        return redirect()
            ->route('torneos.show', $torneo)
            ->with('success', '¡Torneo actualizado exitosamente!');
    }

    /**
     * Ver detalle del torneo
     */
    public function show(Torneo $torneo)
    {
        $torneo->load(['deporte', 'categorias', 'complejo', 'formato', 'organizador']);

        return view('torneos.show', compact('torneo'));
    }

    /**
     * Exportar resultados de partidos finalizados a Excel
     */
    public function exportarResultados(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        $tieneResultados = $torneo->partidos()->where('estado', 'finalizado')->exists();
        if (! $tieneResultados) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'No hay resultados cargados para exportar.');
        }

        $nombreArchivo = 'Resultados_'.Str::slug($torneo->nombre).'.xlsx';

        return Excel::download(new ResultadosPartidosExport($torneo), $nombreArchivo);
    }

    /**
     * Editar torneo (solo si está en borrador)
     */
    public function edit(Torneo $torneo)
    {
        if ($torneo->estado !== 'borrador') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes editar torneos en estado borrador.');
        }

        $deportes = Deporte::all();
        $complejos = Auth::user()->complejos;
        $formatos = FormatoTorneo::all();
        $tamanios = TamanioGrupo::all();
        $avances = AvanceGrupo::all();

        // Cargar solo las categorías del organizador autenticado agrupadas por deporte
        $categoriasPorDeporte = Categoria::where('organizador_id', Auth::id())
            ->get()
            ->groupBy('deporte_id');

        // Detectar datos dependientes
        $tieneEquipos = $torneo->equipos()->count() > 0;
        $tieneGrupos = $torneo->grupos()->count() > 0;
        $tienePartidos = $torneo->partidos()->count() > 0;

        return view('torneos.edit', compact(
            'torneo',
            'deportes',
            'categoriasPorDeporte',
            'complejos',
            'formatos',
            'tamanios',
            'avances',
            'tieneEquipos',
            'tieneGrupos',
            'tienePartidos'
        ));
    }

    /**
     * Actualizar torneo
     */
    public function update(Request $request, Torneo $torneo)
    {
        if ($torneo->estado !== 'borrador') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes editar torneos en estado borrador.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'deporte_id' => 'required|exists:deportes,id',
            'categorias' => 'required|array|min:1|max:10',
            'categorias.*.categoria_id' => 'required|exists:categorias,id',
            'categorias.*.numero_grupos' => 'nullable|integer|min:2|max:8',
            'categorias.*.tamanio_grupo_id' => 'nullable|exists:tamanios_grupos,id',
            'categorias.*.avance_grupos_id' => 'nullable|exists:avances_grupos,id',
            'categorias.*.cupos_categoria' => 'nullable|integer|min:2|max:32',
            'complejo_id' => 'required|exists:complejos_deportivos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_inscripcion' => 'nullable|date|before_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
            'precio_inscripcion' => 'nullable|numeric|min:0',
            'premios' => 'nullable|string',
            'imagen_banner' => 'nullable|image|max:10240', // 10MB max
            'formato_id' => 'required|exists:formatos_torneos,id',
            'confirmar_cambios' => 'nullable|boolean',
            'reglamento_texto' => 'nullable|string',
            'reglamento_pdf' => 'nullable|file|mimes:pdf|max:20480', // 20MB max
            'eliminar_reglamento_pdf' => 'nullable|boolean',
        ]);

        // Verificar que el complejo pertenece al organizador
        $complejo = ComplejoDeportivo::findOrFail($validated['complejo_id']);
        if ($complejo->organizador_id !== Auth::id()) {
            abort(403, 'No puedes usar un complejo que no te pertenece.');
        }

        // Extraer IDs de categorías del array
        $categoriasIds = collect($validated['categorias'])->pluck('categoria_id')->toArray();

        // Verificar que las categorías pertenecen al deporte seleccionado y al organizador
        $categoriasValidas = Categoria::where('deporte_id', $validated['deporte_id'])
            ->where('organizador_id', Auth::id())
            ->whereIn('id', $categoriasIds)
            ->count();

        if ($categoriasValidas !== count($categoriasIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Las categorías seleccionadas no corresponden al deporte elegido o no te pertenecen.');
        }

        // Manejar la imagen si existe
        if ($request->hasFile('imagen_banner')) {
            // Eliminar imagen anterior si existe
            if ($torneo->imagen_banner) {
                Storage::disk('public')->delete($torneo->imagen_banner);
            }
            $validated['imagen_banner'] = $request->file('imagen_banner')->store('torneos/banners', 'public');
        }

        // Manejar el PDF del reglamento
        if ($request->hasFile('reglamento_pdf')) {
            if ($torneo->reglamento_pdf) {
                Storage::disk('public')->delete($torneo->reglamento_pdf);
            }
            $validated['reglamento_pdf'] = $request->file('reglamento_pdf')->store('torneos/reglamentos', 'public');
        } elseif (! empty($validated['eliminar_reglamento_pdf'])) {
            if ($torneo->reglamento_pdf) {
                Storage::disk('public')->delete($torneo->reglamento_pdf);
            }
            $validated['reglamento_pdf'] = null;
        }
        unset($validated['eliminar_reglamento_pdf']);

        // Verificar si el formato tiene grupos
        $formato = FormatoTorneo::find($validated['formato_id']);

        if ($formato->tiene_grupos) {
            // Si tiene grupos, validar que cada categoría tenga configuración
            $request->validate([
                'categorias.*.numero_grupos' => 'required|integer|min:2|max:8',
                'categorias.*.tamanio_grupo_id' => 'required|exists:tamanios_grupos,id',
                'categorias.*.avance_grupos_id' => 'required|exists:avances_grupos,id',
            ]);
        } else {
            // Liga o Eliminación Directa: validar cupos_categoria
            $request->validate([
                'categorias.*.cupos_categoria' => 'required|integer|min:2|max:32',
            ]);
        }

        // Detectar cambios críticos que requieren eliminar datos
        $cambiosCriticos = false;
        $mensajesAdvertencia = [];

        // Cambio de formato
        if ($torneo->formato_id != $validated['formato_id']) {
            $cambiosCriticos = true;
            $mensajesAdvertencia[] = 'Se cambió el formato del torneo';
        }

        // Verificar cambios en configuración de categorías
        foreach ($validated['categorias'] as $categoriaData) {
            $categoriaId = $categoriaData['categoria_id'];
            $categoriaActual = $torneo->categorias->find($categoriaId);

            if ($categoriaActual) {
                // Verificar cambios en grupos
                if (isset($categoriaData['numero_grupos']) &&
                    $categoriaActual->pivot->numero_grupos != $categoriaData['numero_grupos']) {
                    $cambiosCriticos = true;
                    $mensajesAdvertencia[] = 'Se cambió el número de grupos en alguna categoría';
                    break;
                }

                if (isset($categoriaData['tamanio_grupo_id']) &&
                    $categoriaActual->pivot->tamanio_grupo_id != $categoriaData['tamanio_grupo_id']) {
                    $cambiosCriticos = true;
                    $mensajesAdvertencia[] = 'Se cambió el tamaño de los grupos en alguna categoría';
                    break;
                }
            }
        }

        // Si hay cambios críticos y hay datos dependientes
        if ($cambiosCriticos && ($torneo->equipos()->count() > 0 || $torneo->grupos()->count() > 0)) {
            // Verificar si se confirmó el cambio
            if (! $request->has('confirmar_cambios') || ! $request->confirmar_cambios) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('warning', 'Los cambios que intentas realizar eliminarán los grupos y fixture configurados. Los equipos se mantendrán pero perderán su asignación a grupos. Por favor confirma esta acción.');
            }

            // Si se confirmó, eliminar datos dependientes
            DB::beginTransaction();
            try {
                // Eliminar partidos
                $torneo->partidos()->delete();

                // Eliminar llaves
                $torneo->llaves()->delete();

                // Eliminar grupos
                $torneo->grupos()->delete();

                // Limpiar grupo_id de equipos (mantener los equipos)
                $torneo->equipos()->update(['grupo_id' => null]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Error al limpiar los datos dependientes.');
            }
        }

        // Extraer categorías antes de actualizar el torneo
        $categoriasConfig = $validated['categorias'];
        unset($validated['categorias']);

        // Actualizar torneo
        $torneo->update($validated);

        // Sincronizar categorías con su configuración
        $syncData = [];
        foreach ($categoriasConfig as $categoriaData) {
            $categoriaId = $categoriaData['categoria_id'];

            if ($formato->tiene_grupos) {
                $syncData[$categoriaId] = [
                    'numero_grupos' => $categoriaData['numero_grupos'],
                    'tamanio_grupo_id' => $categoriaData['tamanio_grupo_id'],
                    'avance_grupos_id' => $categoriaData['avance_grupos_id'],
                    'cupos_categoria' => null,
                ];
            } else {
                $syncData[$categoriaId] = [
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                    'cupos_categoria' => $categoriaData['cupos_categoria'],
                ];
            }
        }

        $torneo->categorias()->sync($syncData);

        $mensaje = 'Torneo actualizado exitosamente.';
        if ($cambiosCriticos && count($mensajesAdvertencia) > 0) {
            $mensaje .= ' Se eliminaron los grupos y fixture debido a los cambios en la configuración. Los equipos se mantuvieron.';
        }

        return redirect()
            ->route('torneos.show', $torneo)
            ->with('success', $mensaje);
    }

    /**
     * Vista TV del torneo (pública, pantalla completa)
     */
    public function showTv($id)
    {
        $torneo = Torneo::findOrFail($id);

        if (! in_array($torneo->estado, ['activo', 'en_curso', 'finalizado'])) {
            abort(404);
        }

        $torneo->load(['deporte', 'complejo', 'formato']);

        $partidosFinalizados = $torneo->partidos()
            ->with(['equipo1', 'equipo2', 'equipoGanador', 'cancha', 'grupo', 'llave'])
            ->where('estado', 'finalizado')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $proximosPartidos = $torneo->partidos()
            ->with(['equipo1', 'equipo2', 'cancha', 'grupo', 'llave'])
            ->where('estado', '!=', 'finalizado')
            ->whereNotNull('fecha_hora')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $esFutbol = $torneo->deporte->esFutbol();

        $campeones = collect();
        if ($torneo->estado === 'finalizado') {
            $categorias = $torneo->categorias()->withPivot('campeon_id')->get();
            foreach ($categorias as $categoria) {
                if ($categoria->pivot->campeon_id) {
                    $equipo = \App\Models\Equipo::with(['jugadores' => function ($q) {
                        $q->orderBy('equipo_jugador.orden');
                    }])->find($categoria->pivot->campeon_id);
                    if ($equipo) {
                        $campeones->push([
                            'categoria' => $categoria,
                            'equipo'    => $equipo,
                        ]);
                    }
                }
            }
        }

        return view('torneos.tv', compact('torneo', 'partidosFinalizados', 'proximosPartidos', 'esFutbol', 'campeones'));
    }

    /**
     * Vista pública del torneo (sin autenticación)
     */
    public function showPublic($id)
    {
        $torneo = Torneo::findOrFail($id);

        // Cargar relaciones básicas
        $torneo->load([
            'deporte',
            'complejo',
            'organizador',
            'formato',
            'categorias',
            'equipos.categoria',
            'grupos.equipos.jugadores',
            'grupos.categoria',
            'llaves.equipo1',
            'llaves.equipo2',
            'llaves.partido.cancha.complejo',
            'llaves.partido.juegos',
            'llaves.categoria',
        ]);

        // Cargar partidos manualmente ya que partidos() no es una relación tradicional
        $partidos = $torneo->partidos()
            ->with([
                'equipo1.jugadores',
                'equipo1.categoria',
                'equipo2.jugadores',
                'equipo2.categoria',
                'grupo.categoria',
                'cancha',
                'juegos',
            ])
            ->get();

        // Asignar partidos al torneo como una colección
        $torneo->setRelation('partidos', $partidos);

        // Solo mostrar torneos activos, en curso o finalizados públicamente
        if (! in_array($torneo->estado, ['activo', 'en_curso', 'finalizado'])) {
            abort(404);
        }

        // Partidos por grupo
        $partidosPorGrupo = $torneo->partidos->groupBy('grupo.nombre');

        // Partidos por fecha
        $partidosPorFecha = $torneo->partidos
            ->filter(fn ($p) => $p->fecha_hora)
            ->groupBy(fn ($p) => $p->fecha_hora->format('Y-m-d'))
            ->sortKeys();

        // Calcular tabla de posiciones
        $tablaPosiciones = $this->calcularTablaPosicionesPublic($torneo);

        // Organizar llaves por categoría
        $llavesPorCategoria = [];
        if ($torneo->llaves->isNotEmpty()) {
            foreach ($torneo->categorias as $categoria) {
                $llaves = $torneo->llaves->where('categoria_id', $categoria->id);
                if ($llaves->isNotEmpty()) {
                    // Obtener rondas únicas y ordenarlas correctamente
                    $rondasUnicas = $llaves->pluck('ronda')->unique();

                    // Orden correcto de rondas (de mayor a menor número de equipos)
                    $ordenRondas = [
                        'Dieciseisavos de Final' => 1,
                        'Octavos de Final' => 2,
                        'Cuartos de Final' => 3,
                        'Semifinal' => 4,
                        'Final' => 5,
                    ];

                    // Ordenar las rondas según el orden definido
                    $rondasOrdenadas = $rondasUnicas->sort(function ($a, $b) use ($ordenRondas) {
                        $ordenA = $ordenRondas[$a] ?? 999;
                        $ordenB = $ordenRondas[$b] ?? 999;

                        return $ordenA <=> $ordenB;
                    })->values()->toArray();

                    $llavesPorCategoria[$categoria->id] = [
                        'categoria' => $categoria,
                        'llaves_por_ronda' => $llaves->groupBy('ronda'),
                        'rondas' => $rondasOrdenadas,
                    ];
                }
            }
        }

        // Si el usuario autenticado es jugador, pasar sus equipoIds para resaltarlos
        $misEquipoIds = collect();
        $yaInscripto = false;
        if (auth()->check() && auth()->user()->hasRole('Jugador')) {
            $jugador = auth()->user()->jugador;
            if ($jugador) {
                $misEquipoIds = $jugador->equipos()
                    ->whereIn('equipos.torneo_id', [$torneo->id])
                    ->pluck('equipos.id');

                $yaInscripto = \App\Models\InscripcionEquipo::where('torneo_id', $torneo->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->where(function ($q) use ($jugador) {
                        $q->where('lider_jugador_id', $jugador->id)
                            ->orWhereHas('invitaciones', function ($q2) use ($jugador) {
                                $q2->where('jugador_id', $jugador->id)->where('estado', 'aceptada');
                            });
                    })
                    ->exists();
            }
        }

        return view('torneos.public', compact(
            'torneo',
            'partidosPorGrupo',
            'partidosPorFecha',
            'tablaPosiciones',
            'llavesPorCategoria',
            'misEquipoIds',
            'yaInscripto'
        ));
    }

    /**
     * Calcular tabla de posiciones para vista pública (soporta grupos y Liga)
     *
     * Optimizado con filtrado en memoria para evitar queries repetidas
     */
    private function calcularTablaPosicionesPublic(Torneo $torneo)
    {
        $tablaPosiciones = [];

        // Verificar si el torneo tiene grupos o es Liga
        $tieneGrupos = $torneo->formato && $torneo->formato->tiene_grupos;

        // ✅ OPTIMIZACIÓN: Filtrar partidos finalizados una sola vez
        $partidosFinalizados = $torneo->partidos->where('estado', 'finalizado');

        if ($tieneGrupos) {
            // ✅ OPTIMIZACIÓN: Agrupar partidos finalizados por grupo_id para acceso rápido
            $partidosPorGrupo = $partidosFinalizados->groupBy('grupo_id');

            // Calcular posiciones por grupos
            foreach ($torneo->grupos as $grupo) {
                $posiciones = [];
                // Obtener partidos del grupo de la colección agrupada
                $partidosDelGrupo = $partidosPorGrupo->get($grupo->id, collect());

                foreach ($grupo->equipos as $equipo) {
                    // ✅ OPTIMIZACIÓN: Filtrar en memoria en lugar de re-filtrar toda la colección
                    $partidosEquipo = $partidosDelGrupo->filter(function ($partido) use ($equipo) {
                        return $partido->equipo1_id === $equipo->id || $partido->equipo2_id === $equipo->id;
                    });

                    $stats = $this->calcularEstadisticasEquipoPublic($equipo, $partidosEquipo);
                    $posiciones[] = $stats;
                }

                usort($posiciones, function ($a, $b) {
                    if ($a['puntos'] !== $b['puntos']) {
                        return $b['puntos'] - $a['puntos'];
                    }
                    if ($a['diferencia_sets'] !== $b['diferencia_sets']) {
                        return $b['diferencia_sets'] - $a['diferencia_sets'];
                    }
                    if ($a['diferencia_puntos'] !== $b['diferencia_puntos']) {
                        return $b['diferencia_puntos'] - $a['diferencia_puntos'];
                    }

                    return $b['pf'] - $a['pf'];
                });

                $categoriaId = $grupo->categoria_id;
                $categoriaNombre = $grupo->categoria ? $grupo->categoria->nombre : 'Sin categoría';
                $clave = $categoriaId.'|'.$grupo->nombre;

                $cantidadClasifican = 0;
                foreach ($torneo->categorias as $categoria) {
                    if ($categoria->id == $categoriaId) {
                        $avanceGrupoId = $categoria->pivot->avance_grupos_id;
                        if ($avanceGrupoId) {
                            $avanceGrupo = \App\Models\AvanceGrupo::find($avanceGrupoId);
                            if ($avanceGrupo) {
                                $cantidadClasifican = $avanceGrupo->cantidad_avanza_directo ?? 0;
                            }
                        }
                        break;
                    }
                }

                $tablaPosiciones[$clave] = [
                    'categoria_id' => $categoriaId,
                    'categoria_nombre' => $categoriaNombre,
                    'grupo_nombre' => $grupo->nombre,
                    'posiciones' => $posiciones,
                    'cantidad_clasifican' => $cantidadClasifican,
                    'campeon_id' => null,
                ];
            }
        } else {
            // Calcular posiciones por categoría (Liga)
            foreach ($torneo->categorias as $categoria) {
                $equipos = $torneo->equipos->where('categoria_id', $categoria->id);

                if ($equipos->isEmpty()) {
                    continue;
                }

                $posiciones = [];

                foreach ($equipos as $equipo) {
                    // ✅ OPTIMIZACIÓN: Usar colección de partidos finalizados pre-filtrada
                    $partidosEquipo = $partidosFinalizados->filter(function ($partido) use ($equipo) {
                        return $partido->equipo1_id === $equipo->id || $partido->equipo2_id === $equipo->id;
                    });

                    $stats = $this->calcularEstadisticasEquipoPublic($equipo, $partidosEquipo);
                    $posiciones[] = $stats;
                }

                usort($posiciones, function ($a, $b) {
                    if ($a['puntos'] !== $b['puntos']) {
                        return $b['puntos'] - $a['puntos'];
                    }
                    if ($a['diferencia_sets'] !== $b['diferencia_sets']) {
                        return $b['diferencia_sets'] - $a['diferencia_sets'];
                    }
                    if ($a['diferencia_puntos'] !== $b['diferencia_puntos']) {
                        return $b['diferencia_puntos'] - $a['diferencia_puntos'];
                    }

                    return $b['pf'] - $a['pf'];
                });

                $campeonId = $categoria->pivot->campeon_id ?? null;
                $clave = $categoria->id.'|'.$categoria->nombre;

                $tablaPosiciones[$clave] = [
                    'categoria_id' => $categoria->id,
                    'categoria_nombre' => $categoria->nombre,
                    'grupo_nombre' => $categoria->nombre,
                    'posiciones' => $posiciones,
                    'cantidad_clasifican' => 0,
                    'campeon_id' => $campeonId,
                ];
            }
        }

        return $tablaPosiciones;
    }

    /**
     * Calcular estadísticas de un equipo para vista pública
     */
    private function calcularEstadisticasEquipoPublic($equipo, $partidosEquipo)
    {
        $pj = $partidosEquipo->count();
        $pg = 0;
        $pe = 0; // ✅ NUEVO: Partidos empatados
        $pp = 0;
        $pf = 0;
        $pc = 0;
        $sg = 0;
        $sp = 0;

        foreach ($partidosEquipo as $partido) {
            $esEquipo1 = $partido->equipo1_id === $equipo->id;

            if ($esEquipo1) {
                $pf += $partido->sets_equipo1;
                $pc += $partido->sets_equipo2;

                // ✅ ACTUALIZADO: Detectar empates
                if ($partido->equipo_ganador_id === null) {
                    $pe++; // Empate
                } elseif ($partido->equipo_ganador_id === $equipo->id) {
                    $pg++; // Ganó
                } else {
                    $pp++; // Perdió
                }
            } else {
                $pf += $partido->sets_equipo2;
                $pc += $partido->sets_equipo1;

                // ✅ ACTUALIZADO: Detectar empates
                if ($partido->equipo_ganador_id === null) {
                    $pe++; // Empate
                } elseif ($partido->equipo_ganador_id === $equipo->id) {
                    $pg++; // Ganó
                } else {
                    $pp++; // Perdió
                }
            }

            foreach ($partido->juegos as $juego) {
                if ($esEquipo1) {
                    if ($juego->juegos_equipo1 > $juego->juegos_equipo2) {
                        $sg++;
                    } else {
                        $sp++;
                    }
                } else {
                    if ($juego->juegos_equipo2 > $juego->juegos_equipo1) {
                        $sg++;
                    } else {
                        $sp++;
                    }
                }
            }
        }

        return [
            'equipo' => $equipo,
            'pj' => $pj,
            'pg' => $pg,
            'pe' => $pe, // ✅ NUEVO
            'pp' => $pp,
            'pf' => $pf,
            'pc' => $pc,
            'sg' => $sg,
            'sp' => $sp,
            'diferencia_sets' => $sg - $sp,
            'diferencia_puntos' => $pf - $pc,
            'diferencia' => $pf - $pc,
            'puntos' => ($pg * 3) + ($pe * 1), // ✅ ACTUALIZADO: Puntos incluyen empates
        ];
    }

    /**
     * Publicar torneo (cambiar de borrador a activo)
     */
    /**
     * Finalizar torneo cuando todas las categorías han terminado
     */
    public function finalizar(Request $request)
    {
        $validated = $request->validate([
            'torneo_id' => 'required|exists:torneos,id',
        ]);

        $torneo = Torneo::findOrFail($validated['torneo_id']);
        $this->authorize('update', $torneo);

        if ($torneo->estado === 'finalizado') {
            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('info', 'El torneo ya está finalizado.');
        }

        if ($torneo->estado !== 'en_curso') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes finalizar torneos en curso.');
        }

        // Cargar formato del torneo
        $torneo->load(['formato', 'categorias']);

        $todasFinalizadas = true;

        // Verificar finalización según tipo de torneo
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            // Formato con llaves (Fase de Grupos + Eliminación)
            foreach ($torneo->categorias as $categoria) {
                $llaveFinal = $torneo->llaves()
                    ->where('categoria_id', $categoria->id)
                    ->where('ronda', 'Final')
                    ->first();

                if (
                    ! $llaveFinal ||
                    ! $llaveFinal->partido ||
                    $llaveFinal->partido->estado !== 'finalizado' ||
                    ! $llaveFinal->partido->equipo_ganador_id
                ) {
                    $todasFinalizadas = false;
                    break;
                }
            }

            if (! $todasFinalizadas) {
                return redirect()
                    ->route('torneos.llaves.index', $torneo)
                    ->with('error', 'No se puede finalizar el torneo. Aún hay categorías sin finalizar.');
            }
        } else {
            // Liga: verificar que todos los partidos estén finalizados
            $partidosPendientes = $torneo->partidos()
                ->where('estado', '!=', 'finalizado')
                ->count();

            if ($partidosPendientes > 0) {
                return redirect()
                    ->route('torneos.fixture.index', $torneo)
                    ->with('error', "No se puede finalizar el torneo. Aún hay {$partidosPendientes} partido(s) pendiente(s).");
            }

            // Determinar campeones de Liga
            app('App\Http\Controllers\TorneoFixtureController')->determinarCampeonesLiga($torneo);
        }

        // Cambiar estado a finalizado
        $torneo->update(['estado' => 'finalizado']);

        return redirect()
            ->route('torneos.show', $torneo)
            ->with('success', '¡Torneo finalizado exitosamente! 🎉');
    }

    /**
     * Verificar si todos los partidos tienen resultado y finalizar automáticamente.
     * Llamado desde cargarResultado en TorneoFixtureController y TorneoLlaveController.
     */
    public static function intentarFinalizarAutomatico(Torneo $torneo): void
    {
        if ($torneo->estado !== 'en_curso') {
            return;
        }

        $torneo->load(['formato', 'categorias']);

        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            // Fase grupos + eliminación: verificar que todas las finales de llaves estén jugadas
            foreach ($torneo->categorias as $categoria) {
                $llaveFinal = $torneo->llaves()
                    ->where('categoria_id', $categoria->id)
                    ->where('ronda', 'Final')
                    ->first();

                if (! $llaveFinal || ! $llaveFinal->partido || $llaveFinal->partido->estado !== 'finalizado' || ! $llaveFinal->partido->equipo_ganador_id) {
                    return; // Aún falta al menos una final
                }
            }
        } else {
            // Liga o Eliminación Directa: todos los partidos deben estar finalizados
            $pendientes = $torneo->partidos()->where('estado', '!=', 'finalizado')->count();
            if ($pendientes > 0) {
                return;
            }

            // Determinar campeones de Liga
            if ($torneo->formato && $torneo->formato->esLiga()) {
                app('App\Http\Controllers\TorneoFixtureController')->determinarCampeonesLiga($torneo);
            }
        }

        $torneo->update(['estado' => 'finalizado']);
    }

    public function comenzar(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes comenzar torneos en estado activo.');
        }

        // Cargar categorías y formato
        $torneo->load(['categorias', 'formato']);

        // Validar que tenga equipos
        $equiposTotales = $torneo->equipos()->count();
        if ($equiposTotales === 0) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'No puedes publicar un torneo sin equipos. Agrega al menos un equipo.');
        }

        // Validar según tipo de formato
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            // Calcular cupos requeridos por categoría
            $cuposTotalesRequeridos = 0;
            $erroresCategoria = [];

            foreach ($torneo->categorias as $categoria) {
                $numeroGrupos = $categoria->pivot->numero_grupos;
                $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;

                if ($numeroGrupos && $tamanioGrupoId) {
                    $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                    if ($tamanioGrupo) {
                        $cuposCategoria = $numeroGrupos * $tamanioGrupo->tamanio;
                        $cuposTotalesRequeridos += $cuposCategoria;

                        // Verificar equipos de esta categoría
                        $equiposCategoria = $torneo->equipos()->where('categoria_id', $categoria->id)->count();
                        if ($equiposCategoria < $cuposCategoria) {
                            $erroresCategoria[] = "Categoría {$categoria->nombre}: {$equiposCategoria}/{$cuposCategoria} equipos";
                        }
                    }
                }
            }

            // Si hay errores por categoría, mostrarlos
            if (! empty($erroresCategoria)) {
                $mensajeError = "Faltan equipos en las siguientes categorías:\n".implode("\n", $erroresCategoria);

                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', $mensajeError);
            }

            // Validar equipos totales
            if ($equiposTotales < $cuposTotalesRequeridos) {
                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', "El torneo requiere {$cuposTotalesRequeridos} equipos para publicarse. Actualmente hay {$equiposTotales}.");
            }

            $gruposConfigurados = $torneo->grupos()->count() > 0;
            if (! $gruposConfigurados) {
                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', 'Debes configurar los grupos antes de comenzar el torneo.');
            }

            $tienePartidos = $torneo->partidos()->count() > 0;
            if (! $tienePartidos) {
                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', 'Debes generar el fixture antes de comenzar el torneo.');
            }
        } else {
            // Liga o Eliminación Directa: validar cupos_categoria
            $erroresCategoria = [];

            foreach ($torneo->categorias as $categoria) {
                $cuposCategoria = $categoria->pivot->cupos_categoria ?? 0;

                if ($cuposCategoria == 0) {
                    return redirect()
                        ->route('torneos.show', $torneo)
                        ->with('error', 'Debes configurar los cupos por categoría antes de publicar el torneo.');
                }

                // Verificar equipos de esta categoría
                $equiposCategoria = $torneo->equipos()->where('categoria_id', $categoria->id)->count();
                if ($equiposCategoria < $cuposCategoria) {
                    $erroresCategoria[] = "Categoría {$categoria->nombre}: {$equiposCategoria}/{$cuposCategoria} equipos";
                }
            }

            // Si hay errores por categoría, mostrarlos
            if (! empty($erroresCategoria)) {
                $mensajeError = "Faltan equipos en las siguientes categorías:\n".implode("\n", $erroresCategoria);

                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', $mensajeError);
            }

            // Validar que tenga fixture generado
            $tienePartidos = $torneo->partidos()->count() > 0;
            if (! $tienePartidos) {
                return redirect()
                    ->route('torneos.show', $torneo)
                    ->with('error', 'Debes generar el fixture antes de comenzar el torneo.');
            }
        }

        // Validar que todos los partidos tengan fecha/hora y cancha asignada
        $partidosSinProgramar = $torneo->partidos()
            ->where(function ($query) {
                $query->whereNull('fecha_hora')
                    ->orWhereNull('cancha_id');
            })
            ->count();

        if ($partidosSinProgramar > 0) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', "No se puede comenzar el torneo. Hay {$partidosSinProgramar} partido(s) sin fecha/hora o cancha asignada. Completa la programación del fixture.");
        }

        // Cambiar estado a en_curso
        $torneo->update(['estado' => 'en_curso']);

        return redirect()
            ->route('torneos.show', $torneo)
            ->with('success', '¡Torneo comenzado! Ya se pueden cargar resultados de los partidos.');
    }

    /**
     * Cancelar torneo (activo o en_curso)
     */
    public function cancelar(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if (in_array($torneo->estado, ['finalizado', 'cancelado'])) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Este torneo no puede ser cancelado.');
        }

        $torneo->update(['estado' => 'cancelado']);

        return redirect()
            ->route('torneos.index')
            ->with('success', 'El torneo "'.$torneo->nombre.'" ha sido cancelado.');
    }

    /**
     * Eliminar torneo
     */
    public function destroy(Torneo $torneo)
    {
        if (! in_array($torneo->estado, ['borrador', 'cancelado'])) {
            return redirect()
                ->route('torneos.index')
                ->with('error', 'Solo puedes eliminar torneos en borrador o cancelados.');
        }

        $nombre = $torneo->nombre;

        // Eliminar imagen si existe
        if ($torneo->imagen_banner) {
            Storage::disk('public')->delete($torneo->imagen_banner);
        }

        // Eliminar PDF de reglamento si existe
        if ($torneo->reglamento_pdf) {
            Storage::disk('public')->delete($torneo->reglamento_pdf);
        }

        $torneo->delete();

        return redirect()
            ->route('torneos.index')
            ->with('success', "Torneo '{$nombre}' eliminado exitosamente.");
    }

    /**
     * Guardar como borrador desde cualquier paso
     */
    public function saveDraft(Request $request, ?Torneo $torneo = null)
    {
        if ($torneo) {
            // Actualizar torneo existente
            $torneo->update($request->all());
            $message = 'Borrador guardado exitosamente.';
            $route = route('torneos.create-step2', $torneo);
        } else {
            // Crear nuevo borrador desde paso 1
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'deporte_id' => 'required|exists:deportes,id',
                'complejo_id' => 'required|exists:complejos_deportivos,id',
            ]);

            $validated['organizador_id'] = Auth::id();
            $validated['estado'] = 'borrador';

            $torneo = Torneo::create($validated);
            $message = 'Borrador guardado. Puedes continuar después.';
            $route = route('torneos.index');
        }

        return redirect($route)->with('success', $message);
    }
}
