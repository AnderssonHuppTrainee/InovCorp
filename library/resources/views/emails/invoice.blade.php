<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Fatura #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Olá {{ $order->user->name }}, aqui esta sua Fatura</h1>
            <p>Obrigado por sua compra!</p>
        </div>

        <div class="order-details">
            <h2>Detalhes do Pedido #{{ $order->id }}</h2>
            <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Total:</strong> €{{ number_format($order->total, 2, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        </div>

        <p>Em anexo você encontrará a fatura detalhada em formato PDF.</p>
        <p>Se tiver alguma dúvida sobre sua compra, entre em contato conosco.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Amazing Library. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>