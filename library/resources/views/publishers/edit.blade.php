<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Editar </h1>
            </div>

            <x-form action="{{ route('publishers.update', $publisher) }}" method="PUT" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-6">
                    <x-input name="name" label="Nome da Editora" required value="{{ old('name', $publisher->name) }}" />

                    <x-input name="logo" label="Logotipo" type="file"
                        helpText="Deixe em branco para manter o logotipo atual" />

                    @if($publisher->logo)
                        <div>
                            <label class="label">
                                <span class="label-text">Logotipo Atual</span>
                            </label>
                            <img src="{{ asset('storage/' . $publisher->logo) }}" alt="Logo de {{ $publisher->name }}"
                                class="w-32 h-32 object-contain border">
                        </div>
                    @endif
                </div>
            </x-form>
        </div>
    </div>
</x-app-layout>