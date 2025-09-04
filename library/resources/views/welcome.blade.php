<x-guest-layout>


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
    <section class="hero flex items-center justify-center px-6 py-16 bg-base-100">
        <div class="hero-content text-center max-w-2xl">
            <div class="max-w-md">
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

                            <div
                                class="card bg-base-100 shadow hover:shadow-md transition-shadow mx-2 w-[200px] h-[420px] flex flex-col">
                                <!-- capa -->
                                <div class="w-full aspect-[2/3] overflow-hidden">
                                    @if ($book->cover_image)
                                        <img src="{{ $book->cover_image }}" class="w-full h-full object-cover"
                                            alt="Capa do livro">
                                    @else
                                        <x-image-book class="w-full h-full object-cover" />
                                    @endif
                                </div>

                                <!-- conteudo -->
                                <div class="card-body p-3 flex flex-col justify-between">
                                    <h2 class="card-title text-sm sm:text-base font-semibold min-h-[3rem] line-clamp-2"
                                        title="{{ $book->name }}">
                                        {{ $book->name }}
                                    </h2>

                                    <div>
                                        <div class="flex items-center text-xs">
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
                                        <p class="text-xs text-gray-600">{{ number_format($rating, 1, ',', '.') }} / 5</p>
                                    </div>

                                    <div class="card-actions justify-end mt-3">
                                        <label for="modal-{{ $book->id }}"
                                            class="btn btn-primary btn-sm text-white">Detalhes</label>
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
        <x-book-modal :book="$book" />
    @endforeach

    @foreach ($allBooks as $book)
        <x-book-modal :book="$book" />
    @endforeach

    <section id="all-books" class="py-12 bg-base-100">
        <div class="container mx-auto p-4">

            <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center">Melhores Avaliados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6  mx-auto ">
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
        function setSwiperSlideHeight() {
            const slides = document.querySelectorAll('.multi-card-carousel .swiper-slide');
            let maxHeight = 0;

            slides.forEach(slide => {
                const card = slide.querySelector('.card');
                card.style.height = 'auto'; // reset
                if (card.offsetHeight > maxHeight) {
                    maxHeight = card.offsetHeight;
                }
            });

            slides.forEach(slide => {
                const card = slide.querySelector('.card');
                card.style.height = maxHeight + 'px';
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.multi-card-carousel', {
                loop: true,
                autoplay: { delay: 5000, disableOnInteraction: false },
                slidesPerView: 1,
                spaceBetween: 10,
                centeredSlides: true,
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                breakpoints: {
                    480: { slidesPerView: 2, spaceBetween: 15 },
                    768: { slidesPerView: 3, spaceBetween: 20 },
                    1024: { slidesPerView: 4, spaceBetween: 25 },
                    1280: { slidesPerView: 5, spaceBetween: 30 },
                },
                on: {
                    init: setSwiperSlideHeight,
                    resize: setSwiperSlideHeight,
                }
            });
        });
    </script>
</x-guest-layout>