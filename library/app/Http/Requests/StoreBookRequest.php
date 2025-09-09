<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Book;

class StoreBookRequest extends FormRequest
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
    public function rules()
    {
        return [
            'book_id' => [
                'required',
                'exists:books,id',
                function ($attribute, $value, $fail) {
                    // verifica se o livro está disponível
                    $book = Book::find($value);

                    if (!$book) {
                        $fail('O livro selecionado não existe.');
                        return; //valicao de id
                    }
                    if (!$book->isAvailable()) {
                        $fail('Este livro já está requisitado.');
                    }
                }
            ],
            'photo' => 'required|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'photo.required' => 'É necessário enviar uma foto para a requisição.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.max' => 'A imagem não pode ter mais de 2MB.',
        ];
    }
}
