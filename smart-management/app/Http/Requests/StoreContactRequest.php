<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'exists:entities,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_role_id' => ['required', 'exists:contact_roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'gdpr_consent' => ['boolean'],
            'observations' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'entity_id' => 'entidade',
            'first_name' => 'nome',
            'last_name' => 'apelido',
            'contact_role_id' => 'função',
            'phone' => 'telefone',
            'mobile' => 'telemóvel',
            'email' => 'email',
            'gdpr_consent' => 'consentimento RGPD',
            'observations' => 'observações',
            'status' => 'estado',
        ];
    }
}


