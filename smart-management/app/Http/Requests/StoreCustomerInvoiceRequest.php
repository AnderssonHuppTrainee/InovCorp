<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'customer_id' => ['required', 'exists:entities,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,sent,partially_paid,paid,overdue,cancelled'],
        ];
    }

    public function attributes(): array
    {
        return [
            'invoice_date' => 'data da fatura',
            'due_date' => 'data de vencimento',
            'customer_id' => 'cliente',
            'order_id' => 'encomenda',
            'total_amount' => 'valor total',
            'notes' => 'observações',
            'status' => 'estado',
        ];
    }
}




