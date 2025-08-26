<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-3">
            <a href="{{ route('public.books.index')}}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="container mx-auto p-4">
            <div class="flex flex-col md:flex-row gap-6 mb-6">

                <div class="flex-shrink-0">
                    <figure class="w-full h-72 overflow-hidden rounded-lg shadow-md">
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
                    <div class="divider"></div>
                    <!-- avaliações -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Avaliações</h3>

                        @forelse($reviews as $review)
                            <div class=" bg-gray-50 dark:bg-gray-700 mb-4 p-4 rounded-lg shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold">{{ $review->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>

                                <!-- rating  -->
                                <div class="flex items-center mb-2 text-orange-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($review->rating))
                                            <i class="fas fa-star text-orange-400"></i>
                                        @elseif($i - $review->rating < 1)
                                            <i class="fas fa-star-half-alt text-orange-400"></i>
                                        @else
                                            <i class="far fa-star text-orange-400"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-500">
                                        {{ number_format($review->rating, 1) }}/5
                                    </span>
                                </div>

                                <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Nenhuma avaliação disponível para este livro.</p>
                        @endforelse
                        <div class="mt-4">
                            {{ $reviews->links() }}
                        </div>
                    </div>

                    <div class="divider"></div>
                    <div class="flex justify-end">
                        @auth
                            @if(auth()->user()->canRequestMoreBooks() && $book->isAvailable())
                                <a href="{{ route('requests.create', $book) }}" class="btn btn-md btn-outline">
                                    Requisitar
                                </a>
                            @elseif(!$book->isAvailable())
                                <span class="px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded">
                                    Alugado
                                </span>
                            @endif
                        @else
                            @if($book->isAvailable())
                                <a href="{{ route('login') }}" class="btn btn-md btn-outline">
                                    Requisitar
                                </a>
                            @else
                                <span class="px-3 py-1 bg-gray-200 text-gray-600 text-sm rounded">
                                    Alugado
                                </span>
                            @endif
                        @endauth
                    </div>
                    <div class="divider"></div>
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200 border-b pb-2">
                            Livros do Relacionados
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($relatedBooks as $book)
                                <div
                                    class="card bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-shadow duration-300">
                                    @if($book->cover_image)
                                        <figure class="px-4 pt-4">
                                            <img src="{{ $book->cover_image }}" alt="Capa de {{ $book->name }}"
                                                class="rounded-xl h-72 w-full object-cover">
                                        </figure>
                                    @else
                                        <figure class="px-4 pt-4">
                                            <x-image-book class="rounded-xl h-48 w-full object-cover" />
                                        </figure>
                                    @endif
                                    <div class="card-body">
                                        <h3 class="card-title text-gray-900 dark:text-white">
                                            <a href="{{ route('public.books.show', $book) }}"
                                                class="hover:text-primary dark:hover:text-primary">
                                                {{ $book->name }}
                                            </a>
                                        </h3>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                            <p><span class="font-medium">Editora:</span>
                                                {{ $book->publisher->name ?? 'N/A' }}</p>
                                            <p><span class="font-medium">ISBN:</span> {{ $book->isbn }}</p>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>