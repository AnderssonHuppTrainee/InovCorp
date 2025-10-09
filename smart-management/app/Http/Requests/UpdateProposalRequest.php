<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
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
            'proposal_date' => ['required', 'date'],
            'client_id' => ['required', 'exists:entities,id'],
            'validity_date' => ['required', 'date', 'after:proposal_date'],
            'status' => ['required', 'in:draft,closed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_id' => ['required', 'exists:articles,id'],
            'items.*.supplier_id' => ['nullable', 'exists:entities,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'proposal_date' => 'data da proposta',
            'client_id' => 'cliente',
            'validity_date' => 'data de validade',
            'status' => 'estado',
            'items' => 'itens',
            'items.*.article_id' => 'artigo',
            'items.*.supplier_id' => 'fornecedor',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
            'items.*.cost_price' => 'preço de custo',
            'items.*.notes' => 'notas',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'A proposta deve ter pelo menos um item.',
            'items.min' => 'A proposta deve ter pelo menos um item.',
        ];
    }
}


