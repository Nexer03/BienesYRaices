<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoOverlappingVisit;

class UpdateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $visit = $this->route('visit');
        return $this->user()?->can('update', $visit) ?? false;
    }

    public function rules(): array
    {
        $agentId = $this->user()->id;
        $visit   = $this->route('visit');

        return [
            'property_id' => ['required', 'exists:properties,id'],
            'client_id'   => ['required', 'exists:users,id'],
            'visit_date'  => ['required', 'date', 'after:now', new NoOverlappingVisit($agentId, $visit->id)],
            'status'      => ['required', 'in:pending,confirmed,completed,cancelled'],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ];
    }
}
