<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="card bg-base-100 shadow-lg max-w-2xl mx-auto">
            <div class="card-body">
                <h2 class="card-title">Registrar Devolução</h2>

                <div class="mb-6">
                    <div class="flex items-center gap-4 mb-4">
                        @if($bookRequest->book->cover_image)
                            <img src="{{ asset('storage/' . $bookRequest->book->cover_image) }}"
                                alt="{{ $bookRequest->book->name }}" class="w-32 h-48 object-cover rounded-lg">
                        @endif
                        <div>
                            <h3 class="font-bold">{{ $bookRequest->book->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $bookRequest->book->authors->pluck('name')->join(', ') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-semibold">Data de requisição:</p>
                            <p>{{ $bookRequest->request_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Devolução prevista:</p>
                            <p>{{ $bookRequest->expected_return_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('requests.submitReturn', $bookRequest) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-control mb-4">
                        <label class="label" for="return_photo">
                            <span class="label-text">Foto do estado atual do livro*</span>
                        </label>
                        <input type="file" name="return_photo" id="return_photo"
                            class="file-input file-input-bordered w-full" required>
                        <label class="label">
                            <span class="label-text-alt">Envie uma foto clara mostrando o estado do livro</span>
                        </label>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="notes">
                            <span class="label-text">Observações</span>
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="textarea textarea-bordered"
                            placeholder="Algum dano ou observação importante..."></textarea>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check mr-2"></i> Registrar Devolução
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>