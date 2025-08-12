<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Dashboard Administrativo</h1>
                <p class="text-sm text-base-content/70">Visão geral do sistema</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('export.books') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Livros
                </a>
                <a href="{{ route('export.authors') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Autores
                </a>
                <a href="{{ route('export.publishers') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i>
                    Exportar Editoras
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Total de Livros -->
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

            <!-- Total de Autores -->
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

            <!-- Total de Editoras -->
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

        <!-- Segunda Linha de Estatísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Requisições Ativas -->
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

            <!-- Últimos 30 Dias -->
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

            <!-- Entregues Hoje -->
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
                                                                <form class="inline" action="{{ route('requests.approve', $request) }}"
                                                                    method="POST">
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
</x-app-layout>