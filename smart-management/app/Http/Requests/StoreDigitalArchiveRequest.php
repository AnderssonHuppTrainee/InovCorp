<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDigitalArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'], // 50MB max
            'description' => ['nullable', 'string'],
            'document_type' => ['required', 'string', 'max:100'],
            'archivable_type' => ['nullable', 'string', 'max:255'],
            'archivable_id' => ['nullable', 'integer'],
            'is_public' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'file' => 'ficheiro',
            'description' => 'descrição',
            'document_type' => 'tipo de documento',
            'archivable_type' => 'tipo de entidade',
            'archivable_id' => 'entidade',
            'is_public' => 'público',
            'expires_at' => 'data de expiração',
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'O ficheiro não pode exceder 50MB.',
            'expires_at.after' => 'A data de expiração deve ser posterior a hoje.',
        ];
    }
}


















