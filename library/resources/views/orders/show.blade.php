<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Voltar -->
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <!-- Resumo do Pedido -->
        <div class="card bg-base-100  mb-6 p-4 shadow-xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 ">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">
                        Pedido #{{ $order->id }}
                        <span class="badge ml-2 gap-1 text-white
                            @if($order->status === 'paid') badge-success
                            @elseif($order->status === 'pending') badge-warning
                            @elseif($order->status === 'shipped') badge-info
                            @elseif($order->status === 'delivered') badge-success
                            @elseif($order->status === 'failed') badge-error
                            @else badge-neutral @endif">
                            @if($order->status === 'paid') <i class="fas fa-check-circle"></i> Aprovado
                            @elseif($order->status === 'pending') <i class="fas fa-clock"></i> Pendente
                            @elseif($order->status === 'shipped') <i class="fas fa-truck"></i> Enviado
                            @elseif($order->status === 'delivered') <i class="fas fa-check-circle"></i> Entregue
                            @elseif($order->status === 'failed') <i class="fas fa-times-circle"></i> Rejeitado
                            @elseif($order->status === 'cancelled') <i class="fas fa-ban"></i> Cancelado
                            @else {{ ucfirst($order->status) }} @endif
                        </span>
                    </h1>
                    <p class="text-gray-500 mt-1">Criado em: {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
                <div class="text-xl font-semibold text-success">
                    Total: € {{ number_format($order->total, 2, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="card bg-base-100 shadow p-4">
                <h2 class="card-title mb-4">Cliente</h2>
                <div class="flex items-center gap-4">
                    <div class="avatar">
                        <div class="w-16 rounded-full">
                            <img src="{{ $order->user->profile_photo_path }}" alt="{{ $order->user->name }}">
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold">{{ $order->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow p-4">
                <h2 class="card-title mb-4">Morada de Entrega</h2>
                <p>{{ $order->address_line1 }}</p>
                @if($order->address_line2)
                <p>{{ $order->address_line2 }}</p>@endif
                <p>{{ $order->postal_code }} - {{ $order->city }}</p>
                <p>{{ $order->country }}</p>
            </div>
        </div>


        <div class="card bg-base-100 shadow-lg p-4 mb-6">
            <h2 class="card-title mb-4">Detalhes do Pedido</h2>
            <div class="overflow-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Capa</th>
                            <th>Título</th>
                            <th>Autor(es)</th>
                            <th>ISBN</th>
                            <th>Editora</th>
                            <th>Qtde</th>
                            <th>Preço Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="w-15 h-20">
                                    <img src="{{ $item->book->cover_image ?? 'https://via.placeholder.com/100x140?text=No+Image' }}"
                                        class="w-10 h-15 object-cover rounded">
                                </td>
                                <td>{{ $item->book->name }}</td>
                                <td>{{ $item->book->authors->pluck('name')->join(', ') }}</td>
                                <td>{{ $item->book->isbn ?? '-' }}</td>
                                <td>{{ $item->book->publisher->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td>€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-4">
            <div>
                <p class="text-gray-500">Quantidade de livros: <strong>{{ $order->items->sum('quantity') }}</strong></p>
                <p class="text-gray-500">Total do Pedido: <strong>€
                        {{ number_format($order->total, 2, ',', '.') }}</strong></p>
            </div>
            <div class="flex gap-2">

                <a href="{{ route('orders.invoice', $order) }}" class="btn btn-primary btn-sm text-white">
                    Imprimir Fatura
                </a>
            </div>
        </div>

    </div>
</x-app-layout>