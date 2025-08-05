<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Adicionar Novo Livro</h1>
        </div>

        <x-form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data"
            submitText="Cadastrar Livro">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <x-input name="isbn" label="ISBN" required placeholder="Ex: 978-8532530783" />


                <x-input name="name" label="Título do Livro" required />


                <x-input name="publisher_id" label="Editora" type="select" required
                    :options="$publishers->pluck('name', 'id')" />


                <x-input name="price" label="Preço (€)" type="number" step="0.01" min="0" required />


                <x-input name="cover_image" label="Capa do Livro" type="file"
                    helpText="Formatos: JPG, PNG (Max: 2MB)" />

                <div class="md:col-span-2">
                    <x-input name="bibliography" label="Bibliografia" type="textarea" rows="5" />
                </div>


                <div class="md:col-span-2">
                    <label class="label">
                        <span class="label-text">Autores</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($authors as $author)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="authors[]" value="{{ $author->id }}"
                                    class="checkbox checkbox-primary">
                                <span>{{ $author->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('authors')
                        <div class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>
        </x-form>
    </div>
</x-app-layout>