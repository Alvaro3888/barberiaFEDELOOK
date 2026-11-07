<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Turno Reasignado</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h2 style="color: #0d9488; margin-top: 0;">¡Hola, {{ $turno->cliente->nombre ?? $turno->cliente->name }}!</h2>
        <p>Te escribimos de la barbería para informarte que, debido a una reprogramación operativa interna, tu turno ha sido reasignado a otro profesional para garantizar que recibas la atención a tiempo.</p>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #0d9488;">
            <p style="margin: 5px 0;"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</p>
            <p style="margin: 5px 0;"><strong>Hora:</strong> {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs</p>
            <p style="margin: 5px 0;"><strong>Nuevo Profesional Asignado:</strong> {{ $nuevoBarbero->nombre ?? $nuevoBarbero->name }}</p>
        </div>

        <p>Si tenés alguna duda o necesitás cancelar/modificar tu asistencia, podés ingresar a la plataforma o contactarnos.</p>
        <p style="margin-bottom: 0;">¡Te esperamos!</p>
    </div>
</body>
</html>