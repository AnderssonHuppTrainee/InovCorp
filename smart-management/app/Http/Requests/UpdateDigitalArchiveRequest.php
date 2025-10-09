<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDigitalArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_type' => ['required', 'string', 'max:100'],
            'is_public' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'document_type' => 'tipo de documento',
            'is_public' => 'público',
            'expires_at' => 'data de expiração',
        ];
    }

    public function messages(): array
    {
        return [
            'expires_at.after' => 'A data de expiração deve ser posterior a hoje.',
        ];
    }
}


