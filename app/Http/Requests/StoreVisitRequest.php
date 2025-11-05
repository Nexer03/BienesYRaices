<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoOverlappingVisit;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Visit::class) ?? false;
    }

    public function rules(): array
    {
        $agentId = $this->user()->id;

        return [
            'property_id' => ['required', 'exists:properties,id'],
            'client_id'   => ['required', 'exists:users,id'],
            'visit_date'  => ['required', 'date', 'after:now', new NoOverlappingVisit($agentId)],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'visit_date.after' => 'La fecha/hora debe ser futura.',
        ];
    }
}
