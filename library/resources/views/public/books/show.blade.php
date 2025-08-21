<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-3">
            <a href="{{ route('public.home') }}" class="btn btn-ghost gap-2">
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>