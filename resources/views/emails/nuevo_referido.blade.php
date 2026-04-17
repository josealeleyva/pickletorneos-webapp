<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>¡Nuevo organizador referido!</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1f95a6; margin: 0;">¡Felicidades! 🎉</h1>
        </div>

        <!-- Saludo -->
        <h2 style="color:#2c3e50; margin-bottom: 20px;">¡Hola {{ $referidor->name }}! 👋</h2>

        <!-- Mensaje principal -->
        <p style="font-size: 16px; line-height: 1.6; color: #374151;">
            <strong>{{ $referido->name }} {{ $referido->apellido }}</strong> se registró con tu código de referido.
        </p>

        <!-- Info del referido -->
        <div style="background: #f3f4f6; border-left: 4px solid #1f95a6; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #374151;">
                <strong>📧 Email:</strong> {{ $referido->email }}<br>
                @if($referido->organizacion)
                <strong>🏢 Organización:</strong> {{ $referido->organizacion }}
                @endif
            </p>
        </div>

        <!-- Información del beneficio -->
        @php
            $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
            $porcentajeCreditoReferidor = \App\Models\ConfiguracionSistema::get('porcentaje_credito_referidor', 100);
            $montoCreditoReferidor = $precioTorneo * ($porcentajeCreditoReferidor / 100);
        @endphp
        <div style="background: #ecfdf5; border: 1px solid #10b981; padding: 20px; margin: 25px 0; border-radius: 8px;">
            <p style="margin: 0 0 10px 0; color: #065f46; font-size: 18px;">
                <strong>🎁 Tu beneficio:</strong>
            </p>
            <p style="margin: 0; color: #065f46; line-height: 1.6;">
                Recibirás <strong>{{ $porcentajeCreditoReferidor == 100 ? '1 torneo completamente GRATIS' : 'un crédito de $' . number_format($montoCreditoReferidor, 0, ',', '.') }}</strong> (valor ${{ number_format($montoCreditoReferidor, 0, ',', '.') }}) cuando {{ $referido->name }} cree y pague su primer torneo.
            </p>
        </div>

        <!-- Pasos siguientes -->
        <p style="font-size: 14px; color: #6b7280; line-height: 1.6; margin-top: 25px;">
            El beneficio se acreditará automáticamente cuando tu referido active su cuenta. Podrás ver el progreso de tus referidos en tu dashboard.
        </p>

        <!-- Botón de acción -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/referidos/dashboard') }}"
               style="display: inline-block; background: linear-gradient(to right, #1f95a6, #1f95a6); color: white; padding: 14px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 6px rgba(15, 107, 120, 0.3);">
                Ver mis referidos
            </a>
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="color: #6b7280; font-size: 14px; margin: 0;">
                ¡Gracias por ayudarnos a crecer! 🚀
            </p>
            <p style="color: #9ca3af; font-size: 12px; margin-top: 15px;">
                Saludos,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>

    </div>
</body>

</html>
