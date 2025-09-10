<x-app-layout>

    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white">
                <span><i class="fa fa-circle-check mr-3"></i>{{ session('success') }}</span>
            </div>
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
                    <h1 class="text-3xl font-bold text-base-content">
                        Moderação de Avaliações
                        <i class="fa fa-star mr-2"></i>
                    </h1>
                </div>
                <x-resources.filters action="{{ route('reviews.index') }}" clearUrl="{{ route('reviews.index') }}">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">Pesquisar</span>
                        </label>
                        <input type="text" name="search" placeholder="Utilizador ou Livro"
                            value="{{ request('search') }}" class="input input-bordered w-full">
                    </div>
                </x-resources.filters>

                @if($reviews->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6 text-white">
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
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    @if($review->bookRequest->book->cover_image)
                                                        <img src="{{ $review->bookRequest->book->cover_image }}"
                                                            alt="{{ $review->bookRequest->book->name }}"
                                                            class="w-12 h-15 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/46x70" />
                                                    @endif

                                                    <div class="font-bold">
                                                        {{ $review->bookRequest->book->name ?? 'Livro removido' }}
                                                    </div>
                                                </div>
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
                                            <td class=" flex justify-center">
                                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline">
                                                    <i class="fa fa-eye"></i>
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-5 px-4 pb-4 sm:px-6">
                            {{ $reviews->links() }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>