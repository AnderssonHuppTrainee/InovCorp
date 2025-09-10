<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href=" {{ url()->previous() }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="container mx-auto p-4">
            <div class="flex flex-col md:flex-row gap-6 mb-6">

                <div class="flex-shrink-0">
                    <figure class="w-48 h-64 overflow-hidden rounded-lg shadow-md">
                        <img src="{{ $book->cover_image }}" class="w-full h-full object-cover"
                            alt="Capa de {{ $book->name }}">
                    </figure>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ $book->name }}</h1>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">ISBN</h3>
                            <p class="text-lg">{{ $book->isbn }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Editora</h3>
                            <p class="text-lg">{{ $book->publisher->name ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Preço</h3>
                            <p class="text-lg">
                                {{ $book->price = '€ ' . number_format($book->price, 2, ',', '.')}}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Data de Publicação
                            </h3>
                            <p class="text-lg">{{ $book->published_at?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-6 overflow-auto">
                        <!-- Autores -->
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Autores</h3>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($book->authors as $author)
                                <span class="badge badge-primary">{{ $author->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <!-- Sinopse -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Sinopse</h3>
                        <p class=" text-gray-700 dark:text-gray-300 whitespace-pre-line text-justify">
                            {{ $book->bibliography ?? 'Nenhuma sinopse disponível.' }}
                        </p>
                    </div>



                    <!-- Ações -->
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-info  text-white">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error text-white"
                                onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                <i class="fas fa-trash mr-2"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Histórico de Requisições</h2>

                @if($requests->count())
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Data da Solicitação</th>
                                    <th>Data Esperada Devolução</th>
                                    <th>Status</th>
                                    <th>Condição (Devolução)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td>{{ $req->user->name }}</td>
                                        <td>{{ $req->request_date->format('d/m/Y') }}</td>
                                        <td>{{ $req->expected_return_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($req->status) {
                                                    'approved' => 'badge-success',
                                                    'returned' => 'badge-info',
                                                    'rejected' => 'badge-error',
                                                    default => 'badge-warning'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $req->book_condition ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>

                @else
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Nenhuma requisição encontrada para este livro.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>