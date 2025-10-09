<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1'],
            'shared_with' => ['nullable', 'array'],
            'shared_with.*' => ['exists:users,id'],
            'knowledge' => ['boolean'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'calendar_event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'calendar_action_id' => ['required', 'exists:calendar_actions,id'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
        ];
    }

    public function attributes(): array
    {
        return [
            'event_date' => 'data',
            'event_time' => 'hora',
            'duration' => 'duração',
            'shared_with' => 'partilhado com',
            'knowledge' => 'conhecimento',
            'entity_id' => 'entidade',
            'calendar_event_type_id' => 'tipo',
            'calendar_action_id' => 'ação',
            'description' => 'descrição',
            'status' => 'estado',
        ];
    }
}


