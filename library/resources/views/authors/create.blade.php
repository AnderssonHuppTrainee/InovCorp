<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Adicionar Novo Autor</h1>
        </div>

        <x-form action="{{ route('authors.store') }}" method="POST" enctype="multipart/form-data"
            submitText="Cadastrar Autor">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                <x-input name="name" label="Nome do Autor" required />

                <x-input name="photo" label="Profile Photo" type="file" helpText="Formatos: JPG, PNG (Max: 2MB)" />
            </div>
        </x-form>
    </div>
</x-app-layout>