<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto p-4">
            <x-resources.header title="Gestão de Livros" createRoute="{{ route('books.create') }}" 
            exportRoute="{{ route('export.books')}}" />

            <!-- filtros -->
            <x-resources.filters action="{{ route('books.index') }}" clearUrl="{{ route('books.index') }}">
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
            </x-resources.filters>


            <!-- Tabela -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'isbn',
    'direction' => request('sort') === 'isbn' && request('direction') === 'asc' ? 'desc' : 'asc'
]) }}" class="flex items-center">
                                    ISBN
                                    @if(request('sort') === 'isbn')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'name',
    'direction' => request('sort') === 'name' && request('direction') === 'desc' ? 'asc' : 'desc'
]) }}" class="flex items-center">
                                    Nome
                                    @if(request('sort') === 'name')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'desc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'publisher',
    'direction' => request('sort') === 'publisher' && request('direction') === 'asc' ? 'desc' : 'asc'
]) }}" class="flex items-center">
                                    Editora
                                    @if(request('sort') === 'publisher')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'author',
    'direction' => request('sort') === 'author' && request('direction') === 'asc' ? 'desc' : 'asc'
]) }}" class="flex items-center">
                                    Autores
                                    @if(request('sort') === 'author')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'price',
    'direction' => request('sort') === 'price' && request('direction') === 'asc' ? 'desc' : 'asc'
]) }}" class="flex items-center">
                                    Preço
                                    @if(request('sort') === 'price')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td>{{ $book->isbn }}</td>
                                <td class="whitespace-wrap">
                                    <div class="flex items-center">
                                        @if($book->cover_image)
                                            <img src="{{  $book->cover_image }}"
                                                alt="{{ $book->name }}" class="w-12 h-15 mr-3 object-cover">
                                        @else
                                            <img src="https://placehold.co/46x70" class="mr-3" />
                                        @endif
                                        <div>
                                            <div class="font-medium">{{ $book->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->publisher->name ?? '-' }}</td>
                                <td>
                                    {{ $book->authors->pluck('name')->join(', ') }}
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ $book->price = '€ ' . number_format($book->price, 2, ',', '.')}}
                                </td>

                                <td class="flex space-x-2">
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error text-white"
                                            onclick="return confirm('Tem certeza que deseja excluir o livro?')">
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

            <!-- pagination -->
            <div class="mt-4">
                {{ $books->withQueryString()->links() }}
            </div>
        </div>
    </div>

</x-app-layout>