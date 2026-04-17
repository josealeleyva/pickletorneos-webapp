<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planilla - {{ $equipo->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 15px;
            color: #333;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 18px;
            color: #4F46E5;
            margin-bottom: 0;
        }

        .info-torneo {
            background-color: #F3F4F6;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .info-torneo table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-torneo td {
            padding: 2px 0;
            font-size: 10px;
            line-height: 1.3;
        }

        .info-torneo td:first-child {
            font-weight: bold;
            color: #4B5563;
            width: 80px;
        }

        .equipo-nombre {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin: 8px 0;
            text-align: center;
            padding: 6px;
            background-color: #EEF2FF;
        }

        .integrantes-header {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin: 8px 0 5px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #D1D5DB;
        }

        .jugador {
            padding: 5px 8px;
            border-bottom: 1px dotted #E5E7EB;
            font-size: 11px;
            line-height: 1.4;
        }

        .jugador:last-child {
            border-bottom: 1px solid #4F46E5;
        }

        .jugador-numero {
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: #4F46E5;
            color: white;
            text-align: center;
            line-height: 20px;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 8px;
            font-size: 10px;
        }

        .jugador-nombre {
            font-weight: 600;
            color: #1F2937;
            font-size: 11px;
        }

        .firma-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .firma-box {
            border-top: 1px solid #4B5563;
            padding-top: 5px;
            text-align: center;
            margin-top: 30px;
        }

        .firma-label {
            font-size: 10px;
            color: #6B7280;
            font-weight: bold;
        }

        .total-jugadores {
            text-align: right;
            font-size: 10px;
            color: #6B7280;
            margin-top: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PLANILLA DE EQUIPO</h1>
    </div>

    <!-- Información del Torneo -->
    <div class="info-torneo">
        <table>
            <tr>
                <td>Torneo:</td>
                <td><strong>{{ $torneo->nombre }}</strong></td>
            </tr>
            <tr>
                <td>Organizador:</td>
                <td>{{ $torneo->organizador->name }} {{ $torneo->organizador->apellido }}</td>
            </tr>
            <tr>
                <td>Complejo:</td>
                <td>{{ $torneo->complejo->nombre }}</td>
            </tr>
            <tr>
                <td>Categoría:</td>
                <td>{{ $equipo->categoria->nombre }}</td>
            </tr>
        </table>
    </div>

    <!-- Nombre del Equipo -->
    <div class="equipo-nombre">
        {{ $equipo->nombre }}
    </div>

    <!-- Lista de Integrantes -->
    <div class="integrantes-header">
        Integrantes
    </div>

    @foreach($equipo->jugadores as $jugador)
        <div class="jugador">
            <span class="jugador-numero">{{ $loop->iteration }}</span>
            <span class="jugador-nombre">{{ $jugador->nombre_completo }}</span>
            @if($jugador->dni)
                <span style="color: #6B7280; margin-left: 6px; font-size: 9px;">DNI: {{ $jugador->dni }}</span>
            @endif
        </div>
    @endforeach

    <div class="total-jugadores">
        Total de jugadores: {{ $equipo->jugadores->count() }}
    </div>

    <!-- Sección de Firma -->
    <div class="firma-section">
        <div class="firma-box">
            <div class="firma-label">Firma del Responsable</div>
        </div>
    </div>
</body>
</html>
