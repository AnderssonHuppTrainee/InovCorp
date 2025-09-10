<x-guest-layout>
    <div class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info shadow-lg mb-6 text-white">
                <span>{{ session('info') }}</span>
            </div>
        @endif

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
                    <button type="submit" class="btn btn-primary text-white">Aplicar Filtros</button>
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
                            <p class="text-gray-600 text-sm mb-1">por {{ $book->authors->pluck('name')->join(', ') }} </p>
                            <div class="items-center mt-1">
                                <span class="font-bold text-indigo-600">€{{ number_format($book->price, 2, ',', '.') }}</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    @php
                                        $rating = round($book->reviews_avg_rating * 2) / 2;
                                    @endphp

                                    @for ($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($rating))
                                            <i class="fas fa-star text-orange-400"></i>
                                        @elseif($i - $rating < 1)
                                            <i class="fas fa-star-half-alt text-orange-400"></i>
                                        @else
                                            <i class="far fa-star text-orange-400"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($rating, 1, ',', '.') }} / 5
                                </p>
                            </div>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium                                                                                                                                                                                                                                                                                                                                                                    {{ $book->isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $book->isAvailable() ? 'Disponível' : 'Indisponível' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                @auth
                                    @if($book->isAvailable())
                                        <div class="sm:col-span-2 flex justify-end">
                                            <form action="{{ route('cart.add', $book->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary text-white">
                                                    Adicionar ao Carrinho
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(!$book->isAvailable())

                                        <form method="POST" action="{{ route('books.notify', $book) }}"
                                            class="col-span-1 sm:col-span-2 w-full">
                                            @csrf
                                            <button type="submit" class="w-full btn btn-error text-white ">
                                                Notificar-me quando disponível
                                            </button>
                                        </form>

                                    @endif
                                @endauth
                                @guest
                                    @if($book->isAvailable())
                                        <div class="sm:col-span-2 flex justify-end">
                                            <a href="{{ route('login') }}"
                                                class="col-span-1 btn btn-sm btn-primary text-white whitespace-nowrap">
                                                Add ao carrinho
                                            </a>
                                        </div>
                                    @else

                                        <a href="{{ route('login') }}"
                                            class="col-span-1 sm:col-span-2 w-full btn btn-primary text-white text-2sm sm:text-base px-2 sm:px-4 py-1 sm:py-2">
                                            Notificar-me quando disponível
                                        </a>

                                    @endif
                                @endguest
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