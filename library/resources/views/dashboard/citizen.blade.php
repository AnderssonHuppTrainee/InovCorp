<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Minha Área</h1>
                <p class="text-sm text-base-content/70">Gerencie seus pedidos e requisições de livros</p>
            </div>

            @auth
                <a href="{{ route('public.books.index') }}" class="btn btn-primary text-white gap-2">
                    <i class="fas fa-book"></i>
                    Ver Livros
                </a>
            @endauth
        </div>
  
        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white flex items-center">
                <i class="fa fa-circle-check mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif


        @if(!auth()->user()->canRequestMoreBooks())
            <div class="alert alert-warning shadow-lg mb-6 text-white flex items-center">
                <i class="fa fa-circle-exclamation mr-3 text-xl"></i>
                <span>Você já possui 3 requisições ativas! Por favor, devolva um livro.</span>
            </div>
        @endif


        @if(auth()->user()->hasPendingFines())
            <div class="alert alert-error shadow-lg mb-6 text-white flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fa fa-circle-exclamation mr-3 text-xl"></i>
                    <span>Você já possui multas pendentes! Pague agora e regularize sua situação.</span>
                </div>
                <a href="{{ route('fines.index') }}" class="btn btn-sm btn-neutral ml-4 text-white">
                    Pagar
                </a>
            </div>
        @endif


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
                <h2 class="text-xl font-semibold mb-4">Minhas Requisições
                    <i class="fa fa-hand ml-2"></i>
                </h2>
                @if($bookRequests->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhuma requisição encontrada.</span>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Número</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Livro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data Requisição</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Devolução Prevista</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($bookRequests as $bookRequest)
                                    <tr>
                                        <td class="whitespace-nowrap ">
                                            {{ $bookRequest->number }}
                                        </td>
                                        <td class="whitespace-wrap">
                                            <div class="flex items-center">
                                                @if($bookRequest->book->cover_image)
                                                    <img src="{{  $bookRequest->book->cover_image }}" alt="{{ $bookRequest->book->name }}"
                                                        class="w-10 h-15 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/48x72" class="mr-3" />
                                                @endif
                                                <div>
                                                    <div class="font-medium">{{ $bookRequest->book->name }}</div>

                                                </div>

                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ $bookRequest->request_date->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ $bookRequest->expected_return_date->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <x-status-badge :status="$bookRequest->status" />
                                        </td>
                                        <td class="whitespace-nowrap">
                                            @if(auth()->user() && $bookRequest->status === 'approved')
                                                <a href="{{ route('returns.create', $bookRequest->id) }}" class="btn btn-sm btn-accent text-white">
                                                    <i class="fa fa-undo mr-2"></i>
                                                    Devolver
                                                </a>
                                            @elseif(auth()->user() && $bookRequest->status === 'pending')

                                                <form class="inline" action="{{ route('requests.cancel', $bookRequest) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-error ml-2 text-white"
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta requisição?')">
                                                        <i class="fas fa-times mr-1"></i> Cancelar

                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-center">-</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class=" mt-4 px-4 sm:px-6">
                        {{ $bookRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Histórico de Compras <i class="fa fa-shopping-cart ml-2"></i>
                </h2>

                @if($orders->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6 text-white">
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
                                                    @if($item->book->cover_image)
                                                        <img src="{{  $item->book->cover_image }}"
                                                            alt="{{ $item->book->name }}" class="w-12 h-17 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/48x72" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $item->book->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            €{{ number_format($item->price, 2, ',', '.') }} * {{ $item->quantity }}
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
                                            <x-status-badge :status="$order->status"></x-status-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status === 'paid')
                                                    <a href="{{ route('orders.invoice', $order) }}"
                                                        class="btn btn-sm btn-primary text-white mb-2">
                                                        <i class="fas fa-download mr-1"></i> Imprimir Fatura
                                                    </a>
                                                @elseif($order->status === 'pending')
                                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-error text-white"
                                                            onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">
                                                            <i class="fas fa-times mr-1"></i> Cancelar
                                                        </button>
                                                    </form>
                                                @else
                                                    <p class="text-center">-</p>
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