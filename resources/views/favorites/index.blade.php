@extends('layouts.app')

@section('title','Mis favoritos')

@section('content')
<div class="container py-4">

  {{-- Header + Pills de filtro --}}
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
      <h3 class="mb-1">Mis propiedades favoritas</h3>
      <div class="text-muted">Guarda y organiza tus propiedades de interés</div>
    </div>

    <ul class="nav nav-pills bg-light p-1 rounded-3 border">
      <li class="nav-item">
        <a class="nav-link {{ $type === null ? 'active' : '' }}"
           href="{{ route('favorites.index') }}">
          Todos
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $type === 'sale' ? 'active' : '' }}"
           href="{{ route('favorites.index', ['type'=>'sale']) }}">
          Venta
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $type === 'rent' ? 'active' : '' }}"
           href="{{ route('favorites.index', ['type'=>'rent']) }}">
          Renta
        </a>
      </li>
    </ul>
  </div>

  {{-- Mensajes flash --}}
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Empty state --}}
  @if($props->isEmpty())
    <div class="text-center py-5 my-4 bg-light rounded-4 border">
      <div class="display-6 mb-2">☆</div>
      <h5 class="mb-1">Aún no tienes favoritos</h5>
      <p class="text-muted mb-0">Explora propiedades y agrégalas para verlas aquí.</p>
    </div>
  @else

    <div class="row g-4">
      @foreach($props as $p)
        @php
          $img = $p->images->first()->image_path ?? null;
          $isRent = $p->listing_type === 'rent';
        @endphp

        <div class="col-12 col-sm-6 col-lg-4" id="fav-card-{{ $p->id }}" data-fav-card>
          <div class="card h-100 shadow-sm border-0 overflow-hidden position-relative">

            {{-- Imagen / placeholder --}}
            <div class="ratio ratio-16x9 bg-light">
              @if($img)
                <img src="{{ asset('storage/'.$img) }}" class="object-fit-cover w-100 h-100" alt="Imagen de {{ $p->title }}">
              @else
                <div class="d-flex align-items-center justify-content-center text-muted">Sin imagen</div>
              @endif
            </div>

            {{-- Badge tipo (renta/venta) --}}
            <span class="position-absolute top-0 start-0 m-2 badge {{ $isRent ? 'bg-indigo' : 'bg-success' }} rounded-pill"
                  style="--bs-bg-opacity: .9;">
              {{ $isRent ? 'Renta' : 'Venta' }}
            </span>

            {{-- Botón quitar (abre modal) --}}
            <form method="POST"
                action="{{ route('favorites.destroy', $p) }}"
                class="position-absolute top-0 end-0 m-2 fav-remove-form"
                data-prop-id="{{ $p->id }}">
            @csrf
            @method('DELETE')
            <button type="button"
                    class="btn btn-light btn-sm border rounded-circle fav-remove-btn"
                    title="Quitar de favoritos">
                <i class="fa-solid fa-trash-can"></i>
            </button>
            </form>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1 text-truncate" title="{{ $p->title }}">{{ $p->title }}</h5>
              @if($p->location)
                <div class="text-muted small mb-2 text-truncate" title="{{ $p->location }}">
                  <i class="fa-solid fa-location-dot me-1"></i> {{ $p->location }}
                </div>
              @endif

              <div class="d-flex align-items-baseline gap-2 mb-3">
                <div class="fs-5 fw-bold">
                  ${{ number_format($p->price,0) }}
                </div>
                @if($isRent)
                  <div class="text-muted">/día</div>
                @endif
              </div>

              {{-- Meta rápida (si tienes) --}}
              <div class="d-flex flex-wrap gap-2 mb-3">
                @if(!is_null($p->bedrooms))
                  <span class="badge text-bg-light"><i class="fa-solid fa-bed me-1"></i> {{ $p->bedrooms }}</span>
                @endif
                @if(!is_null($p->bathrooms))
                  <span class="badge text-bg-light"><i class="fa-solid fa-bath me-1"></i> {{ $p->bathrooms }}</span>
                @endif
                @if(!empty($p->city))
                  <span class="badge text-bg-light"><i class="fa-solid fa-city me-1"></i> {{ $p->city }}</span>
                @endif
              </div>

              <div class="mt-auto d-flex gap-2">
                <a href="{{ route('properties.show', $p) }}" class="btn btn-primary w-100">Ver</a>

                @can('update', $p)
                    <a href="{{ route('properties.edit', $p) }}" class="btn btn-outline-secondary" title="Editar">
                    <i class="fa-solid fa-pen"></i>
                    </a>
                @endcan
                </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $props->links() }}
    </div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  document.querySelectorAll('.fav-remove-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const form = e.currentTarget.closest('.fav-remove-form');
      const url  = form.getAttribute('action');
      const id   = form.dataset.propId;
      const card = document.getElementById(`fav-card-${id}`);

      const res = await Swal.fire({
        title: 'Quitar de favoritos',
        text: 'Esta propiedad se eliminará de tu lista.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-secondary'
        }
      });

      if (!res.isConfirmed) return;

      try {
        const resp = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: new URLSearchParams({ _method: 'DELETE' })
        });

        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        // quitar la card del grid
        card?.remove();

        Swal.fire({
          icon: 'success',
          title: 'Eliminada',
          text: 'Se quitó de tus favoritos.',
          timer: 1400,
          showConfirmButton: false
        });

        // si ya no quedan cards, muestra empty state
        if (!document.querySelector('[data-fav-card]')) {
          const grid = document.querySelector('.row.g-4');
          if (grid) {
            grid.innerHTML = `
              <div class="text-center py-5 my-4 bg-light rounded-4 border w-100">
                <div class="display-6 mb-2">☆</div>
                <h5 class="mb-1">Aún no tienes favoritos</h5>
                <p class="text-muted mb-0">Explora propiedades y agrégalas para verlas aquí.</p>
              </div>`;
          }
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Ups',
          text: 'No pudimos quitarla. Intenta de nuevo.'
        });
      }
    });
  });
});
</script>

@endsection
