<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>¡Ganaste un torneo gratis!</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <!-- Header con Celebración -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #10b981; margin: 0; font-size: 32px;">🎉 ¡FELICIDADES! 🎉</h1>
        </div>

        <!-- Saludo -->
        <h2 style="color:#2c3e50; margin-bottom: 20px;">¡Hola {{ $referidor->name }}! 👋</h2>

        <!-- Mensaje principal -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 25px; border-radius: 10px; margin-bottom: 25px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 24px;">¡Ganaste 1 Torneo GRATIS!</h3>
            @php
                $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
            @endphp
            <p style="margin: 0; font-size: 18px; opacity: 0.95;">
                Valor: <strong>${{ number_format($precioTorneo, 0, ',', '.') }}</strong>
            </p>
        </div>

        <!-- Información del referido que activó -->
        <p style="font-size: 16px; line-height: 1.6; color: #374151; margin-bottom: 20px;">
            <strong>{{ $referido->referido->name }} {{ $referido->referido->apellido }}</strong> pagó su primer torneo y activó su cuenta.
        </p>

        <div style="background: #f3f4f6; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #374151;">
                <strong>📧 Email del referido:</strong> {{ $referido->referido->email }}
            </p>
            @if($referido->referido->organizacion)
            <p style="margin: 0; color: #374151;">
                <strong>🏢 Organización:</strong> {{ $referido->referido->organizacion }}
            </p>
            @endif
        </div>

        <!-- Detalles del crédito -->
        <div style="background: #ecfdf5; border: 2px solid #10b981; padding: 20px; margin: 25px 0; border-radius: 8px;">
            <p style="margin: 0 0 10px 0; color: #065f46; font-size: 18px;">
                <strong>💰 Tu crédito está listo:</strong>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; color: #065f46; line-height: 1.8;">
                <li><strong>Monto:</strong> ${{ number_format($precioTorneo, 0, ',', '.') }} (1 torneo gratis)</li>
                <li><strong>Estado:</strong> Disponible para usar</li>
                <li><strong>Válido hasta:</strong> {{ now()->addMonths(12)->format('d/m/Y') }} (12 meses)</li>
            </ul>
        </div>

        <!-- Cómo usar el crédito -->
        <div style="background: #fffbeb; border: 1px solid #fbbf24; padding: 20px; margin: 25px 0; border-radius: 8px;">
            <p style="margin: 0 0 10px 0; color: #92400e; font-size: 16px;">
                <strong>ℹ️ ¿Cómo usar tu torneo gratis?</strong>
            </p>
            <ol style="margin: 10px 0; padding-left: 20px; color: #78350f; line-height: 1.8;">
                <li>Crea un nuevo torneo como siempre</li>
                <li>En la página de pago, verás la opción "Usar Crédito Gratis"</li>
                <li>Haz clic y tu torneo se activará sin costo</li>
            </ol>
        </div>

        <!-- Call to action -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/torneos/crear/paso-1') }}"
               style="display: inline-block; background: linear-gradient(to right, #10b981, #059669); color: white; padding: 16px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                Crear mi Torneo Gratis Ahora
            </a>
        </div>

        <!-- Recordatorio de seguir refiriendo -->
        <div style="background: #f9fafb; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <p style="margin: 0 0 10px 0; color: #374151; font-size: 14px;">
                <strong>💡 ¿Sabías que...?</strong>
            </p>
            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                Por cada organizador que refieras y se active, ganas otro torneo gratis. ¡Sigue compartiendo tu código de referido para seguir ganando!
            </p>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ url('/referidos/dashboard') }}"
                   style="color: #6366f1; text-decoration: none; font-weight: bold; font-size: 14px;">
                    Ver mi Dashboard de Referidos →
                </a>
            </div>
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
