<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Notificación de Partido</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px;">
    <div
        style="max-width: 600px; margin: auto; background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color:#2c3e50;">¡Hola {{ $jugador['nombre'] }}! 👋</h2>
        <p>Te confirmamos tu próximo partido de {{ $partido['deporte'] . ' de '. $partido['instancia'] }}:</p>

        <ul style="list-style:none; padding:0; line-height:1.6;">
            <li>🏟️ <strong>Complejo:</strong> {{ $partido['complejo'] }}</li>
            <li>🎾 <strong>Cancha:</strong> {{ $partido['cancha'] }}</li>
            <li>📅 <strong>Fecha:</strong> {{ $partido['fecha'] }}</li>
            <li>🕒 <strong>Hora:</strong> {{ $partido['hora'] }}</li>
            <li>⚔️ <strong>Rival:</strong> {{ $partido['rival'] }}</li>
        </ul>

        <p>
            📍 <strong>Ubicación:</strong><br>
            {{ $partido['direccion'] }}<br>
        </p>

        <p>Te recomendamos llegar <strong>10 minutos antes</strong> para hacer la entrada en calor 💪</p>

        <p style="margin-top:30px;">¡Nos vemos en la cancha!<br><strong>{{ $partido['club'] }}</strong></p>
    </div>
</body>

</html>