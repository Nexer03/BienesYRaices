@extends('layouts.app')

@section('title', 'Registrar venta')

@section('content')
<div class="container py-4">

  <h3 class="mb-4">Registrar venta de propiedad</h3>

  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">

      <h5 class="mb-3">{{ $property->title }}</h5>

      <p><strong>Cliente:</strong> {{ $client->name }}</p>
      <p><strong>Fecha de visita:</strong> {{ $visit->visit_date->format('d/m/Y H:i') }}</p>

      <hr>

      {{--  SI NO HAY VENTA → MOSTRAR FORMULARIO --}}
      @if(!$sale)

      <form method="POST" action="{{ route('agent.visits.sale.store', $visit) }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Precio de venta (MXN)</label>
          <input type="number" step="0.01" name="sale_price"
                 class="form-control"
                 value="{{ old('sale_price', $property->price ?? '') }}"
                 required>
        </div>

        <div class="mb-3">
          <label class="form-label">Comisión aplicada</label>
          <input type="text" class="form-control"
                 value="{{ $commission ? $commission->percentage.'%' : '0%' }}"
                 disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Notas (opcional)</label>
          <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">Registrar venta</button>
      </form>

      @else
        {{--  SI YA EXISTE VENTA → MOSTRAR PAYPAL --}}
        <h5 class="mt-3 mb-2">Pagar comisión</h5>

        <p>
            Comisión a pagar:
            <strong>${{ number_format($sale->commission_amount, 2) }} MXN</strong>
        </p>

        {{-- Mostrar botón PayPal SOLO si la comisión NO ha sido pagada --}}
        @if (is_null($sale->commission_paid_at))
            <div id="paypal-button-container"></div>
        @else
            <p class="text-green-600 font-semibold">Comisión pagada correctamente.</p>
        @endif

      <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency={{ config('services.paypal.currency', 'MXN') }}&intent=capture&components=buttons"></script>

      <script>
      paypal.Buttons({
        createOrder: function() {
            return fetch("{{ route('agent.paypal.commission.create', $visit->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(function (data) {
                if (data?.id) {
                    return data.id;
                }

                throw new Error(data?.error || 'No se pudo crear la orden de PayPal.');
            });
        },

        onApprove: function(data) {
            return fetch("{{ route('agent.paypal.captureCommission', $visit->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: data.orderID,
                    sale_id: "{{ $sale->id }}"
                })
            })
            .then(res => res.json())
            .then(function(response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                    return;
                }

                if (response.success) {
                    alert("Pago de comisión registrado correctamente.");
                    window.location.reload();
                } else {
                    alert(response.error || "Ocurrió un error al registrar el pago.");
                }

            })
            .catch(function(error) {
                console.error(error);
                alert("Error de comunicación con el servidor.");
            });
        }

      }).render('#paypal-button-container');

      </script>


      @endif

    </div>
  </div>

</div>
@endsection
