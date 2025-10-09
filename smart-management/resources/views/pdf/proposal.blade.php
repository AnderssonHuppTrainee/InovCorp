<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta {{ $proposal->number }}</title>
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
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2563eb;
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
            color: #2563eb;
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
            background: #2563eb;
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
            background: #2563eb;
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
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PROPOSTA COMERCIAL</h1>
        <div class="number">Nº {{ $proposal->number }}</div>
    </div>

    <!-- Informações -->
    <div class="info-section">
        <div class="info-column">
            <div class="info-box">
                <h3>Cliente</h3>
                <p><strong>{{ $proposal->client->name }}</strong></p>
                <p>NIF: {{ $proposal->client->tax_number }}</p>
                <p>{{ $proposal->client->address }}</p>
                <p>{{ $proposal->client->postal_code }} {{ $proposal->client->city }}</p>
                @if($proposal->client->phone)
                    <p>Tel: {{ $proposal->client->phone }}</p>
                @endif
                @if($proposal->client->email)
                    <p>Email: {{ $proposal->client->email }}</p>
                @endif
            </div>
        </div>
        
        <div class="info-column">
            <div class="info-box">
                <h3>Informações da Proposta</h3>
                <p><span class="info-label">Data:</span> {{ $proposal->proposal_date->format('d/m/Y') }}</p>
                <p><span class="info-label">Validade:</span> {{ $proposal->validity_date->format('d/m/Y') }}</p>
                <p><span class="info-label">Estado:</span> 
                    <strong>{{ $proposal->status === 'draft' ? 'Rascunho' : 'Fechado' }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Tabela de Artigos -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Ref.</th>
                <th style="width: 35%">Artigo</th>
                <th style="width: 20%">Fornecedor</th>
                <th class="text-center" style="width: 10%">Qtd.</th>
                <th class="text-right" style="width: 10%">Preço</th>
                <th class="text-right" style="width: 10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proposal->items as $item)
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
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} €</td>
                    <td class="text-right">
                        <strong>{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} €</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totais -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($proposal->total_amount, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-final">
                <td>TOTAL:</td>
                <td class="text-right">{{ number_format($proposal->total_amount, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>

    <!-- Notas -->
    <div class="notes">
        <strong>Observações:</strong><br>
        Esta proposta é válida até {{ $proposal->validity_date->format('d/m/Y') }}.<br>
        Todos os preços são apresentados em Euros (€).
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Proposta gerada automaticamente em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>


