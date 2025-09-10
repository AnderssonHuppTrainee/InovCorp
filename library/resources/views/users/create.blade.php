<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Criar Novo Utilizador</h1>
            </div>

            <x-form action="{{ route('users.store')}}" method="POST" enctype="multipart/form-data"
                submitText="Cadastrar Utilizador">
                <div class="grid grid-cols-1 gap-6">
                    <x-input name="name" label="Nome" required />
                    <x-input name="email" label="Email" required />
                    <x-input name="photo" label="Profile Photo" type="file" helpText="Formatos: JPG, PNG (Max: 2MB)" />
                    <div>
                        <label class="label">
                            <span class="label-text">Tipo de Utilizador</span>
                        </label>
                        <select name="role" class="select select-bordered w-full" required>
                            <option value="citizen">Cidadão</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
</x-app-layout>