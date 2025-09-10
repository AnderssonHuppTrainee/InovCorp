<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto px-4 py-4">
            <x-resources.header title="Gestão de Utilizadores" createRoute="{{ route('users.create') }}"
                exportRoute="{{ route('export.users') }}" />

            <!-- filtros -->
            <x-resources.filters action="{{ route('users.index') }}" clearUrl="{{ route('users.index') }}">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome do utilizador" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>
            </x-resources.filters>

            <!-- table -->
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="whitespace-nowrap">
                                    <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'name',
    'direction' => request('sort') === 'name' && request('direction') === 'desc' ? 'asc' : 'desc'
]) }}" class="flex items-center">
                                        Nome
                                        @if(request('sort') === 'name')
                                            <i
                                                class="fas fa-sort-{{ request('direction') === 'desc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="whitespace-nowrap">Foto</th>
                                <th class="whitespace-nowrap">Email</th>
                                <th class="whitespace-nowrap">Tipo</th>
                                <th class="whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></td>
                                    <td>
                                        @if($user->profile_photo_path)
                                            <img src="{{ $user->profile_photo_path }}" alt="{{ $user->name }}"
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="avatar placeholder w-10 h-10">
                                                <img src="https://avatar.iran.liara.run/public"
                                                    class=" rounded-full object-cover">
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role === 'admin' ? 'Administrador' : 'Cidadão' }}</td>
                                    <td class="flex space-x-2">
                                        <a href="{{ route('users.show', $user) }}"
                                            class="btn btn-sm btn-neutral text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-error text-white"
                                                onclick="return confirm('Tem certeza que deseja excluir este utilizador?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Nenhum utilizador encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- pagination -->
            </div>
            <x-resources.pagination :items="$users" />
        </div>
</x-app-layout>