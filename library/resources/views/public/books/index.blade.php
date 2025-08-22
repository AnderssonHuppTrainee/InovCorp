<x-guest-layout>
    <div class="container mx-auto px-4 py-6">


        <h1 class="text-3xl font-bold text-gray-900 mb-4">Catálogo de Livros</h1>

        <div class="bg-base-100 rounded-lg shadow p-6 mb-6">
            <form action="{{ route('public.books.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Título, autor ou ISBN"
                        class="input input-bordered w-full" />
                </div>


                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Preço mínimo</span>
                    </label>
                    <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}"
                        placeholder="€ 0,00" class="input input-bordered w-full" />
                </div>


                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Preço máximo</span>
                    </label>
                    <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}"
                        placeholder="€100,00" class="input input-bordered w-full" />
                </div>


                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Ordenar por</span>
                    </label>
                    <select name="sort" class="select select-bordered w-full">
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

                <div class="md:col-span-4 flex justify-end space-x-2 mt-2">
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    <a href="{{ route('public.books.index') }}" class="btn btn-outline">Limpar</a>
                </div>
            </form>
        </div>



        @if($books->isEmpty())
            <div class="text-center py-12">
                <p class="text-lg text-gray-600">Nenhum livro encontrado com os filtros aplicados.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6  mx-auto">
                @foreach($books as $book)
                    <div class="card bg-base-100 shadow hover:shadow-md transition-shadow">

                        @if ($book->cover_image)
                            <figure class="aspect-[2/3]">
                                <img src="{{ $book->cover_image }}" class="h-full w-full object-cover">
                            </figure>
                        @else
                            <figure class="aspect-[2/3]">
                                <x-image-book class="h-48 w-full object-cover" />
                            </figure>
                        @endif


                        <div class="card-body p-4">
                            <h2 class="card-title line-clamp-2 text-sm sm:text-base">
                                <a href="{{ route('public.books.show', $book) }}" class="hover:underline text-indigo-600">
                                    {{ $book->name }}
                                </a>
                            </h2>
                            <p class="text-gray-600 text-sm mb-2">por {{ $book->authors->pluck('name')->join(', ') }} </p>

                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-indigo-600">€{{ number_format($book->price, 2, ',', '.') }}</span>

                                @auth
                                    @if(auth()->user()->canRequestMoreBooks() && $book->isAvailable())
                                        <a href="{{ route('requests.create', $book) }}" class="btn btn-sm btn-outline">
                                            Requisitar
                                        </a>
                                    @elseif(!$book->isAvailable())
                                        <span class="px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded">
                                            Alugado
                                        </span>
                                    @endif
                                @else
                                    @if($book->isAvailable())
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline">
                                            Requisitar
                                        </a>
                                    @else
                                        <span class="px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded">
                                            Alugado
                                        </span>
                                    @endif
                                @endauth
                            </div>


                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium                                                                                                                                                                                                                                                                                                                                                                    {{ $book->isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $book->isAvailable() ? 'Disponível' : 'Indisponível' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="mt-8">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </div>

</x-guest-layout>