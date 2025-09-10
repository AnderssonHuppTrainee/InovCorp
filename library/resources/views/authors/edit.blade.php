<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Editar Autor</h1>
            </div>

            <x-form action="{{ route('authors.update', $author) }}" method="PUT" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-6">
                    <x-input name="name" label="Nome do Autor" required value="{{ old('name', $author->name) }}" />

                    <x-input name="photo" label="Foto do Autor" type="file"
                        helpText="Deixe em branco para manter a foto atual" />

                    @if($author->photo)
                        <div>
                            <label class="label">
                                <span class="label-text">Foto Atual</span>
                            </label>
                            <img src="{{ asset('storage/' . $author->photo) }}" alt="Foto de {{ $author->name }}"
                                class="w-32 h-32 rounded-full object-cover border">
                        </div>
                    @endif
                </div>
            </x-form>
        </div>
    </div>
</x-app-layout>