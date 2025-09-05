<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Minha Área</h1>
                <p class="text-sm text-base-content/70">Gerencie seus pedidos de livros</p>
            </div>

            @auth
                <a href="{{ route('public.books.index') }}" class="btn btn-primary text-white gap-2">
                    <i class="fas fa-book"></i>
                    Ver livros
                </a>
            @endauth
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Total de Pedidos -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-primary/10 p-3 text-primary">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Total de Pedidos</h2>
                            <p class="text-2xl font-bold">{{ $orders->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedidos Pagos -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-success/10 p-3 text-success">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Pedidos Pagos</h2>
                            <p class="text-2xl font-bold">{{ $orders->where('status', 'paid')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedidos Pendentes -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-warning/10 p-3 text-warning">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Pedidos Pendentes</h2>
                            <p class="text-2xl font-bold">{{ $orders->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Histórico de Compras</h2>

                @if($orders->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <i class="fas fa-info-circle mr-2"></i>
                        Você ainda não fez nenhuma compra.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-50">
                                <tr class="hover">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pedido #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Itens
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            #{{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @foreach($order->items as $item)
                                                <div class="flex items-center mb-2">
                                                    @if($item->book->capa)
                                                        <img src="{{ asset('storage/' . $item->book->capa) }}"
                                                            alt="{{ $item->book->name }}" class="w-12 h-17 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/48x72" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $item->book->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            €{{ number_format($item->price, 2, ',', '.') }} × {{ $item->quantity }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            €{{ number_format($order->total, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="badge badge-md gap-1 text-white
                                                     @if($order->status === 'paid') badge-success
                                                      @elseif($order->status === 'pending') badge-warning
                                                       @elseif($order->status === 'shipped') badge-info
                                                     @elseif($order->status === 'delivered') badge-success
                                                       @elseif($order->status === 'failed') badge-error
                                                      @elseif($order->status === 'cancelled') badge-neutral
                                                     @else badge-neutral 
                                                      @endif">
                                                @if($order->status === 'paid')
                                                    <i class="fas fa-check-circle"></i> Pago
                                                @elseif($order->status === 'pending')
                                                    <i class="fas fa-clock"></i> Pendente
                                                @elseif($order->status === 'shipped')
                                                    <i class="fas fa-truck"></i> Enviado
                                                @elseif($order->status === 'delivered')
                                                    <i class="fas fa-check-circle"></i> Entregue
                                                @elseif($order->status === 'failed')
                                                    <i class="fas fa-times-circle"></i> Falhou
                                                @elseif($order->status === 'cancelled')
                                                    <i class="fas fa-ban"></i> Cancelado
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status === 'paid')
                                                <a href="{{ route('orders.invoice', $order) }}"
                                                    class="btn btn-sm btn-primary text-white mb-2">
                                                    <i class="fas fa-download mr-1"></i> Imprimir Fatura
                                                </a>
                                            @endif

                                            @if($order->status === 'pending')
                                                <form action="{{ route('orders.index', $order) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-error text-white"
                                                        onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                                        <i class="fas fa-times mr-1"></i> Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>