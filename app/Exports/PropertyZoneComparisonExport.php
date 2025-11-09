<?php

namespace App\Exports;

use App\Models\Property;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyZoneComparisonExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection<int, \App\Models\Property>
     */
    public function collection(): Collection
    {
        return Property::query()
            ->select('city')
            ->whereNotNull('city')
            ->selectRaw('COUNT(*) as total_properties')
            ->selectRaw("SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_count")
            ->selectRaw("SUM(CASE WHEN status = 'rented' THEN 1 ELSE 0 END) as rented_count")
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count")
            ->selectRaw('AVG(price) as average_price')
            ->groupBy('city')
            ->orderBy('city')
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->city,
            (int) $row->total_properties,
            (int) $row->sold_count,
            (int) $row->rented_count,
            (int) $row->available_count,
            number_format((float) $row->average_price, 2, '.', ''),
        ];
    }

    public function headings(): array
    {
        return [
            'Ciudad',
            'Total de Propiedades',
            'Vendidas',
            'Rentadas',
            'Disponibles',
            'Precio Promedio',
        ];
    }
}
