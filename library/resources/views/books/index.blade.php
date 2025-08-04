<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Livros</h1>
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Novo Livro
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">

            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome ou ISBN" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Autor</span>
                    </label>
                    <select name="author" class="select select-bordered">
                        <option value="">Todos os autores</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" @selected(request('author') == $author->id)>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Editora</span>
                    </label>
                    <select name="publisher" class="select select-bordered">
                        <option value="">Todas as editoras</option>
                        @foreach ($publishers as $publisher)
                            <option value="{{ $publisher->id }}" @selected(request('publisher') == $publisher->id)>
                                {{ $publisher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                <div class="form-control">
                    <a href="{{ route('books.index') }}" class="btn btn-outline">Limpar</a>
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Nome</th>
                        <th>Editora</th>
                        <th>Autores</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->name }}</td>
                            <td>{{ $book->publisher->name ?? '-' }}</td>
                            <td>
                                {{ $book->authors->pluck('name')->join(', ') }}
                            </td>
                            <td>
                                @php
                                    try {
                                        $price = Crypt::decryptString($book->price);
                                        echo '€ ' . number_format($price, 2, ',', '.');
                                    } catch (Exception $e) {
                                        echo '-';
                                    }
                                @endphp
                            </td>

                            <td class="flex space-x-2">
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error"
                                        onclick="return confirm('Tem certeza?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhum livro encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-4">
            {{ $books->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>