<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'order_date' => ['required', 'date'],
            'client_id' => ['required', 'exists:entities,id'],
            'delivery_date' => ['nullable', 'date', 'after:order_date'],
            'status' => ['required', 'in:draft,closed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_id' => ['required', 'exists:articles,id'],
            'items.*.supplier_id' => ['nullable', 'exists:entities,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'order_date' => 'data da encomenda',
            'client_id' => 'cliente',
            'delivery_date' => 'data de entrega',
            'status' => 'estado',
            'items' => 'itens',
            'items.*.article_id' => 'artigo',
            'items.*.supplier_id' => 'fornecedor',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
            'items.*.notes' => 'notas',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'A encomenda deve ter pelo menos um item.',
            'items.min' => 'A encomenda deve ter pelo menos um item.',
        ];
    }
}
