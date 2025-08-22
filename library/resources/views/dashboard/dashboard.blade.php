<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Dashboard Administrativo</h1>
                <p class="text-sm text-base-content/70">Visão geral do sistema</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('books.import') }}" class="btn btn-info btn-sm gap-2 text-white">
                    <i class="fas fa-download"></i>
                    Importar Livros
                </a>
                <a href="{{ route('export.books') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Livros
                </a>
                <!--<a href="{{ route('export.authors') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Autores
                </a>
                <a href="{{ route('export.publishers') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Editoras
                </a>-->
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-primary/10 p-3 text-primary">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Total de Livros</h2>
                            <p class="text-2xl font-bold">{{ $stats['books'] }}</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-secondary/10 p-3 text-secondary">
                            <i class="fas fa-user-edit text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Total de Autores</h2>
                            <p class="text-2xl font-bold">{{ $stats['authors'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-accent/10 p-3 text-accent">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Total de Editoras</h2>
                            <p class="text-2xl font-bold">{{ $stats['publishers'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-info/10 p-3 text-info">
                            <i class="fas fa-exchange-alt text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Requisições Ativas</h2>
                            <p class="text-2xl font-bold">{{ $activeRequestsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-warning/10 p-3 text-warning">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Últimos 30 Dias</h2>
                            <p class="text-2xl font-bold">{{ $recentRequestsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card bg-base-100 shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-success/10 p-3 text-success">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <h2 class="card-title text-sm sm:text-base">Entregues Hoje</h2>
                            <p class="text-2xl font-bold">{{ $returnedTodayCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Últimas Requisições</h2>
                @if($requests->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6 text-white">
                        <div>
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Nenhuma requisição encontrada.</span>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">

                        <table class="table table-zebra w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->number }}</td>
                                        <td class="px-6 py-4 whitespace-wrap">
                                            <div class="flex items-center">
                                                @if($request->book->cover_image)
                                                    <img src="{{ $request->book->cover_image }}" alt="{{ $request->book->name }}" class="w-12 h-17 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/48x72" class="mr-3" />
                                                @endif

                                                <div>
                                                    <div class="font-bold">{{ $request->book->name }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{  Str::limit($request->book->authors->pluck('name')->join(', '), 30) }}
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->request_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $request->expected_return_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="badge ml-2 gap-1 text-white
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
                                            <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline">
                                                Ver
                                            </a>
                                            @if(auth()->user()->isAdmin() && $request->status === 'pending')
                                                <form class="inline" action="{{ route('requests.approve', $request) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success text-white ml-2">
                                                        Aprovar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end m-4">
                        <a href="{{ route('requests.index') }}" class="link-info">Ver todos as requisições...</a>
                    </div>
                    <div class="mt-2">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
        <!--LIVROS Devolvidos-->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Livros Devolvidos Recentemente</h2>

                @if($returnedBooks->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6 text-white">
                        <i class="fas fa-info-circle"></i>
                        <span>Nenhum livro devolvido recentemente.</span>
                    </div>
                    <div class="flex justify-end m-4">
                        <a href="{{ route('returns.index') }}" class="link-info">Ver todas as devoluções...</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">

                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Livro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Utilizador</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data Devolução</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dias</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returnedBooks as $request)
                                    <tr class="hover">
                                        <td class="whitespace-nowrap">{{ $request->number }}</td>
                                        <td class="whitespace-wrap">
                                            <div class="flex items-center">
                                                @if($request->book->cover_image)
                                                    <img src="{{ $request->book->cover_image }}"
                                                        alt="{{ $request->book->name }}" class="w-12 h-17 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/48x72" class="mr-3" />
                                                @endif
                                                <div>
                                                    <div class="font-bold">{{ Str::limit($request->book->name, 30) }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{  Str::limit($request->book->authors->pluck('name')->join(', '), 30) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap">{{ $request->user->name }}</td>
                                        <td class="whitespace-nowrap">
                                            {{ $request->returned_date->format('d/m/Y') }}
                                            @if($request->isOverdue())
                                                <span class="badge badge-error ml-2 text-white">Atrasado</span>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap">
                                            {{ round($request->actual_days) }} dias
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <a href="{{ route('returns.reviewReturn', $request) }}"
                                                class="btn btn-sm btn-outline">
                                                Ver
                                            </a>
                                            @if(auth()->user()->isAdmin() && $request->status === 'pending_returned')
                                                <form class="inline" action="{{ route('returns.approveReturn', $request) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ml-2 text-white">
                                                        Aprovar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end m-4">
                        <a href="{{ route('returns.index') }}" class="link-info">Ver todas as devoluções...</a>
                    </div>
                    <div class="px-4 pb-4 sm:px-6">
                        {{ $returnedBooks->links() }}
                    </div>
                @endif
            </div>
        </div>
        <!--Multas pendentes-->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body ">
                <h2 class="text-xl font-semibold mb-4">Últimas multas</h2>

                @if($fines->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6 text-white">
                        <i class="fas fa-info-circle"></i>
                        <span>Nenhum livro devolvido recentemente.</span>
                    </div>
                    <div class="flex justify-end m-4">
                        <a href="{{ route('fines.index') }}" class="link-info">Ver todas as multas...</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Livro
                                    </th>
                                      <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Utilizador
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Motivo
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fines as $fine)
                                    <tr>
                                        <td>{{ $fine->bookRequest->book->name ?? 'Livro removido' }}</td>
                                         <td>{{ $fine->bookRequest->user->name ?? 'Utilizador removido' }}</td>
                                        <td>{{ $fine->reason }}</td>
                                        <td class="whitespace-nowrap">€ {{ number_format($fine->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($fine->status === 'pending')
                                                <span class="badge badge-warning text-white">
                                                    <i class="fas fa-clock"></i>
                                                    Pendente
                                                </span>
                                            @else
                                                <span class="badge badge-success text-white">
                                                    <i class="fas fa-check-circle"></i>
                                                    Paga
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end m-4">
                        <a href="{{ route('fines.index') }}" class="link-info">Ver todas as multas...</a>
                    </div>
                    <div class="px-4 pb-4 sm:px-6">
                        {{ $fines->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ultimos livros adicionados -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Últimos Livros Adicionados</h2>
                @if($stats['latestBooks']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Título</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Autor(es)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Editora</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ISBN</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stats['latestBooks'] as $book)
                                    <tr>
                                        <td>{{ $book->name }}</td>
                                        <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                                        <td>{{ $book->publisher->name ?? '-' }}</td>
                                        <td>{{ $book->isbn }}</td>
                                        <td>{{ $book->created_at->format('d/m/Y') }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="flex justify-end m-4">
                            <a href="{{ route('books.index') }}" class="link-info">Ver todos os livros...</a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhum livro cadastrado ainda.</span>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>