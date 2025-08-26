<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
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

    <!-- Hero Section -->
    <section class="flex items-center justify-center px-6 py-16 bg-base-100">
        <div class="text-center max-w-2xl">
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">Bem-vindo à Amazing Library</h1>
            <p class="text-lg mb-6 text-gray-600">
                Descubra, explore e leia os melhores livros com facilidade.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="btn btn-primary text-white">Acessar Conta</a>
                <a href="{{ route('public.books.index')}}" class="btn btn-outline">Explorar Livros</a>
            </div>
        </div>
    </section>


    <section class="py-12 bg-base-200">
        <div class="container mx-auto p-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Últimos Livros Adicionados</h2>

            <div class="swiper multi-card-carousel">
                <div class="swiper-wrapper">
                    @foreach ($latestBooks as $book)
                        <div class="swiper-slide">

                            <div class="card bg-base-100 shadow hover:shadow-md transition-shadow mx-2">
                                <!-- capa -->
                                <div class="w-[200px] h-[300px] mx-auto overflow-hidden">
                                    @if ($book->cover_image)
                                        <figure class="aspect-[2/3]">
                                            <img src="{{ $book->cover_image }}" class="h-72 w-full object-cover">
                                        </figure>
                                    @else
                                        <figure class="aspect-[2/3]">
                                            <x-image-book class="h-72 w-full object-cover" />
                                        </figure>
                                    @endif
                                </div>

                                <!-- conteudo -->
                                <div class="card-body p-4">
                                    <h2 class="card-title text-sm sm:text-base line-clamp-2">
                                        {{ Str::limit($book->name, 20) }}
                                    </h2>
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
                                    <div class="card-actions justify-end">
                                        <label for="modal-{{ $book->id }}" class="btn btn-primary btn-sm text-white">
                                            Detalhes
                                        </label>
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
    </section>

    <!-- modal para cada livro -->
    @foreach ($latestBooks as $book)
        <!-- input checkbox que controla o modal -->
        <input type="checkbox" id="modal-{{ $book->id }}" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box max-w-2xl relative">
                <label for="modal-{{ $book->id }}" class="btn btn-sm btn-circle absolute right-2 top-2">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </label>

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- capa -->
                    <div class="flex-shrink-0">
                        @if ($book->cover_image)
                            <figure class="w-48 h-72 overflow-hidden rounded-lg shadow-md">
                                <img src="{{ $book->cover_image }}" class="w-full h-full object-cover"
                                    alt="Capa de {{ $book->name }}">
                            </figure>
                        @else
                            <figure class="w-48 h-72 overflow-hidden rounded-lg shadow-md">
                                <x-image-book class="w-full h-full object-cover" alt="Capa de {{ $book->name }}" />
                            </figure>
                        @endif
                    </div>

                    <!-- detalhes -->
                    <div>
                        <h3 class="text-2xl font-bold">{{ $book->name }}</h3>
                        <div class="divider my-2"></div>

                        <div class="space-y-2 text-sm">
                            <p>
                                <span class="font-semibold">ISBN:</span> {{ $book->isbn }}
                            </p>
                            <p>
                                <span class="font-semibold">Autor(es):</span>{{ $book->authors->pluck('name')->join(', ') }}
                            </p>
                            <p>
                                <span class="font-semibold">Editora:</span> {{ $book->publisher->name }}
                            </p>
                            <p>
                                <span class="font-semibold">Preço:</span>
                                {{ $book->price = '€ ' . number_format($book->price, 2, ',', '.')}}
                            </p>

                            <div class="divider my-2"></div>

                            <p class="font-semibold">Sinopse:</p>
                            <p class="text-justify">{{ $book->bibliography }}</p>

                            <div class="divider my-2"></div>

                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-4">Avaliações</h3>

                                @forelse($book->reviews as $review)
                                    <div class=" bg-gray-50 dark:bg-gray-700 mb-4 p-4 rounded-lg shadow-sm">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold">{{ $review->user->name }}</span>
                                            <span
                                                class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
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

                            </div>
                            <div class="flex justify-end">
                                @auth
                                    <a href="{{ route('requests.create', $book->id) }}" class="btn btn-primary btn-sm mt-4">
                                        Requisitar
                                    </a>
                                @endauth

                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-4 ">
                                        Requisitar
                                    </a>
                                @endguest
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endforeach


    <section id="all-books" class="py-12 bg-base-100">
        <div class="container mx-auto p-4 ">

            <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center">Principais Livros</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6  mx-auto">
                @foreach ($allBooks as $book)
                    <div class="card bg-base-100 shadow hover:shadow-md transition-shadow">
                        @if ($book->cover_image)
                            <figure class="aspect-[2/3]">
                                <img src="{{ $book->cover_image }}" class="h-full w-full object-cover">
                            </figure>
                        @else
                            <figure class="aspect-[2/3]">
                                <x-image-book class="h-72 w-full object-cover" />
                            </figure>
                        @endif

                        <div class="card-body p-4">
                            <h2 class="card-title text-sm sm:text-base line-clamp-2">{{ $book->name }}</h2>
                            <p class="text-sm text-gray-500">{{ Str::limit($book->bibliography, 50) }}</p>
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
                            <div class="card-actions justify-end">
                                <label for="modal-{{ $book->id }}" class="btn btn-sm btn-outline">Detalhes</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center mt-8">
                <a href="{{ route('public.books.index') }}" class="btn btn-outline">
                    Ver Todos os Livros
                </a>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.multi-card-carousel', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
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
</x-guest-layout>