<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Adicionar Nova Editora</h1>
        </div>

        <x-form action="{{ route('publishers.store') }}" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-6">
                <x-input name="name" label="Nome da Editora" required />

                <x-input name="logo" label="Logotipo" type="file" helpText="Formatos: JPG, PNG (Max: 2MB)" />
            </div>
        </x-form>
    </div>
</x-app-layout>