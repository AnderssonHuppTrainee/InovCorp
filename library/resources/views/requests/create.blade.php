<x-app-layout>
    <div class="container mx-auto px-4 py-8">


        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto p-4">
            <h1 class=" card-title text-3xl font-bold mb-6">Requisitar Livro</h1>
            <div class="bg-white overflow-hidden ">

                <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">

                    <div class="flex flex-col md:flex-row gap-6 mb-6">

                        <div class="flex-shrink-0">
                            <figure class="w-48 h-64 overflow-hidden rounded-lg shadow-md">
                                <img src="{{ $book->cover_image }}" class="w-full h-full object-cover"
                                    alt="Capa de {{ $book->name }}">
                            </figure>
                        </div>


                        <div class="flex-1">
                            <h2 class="text-xl font-semibold mb-4">{{ $book->name }}</h2>
                            <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
                            <p><span class="font-semibold">Autor(es):</span>
                                {{ $book->authors->pluck('name')->join(', ')  }}</p>
                            <p class="mt-2"><span class="font-semibold">Sinopse:</span></p>
                            <p class="text-justify text-gray-700">{{ $book->bibliography }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-label for="photo" :value="__('Foto de Identificação')" />
                        <x-input id="photo" class="block mt-1 w-full" type="file" name="photo" required />
                        <p class="text-sm text-gray-500 mt-1">Envie uma foto sua segurando seu documento
                            de
                            identificação.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="fas fa-check mr-2"></i> Confirmar Requisição
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>