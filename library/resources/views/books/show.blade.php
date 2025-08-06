<x-app-layout>

    <div class="mb-6">
        <a href="{{ route('books.index') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i> Voltar para lista
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">

            <div class="md:w-1/3 p-6 flex justify-center">
                <div class="w-full max-w-xs">
                    <x-image-book class="w-full h-auto rounded-lg shadow-md" alt="Capa de {{ $book->name }}" />
                </div>
            </div>


            <div class="md:w-2/3 p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ $book->name }}</h1>


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
                            @php
                                try {
                                    $price = Crypt::decryptString($book->price);
                                    echo '€ ' . number_format($price, 2, ',', '.');
                                } catch (Exception $e) {
                                    echo '-';
                                }
                            @endphp
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Data de Publicação</h3>
                        <p class="text-lg">{{ $book->published_at?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                <!-- Autores -->
                <div class="mb-6">
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
                    <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                        {{ $book->bibliography ?? 'Nenhuma sinopse disponível.' }}
                    </p>
                </div>

                <!-- Ações -->
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-info">
                        <i class="fas fa-edit mr-2"></i> Editar
                    </a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error"
                            onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                            <i class="fas fa-trash mr-2"></i> Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção Adicional (opcional) 
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Detalhes Adicionais</h2>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Informações Técnicas</h3>
                        <ul class="space-y-2">
                            <li><strong>Páginas:</strong> {{ $book->pages ?? '-' }}</li>
                            <li><strong>Idioma:</strong> {{ $book->language ?? '-' }}</li>
                            <li><strong>Edição:</strong> {{ $book->edition ?? '-' }}</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Estatísticas</h3>
                        <ul class="space-y-2">
                            <li><strong>Cadastrado em:</strong> {{ $book->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Última atualização:</strong> {{ $book->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>-->

</x-app-layout>