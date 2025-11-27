<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Visit;
use Carbon\Carbon;

class NoOverlappingVisit implements ValidationRule
{
    public function __construct(
        protected int $agentId,
        protected ?int $ignoreVisitId = null, // para update
        protected int $durationMinutes = 60
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($value);
        $end   = (clone $start)->addMinutes($this->durationMinutes);

        $query = Visit::where('agent_id', $this->agentId)
            ->when($this->ignoreVisitId, fn($q) => $q->where('id', '!=', $this->ignoreVisitId))
            ->where(function ($q) use ($start, $end) {
                // solape cuando (A.start < B.end) y (B.start < A.end)
                $q->where('visit_date', '<', $end)
                  ->where('visit_date', '>=', $start->copy()->subMinutes($this->durationMinutes));
            });

        if ($query->exists()) {
            $fail('Ya existe otra visita del agente que se cruza con este horario.');
        }
    }
}
