<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Propiedades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <div class="d-flex justify-content-between align-items-center">
        <h1>Mis Propiedades</h1>
        <a href="{{ route('properties.create') }}" class="btn btn-primary">Añadir Nueva Propiedad</a>
    </div>

    <div class="my-3">
        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary {{ !request('type') ? 'active' : '' }}">Todas</a>
        <a href="{{ route('properties.index', ['type' => 'rent']) }}" class="btn btn-outline-secondary {{ request('type') == 'rent' ? 'active' : '' }}">Solo Renta</a>
        <a href="{{ route('properties.index', ['type' => 'sale']) }}" class="btn btn-outline-secondary {{ request('type') == 'sale' ? 'active' : '' }}">Solo Venta</a>
    </div>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse ($properties as $property)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5 class="card-title mb-0">
                            <a href="{{ route('properties.show', $property) }}" class="text-decoration-none text-dark">{{ $property->title }}</a>
                        </h5>
                        {{-- MUESTRA SI ES RENTA O VENTA --}}
                        @if($property->listing_type == 'rent')
                            <span class="badge bg-primary">Renta</span>
                        @else
                            <span class="badge bg-success">Venta</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                        <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('¿Estás seguro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </div>
                </div>

                <p class="card-text">Precio: ${{ number_format($property->price, 2) }}</p>

                <h6>Imágenes:</h6>
                @if ($property->images->isNotEmpty())
                    <div>
                        @foreach ($property->images as $image)
                            {{-- LÍNEA CORREGIDA --}}
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
        <div class="alert alert-info">No tienes propiedades que coincidan con el filtro seleccionado.</div>
    @endforelse

</body>
</html>
