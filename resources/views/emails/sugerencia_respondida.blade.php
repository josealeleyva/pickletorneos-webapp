<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu sugerencia ha sido respondida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .card {
            background-color: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .card-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .card-content {
            color: #333;
            font-size: 15px;
            line-height: 1.8;
        }
        .response-card {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .response-card .card-title {
            color: #10b981;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px 15px;
            }
            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✉️</div>
            <h1>¡Tu sugerencia ha sido respondida!</h1>
            <p>El equipo de Punto de Oro ha revisado tu mensaje</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                ¡Hola, {{ $sugerencia->user->nombre }}!
            </div>

            <div class="message">
                <p>Hemos revisado tu {{ $sugerencia->tipo === 'sugerencia' ? 'sugerencia' : 'mensaje' }} y queremos agradecerte por tomarte el tiempo de contactarnos. Tu opinión es muy importante para nosotros y nos ayuda a mejorar Punto de Oro cada día.</p>
            </div>

            <!-- Sugerencia Original -->
            <div class="card">
                <div class="card-title">
                    📋 Tu {{ $sugerencia->tipo }}
                </div>
                <div class="card-content">
                    <strong>Asunto:</strong> {{ $sugerencia->asunto }}<br><br>
                    <strong>Mensaje:</strong><br>
                    {{ $sugerencia->mensaje }}
                </div>
            </div>

            <!-- Respuesta del Admin -->
            <div class="response-card">
                <div class="card-title">
                    💬 Respuesta del equipo
                </div>
                <div class="card-content">
                    {{ $sugerencia->respuesta }}
                </div>
            </div>

            <div class="message">
                <p>Si tienes alguna otra pregunta o comentario, no dudes en contactarnos nuevamente. Estamos aquí para ayudarte.</p>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button">
                    Acceder a Mi Dashboard
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Punto de Oro</strong> - Plataforma de Gestión de Torneos Deportivos</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>Si necesitas ayuda adicional, envíanos una nueva sugerencia desde tu dashboard.</p>
            <p style="margin-top: 15px;">
                <a href="{{ route('landing') }}">Visitar sitio web</a> |
                <a href="{{ route('login') }}">Iniciar sesión</a>
            </p>
            <p style="margin-top: 10px; color: #9ca3af;">
                © {{ date('Y') }} Punto de Oro. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
