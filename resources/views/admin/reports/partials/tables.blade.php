{{-- Ventas por agente --}}
<div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-sm font-semibold">Ventas por agente</h3>
      </div>
      <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
              <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                      <th class="px-4 py-2 text-left  font-medium text-gray-600 dark:text-gray-300">Agente</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Vendidas</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Total vendido</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">% Comisión</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Comisión</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">% Cargo</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Cargo</th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @forelse ($salesByAgent as $agent)
                      <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/40">
                          <td class="px-4 py-2 font-medium">{{ $agent->name }}</td>
                          <td class="px-4 py-2 text-right">{{ number_format($agent->properties_count) }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($agent->total_sales_amount ?? 0, 2, '.', ',') }}</td>
                          <td class="px-4 py-2 text-right">{{ $agent->commission_rate !== null ? number_format($agent->commission_rate, 2).'%' : '—' }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($agent->commission_total ?? 0, 2, '.', ',') }}</td>
                          <td class="px-4 py-2 text-right">{{ $agent->customer_rate !== null ? number_format($agent->customer_rate, 2).'%' : '—' }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($agent->customer_charge_total ?? 0, 2, '.', ',') }}</td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-300">
                              No hay ventas registradas para mostrar.
                          </td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
  </div>

  {{-- Rentas por agente --}}
  <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-sm font-semibold">Rentas cerradas por agente</h3>
      </div>
      <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
              <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                      <th class="px-4 py-2 text-left  font-medium text-gray-600 dark:text-gray-300">Agente</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Reservas</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Ingresos</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">% Comisión</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Comisión</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">% Cargo</th>
                      <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Cargo</th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @forelse ($rentalsByAgent as $row)
                      <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/40">
                          <td class="px-4 py-2 font-medium">{{ $row->agent->name }}</td>
                          <td class="px-4 py-2 text-right">{{ number_format($row->total_reservations) }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($row->total_revenue, 2, '.', ',') }}</td>
                          <td class="px-4 py-2 text-right">{{ $row->commission_rate !== null ? number_format($row->commission_rate, 2).'%' : '—' }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($row->commission_total ?? 0, 2, '.', ',') }}</td>
                          <td class="px-4 py-2 text-right">{{ $row->customer_rate !== null ? number_format($row->customer_rate, 2).'%' : '—' }}</td>
                          <td class="px-4 py-2 text-right">${{ number_format($row->customer_charge ?? 0, 2, '.', ',') }}</td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-300">
                              No hay reservas confirmadas para mostrar.
                          </td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
  </div>
</div>

{{-- Comparativa por zona --}}
<div class="mt-10 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
  <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
      <h3 class="text-sm font-semibold">Comparativa por zona</h3>
  </div>
  <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
          <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                  <th class="px-4 py-2 text-left  font-medium text-gray-600 dark:text-gray-300">Ciudad</th>
                  <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Total</th>
                  <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Disponibles</th>
                  <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Vendidas</th>
                  <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Rentadas</th>
                  <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Precio promedio</th>
              </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              @forelse ($zoneComparison as $zone)
                  <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/40">
                      <td class="px-4 py-2 font-medium">{{ $zone->city }}</td>
                      <td class="px-4 py-2 text-right">{{ number_format($zone->total_properties) }}</td>
                      <td class="px-4 py-2 text-right">{{ number_format($zone->available_count) }}</td>
                      <td class="px-4 py-2 text-right">{{ number_format($zone->sold_count) }}</td>
                      <td class="px-4 py-2 text-right">{{ number_format($zone->rented_count) }}</td>
                      <td class="px-4 py-2 text-right">${{ number_format($zone->average_price, 2, '.', ',') }}</td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-300">
                          No hay información suficiente para comparar zonas.
                      </td>
                  </tr>
              @endforelse
          </tbody>
      </table>
  </div>
</div>
