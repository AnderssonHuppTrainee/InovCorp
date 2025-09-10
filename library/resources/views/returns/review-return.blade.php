<<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white">
                <span><i class="fa fa-circle-check mr-3"></i>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card bg-base-100">
            <div class="card-body p-4">


                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i>
                        Avaliação de Devolução
                    </h1>
                    <x-status-badge :status="$bookRequest->status"></x-status-badge>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div class="card bg-base-200 shadow-inner">
                        <div class="card-body p-4">
                            <h3 class="card-title text-lg mb-4">Foto enviada pelo cidadão</h3>
                            <div class="flex justify-center">
                                @if($bookRequest->return_photo_path)
                                    <img src="{{ asset('storage/' . $bookRequest->return_photo_path) }}"
                                        alt="Foto do livro devolvido" class="max-w-xs rounded-lg shadow-md object-cover">
                                @else
                                    <p class="text-red-500 font-medium">Nenhuma foto enviada.</p>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="card bg-base-200 shadow-inner">
                        <div class="card-body p-4 space-y-4">
                            <h3 class="card-title text-lg mb-4">Informações</h3>

                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="w-12 rounded-full">
                                        <img src="{{ $bookRequest->user->profile_photo_url }}"
                                            alt="{{ $bookRequest->user->name }}">
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $bookRequest->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $bookRequest->user->email }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <p><span class="font-semibold">Data de requisição:</span>
                                        {{ $bookRequest->request_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p><span class="font-semibold">Livro:</span> {{ $bookRequest->book->name }}</p>
                                </div>
                                <div>
                                    <p><span class="font-semibold">Data prevista de devolução:</span>
                                        {{ $bookRequest->expected_return_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p><span class="font-semibold">Data de devolução:</span>
                                        {{ $bookRequest->returned_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Observações do Utilizador</h3>
                    <p class="bg-base-200 p-4 rounded-lg">
                        {{ $bookRequest->notes ?? 'Nenhuma observação' }}
                    </p>
                </div>


                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Condição do livro</h3>
                    <select name="book_condition" class="select select-bordered w-full" required
                        form="approveReturnForm">
                        <option value="">Selecione</option>
                        <option value="Excellent">Excelente</option>
                        <option value="Good">Bom</option>
                        <option value="Bad">Mau</option>
                        <option value="Damaged">Danificado</option>
                        <option value="Lost">Perdido</option>
                    </select>
                </div>


                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <form id="approveReturnForm" action="{{ route('returns.approveReturn', $bookRequest) }}"
                        method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn btn-success w-full text-white">
                            <i class="fas fa-check mr-2"></i> Aprovar Devolução
                        </button>
                    </form>

                    <form action="{{ route('returns.rejectReturn', $bookRequest) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn btn-error w-full text-white">
                            <i class="fas fa-times mr-2"></i> Rejeitar Devolução
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    </x-app-layout>