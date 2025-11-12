<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierInvoiceRequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:entities,id'],
            'supplier_order_id' => ['nullable', 'exists:supplier_orders,id'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'payment_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'status' => ['required', 'in:pending_payment,paid'],
            'send_email' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'invoice_date' => 'data da fatura',
            'due_date' => 'data de vencimento',
            'supplier_id' => 'fornecedor',
            'supplier_order_id' => 'encomenda de fornecedor',
            'total_amount' => 'valor total',
            'document' => 'documento',
            'payment_proof' => 'comprovativo de pagamento',
            'status' => 'estado',
        ];
    }
}


















