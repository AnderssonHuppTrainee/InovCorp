<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white">
                <span><i class="fa fa-circle-check mr-3"></i>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card  mx-auto ">
            <div class="card-body p-4">


                <h1 class="card-title text-3xl mb-6">
                    <i class="fas fa-undo-alt mr-2"></i>
                    Registrar Devolução
                </h1>


                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8">
                    <div
                        class="bg-base-200 rounded-lg shadow-md overflow-hidden w-40 h-60 flex items-center justify-center">
                        @if($bookRequest->book->cover_image)
                            <img src="{{ $bookRequest->book->cover_image }}" class="w-full h-full object-cover" />
                        @else
                            <div class="text-center p-4">
                                <i class="fas fa-book text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500">Sem capa</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2">
                        <h3 class="text-xl font-bold">{{ $bookRequest->book->name }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $bookRequest->book->authors->pluck('name')->join(', ') }}
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
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
                </div>


                <form action="{{ route('returns.store', $bookRequest) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    <div class="form-control">
                        <label class="label" for="return_photo">
                            <span class="label-text font-semibold">Foto do estado atual do livro*</span>
                        </label>
                        <input type="file" name="return_photo" id="return_photo"
                            class="file-input file-input-bordered w-full" required>
                        <label class="label">
                            <span class="label-text-alt">Envie uma foto clara mostrando o estado do livro</span>
                        </label>
                    </div>
                    <div class="form-control ">
                        <label class="label" for="notes">
                            <span class="label-text font-semibold">Observações:</span>
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="textarea textarea-bordered w-full"
                            placeholder="Algum dano ou observação importante..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check mr-2"></i> Registrar Devolução
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</x-app-layout>