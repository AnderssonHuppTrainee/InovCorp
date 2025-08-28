<x-app-layout>
    <style>
        .multi-card-carousel {
            width: 100%;
            padding: 20px 0;

            position: relative;
        }

        .multi-card-carousel .swiper-slide {
            width: auto !important;

        }

        /* botões de navegacao */
        .multi-card-carousel .swiper-button-prev,
        .multi-card-carousel .swiper-button-next {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            top: 50%;
            transform: translateY(-50%);
        }

        .multi-card-carousel .swiper-button-prev {
            left: 0;
        }

        .multi-card-carousel .swiper-button-next {
            right: 0;
        }

        .multi-card-carousel .swiper-button-prev:after,
        .multi-card-carousel .swiper-button-next:after {
            font-size: 20px;
            color: #333;
            font-weight: bold;
        }
    </style>
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
                    <figure class="w-48 h-72 overflow-hidden rounded-lg shadow-md">
                        <img src="{{ $book->cover_image }}" class="w-full h-full object-cover"
                            alt="Capa de {{ $book->name }}">
                    </figure>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ $book->name }}</h1>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                ISBN
                            </h3>
                            <p class="text-lg">{{ $book->isbn }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Editora
                            </h3>
                            <p class="text-lg">{{ $book->publisher->name ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Preço
                            </h3>
                            <p class="text-lg">
                                {{ $book->price = '€ ' . number_format($book->price, 2, ',', '.')}}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Data de Publicação
                            </h3>
                            <p class="text-lg">{{ $book->published_at?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <!-- Autores -->
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Autores
                            </h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($book->authors as $author)
                                    <span class="badge badge-primary">{{ $author->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Avaliação
                            </h3>
                            <div>
                                <div class="flex items-center mt-2">
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
                </div>
            </div>
            <div class="divider"></div>

            <div class="mb-8">
                <div class="container mx-auto p-4 ">
                    <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200 border-b pb-2">
                        Livros do Relacionados
                    </h2>

                    <div class="swiper multi-card-carousel">
                        <div class="swiper-wrapper">
                            @foreach($relatedBooks as $book)
                                <div class="swiper-slide">
                                    <div
                                        class="card bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-shadow duration-300">

                                        <div class="w-[200px] h-[300px] mx-auto overflow-hidden">
                                            @if ($book->cover_image)
                                                <figure class="aspect-[2/3]">
                                                    <img src="{{ $book->cover_image }}" class="h-full w-full object-cover">
                                                </figure>
                                            @else
                                                <figure class="aspect-[2/3]">
                                                    <x-image-book class="h-72 w-full object-cover" />
                                                </figure>
                                            @endif
                                        </div>
                                        <div class="card-body p-4">
                                            <h3 class="card-title text-gray-900 dark:text-white">
                                                <a href="{{ route('public.books.show', $book) }}"
                                                    class="hover:text-primary dark:hover:text-primary">
                                                    {{ Str::limit($book->name, 15) }}
                                                </a>
                                            </h3>
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
                                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                <p><span class="font-medium">Editora:</span>
                                                    {{ Str::limit($book->publisher->name, 15) ?? 'N/A' }}</p>
                                                <p><span class="font-medium">ISBN:</span> {{ $book->isbn }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.multi-card-carousel', {
                loop: true,
                slidesPerView: 1,
                spaceBetween: 10,
                centeredSlides: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {

                    480: {
                        slidesPerView: 2,
                        spaceBetween: 15
                    },

                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },

                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 25
                    },

                    1280: {
                        slidesPerView: 5,
                        spaceBetween: 30
                    }
                }
            });

        });
    </script>
</x-app-layout>