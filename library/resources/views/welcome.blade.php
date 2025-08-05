<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        .multi-card-carousel {
            width: 100%;
            padding: 20px 40px;
            /* Espaço para os botões */
            position: relative;
        }

        /* Botões de navegação */
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
            <h1 class="text-4xl font-bold mb-4">Bem-vindo à Amazing Library</h1>
            <p class="text-lg mb-6 text-gray-600">
                Descubra, explore e leia os melhores livros com facilidade.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="btn btn-primary">Acessar Conta</a>
                <a href="#all-books" class="btn btn-outline">Explorar Livros</a>
            </div>
        </div>
    </section>

    <!-- Carrossel dos ultimos livros) -->
    <section class="px-6 py-12 bg-base-200 w-full">
        <h2 class="text-2xl font-bold mb-8 text-center">Últimos Livros Adicionados</h2>

        <div class="swiper multi-card-carousel">
            <div class="swiper-wrapper">
                @foreach ($latestBooks as $book)
                    <div class="swiper-slide">
                        <!-- Card usando classes do DaisyUI -->
                        <div class="card w-64 bg-base-100 shadow-xl mx-2">
                            <!-- Capa -->
                            <div class="w-[200px] h-[300px] mx-auto mt-4 overflow-hidden">
                                <figure>
                                    <x-image-book class="w-full h-full object-cover transition-transform hover:scale-105"
                                        alt="Capa de {{ $book->name }}" />
                                </figure>
                            </div>

                            <!-- Conteúdo -->
                            <div class="card-body">
                                <h3 class="card-title">{{ $book->name }}</h3>

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
    </section>
    <!-- modal para cada livro -->
    @foreach ($latestBooks as $book)
        <!-- Input checkbox invisível que controla o modal -->
        <input type="checkbox" id="modal-{{ $book->id }}" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box max-w-2xl relative">
                <label for="modal-{{ $book->id }}" class="btn btn-sm btn-circle absolute right-2 top-2">
                    ✕
                </label>

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Capa do livro -->
                    <figure class="flex-shrink-0">
                        <x-image-book class="w-48 h-64 object-cover rounded-lg shadow-md" alt="Capa de {{ $book->name }}" />
                    </figure>

                    <!-- Detalhes -->
                    <div>
                        <h3 class="text-2xl font-bold">{{ $book->name }}</h3>
                        <div class="divider my-2"></div>

                        <div class="space-y-2">
                            <p><span class="font-semibold">Autor:</span> {{ $book->author }}</p>
                            <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
                            <p><span class="font-semibold">Preço:</span>
                                @php
                                    try {
                                        $price = Crypt::decryptString($book->price);
                                        echo '€ ' . number_format($price, 2, ',', '.');
                                    } catch (Exception $e) {
                                        echo '-';
                                    }
                                @endphp
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
    <section id="all-books" class="px-6 py-12 w-full bg-base-100">
        <h2 class="text-2xl font-bold mb-6 text-center">Todos os Livros</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            @foreach ($allBooks as $book)
                <div class="card bg-base-100 shadow">
                    <figure><x-image-book class="h-48 w-full object-cover" /></figure>
                    <div class="card-body">
                        <h2 class="card-title">{{ $book->name }}</h2>
                        <p class="text-sm text-gray-500">{{ Str::limit($book->bibliography, 50) }}</p>
                        <div class="card-actions justify-end">
                            <label for="modal-{{ $book->id }}" class="btn btn-sm btn-outline">Detalhes</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $allBooks->links() }}
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
                slidesPerView: 3,
                spaceBetween: 40,
                centeredSlides: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2, spaceBetween: 15 },
                    1024: { slidesPerView: 3, spaceBetween: 20 }
                }
            });

        });
    </script>
</x-guest-layout>