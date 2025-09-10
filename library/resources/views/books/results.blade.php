<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Resultados da pesquisa</h1>

        @if(count($books) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($books as $index => $book)
                    @php
                        $info = $book['volumeInfo'] ?? [];
                    @endphp

                    <div class="card bg-base-100 shadow-xl">
                        @if(isset($info['imageLinks']['thumbnail']))
                            <figure>
                                <img src="{{ $info['imageLinks']['thumbnail'] }}" alt="Capa do livro"
                                    class="h-64 w-full object-cover">
                            </figure>
                        @endif
                        <div class="card-body">
                            <h2 class="card-title text-base">
                                {{ Str::limit($info['title'] ?? 'Sem título', 50) }}
                            </h2>
                            <p class="text-sm text-gray-500">{{ $info['authors'][0] ?? 'Autor desconhecido' }}</p>
                            <div class="card-actions justify-end">
                                <label for="bookModal{{ $index }}" class="btn btn-sm btn-info">Detalhes</label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal DaisyUI -->
                    <input type="checkbox" id="bookModal{{ $index }}" class="modal-toggle" />
                    <div class="modal">
                        <div class="modal-box max-w-3xl">
                            <h3 class="font-bold text-lg mb-4">{{ $info['title'] ?? 'Sem título' }}</h3>

                            <div class="space-y-2 text-sm">
                                <p><strong>Autor(es):</strong> {{ implode(', ', $info['authors'] ?? ['Desconhecido']) }}</p>
                                <p><strong>Publicado:</strong> {{ $info['publishedDate'] ?? '---' }}</p>
                                <p><strong>Editora:</strong> {{ $info['publisher'] ?? '---' }}</p>
                                <p><strong>Descrição:</strong> {!! $info['description'] ?? 'Sem descrição disponível' !!}</p>
                                <p><strong>ISBN:</strong> {{ $info['industryIdentifiers'][0]['identifier'] ?? '---' }}</p>
                            </div>

                            <div class="modal-action">
                                <form action="{{ route('books.storeGoogle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="books[]" value="{{ htmlspecialchars(json_encode($info)) }}">
                                    <button type="submit" class="btn btn-success">Importar</button>
                                </form>
                                <label for="bookModal{{ $index }}" class="btn">Fechar</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            <div class="mt-6 flex justify-center space-x-2">
                @if(request()->get('startIndex', 0) > 0)
                    <a href="{{ route('books.searchGoogle', array_merge(request()->all(), ['startIndex' => request()->get('startIndex', 0) - 12])) }}"
                        class="btn btn-outline">⬅️ Anterior</a>
                @endif
                @if(count($books) == 12)
                    <a href="{{ route('books.searchGoogle', array_merge(request()->all(), ['startIndex' => request()->get('startIndex', 0) + 12])) }}"
                        class="btn btn-outline">Próximo ➡️</a>
                @endif
            </div>
        @else
            <p class="text-gray-500">Nenhum livro encontrado.</p>
        @endif
    </div>
</x-app-layout>