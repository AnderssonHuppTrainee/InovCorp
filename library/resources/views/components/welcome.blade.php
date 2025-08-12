<div class="container mx-auto px-4 py-6">
    <!-- Cabeçalho e botões de exportação -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('export.books') }}" class="btn btn-outline gap-2">
                <i class="fas fa-file-excel"></i>
                Exportar Livros
            </a>
            <a href="{{ route('export.authors') }}" class="btn btn-outline gap-2">
                <i class="fas fa-file-excel"></i>
                Exportar Autores
            </a>
            <a href="{{ route('export.publishers') }}" class="btn btn-outline gap-2">
                <i class="fas fa-file-excel"></i>
                Exportar Editoras
            </a>
        </div>
    </div>

    <!-- Primeira linha de estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
        <!-- Livros -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg md:col-span-2">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-primary/10 dark:bg-primary/20 p-4">
                        <i class="fas fa-book text-primary dark:text-primary-light text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Total de Livros</h2>
                        <p class="text-3xl font-bold">{{ $stats['books'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Autores -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg md:col-span-2">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-secondary/10 dark:bg-secondary/20 p-4">
                        <i class="fas fa-user-edit text-secondary dark:text-secondary-light text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Total de Autores</h2>
                        <p class="text-3xl font-bold">{{ $stats['authors'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Editoras -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg md:col-span-2">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-accent/10 dark:bg-accent/20 p-4">
                        <i class="fas fa-building text-accent dark:text-accent-light text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Total de Editoras</h2>
                        <p class="text-3xl font-bold">{{ $stats['publishers'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda linha de estatísticas (Requisições) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card bg-white dark:bg-gray-800 shadow-lg">
            <div class="card-body">
                <h2 class="text-xl font-semibold">Requisições Ativas</h2>
                <p class="text-3xl font-bold">{{ $activeRequestsCount ?? 0 }}</p>
            </div>
        </div>
        <div class="card bg-white dark:bg-gray-800 shadow-lg">
            <div class="card-body">
                <h2 class="text-xl font-semibold">Últimos 30 dias</h2>
                <p class="text-3xl font-bold">{{ $recentRequestsCount ?? 0}}</p>
            </div>
        </div>
        <div class="card bg-white dark:bg-gray-800 shadow-lg">
            <div class="card-body">
                <h2 class="text-xl font-semibold">Entregues Hoje</h2>
                <p class="text-3xl font-bold">{{ $returnedTodayCount ?? 0}}</p>
            </div>
        </div>
    </div>

    <!-- Últimos livros adicionados -->
    <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
        <div class="card-body">
            <h2 class="text-xl font-semibold mb-4">Últimos Livros Adicionados</h2>
            @if($stats['latestBooks']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor(es)</th>
                                <th>Editora</th>
                                <th>ISBN</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
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
                </div>
            @else
                <div class="alert alert-info shadow-lg">
                    <div>
                        <i class="fas fa-info-circle"></i>
                        <span>Nenhum livro cadastrado ainda.</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Lista de requisições -->
    <div class="card bg-white dark:bg-gray-800 shadow-lg">
        <div class="card-body">
            <h2 class="text-xl font-semibold mb-4">Últimas Requisições</h2>
            @if($requests->isEmpty())
                <div class="alert alert-info shadow-lg">
                    <div>
                        <i class="fas fa-info-circle"></i>
                        <span>Nenhuma requisição encontrada.</span>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Livro</th>
                                <th>Data Requisição</th>
                                <th>Devolução Prevista</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                            <tr>
                                                <td>{{ $request->number }}</td>
                                                <td>{{ $request->book->title }}</td>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->expected_return_date->format('d/m/Y') }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                                                                            {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' :
                                ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline">
                                                        Ver
                                                    </a>
                                                    @if(auth()->user()->isAdmin() && $request->status === 'pending')
                                                        <form class="inline" action="{{ route('requests.approve', $request) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success ml-2">
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
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>