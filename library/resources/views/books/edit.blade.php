<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Editar </h1>
            </div>

            <x-form action="{{ route('books.update', $book) }}" method="PUT" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="isbn" label="ISBN" required value="{{ old('isbn', $book->isbn) }}" />

                    <x-input name="name" label="Título do Livro" required value="{{ old('name', $book->name) }}" />

                    <x-input name="publisher_id" label="Editora" type="select" required
                        :value="old('publisher_id', $book->publisher_id)" :options="$publishers->pluck('name', 'id')" />

                    <x-input name="price" label="Preço (€)" type="number" step="0.01" min="0" required
                        value="{{ old('price', $book->price) }}" />

                    <x-input name="cover_image" label="Capa do Livro" type="file"
                        helpText="Deixe em branco para manter a imagem atual" />

                    @if($book->cover_image)
                        <div class="md:col-span-2">
                            <label class="label">
                                <span class="label-text">Imagem Atual</span>
                            </label>
                            <img src="{{  $book->cover_image }}" alt="Capa do livro {{ $book->name }}"
                                class="w-32 h-48 object-contain border rounded">
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <x-input name="bibliography" label="Bibliografia" type="textarea" rows="5"
                            value="{{ old('bibliography', $book->bibliography) }}" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="label">
                            <span class="label-text">Autores</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-2 border rounded-md 
                        scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 
                    dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">
                                @foreach($authors as $author)
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="authors[]" value="{{ $author->id }}"
                                            class="checkbox checkbox-primary" {{ in_array($author->id, old('authors', $book->authors->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span>{{ $author->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('authors')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                </div>
            </x-form>
        </div>
    </div>
</x-app-layout>