<x-app-layout>
    <style>
        .alert-close {
            @apply cursor-pointer;
        }
    </style>
    <div class="container mx-auto px-4 py-8">
        <!--notificacao-->
        @if(session('success'))
            <div class="flex items-center justify-between alert alert-success shadow-lg mb-6" id="alert">

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="alert-close"
                        onclick="document.getElementById('alert').style.display='none'">
                        <i class=" fas fa-times text-white"></i>
                    </button>
                </div>

            </div>

        @endif

        @if(session('error'))
            <div class="flex items-center justify-between alert alert-error shadow-lg mb-6" id="alert">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="alert-close"
                        onclick="document.getElementById('alert').style.display='none'">
                        <i class=" fas fa-times text-white"></i>
                    </button>
                </div>

            </div>
        @endif
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden">

                <div class="flex justify-start mb-3 items-center p-4">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Importar Livros do Google Books
                        <i class="fa fa-download ml-2" aria-hidden="true"></i>
                    </h1>
                </div>

                <x-resources.filters action="{{ route('books.import') }}" :clear-url="route('books.import')">
                    <div class="form-control w-full max-w-xs">
                        <label for="title" class="label">Título</label>
                        <input type="text" name="title" id="title" value="{{ request('title') }}"
                            class="input input-bordered w-full" placeholder="Laravel">
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label for="author" class="label">Autor</label>
                        <input type="text" name="author" id="author" value="{{ request('author') }}"
                            class="input input-bordered w-full" placeholder="Taylor Otwell">
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label for="isbn" class="label">ISBN</label>
                        <input type="text" name="isbn" id="isbn" value="{{ request('isbn') }}"
                            class="input input-bordered w-full" placeholder="9781234567890">
                    </div>

                </x-resources.filters>


                @if(!empty($books) && count($books) > 0)
                    <h2 class="text-2xl font-semibold mb-4">Resultados</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($books as $index => $book)
                            @php
                                $info = $book['volumeInfo'] ?? [];
                                $thumbnail = data_get($info, 'imageLinks.thumbnail');
                            @endphp

                            <div class="card bg-base-100 shadow hover:shadow-md transition-shadow">
                                <figure class="aspect-[2/3]">
                                    @if($thumbnail)
                                        <img src="{{ $thumbnail }}" alt="Capa do livro" class="h-full w-full object-cover">
                                    @else
                                        <img src="https://placehold.co/200x300" alt="Capa padrão"
                                            class="h-full w-full object-cover">
                                    @endif
                                </figure>

                                <div class="card-body p-4">
                                    <h2 class="card-title line-clamp-2 text-sm sm:text-base">
                                        {{ Str::limit($info['title'] ?? 'Sem título', 50) }}
                                    </h2>
                                    <p class="text-sm text-gray-600 mb-2">
                                        por {{ $info['authors'][0] ?? 'Autor desconhecido' }}
                                    </p>
                                    <div class="card-actions justify-end">
                                        <label for="bookModal{{ $index }}" class="btn btn-sm btn-info">Detalhes</label>
                                    </div>
                                </div>
                            </div>

                            <!-- modal -->
                            <input type="checkbox" id="bookModal{{ $index }}" class="modal-toggle" />
                            <div class="modal">
                                <div class="modal-box max-w-3xl">
                                    <label for="bookModal{{ $index }}" class="btn btn-sm btn-circle absolute right-2 top-2">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </label>
                                    <div class="flex flex-col md:flex-row gap-6">
                                        <figure class="flex-shrink-0">
                                            @if($thumbnail)
                                                <img src="{{ $thumbnail }}" alt="Capa do livro"
                                                    class="w-48 h-64 object-cover rounded-lg shadow-md">
                                            @else
                                                <img src="https://placehold.co/200x300" alt="Capa padrão"
                                                    class="w-48 h-64 object-cover rounded-lg shadow-md">
                                            @endif
                                        </figure>

                                        <div>
                                            <h3 class="font-bold text-2xl">{{ $info['title'] ?? 'Sem título' }}</h3>
                                            <div class="divider my-2"></div>
                                            <div class="space-y-2 text-sm">
                                                <p><strong>ISBN:</strong>
                                                    {{ $info['industryIdentifiers'][0]['identifier'] ?? '---' }}</p>
                                                <p><strong>Autor(es):</strong>
                                                    {{ implode(', ', $info['authors'] ?? ['Desconhecido']) }}</p>
                                                <p><strong>Publicado:</strong> {{ $info['publishedDate'] ?? '---' }}</p>
                                                <p><strong>Editora:</strong> {{ $info['publisher'] ?? '---' }}</p>
                                                <div class="divider my-2"></div>
                                                <p><strong>Sinopse:</strong>
                                                    {!! $info['description'] ?? 'Sem descrição disponível' !!}</p>
                                            </div>
                                            <div class="divider my-2"></div>
                                            <div class="modal-action">
                                                <form action="{{ route('books.storeGoogle') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="book" value='@json($info)'>
                                                    <button type="submit" class="btn btn-success text-white">Importar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>


                    <div class="mt-6 flex justify-center space-x-2">
                        @php $startIndex = request()->get('startIndex', 0); @endphp
                        @if($startIndex > 0)
                            <a href="{{ route('books.import', array_merge(request()->all(), ['startIndex' => $startIndex - 12])) }}"
                                class="btn btn-outline">⬅️ Anterior</a>
                        @endif
                        @if(count($books) == 12)
                            <a href="{{ route('books.import', array_merge(request()->all(), ['startIndex' => $startIndex + 12])) }}"
                                class="btn btn-outline">Próximo ➡️</a>
                        @endif
                    </div>
                @elseif(request()->all())

                    <div class="text-center py-12">
                        <p class="text-lg text-gray-600">Nenhum livro encontrado para a pesquisa.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>