<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('reviews.index') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="card bg-base-100 mx-auto">
            <div class="card-body p-4">

                <div class="card-title mb-6">
                    <h1 class="text-3xl font-bold text-base-content">Detalhes da Avaliação</h1>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div class="flex justify-center">
                        <div
                            class="bg-base-200 rounded-lg shadow-md overflow-hidden w-40 h-60 flex items-center justify-center">
                            @if($review->bookRequest->book->cover_image)
                                <img src="{{ $review->bookRequest->book->cover_image }}"
                                    class="w-full h-full object-cover" />
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-book-open text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">Sem imagem</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-2xl">Requisição #{{ $review->bookRequest->number }}
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
                        </h2>
                        <div class="text-sm text-gray-500 mt-1">
                            Criada em: {{ $review->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <p><strong>Livro:</strong> {{ $review->bookRequest->book->name ?? 'Livro removido' }}</p>
                            <p><strong>Cidadão:</strong> {{ $review->user->name }} ({{ $review->user->email }})</p>
                            <p><strong>Avaliação:</strong> {{ $review->rating }}</p>
                            <p><strong>Comentário:</strong> {{ $review->comment }}</p>
                            <p><strong>Status Atual:</strong> {{ ucfirst($review->status) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row gap-3 items-center justify-end">
                    @if ($review->status === 'suspended')
                        <div>
                            <form action="{{ route('reviews.update', $review) }}" method="POST" class="mt-2 flex gap-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <x-button class="btn btn-success">Aprovar</x-button>
                            </form>
                        </div>
                        <div>
                            <form action="{{ route('reviews.update', $review) }}" method="POST" class="mt-2 flex gap-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="refused">
                                <x-button class="btn btn-error">Recusar</x-button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
</x-app-layout>