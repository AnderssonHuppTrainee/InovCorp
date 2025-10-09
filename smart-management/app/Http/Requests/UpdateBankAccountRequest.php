<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'account_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bank_accounts', 'account_number')->ignore($this->bank_account),
            ],
            'iban' => ['nullable', 'string', 'max:34'],
            'swift' => ['nullable', 'string', 'max:11'],
            'bank_name' => ['required', 'string', 'max:255'],
            'balance' => ['nullable', 'numeric'],
            'currency' => ['required', 'string', 'max:3'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'account_number' => 'número da conta',
            'iban' => 'IBAN',
            'swift' => 'SWIFT/BIC',
            'bank_name' => 'nome do banco',
            'balance' => 'saldo',
            'currency' => 'moeda',
            'is_active' => 'ativa',
        ];
    }
}
