<x-app-layout>

    <div class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>

        <div class="card mx-auto">
            <div class="card-body p-4">
                <div class="card-title mb-6">
                    <h1 class="text-3xl font-bold text-base-content">Moderação de Avaliações</h1>
                </div>

                @if($reviews->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhuma avaliação encontrada.</span>
                        </div>
                    </div>
                @else

                    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Livro
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Utilizador
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Comentário
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($reviews as $review)
                                        <tr>
                                            <td>
                                                {{ $review->bookRequest->book->name ?? 'Livro removido' }}
                                            </td>
                                            <td>
                                                {{ $review->user->name }}
                                            </td>
                                            <td>
                                                {{ Str::limit($review->comment, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-white">
                                                @if($review->status === 'suspended')
                                                    <span class="badge badge-lg ml-2 gap-1 text-white badge-warning">
                                                        <i class="fas fa-clock"></i>
                                                        Suspenso
                                                    </span>
                                                @elseif($review->status === 'active')
                                                    <span class="badge badge-lg ml-2 gap-1 text-white badge-success">
                                                        <i class="fas fa-check-circle"></i>
                                                        Ativo
                                                    </span>
                                                @else
                                                    <span class="badge badge-lg ml-2 gap-1 text-white badge-error">
                                                        <i class="fas fa-times-circle"></i>
                                                        Recusado
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 pb-4 sm:px-6">
                            {{ $reviews->links() }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>