<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoremessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'body.required' => 'A mensagem não pode estar vazia.',
            'body.max' => 'A mensagem não pode ter mais de 1000 caracteres.',
            'body.min' => 'A mensagem deve ter pelo menos 1 caractere.',
        ];
    }
}
