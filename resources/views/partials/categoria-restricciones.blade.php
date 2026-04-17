@php
    $edadMin = $categoria->pivot->edad_minima ?? null;
    $edadMax = $categoria->pivot->edad_maxima ?? null;
    $genero  = $categoria->pivot->genero_permitido ?? null;
    $tieneRestricciones = $edadMin || $edadMax || $genero;
@endphp

<span class="inline-flex flex-wrap gap-1 items-center">
    @if(!$tieneRestricciones)
        <span class="inline-flex items-center text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full font-medium">
            Libre
        </span>
    @else
        @if($genero === 'masculino')
            <span class="inline-flex items-center text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full font-medium">
                ♂ Masculino
            </span>
        @elseif($genero === 'femenino')
            <span class="inline-flex items-center text-xs bg-pink-50 text-pink-700 border border-pink-200 px-2 py-0.5 rounded-full font-medium">
                ♀ Femenino
            </span>
        @elseif($genero === 'mixto')
            <span class="inline-flex items-center text-xs bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-full font-medium">
                ⚥ Mixto
            </span>
        @endif

        @if($edadMin && $edadMax)
            <span class="inline-flex items-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-medium">
                {{ $edadMin }}–{{ $edadMax }} años
            </span>
        @elseif($edadMin)
            <span class="inline-flex items-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-medium">
                +{{ $edadMin }} años
            </span>
        @elseif($edadMax)
            <span class="inline-flex items-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-medium">
                Hasta {{ $edadMax }} años
            </span>
        @endif
    @endif
</span>
