@extends('layouts.dashboard')

@section('title', 'Editar Complejo')
@section('page-title', 'Editar Complejo')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
        z-index: 0;
    }
    .leaflet-container {
        border: 2px solid #e5e7eb;
    }

    /* Autocompletado de direcciones */
    #sugerenciasDireccion {
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    #sugerenciasDireccion div {
        cursor: pointer;
        transition: background-color 0.15s;
    }
    #sugerenciasDireccion div:hover {
        background-color: #eef2ff;
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('complejos.index') }}" class="hover:text-brand-600">Complejos</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Editar: {{ $complejo->nombre }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <form action="{{ route('complejos.update', $complejo) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nombre del Complejo -->
            <div class="mb-6">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Complejo <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre', $complejo->nombre) }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Padel Center Rosario"
                >
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dirección con búsqueda en mapa integrada -->
            <div class="mb-6">
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                    Dirección <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="direccion"
                        name="direccion"
                        value="{{ old('direccion', $complejo->direccion) }}"
                        required
                        autocomplete="off"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('direccion') border-red-500 @enderror"
                        placeholder="Ej: Av. Pellegrini 1234, Rosario"
                    >
                    <button
                        type="button"
                        onclick="buscarDireccionEnMapa()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs rounded-md transition"
                        title="Buscar en el mapa"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>

                    <!-- Sugerencias de autocompletado -->
                    <div id="sugerenciasDireccion" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    💡 Escribe la dirección y haz clic en el icono para buscarla en el mapa
                </p>
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ubicación en el Mapa -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ubicación en el Mapa <span class="text-gray-400 text-xs">(Opcional)</span>
                </label>

                <p class="mb-3 text-xs text-gray-500">
                    💡 Haz clic en el mapa para seleccionar la ubicación exacta o arrastra el marcador
                </p>

                <!-- Mapa -->
                <div id="map"></div>

                <!-- Inputs ocultos para enviar las coordenadas -->
                <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud', $complejo->latitud) }}">
                <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud', $complejo->longitud) }}">

                @error('latitud')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('longitud')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contacto -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono <span class="text-gray-400 text-xs">(Opcional)</span>
                    </label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        value="{{ old('telefono', $complejo->telefono) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('telefono') border-red-500 @enderror"
                        placeholder="3416123456"
                    >
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-gray-400 text-xs">(Opcional)</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $complejo->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="contacto@complejo.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('complejos.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    let timeoutBusqueda;

    // Inicializar mapa
    document.addEventListener('DOMContentLoaded', function() {
        // Coordenadas por defecto: Santiago del Estero, Argentina
        const defaultLat = -27.791514;
        const defaultLng = -64.262273;

        // Obtener coordenadas existentes del complejo
        const existingLat = document.getElementById('latitud').value;
        const existingLng = document.getElementById('longitud').value;

        // Determinar coordenadas iniciales
        let initLat = existingLat ? parseFloat(existingLat) : defaultLat;
        let initLng = existingLng ? parseFloat(existingLng) : defaultLng;
        let initZoom = existingLat && existingLng ? 15 : 13;

        // Crear mapa
        map = L.map('map').setView([initLat, initLng], initZoom);

        // Agregar capa de tiles (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Si hay coordenadas existentes, agregar marcador
        if (existingLat && existingLng) {
            agregarMarcador(parseFloat(existingLat), parseFloat(existingLng));
        }

        // Click en el mapa para seleccionar ubicación
        map.on('click', function(e) {
            agregarMarcador(e.latlng.lat, e.latlng.lng);
        });

        // Autocompletado mientras el usuario escribe
        const inputDireccion = document.getElementById('direccion');
        inputDireccion.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            const query = this.value.trim();

            if (query.length < 3) {
                ocultarSugerencias();
                return;
            }

            // Debounce: esperar 500ms después de que el usuario deje de escribir
            timeoutBusqueda = setTimeout(() => {
                buscarSugerencias(query);
            }, 500);
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#direccion') && !e.target.closest('#sugerenciasDireccion')) {
                ocultarSugerencias();
            }
        });

        // Permitir Enter para buscar en el mapa
        inputDireccion.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarDireccionEnMapa();
            }
        });
    });

    // Buscar sugerencias de direcciones
    async function buscarSugerencias(query) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ar&addressdetails=1`
            );
            const data = await response.json();

            if (data.length > 0) {
                mostrarSugerencias(data);
            } else {
                ocultarSugerencias();
            }
        } catch (error) {
            console.error('Error al buscar sugerencias:', error);
        }
    }

    // Mostrar lista de sugerencias
    function mostrarSugerencias(sugerencias) {
        const contenedor = document.getElementById('sugerenciasDireccion');
        contenedor.innerHTML = '';

        sugerencias.forEach(sug => {
            const div = document.createElement('div');
            div.className = 'px-4 py-2 border-b border-gray-200 last:border-b-0';

            // Mostrar versión formateada en la lista
            const direccionCorta = formatearDireccion(sug);

            div.innerHTML = `
                <p class="text-sm text-gray-900 font-medium">${direccionCorta}</p>
                <p class="text-xs text-gray-500 mt-0.5">${sug.display_name}</p>
            `;
            div.onclick = () => seleccionarSugerencia(sug);
            contenedor.appendChild(div);
        });

        contenedor.classList.remove('hidden');
    }

    // Ocultar sugerencias
    function ocultarSugerencias() {
        document.getElementById('sugerenciasDireccion').classList.add('hidden');
    }

    // Seleccionar una sugerencia
    function seleccionarSugerencia(sugerencia) {
        // Formatear dirección para que sea más corta y legible
        const direccionFormateada = formatearDireccion(sugerencia);
        document.getElementById('direccion').value = direccionFormateada;
        ocultarSugerencias();

        // Ubicar en el mapa
        const lat = parseFloat(sugerencia.lat);
        const lng = parseFloat(sugerencia.lon);
        agregarMarcador(lat, lng);
        marker.bindPopup(`<b>${direccionFormateada}</b>`).openPopup();
    }

    // Formatear dirección para que sea más legible
    function formatearDireccion(sugerencia) {
        const address = sugerencia.address;

        if (!address) {
            return sugerencia.display_name;
        }

        let partes = [];

        // 1. Calle y número
        if (address.road) {
            let calle = address.road;
            if (address.house_number) {
                calle = `${calle} ${address.house_number}`;
            }
            partes.push(calle);
        }

        // 2. Ciudad/Localidad (en orden de prioridad)
        const ciudad = address.city || address.town || address.village || address.municipality;
        if (ciudad) {
            partes.push(ciudad);
        }

        // 3. Provincia (solo si es diferente a la ciudad)
        if (address.state && address.state !== ciudad) {
            partes.push(address.state);
        }

        // Si no hay suficiente información, usar el display_name original
        if (partes.length === 0) {
            return sugerencia.display_name;
        }

        return partes.join(', ');
    }

    // Buscar la dirección actual en el mapa (botón)
    async function buscarDireccionEnMapa() {
        const direccion = document.getElementById('direccion').value.trim();

        if (!direccion) {
            alert('Por favor ingresa una dirección');
            return;
        }

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}&limit=1&addressdetails=1`
            );
            const data = await response.json();

            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                agregarMarcador(lat, lng);

                const direccionFormateada = formatearDireccion(data[0]);
                marker.bindPopup(`<b>Ubicación encontrada:</b><br>${direccionFormateada}`).openPopup();
            } else {
                alert('No se encontró la dirección. Intenta ser más específico o haz clic directamente en el mapa.');
            }
        } catch (error) {
            console.error('Error al buscar dirección:', error);
            alert('Hubo un error al buscar. Por favor intenta nuevamente.');
        }
    }

    // Agregar o mover marcador
    function agregarMarcador(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            // FIX: Usar el icono por defecto de Leaflet con las rutas correctas
            const defaultIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            marker = L.marker([lat, lng], {
                draggable: true,
                icon: defaultIcon
            }).addTo(map);

            // Actualizar coordenadas al arrastrar
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
            });
        }

        map.setView([lat, lng], 15);
        actualizarCoordenadas(lat, lng);
    }

    // Actualizar campos de coordenadas
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latitud').value = lat.toFixed(6);
        document.getElementById('longitud').value = lng.toFixed(6);
    }
</script>
@endpush
