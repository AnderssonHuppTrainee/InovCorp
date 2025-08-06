<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Autores</h1>
            <a href="{{ route('authors.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Novo Autor
            </a>
        </div>

        <!-- filtros -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">

            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome do autor" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                <div class="form-control">
                    <a href="{{ route('authors.index') }}" class="btn btn-outline">Limpar</a>
                </div>
            </form>
        </div>

        <!-- table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Foto</th>
                        <th>Livros</th>
                        <th>Ações</th>
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
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral-focus text-neutral-content rounded-full w-10">
                                            <span>{{ substr($author->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ $author->books->pluck('name')->join(', ') ?: 'Nenhum livro' }}
                            </td>
                            <td class="flex space-x-2">
                                <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('authors.destroy', $author) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error"
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
        <div class="mt-4">
            {{ $authors->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>