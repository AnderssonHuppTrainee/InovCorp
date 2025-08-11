<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Requisitar Livro</h1>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">

                        <div class="flex flex-col md:flex-row gap-6 mb-6">
                            <!-- Foto da capa -->
                            <div class="flex-shrink-0">
                                <figure class="w-48 h-64 overflow-hidden rounded-lg shadow-md">
                                    <x-image-book class="w-full h-full object-cover" :book="$book"
                                        alt="Capa do livro {{ $book->title }}" />
                                </figure>
                            </div>

                            <!-- Detalhes do livro -->
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold mb-2">{{ $book->name }}</h3>
                                <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
                                <p class="mt-2 text-gray-700"><span class="font-semibold">Sinopse:</span></p>
                                <p class="text-justify">{{ $book->bibliography }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-label for="photo" :value="__('Foto de Identificação')" />
                            <x-input id="photo" class="block mt-1 w-full" type="file" name="photo" required />
                            <p class="text-sm text-gray-500 mt-1">Envie uma foto sua segurando seu documento de
                                identificação.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Confirmar Requisição') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>