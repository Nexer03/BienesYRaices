@extends('layouts.app')

@section('title','Mis estadísticas')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Estadísticas del agente</h3>

  {{-- Filtros --}}
  <form method="GET" class="mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="from" value="{{ optional($from)->toDateString() }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="to" value="{{ optional($to)->toDateString() }}" class="form-control">
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary w-100" type="submit">Aplicar</button>
      </div>
      <div class="col-md-3">
        <a href="{{ route('agent.analytics') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
      </div>
    </div>
  </form>

  {{-- Cards de propiedades y total de visitas --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted">Propiedades totales</div>
        <div class="fs-3 fw-bold">{{ $totalProps }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted">En venta</div>
        <div class="fs-3 fw-bold">{{ $saleProps }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted">En renta</div>
        <div class="fs-3 fw-bold">{{ $rentProps }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted">Visitas (90 días)</div>
        <div class="fs-3 fw-bold">{{ $visits['total'] }}</div>
      </div></div>
    </div>
  </div>

  {{-- Distribución por estado (badges) --}}
  <div class="row g-3 mb-4">
    @php
      $badges = [
        'pending'   => ['Pendientes','warning'],
        'confirmed' => ['Confirmadas','primary'],
        'completed' => ['Completadas','success'],
        'cancelled' => ['Canceladas','danger'],
      ];
    @endphp
    @foreach($badges as $key => [$label, $color])
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <span class="badge bg-{{ $color }} mb-2">{{ $label }}</span>
            <div class="fs-4 fw-semibold">{{ $visits[$key] }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Serie de visitas en el rango --}}
  <div class="card">
    <div class="card-body">
      <h5 class="card-title mb-3">
        @if(request('from') || request('to'))
          Visitas por día ({{ optional($from)->toDateString() }} — {{ optional($to)->toDateString() }})
        @else
          Visitas por día (últimos 30 días)
        @endif
      </h5>
      <canvas id="visitsChart" height="110"></canvas>
    </div>
  </div>

  {{-- Top 5 propiedades por visitas en el rango --}}
  <div class="card mt-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Top 5 propiedades por visitas</h5>
      @if($topProps->isEmpty())
        <div class="text-muted">No hay visitas en el rango seleccionado.</div>
      @else
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Propiedad</th>
                <th class="text-center">Visitas</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topProps as $row)
                <tr>
                  <td>{{ $row->property?->title ?? 'Propiedad #'.$row->property_id }}</td>
                  <td class="text-center">
                    <span class="badge bg-primary">{{ $row->total }}</span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('properties.edit', $row->property_id) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <a href="{{ route('properties.show', $row->property_id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    const ctx = document.getElementById('visitsChart');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: @json($labels),
        datasets: [{
          label: 'Visitas',
          data: @json($values),
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        interaction: { mode: 'nearest', intersect: false },
        scales: {
          x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
          y: { beginAtZero: true, precision: 0 }
        },
        plugins: { legend: { display: false } }
      }
    });
  })();
</script>
@endpush
