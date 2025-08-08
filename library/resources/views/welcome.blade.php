<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        .multi-card-carousel {
            width: 100%;
            padding: 20px 0;
            /* Reduzi o padding lateral para ganhar espaço */
            position: relative;
        }

        .multi-card-carousel .swiper-slide {
            width: auto !important;
            /* Permite que os slides se ajustem ao conteúdo */
        }

        /* Ajuste o tamanho dos cards para se adaptarem melhor */
        .card {
            min-width: 200px;
            /* Largura mínima para manter a legibilidade */
            max-width: 260px;
            /* Largura máxima para não ficar muito grande */
            width: 100%;
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
                <a href="{{ route('login') }}" class="btn btn-primary">Acessar Conta</a>
                <a href="#all-books" class="btn btn-outline">Explorar Livros</a>
            </div>
        </div>
    </section>

    <!-- Carrossel dos ultimos livros -->
    <section class="py-12 bg-base-200">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-8 text-center">Últimos Livros Adicionados</h2>

            <div class="swiper multi-card-carousel">
                <div class="swiper-wrapper">
                    @foreach ($latestBooks as $book)
                        <div class="swiper-slide">

                            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow mx-2">
                                <!-- capa -->
                                <div class="w-[200px] h-[300px] mx-auto mt-4 overflow-hidden">
                                    <figure>
                                        <x-image-book
                                            class="w-full h-full object-cover transition-transform hover:scale-105"
                                            alt="Capa de {{ $book->name }}" />
                                    </figure>
                                </div>

                                <!-- conteudo -->
                                <div class="card-body">
                                    <h3 class="card-title">{{ Str::limit($book->name, 20) }}</h3>

                                    <label for="modal-{{ $book->id }}" class="btn btn-primary btn-sm  self-end mt-4">
                                        Detalhes
                                    </label>
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
                    ✕
                </label>

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- capa -->
                    <figure class="flex-shrink-0">
                        <x-image-book class="w-48 h-64 object-cover rounded-lg shadow-md" alt="Capa de {{ $book->name }}" />
                    </figure>

                    <!-- detalhes -->
                    <div>
                        <h3 class="text-2xl font-bold">{{ $book->name }}</h3>
                        <div class="divider my-2"></div>

                        <div class="space-y-2">
                            <p><span class="font-semibold">Autor:</span>{{ $book->authors->pluck('name')->join(', ') }}</p>
                            <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
                            <p><span class="font-semibold">Preço:</span>
                                {{ $book->price = '€ ' . number_format($book->price, 2, ',', '.')}}
                            </p>

                            <div class="divider my-2"></div>

                            <p class="font-semibold">Sinopse:</p>
                            <p class="text-justify">{{ $book->bibliography }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Todos os Livros -->
    <section id="all-books" class="py-12 bg-base-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center">Todos os Livros</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                @foreach ($allBooks as $book)
                    <div class="card bg-base-100 shadow hover:shadow-md transition-shadow">
                        <figure class="aspect-[2/3]">
                            <x-image-book class="h-48 w-full object-cover" />
                        </figure>
                        <div class="card-body p-4">
                            <h2 class="card-title text-sm sm:text-base line-clamp-2">{{ $book->name }}</h2>
                            <p class="text-sm text-gray-500">{{ Str::limit($book->bibliography, 50) }}</p>
                            <div class="card-actions justify-end">
                                <label for="modal-{{ $book->id }}" class="btn btn-sm btn-outline">Detalhes</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-actions justify-end mt-8">
                {{ $allBooks->links() }}
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