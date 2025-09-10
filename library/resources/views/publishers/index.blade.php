<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto px-4 py-4">
            <x-resources.header title="Gestão de Editoras" createRoute="{{ route('publishers.create') }}"
                exportRoute="{{ route('export.publishers') }}" />

            <x-resources.filters action="{{ route('publishers.index') }}" clearUrl="{{ route('publishers.index') }}">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome da editora" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>
            </x-resources.filters>


            <!-- Tabela -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-x-auto">
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

                            <th class="whitespace-nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($publishers as $publisher)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($publisher->logo)
                                            <img src="{{ $publisher->logo }}" alt="{{ $publisher->name }}"
                                                class="w-50 h-50 object-containt">
                                        @else
                                            <img src="https://placehold.co/50" class="object-cover">
                                        @endif
                                        <div class="font-medium">{{ $publisher->name }}</div>
                                    </div>
                                </td>
                                <td class="flex space-x-2">
                                    <a href="{{ route('publishers.show', $publisher) }}"
                                        class="btn btn-sm btn-neutral  text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('publishers.edit', $publisher) }}"
                                        class="btn btn-sm btn-info  text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('publishers.destroy', $publisher) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error  text-white"
                                            onclick="return confirm('Tem certeza que deseja excluir esta editora?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Nenhuma editora encontrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <div class="mt-4">
                {{ $publishers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>