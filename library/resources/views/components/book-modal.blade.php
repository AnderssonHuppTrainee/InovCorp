{{-- Modal toggle checkbox --}}
<input type="checkbox" id="modal-{{ $book->id }}" class="modal-toggle" />

<div class="modal">
    <div class="modal-box max-w-3xl relative" style="max-height: 70vh;">

        <label for="modal-{{ $book->id }}" class="btn btn-sm btn-circle absolute right-2 top-2">
            <i class="fa fa-times" aria-hidden="true"></i>
        </label>

        <div class="flex flex-col md:flex-row gap-6">

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


            <div class="flex-1">
                <h3 class="text-2xl font-bold">{{ $book->name }}</h3>
                <div class="divider my-2"></div>

                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
                    <p><span class="font-semibold">Autor(es):</span> {{ $book->authors->pluck('name')->join(', ') }}</p>
                    <p><span class="font-semibold">Editora:</span> {{ $book->publisher->name }}</p>
                    <p><span class="font-semibold">Preço:</span> € {{ number_format($book->price, 2, ',', '.') }}</p>

                    <div class="divider my-2"></div>

                    <p class="font-semibold">Sinopse:</p>
                    <p class="text-justify">{{ $book->bibliography }}</p>

                    <div class="divider my-2"></div>

                    <h3 class="text-lg font-semibold mb-4">Avaliações</h3>
                    <div class="max-h-60 overflow-y-auto pr-2 space-y-4">
                        @forelse($book->reviews as $review)
                            <div class="bg-gray-50 dark:bg-gray-700 mb-4 p-4 rounded-lg shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold">{{ $review->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>

                                {{-- Rating --}}
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
                                    <span
                                        class="ml-2 text-sm text-gray-500">{{ number_format($review->rating, 1) }}/5</span>
                                </div>

                                <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Nenhuma avaliação disponível para este livro.</p>
                        @endforelse
                    </div>
                    <div class="flex justify-end mt-2 gap-2">
                        @auth
                            <div>
                                <form action="{{ route('requests.create', $book->id) }}" method="GET">
                                    @csrf
                                    <button type="submit" class="btn btn-outline">
                                        Requisitar
                                    </button>
                                </form>
                            </div>
                            <div>
                                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary text-white">
                                        Adicionar ao Carrinho
                                    </button>
                                </form>
                            </div>

                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-4 text-white">
                                Adicionar ao carrinho
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>