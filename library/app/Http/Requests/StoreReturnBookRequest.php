<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnBookRequest extends FormRequest
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
            'return_photo' => 'required|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'return_photo.required' => 'É necessário enviar uma foto para a devolução.',
            'return_photo.image' => 'O arquivo deve ser uma imagem.',
            'return_photo.max' => 'A imagem não pode ter mais de 2MB.',
        ];
    }
}
