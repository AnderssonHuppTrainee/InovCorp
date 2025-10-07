<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Core\Entity;

class UpdateEntityRequest extends FormRequest
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
    public function rules(Entity $entity): array
    {
        return [
            'tax_number' => [
                'required',
                'max:20',
                Rule::unique('entities')
                    ->ignore($entity->id)
                    ->whereNull('deleted_at')
            ],
            'name' => 'required|max:255',
            'types' => 'required|array|min:1',
            'types.*' => 'in:client,supplier',
            'address' => 'required|max:500',
            'postal_code' => 'required|regex:/^\d{4}-\d{3}$/',
            'city' => 'required|max:100',
            'country_id' => 'required|exists:countries,id',
            'phone' => 'nullable|max:20',
            'mobile' => 'nullable|max:20',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'gdpr_consent' => 'boolean',
            'observations' => 'nullable|max:1000',
            'status' => 'required|in:active,inactive'
        ];
    }
}
