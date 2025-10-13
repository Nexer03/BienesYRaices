<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Propiedades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h1>Nuestras Propiedades</h1>
    <hr>

    @forelse ($properties as $property)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $property->title }}</h5>
                <p class="card-text">Precio: ${{ number_format($property->price, 2) }}</p>

                <h6>Imágenes:</h6>
                @if ($property->images->isNotEmpty())
                    <div>
                        @foreach ($property->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Imagen de la propiedad" width="150" class="img-thumbnail me-2">
                        @endforeach
                    </div>
                @else
                    <p>No hay imágenes para esta propiedad.</p>
                @endif

                <h6 class="mt-3">Amenidades:</h6>
                @if ($property->amenities->isNotEmpty())
                    <div>
                        @foreach ($property->amenities as $amenity)
                            <span class="badge bg-secondary me-1">{{ $amenity->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p>No se especificaron amenidades.</p>
                @endif

            </div>
        </div>
    @empty
        <p>Aún no hay propiedades registradas.</p>
    @endforelse

</body>
</html>
