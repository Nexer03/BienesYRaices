@php($user = $criteria->user)
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f7fafc; color: #2d3748; }
        .container { max-width: 640px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 12px; }
        .header { border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px; }
        .property { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; }
        .cta { display: inline-block; padding: 12px 18px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; }
        .footer { margin-top: 24px; font-size: 12px; color: #718096; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Resumen de alertas para "{{ $criteria->name }}"</h2>
        <p>Hola {{ $user->name }}, encontramos {{ $properties->count() }} nuevas propiedades que coinciden con tus filtros.</p>
        <p>Canal: {{ ucfirst($channel) }} | Frecuencia: {{ $criteria->frequency }}</p>
    </div>

    @foreach($properties as $property)
        <div class="property">
            <h3>{{ $property->title }}</h3>
            <p style="margin: 4px 0;">Ciudad: {{ $property->city }} — ${{ number_format($property->price, 2, '.', ',') }}</p>
            <p style="margin: 4px 0;">{{ ucfirst($property->listing_type) }} • {{ $property->bedrooms }} hab / {{ $property->bathrooms }} baños</p>
            <a class="cta" href="{{ route('properties.show', $property) }}">Ver propiedad</a>
        </div>
    @endforeach

    <div class="footer">
        <p>Para pausar o modificar esta alerta visita tu panel de alertas.</p>
        <p>Si no deseas recibir mensajes en {{ $channel }}, usa este enlace de baja: {{ route('alerts.unsubscribe', [$criteria, $channel]) }}</p>
    </div>
</div>
</body>
</html>
