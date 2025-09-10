<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Editar </h1>
            </div>
            <x-form action="{{ route('users.update', $user) }}" method="PUT" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-6">
                    <x-input name="name" label="Nome" required value="{{ old('name', $user->name) }}" />
                    <x-input name="email" label="Email" required value="{{ old('email', $user->email) }}" />

                    <div>
                        <label class="label">
                            <span class="label-text">Tipo de Utilizador</span>
                        </label>
                        <select name="role" class="select select-bordered w-full" required>
                            <option value="citizen" @selected(old('role', $user->role) === 'citizen')>Cidadão</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrador</option>
                        </select>
                    </div>

                    <x-input name="photo" label="Foto" type="file"
                        helpText="Deixe em branco para manter a foto atual" />

                    @if($user->profile_photo_path)
                        <div>
                            <label class="label">
                                <span class="label-text">Foto Atual</span>
                            </label>
                            <img src="{{ $user->profile_photo_path }}" alt="Foto de {{ $user->name }}"
                                class="w-32 h-32 rounded-full object-cover border">
                        </div>
                    @endif
                </div>
            </x-form>
        </div>
    </div>
</x-app-layout>