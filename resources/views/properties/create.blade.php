<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Propiedad</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h1>Crear Nueva Propiedad</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Precio</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Imágenes de la Propiedad</label>
            <input type="file" class="form-control" id="images" name="images[]" multiple>
        </div>

        <div class="mb-3">
            <h3>Selecciona las Amenidades</h3>
            @foreach ($amenityCategories as $category)
                <div class="mt-3">
                    <h5>{{ $category->name }}</h5>
                    <div class="row">
                        @foreach ($category->amenities as $amenity)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}">
                                    <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                        {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Guardar Propiedad</button>
    </form>

</body>
</html>
