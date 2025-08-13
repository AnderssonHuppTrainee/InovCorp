<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('authors.index') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i> Voltar para lista
        </a>
    </div>
    <div class="container mx-auto px-4 py-8">

        <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
            @if($user->photo)
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}"
                        class="w-32 h-32 rounded-full object-cover shadow-md">
                </div>
            @else
                <div class="flex-shrink-0">
                    <img src="https://picsum.photos/seed/{{ substr($user->name, 0, 100) }}/128/128" alt="{{ $user->name }}"
                        class="rounded-full w-32 h-32 object-cover">
                </div>
            @endif

            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <div class="mt-4">
                    <a href="{{ route('authors.edit', $user) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit mr-2"></i> Editar Utilizador
                    </a>
                </div>
            </div>
        </div>
        <!--lista de REQUESTS-->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Histórico de Requisições</h2>

            @if($requests->count())
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Data da Solicitação</th>
                                <th>Data Esperada Devolução</th>
                                <th>Status</th>
                                <th>Condição (Devolução)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td>{{ $req->book->name ?? 'Livro removido' }}</td>
                                    <td>{{ $req->request_date->format('d/m/Y') }}</td>
                                    <td>{{ $req->expected_return_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <span class="badge
                                                                                    @if($req->status === 'approved') badge-success
                                                                                    @elseif($req->status === 'returned') badge-info
                                                                                    @elseif($req->status === 'rejected') badge-error
                                                                                    @else badge-warning @endif">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $req->book_condition ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Nenhuma requisição encontrada para este usuário.</p>
            @endif
        </div>
</x-app-layout>