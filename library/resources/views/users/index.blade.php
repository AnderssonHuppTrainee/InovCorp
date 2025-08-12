<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <x-resources.header title="Utilizadores" createRoute="{{ route('users.create') }}" />

        <!-- Filtros -->

        <x-resources.filters action="{{ route('users.index') }}" clearUrl="{{ route('users.index') }}">
            <div class="form-control w-full max-w-xs">
                <label class="label">
                    <span class="label-text">Pesquisar</span>
                </label>
                <input type="text" name="search" placeholder="Nome do utilizador" value="{{ request('search') }}"
                    class="input input-bordered w-full">
            </div>
        </x-resources.filters>
        <!--<div class="mb-4">
            <x-button href="{{ route('users.create') }}">
                {{ __('Criar Novo Admin') }}
            </x-button>
        </div>-->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="table table-zebra w-full">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->role === 'admin' ? 'Administrador' : 'Cidadão' }}
                            </td>

                            <td class="flex space-x-2">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error text-white"
                                        onclick="return confirm('Tem certeza que deseja excluir esta editora?')">
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
    </div>

</x-app-layout>