<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-3">
            <a href="{{ url()->previous()}}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                @if($author->photo)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $author->photo) }}" alt="{{ $author->name }}"
                            class="w-32 h-32 rounded-full object-cover shadow-md">
                    </div>
                @else
                    <div class="flex-shrink-0">
                        <img src="https://avatar.iran.liara.run/public" class="rounded-full w-32 h-32 object-cover">
                    </div>
                @endif

                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $author->name }}</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">
                        {{ $author->books->count() }} {{ Str::plural('livro', $author->books->count()) }}
                    </p>

                    <!-- edicao -->

                    <div class="mt-4">
                        <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                    </div>

                </div>
            </div>

            <!-- lista de Livros -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200 border-b pb-2">
                    Livros do Autor
                </h2>

                @if($books->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($books as $book)
                            <div
                                class="card bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-shadow duration-300">
                                @if($book->cover_image)
                                    <figure class="px-4 pt-4 ">
                                        <img src="{{ $book->cover_image }}" alt="Capa de {{ $book->name }}"
                                            class="rounded-xl w-full h-full object-cover">
                                    </figure>
                                @else
                                    <figure class="px-4 pt-4">
                                        <x-image-book class="rounded-xl h-48 w-full object-cover" />
                                    </figure>
                                @endif
                                <div class="card-body">
                                    <h3 class="card-title text-gray-900 dark:text-white">
                                        <a href="{{ route('books.show', $book) }}"
                                            class="hover:text-primary dark:hover:text-primary">
                                            {{ $book->name }}
                                        </a>
                                    </h3>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <p><span class="font-medium">Editora:</span> {{ $book->publisher->name ?? 'N/A' }}</p>
                                        <p><span class="font-medium">ISBN:</span> {{ $book->isbn }}</p>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info shadow-lg">
                        <div>
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Nenhum livro encontrado para este autor.</span>
                        </div>
                    </div>
                @endif
            </div>


            @if($books->hasPages())
                <div class="mt-8">
                    {{ $books->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>