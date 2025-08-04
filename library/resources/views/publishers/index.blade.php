<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Editoras</h1>
            <a href="{{ route('publishers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Nova Editora
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">

            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="form-control w-full max-w-xs">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" placeholder="Nome da editora" value="{{ request('search') }}"
                        class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                <div class="form-control">
                    <a href="{{ route('publishers.index') }}" class="btn btn-outline">Limpar</a>
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">Nome</th>
                        <th class="whitespace-nowrap">Logo</th>
                        <th class="whitespace-nowrap">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($publishers as $publisher)
                        <tr>
                            <td>{{ $publisher->name }}</td>

                            <td>
                                @if($publisher->logo)
                                    <img src="{{ asset('storage/' . $publisher->logo) }}" alt="{{ $publisher->name }}"
                                        class="w-10 h-10 rounded-full object-containt">
                                @else
                                    <div class="logo placeholder">
                                        <div class="bg-neutral-focus text-neutral-content rounded-full w-10">
                                            <span>{{ substr($publisher->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <td class="flex space-x-2">
                                <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('publishers.destroy', $publisher) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error"
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

        <!-- Paginação -->
        <div class="mt-4">
            {{ $publishers->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>