<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Minha Área</h1>
                <p class="text-sm text-base-content/70">Gerencie suas requisições de livros</p>
            </div>

            @auth
                @if(auth()->user()->canRequestMoreBooks())
                    <a href="{{ route('public.books.index') }}" class="btn btn-primary gap-2">
                        <i class="fas fa-plus"></i>
                        Nova Requisição
                    </a>
                @endif
            @endauth
        </div>


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
                                <tr>
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
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                                        {{ $request->book->authors->pluck('name')->join(', ') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->request_date->format('d/m/Y') }}
                                        </td>
                                        <td class="{{ $request->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $request->expected_return_date->format('d/m/Y') }}
                                            @if($request->isOverdue())
                                                <span class="badge badge-error ml-2">Atrasado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="badge badge-lg ml-2 gap-1 text-white
                                                                                                                                                                                                                                                @if($request->status === 'approved') badge-success
                                                                                                                                                                                                                                                @elseif(in_array($request->status, ['pending', 'pending_returned'])) badge-warning
                                                                                                                                                                                                                                                @elseif($request->status === 'returned') badge-success
                                                                                                                                                                                                                                                @elseif($request->status === 'rejected') badge-error
                                                                                                                                                                                                                                                @else badge-neutral 
                                                                                                                                                                                                                                                @endif">

                                                @if($request->status === 'approved')
                                                    <i class="fas fa-check-circle"></i>
                                                @elseif(in_array($request->status, ['pending', 'pending_returned']))
                                                    <i class="fas fa-clock"></i>
                                                @elseif($request->status === 'returned')
                                                    <i class="fas fa-undo"></i>
                                                @elseif($request->status === 'rejected')
                                                    <i class="fas fa-times-circle"></i>
                                                @endif

                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($request->status === 'approved')
                                                <a href="{{ route('requests.returnForm', $request) }}"
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

        @if(!auth()->user()->canRequestMoreBooks())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Você atingiu o limite máximo de 3 livros requisitados simultaneamente.
            </div>
        @endif
    </div>
</x-app-layout>