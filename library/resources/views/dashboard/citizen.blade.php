<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Minha Área</h1>
                <p class="text-sm text-base-content/70">Gerencie suas requisições de livros</p>
            </div>

            @auth
                @if(auth()->user()->canRequestMoreBooks())
                    <a href="{{ route('public.books.index') }}" class="btn btn-primary text-white gap-2">
                        <i class="fas fa-plus"></i>
                        Nova Requisição
                    </a>
                @endif
            @endauth
        </div>
        <!--ALERTA DE COIMAS-->
        @if(auth()->user()->hasPendingFines())
            <div class="alert alert-error shadow-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2 text-white"></i>
                <div>
                    <h3 class="font-bold text-white">Você possui multas pendentes!</h3>
                    <div class="text-sm text-white">
                        Total em dívida: <strong>€
                            {{ number_format(auth()->user()->totalFines(), 2, ',', '.') }}</strong>
                    </div>
                </div>
                <a href="{{ route('fines.index') }}" class="btn btn-sm btn-light ml-auto">
                    <i class="fas fa-wallet mr-1"></i> Pagar Agora
                </a>
            </div>
        @elseif(!auth()->user()->canRequestMoreBooks())
            <div class="alert alert-warning shadow-lg mb-6">
                <i class="fas fa-exclamation-triangle mr-2 text-white"></i>
                <p class="font-bold text-white">Você atingiu o limite máximo de 3 livros requisitados simultaneamente.</p>
            </div>

        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Total de Requisições -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-primary/10 p-3 text-primary">
                            <i class="fas fa-book-open text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Total Requisições</h2>
                            <p class="text-2xl font-bold">{{ $stats['total_requests'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Em Andamento -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-info/10 p-3 text-info">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Em Andamento</h2>
                            <p class="text-2xl font-bold">{{ $stats['active_requests'] }}</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-error/10 p-3 text-error">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Atrasadas</h2>
                            <p class="text-2xl font-bold">{{ $stats['overdue_requests'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card  bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Meus Livros Requisitados</h2>

                @if($requests->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <i class="fas fa-info-circle mr-2"></i>
                        Você ainda não fez nenhuma requisição.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-50">
                                <tr class="hover">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Livro
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data Requisição
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Devolução Prevista
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
                                @foreach($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-wrap">
                                            <div class="flex items-center">
                                                @if($request->book->cover_image)
                                                    <img src="{{ asset('storage/' . $request->book->cover_image) }}"
                                                        alt="{{ $request->book->name }}" class="w-10 h-10 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/48x72" class="mr-3" />
                                                @endif
                                                <div>
                                                    <div class="font-medium">{{ $request->book->name }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{Str::limit($request->book->authors->pluck('name')->join(', '), 30) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->request_date->format('d/m/Y') }}
                                        </td>
                                        <td
                                            class=" whitespace-nowrap {{ $request->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $request->expected_return_date->format('d/m/Y') }}
                                            @if($request->isOverdue())
                                                <span class="badge badge-error ml-2 text-white">Atrasado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="badge badge-lg ml-2 gap-1 text-white
                                                                            @if($request->status === 'approved') badge-success
                                                                            @elseif(in_array($request->status, ['pending', 'pending_returned'])) badge-warning
                                                                            @elseif($request->status === 'returned') badge-success
                                                                            @elseif($request->status === 'rejected') badge-error
                                                                            @else badge-neutral 
                                                                            @endif">

                                                @if($request->status === 'approved')
                                                    <i class="fas fa-check-circle"></i> Aprovado
                                                @elseif($request->status === 'pending')
                                                    <i class="fas fa-clock"></i> Pendente
                                                @elseif($request->status === 'pending_returned')
                                                    <i class="fas fa-clock"></i> Devolução Pendente
                                                @elseif($request->status === 'returned')
                                                    <i class="fas fa-undo"></i> Devolvido
                                                @elseif($request->status === 'rejected')
                                                    <i class="fas fa-times-circle"></i> Rejeitado
                                                @elseif($request->status === 'cancelled')
                                                    <i class="fas fa-ban"></i> Cancelado
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($request->status === 'approved')
                                                <a href="{{ route('returns.returnForm', $request) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-undo mr-1"></i> Devolver
                                                </a>
                                            @elseif($request->status === 'pending' && auth()->id() === $request->user_id)
                                                <form action="{{ route('requests.cancel', $request) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-danger ml-2"
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta requisição?')">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @else
                                                <p class="flex justify-center">-</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>