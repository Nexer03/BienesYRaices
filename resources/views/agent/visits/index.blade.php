@extends('layouts.app')

@section('title', 'Mis visitas')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Mis visitas</h3>
    <a class="btn btn-primary" href="{{ route('agent.visits.create') }}">
      <i class="bi bi-plus-lg me-1"></i> Nueva visita
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="min-width:160px;">Fecha / hora</th>
              <th>Propiedad</th>
              <th>Cliente</th>
              <th style="width:210px;">Estado</th>
              <th class="text-end" style="width:180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($visits as $visit)
              <tr>
                <td>
                  @if($visit->visit_date)
                    {{ $visit->visit_date->format('Y-m-d H:i') }}
                  @else
                    —
                  @endif
                </td>
                <td>{{ $visit->property?->title ?? '—' }}</td>
                <td>{{ $visit->client?->name ?? '—' }}</td>
                <td>
                  <form action="{{ route('agent.visits.status', $visit) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <div class="input-group input-group-sm" style="max-width:200px;">
                      <select name="status" class="form-select" onchange="this.form.submit()">
                        @foreach(['pending','confirmed','completed','cancelled'] as $st)
                          <option value="{{ $st }}" @selected($visit->status === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                      </select>
                      <button class="btn btn-outline-secondary" title="Guardar">
                        <i class="bi bi-check2"></i>
                      </button>
                    </div>
                  </form>
                </td>
                <td class="text-end">
                  <a href="{{ route('agent.visits.edit', $visit) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar
                  </a>
                  <form action="{{ route('agent.visits.destroy', $visit) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar esta visita?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i> Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No tienes visitas registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if(method_exists($visits, 'links'))
      <div class="card-footer">
        {{ $visits->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
