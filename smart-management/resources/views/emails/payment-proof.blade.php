<x-mail::message>
# Comprovativo de Pagamento

Estimado(a) {{ $supplier->name }},

Enviamos em anexo o comprovativo de pagamento da fatura **{{ $invoice->number }}**.

## Detalhes da Fatura

- **Número:** {{ $invoice->number }}
- **Data:** {{ $invoice->invoice_date->format('d/m/Y') }}
- **Valor:** {{ number_format($invoice->total_amount, 2, ',', '.') }} €
- **Data de Vencimento:** {{ $invoice->due_date->format('d/m/Y') }}

Qualquer questão, entre em contacto connosco.

Cumprimentos,<br>
{{ config('app.name') }}
</x-mail::message>
