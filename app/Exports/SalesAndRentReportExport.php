<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesAndRentReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $filters,
        private readonly array $data,
    ) {
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->filters, $this->data),
            new SalesSheet($this->data['salesByAgent']),
            new RentalsSheet($this->data['rentalsByAgent']),
            new ZoneSheet($this->data['zoneComparison']),
        ];
    }
}

class SummarySheet implements FromArray, WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly array $data,
    ) {
    }

    public function array(): array
    {
        $formatDate = static fn (?\Carbon\Carbon $date) => $date?->format('Y-m-d') ?? 'No especificado';

        return [
            ['Filtros aplicados'],
            ['Desde', $formatDate($this->filters['from'])],
            ['Hasta', $formatDate($this->filters['to'])],
            [
                'Agente',
                $this->data['filterAgentName']
                    ?? ($this->filters['agent'] ?: 'Todos'),
            ],
            ['Ciudad', $this->filters['city'] ?: 'Todas'],
            [],
            ['Indicador', 'Valor'],
            ['Propiedades vendidas', $this->data['totalSoldProperties']],
            ['Valor total vendido', $this->formatCurrency($this->data['totalValueSold'])],
            ['Reservas confirmadas', $this->data['totalRentalReservations']],
            ['Ingresos por rentas', $this->formatCurrency($this->data['totalRentalRevenue'])],
            ['Comisiones ventas', $this->formatCurrency($this->data['salesCommissionTotal'])],
            ['Comisiones rentas', $this->formatCurrency($this->data['rentalCommissionTotal'])],
        ];
    }

    public function title(): string
    {
        return 'Resumen';
    }

    private function formatCurrency(float $value): string
    {
        return '$' . number_format($value, 2, '.', ',');
    }
}

class SalesSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $salesByAgent)
    {
    }

    public function collection(): Collection
    {
        return $this->salesByAgent;
    }

    public function map($row): array
    {
        return [
            $row->name,
            (int) $row->properties_count,
            (float) $row->total_sales_amount,
            $row->commission_rate !== null
                ? number_format((float) $row->commission_rate * 100, 2, '.', '') . '%'
                : 'N/D',
            (float) $row->commission_total,
        ];
    }

    public function headings(): array
    {
        return [
            'Agente',
            'Propiedades vendidas',
            'Total vendido (MXN)',
            'Tasa comisión',
            'Comisión calculada (MXN)',
        ];
    }

    public function title(): string
    {
        return 'Ventas';
    }
}

class RentalsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $rentalsByAgent)
    {
    }

    public function collection(): Collection
    {
        return $this->rentalsByAgent;
    }

    public function map($row): array
    {
        return [
            optional($row->agent)->name,
            (int) $row->total_reservations,
            (float) $row->total_revenue,
            $row->commission_rate !== null
                ? number_format((float) $row->commission_rate * 100, 2, '.', '') . '%'
                : 'N/D',
            (float) $row->commission_total,
        ];
    }

    public function headings(): array
    {
        return [
            'Agente',
            'Reservas confirmadas',
            'Ingresos por rentas (MXN)',
            'Tasa comisión',
            'Comisión calculada (MXN)',
        ];
    }

    public function title(): string
    {
        return 'Rentas';
    }
}

class ZoneSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $zones)
    {
    }

    public function collection(): Collection
    {
        return $this->zones;
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
            'Precio Promedio (MXN)',
        ];
    }

    public function title(): string
    {
        return 'Comparativa por zona';
    }
}
