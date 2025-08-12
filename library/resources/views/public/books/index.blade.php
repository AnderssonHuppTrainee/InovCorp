<x-guest-layout>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Cabeçalho e Filtros -->
        <div class="mt-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Catálogo de Livros</h1>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form action="{{ route('public.books.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Campo de Busca -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Título, autor ou ISBN">
                    </div>

                    <!-- Filtro de Preço Mínimo -->
                    <div>
                        <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Preço mínimo</label>
                        <input type="number" step="0.01" name="min_price" id="min_price"
                            value="{{ request('min_price') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="€ 0,00">
                    </div>

                    <!-- Filtro de Preço Máximo -->
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Preço máximo</label>
                        <input type="number" step="0.01" name="max_price" id="max_price"
                            value="{{ request('max_price') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="€100,00">
                    </div>

                    <!-- Ordenação -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                        <select name="sort" id="sort"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Título (A-Z)
                            </option>
                            <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Título
                                (Z-A)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preço (menor
                                primeiro)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preço
                                (maior primeiro)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais recentes
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex justify-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('public.books.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem de Livros -->
        @if($books->isEmpty())
            <div class="text-center py-12">
                <p class="text-lg text-gray-600">Nenhum livro encontrado com os filtros aplicados.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                @foreach($books as $book)
                    <div class="card bg-base-100 shadow hover:shadow-md transition-shadow">

                        <!--@if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div class="text-gray-500 text-center p-4">
                                                <i class="fas fa-book-open fa-3x mb-2"></i>
                                                <p>Sem imagem</p>
                                            </div>
                                        @endif-->
                        <figure class="aspect-[2/3]">
                            <x-image-book class="h-48 w-full object-cover" />
                        </figure>

                        <!-- Detalhes do Livro -->
                        <div class="card-body p-4">
                            <h2 class="card-title text-sm sm:text-base line-clamp-2">{{ $book->name }}</h2>
                            <p class="text-gray-600 text-sm mb-2">por {{ $book->authors->pluck('name')->join(', ') }} </p>

                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-indigo-600">€{{ number_format($book->price, 2, ',', '.') }}</span>

                                @auth
                                    @if($book->isAvailable())
                                        <div class="card-actions justify-end">
                                            <a href="{{ route('requests.create', $book) }}" class="btn btn-sm btn-outline">
                                                Requisitar
                                            </a>
                                        </div>
                                    @else
                                        <span class="px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded">
                                            Indisponível
                                        </span>
                                    @endif
                                @endauth
                            </div>

                            <!-- Badge de disponibilidade -->
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                                                                                                                                                                                                            {{ $book->isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $book->isAvailable() ? 'Disponível' : 'Indisponível' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="mt-8">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-guest-layout>