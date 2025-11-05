@extends('layouts.app')

@section('title', 'Nueva visita')

@section('content')
<div class="container py-4">

  <h3 class="mb-3">Nueva visita</h3>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('agent.visits.store') }}" class="card">
    @csrf
    <div class="card-body">

      <div class="mb-3">
        <label class="form-label">Propiedad</label>
        <select name="property_id" class="form-select" required>
          <option value="">Selecciona una propiedad</option>
          @foreach($properties as $id => $title)
            <option value="{{ $id }}" @selected(old('property_id')==$id)>{{ $title }}</option>
          @endforeach
        </select>
        @error('property_id') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Cliente</label>
        <select name="client_id" class="form-select" required>
          <option value="">Selecciona un cliente</option>
          @foreach($clients as $id => $name)
            <option value="{{ $id }}" @selected(old('client_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
        @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha y hora</label>
        <input type="datetime-local" name="visit_date" class="form-control" value="{{ old('visit_date') }}" required>
        @error('visit_date') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Notas (opcional)</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

    </div>
    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="{{ url()->previous() }}">Cancelar</a>
    </div>
  </form>

</div>
@endsection
