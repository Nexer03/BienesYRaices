<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Comisiones del Sistema</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-6xl mx-auto px-4 py-12 space-y-10">

    {{-- TÍTULO Y BOTÓN DE REFRESCAR --}}
    <div class="flex items-center justify-between">
      <h2 class="text-3xl font-semibold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-percent text-blue-600"></i> Comisiones del Sistema
      </h2>
      <a href="{{ route('admin.commissions.index') }}"
         class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
        <i class="fa-solid fa-rotate-right"></i> Actualizar
      </a>
    </div>

    {{-- ALERTA DE ÉXITO --}}
    @if (session('success'))
      <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
        {{ session('success') }}
      </div>
    @endif

    {{-- FORMULARIO NUEVA COMISIÓN --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Registrar nueva comisión</h3>

      <form method="POST" action="{{ route('admin.commissions.store') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-5">
        @csrf

        {{-- AGENTE --}}
        <div>
          <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Agente (opcional)</label>
          <select name="user_id" id="user_id"
                  class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Comisión general</option>
            @foreach ($agents as $agent)
              <option value="{{ $agent->id }}" @selected(old('user_id') == $agent->id)>
                {{ $agent->name }}
              </option>
            @endforeach
          </select>
          @error('user_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- TIPO --}}
        <div>
          <label for="listing_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de operación</label>
          <select name="listing_type" id="listing_type" required
                  class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="sale" @selected(old('listing_type') === 'sale')>Venta</option>
            <option value="rent" @selected(old('listing_type') === 'rent')>Renta</option>
            <option value="both" @selected(old('listing_type') === 'both')>Ambos</option>
          </select>
          @error('listing_type')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- COMISIÓN AGENTE --}}
        <div>
          <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">Comisión del agente (%)</label>
          <input type="number" step="0.01" min="0" max="100"
                 name="percentage" id="percentage" value="{{ old('percentage') }}" required
                 class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          @error('percentage')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        

        {{-- FECHA VIGENCIA --}}
        <div>
          <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-1">Vigencia desde</label>
          <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from') }}"
                 class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          @error('effective_from')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- NOTAS --}}
        <div class="md:col-span-3">
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas internas</label>
          <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                 placeholder="Ej. Comisión preferencial por rendimiento"
                 class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          @error('notes')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- BOTÓN --}}
        <div class="md:col-span-2 flex items-end">
          <button type="submit"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
          </button>
        </div>
      </form>
    </section>

    {{-- TABLA DE COMISIONES --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Comisiones registradas</h3>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tipo</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Agente</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Comisión agente</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vigente desde</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notas</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse ($commissions as $commission)
              <tr>
                <td class="px-4 py-3 text-sm text-gray-800">
                  {{ __(match($commission->listing_type) {
                      'sale' => 'Venta',
                      'rent' => 'Renta',
                      default => 'Ambos',
                  }) }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ $commission->agent?->name ?? 'General del sistema' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ number_format($commission->percentage, 2) }}%
                </td>
               
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ optional($commission->effective_from)->format('d/m/Y') ?? '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ $commission->notes ?: '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-right">
                  <div class="inline-flex gap-3">
                    <a href="{{ route('admin.commissions.edit', $commission) }}"
                       class="text-blue-600 hover:text-blue-700 font-medium">
                       <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form method="POST"
                          action="{{ route('admin.commissions.destroy', $commission) }}"
                          onsubmit="return confirm('¿Deseas eliminar esta comisión?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="text-red-600 hover:text-red-700 font-medium">
                              <i class="fa-solid fa-trash-can"></i> Eliminar
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                  Aún no hay comisiones configuradas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

</body>
</html>
