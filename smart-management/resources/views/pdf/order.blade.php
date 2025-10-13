<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda {{ $order->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #16a34a;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header .number {
            font-size: 16px;
            color: #666;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .info-box h3 {
            color: #16a34a;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-box p {
            margin: 5px 0;
        }

        .info-label {
            color: #666;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table thead {
            background: #16a34a;
            color: white;
        }

        table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 30px;
            text-align: right;
        }

        .totals table {
            margin-left: auto;
            width: 300px;
        }

        .totals td {
            padding: 8px 12px;
        }

        .total-final {
            background: #16a34a;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .notes {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            padding: 12px;
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 60px;">
        <div style="text-align: right;">
            <h1>ENCOMENDA</h1>
            <div class="number">Nº {{ $order->number }}</div>
        </div>
    </div>



    <div class="info-section">
        <div class="info-column">
            <div class="info-box">
                <h3>Cliente</h3>
                <p><strong>{{ $order->client->name }}</strong></p>
                <p>NIF: {{ $order->client->tax_number }}</p>
                <p>{{ $order->client->address }}</p>
                <p>{{ $order->client->postal_code }} {{ $order->client->city }}</p>
                @if($order->client->phone)
                    <p>Tel: {{ $order->client->phone }}</p>
                @endif
                @if($order->client->email)
                    <p>Email: {{ $order->client->email }}</p>
                @endif
            </div>
        </div>

        <div class="info-column">
            <div class="info-box">
                <h3>Informações da Encomenda</h3>
                <p><span class="info-label">Data:</span> {{ $order->order_date->format('d/m/Y') }}</p>
                @if($order->delivery_date)
                    <p><span class="info-label">Entrega:</span> {{ $order->delivery_date->format('d/m/Y') }}</p>
                @endif
                @if($order->proposal)
                    <p><span class="info-label">Proposta:</span> {{ $order->proposal->number }}</p>
                @endif
                <p><span class="info-label">Estado:</span>
                    <strong>{{ $order->status === 'draft' ? 'Rascunho' : 'Fechado' }}</strong>
                </p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%">Ref.</th>
                <th style="width: 40%">Artigo</th>
                <th style="width: 20%">Fornecedor</th>
                <th class="text-center" style="width: 10%">Qtd.</th>
                <th class="text-right" style="width: 15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->article->reference }}</td>
                    <td>
                        <strong>{{ $item->article->name }}</strong>
                        @if($item->notes)
                            <br><small style="color: #666;">{{ $item->notes }}</small>
                        @endif
                    </td>
                    <td>{{ $item->supplier->name ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-right">
                        <strong>{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} €</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($order->total_amount, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-final">
                <td>TOTAL:</td>
                <td class="text-right">{{ number_format($order->total_amount, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>


    <div class="notes">
        <strong>Observações:</strong><br>
        @if($order->delivery_date)
            Data prevista de entrega: {{ $order->delivery_date->format('d/m/Y') }}.<br>
        @endif
        Todos os preços são apresentados em Euros (€).
    </div>


    <div class="footer">
        <p>Encomenda gerada automaticamente em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>

</html>