<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto px-4 py-4">
            <x-resources.header title="Gestão de Autores" createRoute="{{ route('authors.create') }}"
                exportRoute="{{ route('export.authors') }}" />

            <!-- filtros -->
            <x-resources.filters action="{{ route('authors.index') }}" clearUrl="{{ route('authors.index') }}">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome do autor" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>
            </x-resources.filters>

            <!-- table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <table class="table table-zebra w-full">
                    <thead>
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
                            <th class="whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery([
    'sort' => 'book',
    'direction' => request('sort') === 'book' && request('direction') === 'asc' ? 'desc' : 'asc'
]) }}" class="flex items-center">
                                    Livros
                                    @if(request('sort') === 'isbn')
                                        <i
                                            class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="whitespace-nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($authors as $author)
                            <tr>
                                <td><a href="{{ route('authors.show', $author) }}">{{ $author->name }}</a></td>

                                <td>
                                    @if($author->photo)
                                        <img src="{{ asset('storage/' . $author->photo) }}" alt="{{ $author->name }}"
                                            class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="avatar placeholder w-10 h-10">
                                            <img src="https://avatar.iran.liara.run/public" class=" rounded-full object-cover">
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ $author->books->pluck('name')->join(', ') ?: 'Nenhum livro' }}
                                </td>
                                <td class="flex space-x-2">
                                    <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-info  text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('authors.destroy', $author) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error  text-white"
                                            onclick="return confirm('Tem certeza que deseja excluir este autor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Nenhum autor encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- pagination -->
            <x-resources.pagination :items="$authors" />
        </div>
    </div>
</x-app-layout>