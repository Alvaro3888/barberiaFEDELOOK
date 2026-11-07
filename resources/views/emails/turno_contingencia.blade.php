<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización sobre tu turno</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; color: #1e293b; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #0f172a; margin-top: 0;">Hola, {{ $turno->cliente->nombre ?? $turno->cliente->name ?? 'Cliente' }}</h2>
        
        <p>Lamentamos informarte que, debido a un inconveniente operativo imprevisto con el profesional asignado, <strong>no pudimos reasignar automáticamente</strong> tu cita programada para:</p>
        
        <div style="background: #f8fafc; border-left: 4px solid #0d9488; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 8px 0;"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</p>
            <p style="margin: 0 0 8px 0;"><strong>Hora:</strong> {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs</p>
            <p style="margin: 0;"><strong>Servicio:</strong> 
                @if($turno->servicios->isNotEmpty())
                    {{ $turno->servicios->pluck('nombre')->implode(', ') }}
                @else
                    {{ $turno->servicio->nombre ?? 'Corte / Servicio' }}
                @endif
            </p>
        </div>

        <p>Tu turno ha quedado registrado bajo un protocolo de <strong>contingencia</strong>. Nuestro equipo administrativo se pondrá en contacto con vos a la brevedad (o podés responder a este mensaje/comunicarte con nosotros) para coordinar una <strong>reprogramación prioritaria</strong> de tu día y horario, o gestionar la devolución de tu pago web en caso de que corresponda.</p>

        <p style="margin-top: 30px;">Pedimos nuestras más sinceras disculpas por este inconveniente ajeno a vos.</p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #64748b; text-align: center; margin: 0;">Este es un mensaje automático de la sucursal. Por favor, no respondas directamente a este correo si no está configurado para recibir respuestas.</p>
    </div>
</body>
</html>