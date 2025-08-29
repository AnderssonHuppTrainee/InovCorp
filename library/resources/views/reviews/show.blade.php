<x-app-layout>
    <div class="container px-4 py-6">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6">
                <span>{{ session('success') }}</span>
            </div>
        @endif


        <div class="mb-3">
            <a href="{{ route('reviews.index') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>


        <div class="card bg-base-100 ">
            <div class="card-body p-4 space-y-6">

                <div class="card-title">
                    <h1 class="text-3xl font-bold text-base-content">Detalhes da Avaliação</h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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


                    <div class="md:col-span-2 space-y-2">
                        <h2 class="text-2xl font-semibold flex items-center">
                            Requisição #{{ $review->bookRequest->number }}
                            @if($review->status === 'suspended')
                                <span class="badge badge-warning ml-3 gap-2 text-white">
                                    <i class="fas fa-clock"></i> Suspenso
                                </span>
                            @elseif($review->status === 'active')
                                <span class="badge badge-success ml-3 gap-2 text-white">
                                    <i class="fas fa-check-circle"></i> Ativo
                                </span>
                            @else
                                <span class="badge badge-error ml-3 gap-2 text-white">
                                    <i class="fas fa-times-circle"></i> Recusado
                                </span>
                            @endif
                        </h2>

                        <p class="text-sm text-gray-500">
                            Criada em: {{ $review->created_at->format('d/m/Y H:i') }}
                        </p>

                        <div class="space-y-3">
                            <p>
                                <strong>Livro:</strong>
                                {{ $review->bookRequest->book->name ?? 'Livro removido' }}
                            </p>
                            <div>
                                <p><strong>Autores:</strong></p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($review->bookRequest->book->authors as $author)
                                        <span class="badge badge-primary">
                                            {{ $author->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <p>
                                <strong>Cidadão:</strong>
                                {{ $review->user->name }} ({{ $review->user->email }})
                            </p>
                            <p>
                                <strong>Avaliação:</strong>
                                {{ $review->rating }}
                            </p>
                            <p>
                                <strong>Comentário:</strong>
                                {{ $review->comment }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>


                @if ($review->status === 'suspended')
                    <div class="card-actions justify-end flex gap-3">

                        <form action="{{ route('reviews.update', $review) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="active">
                            <x-button color="success">Aprovar</x-button>
                        </form>

                        <x-button type="button" color="error" onclick="document.getElementById('rejectModal').showModal()">
                            Recusar
                        </x-button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Recusa --}}
        <dialog id="rejectModal" class="modal">
            <div class="modal-box max-w-lg">
                <h3 class="font-bold text-lg">Recusar Avaliação</h3>
                <p class="py-2">Informe a justificação da recusa:</p>

                <form method="POST" action="{{ route('reviews.update', $review) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="refused">

                    <textarea name="reason" rows="4" class="textarea textarea-bordered w-full"
                        placeholder="Motivo da recusa..." required></textarea>

                    <div class="modal-action flex justify-end gap-2">
                        <x-button type="submit" color="error">Recusar</x-button>
                        <x-button type="button" color="neutral"
                            onclick="document.getElementById('rejectModal').close()">Cancelar</x-button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
</x-app-layout>