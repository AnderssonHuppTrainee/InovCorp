<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Minha Área</h1>
                <p class="text-sm text-base-content/70">Gerencie suas requisições de livros</p>
            </div>

            @auth
                @if(auth()->user()->canRequestMoreBooks())
                    <a href="{{ route('books.index') }}" class="btn btn-primary gap-2">
                        <i class="fas fa-plus"></i>
                        Nova Requisição
                    </a>
                @endif
            @endauth
        </div>

        <!-- Cards de Estatísticas -->
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

            <!-- Atrasadas -->
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
        <div class="card bg-base-100 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Meus Livros Requisitados</h2>

                @if($requests->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Você ainda não fez nenhuma requisição.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Data Requisição</th>
                                    <th>Devolução Prevista</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                                    <tr>
                                                        <td>
                                                            <div class="flex items-center">
                                                                @if($request->book->cover_image)
                                                                    <img src="{{ asset('storage/' . $request->book->cover_image) }}"
                                                                        alt="{{ $request->book->title }}"
                                                                        class="w-10 h-10 rounded mr-3 object-cover">
                                                                @endif
                                                                <div>
                                                                    <div class="font-medium">{{ $request->book->title }}</div>
                                                                    <div class="text-sm text-gray-500">{{ $request->book->author }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                        <td class="{{ $request->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                                            {{ $request->expected_return_date->format('d/m/Y') }}
                                                            @if($request->isOverdue())
                                                                <span class="badge badge-error ml-2">Atrasado</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $request->status === 'approved' ? 'badge-success' :
                                    ($request->status === 'pending' ? 'badge-warning' : 'badge-info') }}">
                                                                {{ ucfirst($request->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($request->status === 'approved')
                                                                <a href="{{ route('requests.returnForm', $request)}}"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-undo mr-1"></i> Devolver
                                                                </a>
                                                            @else
                                                                @if($request->status === 'returned')
                                                                @else
                                                                    <button class="btn btn-sm btn-dan">Cancelar</button>
                                                                @endif
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

        <!-- Botão para nova requisição -->
        @if(!auth()->user()->canRequestMoreBooks())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Você atingiu o limite máximo de 3 livros requisitados simultaneamente.
            </div>
        @endif
    </div>
</x-app-layout>