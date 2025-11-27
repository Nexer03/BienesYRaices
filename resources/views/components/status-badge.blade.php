@props(['status'])

@php
    $map = [
        'available' => ['label' => 'Disponible', 'class' => 'badge bg-success'],
        'pending'   => ['label' => 'Pendiente',  'class' => 'badge bg-warning text-dark'],
        'rented'    => ['label' => 'Rentada',    'class' => 'badge bg-info text-dark'],
        'sold'      => ['label' => 'Vendida',    'class' => 'badge bg-secondary'],
    ];
    $conf = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge bg-light text-dark'];

    // Fallback minimal por si no hay Bootstrap
    $fallback = 'inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold';
    $fallbackBg = match($status) {
        'available' => 'bg-green-100 text-green-800',
        'pending'   => 'bg-yellow-100 text-yellow-800',
        'rented'    => 'bg-cyan-100 text-cyan-800',
        'sold'      => 'bg-gray-200 text-gray-800',
        default     => 'bg-gray-100 text-gray-800',
    };
@endphp

<span class="{{ $conf['class'] ?? ($fallback.' '.$fallbackBg) }}">
  {{ $conf['label'] }}
</span>
