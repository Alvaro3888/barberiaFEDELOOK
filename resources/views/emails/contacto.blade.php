<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; max-width: 600px; margin: 0 auto; border-top: 4px solid #00a99d; }
        h2 { color: #111; margin-top: 0; }
        .field { margin-bottom: 12px; }
        .label { font-weight: bold; color: #555; }
        .box-mensaje { background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 3px solid #00a99d; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Nueva consulta desde la web</h2>
        <p>Se ha recibido un nuevo mensaje a través del formulario de contacto:</p>
        
        <div class="field">
            <span class="label">Nombre:</span> {{ $datos['nombre'] }}
        </div>
        <div class="field">
            <span class="label">Email de contacto:</span> {{ $datos['email'] }}
        </div>
        <div class="field">
            <span class="label">Teléfono:</span> {{ $datos['telefono'] ?? 'No especificado' }}
        </div>
        <div class="field">
            <span class="label">Sucursal de referencia:</span> {{ $sucursalNombre }}
        </div>

        <div class="box-mensaje">
            <span class="label">Mensaje:</span><br>
            <p style="white-space: pre-line; margin-bottom: 0;">{{ $datos['mensaje'] }}</p>
        </div>
    </div>
</body>
</html>