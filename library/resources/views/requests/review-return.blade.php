<x-app-layout>

    <!-- Botão voltar -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Card principal -->
    <div class="card bg-base-100 shadow-xl mx-auto">
        <div class="card-body">

            <!-- Título -->
            <h1 class="card-title text-3xl mb-6">
                <i class="fas fa-clipboard-check mr-2"></i> Avaliação de Devolução
            </h1>

            <!-- Dados principais -->
            <div class="space-y-2 text-sm">
                <p><span class="font-semibold">Utilizador:</span> {{ $bookRequest->user->name }}</p>
                <p><span class="font-semibold">Livro:</span> {{ $bookRequest->book->name }}</p>
                <p><span class="font-semibold">Data de requisição:</span>
                    {{ $bookRequest->request_date->format('d/m/Y') }}</p>
                <p><span class="font-semibold">Data prevista de devolução:</span>
                    {{ $bookRequest->expected_return_date->format('d/m/Y') }}</p>
            </div>

            <!-- Foto enviada -->
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Foto enviada pelo cidadão</h3>
                <div class="bg-base-200 rounded-lg p-4 flex justify-center">
                    @if($bookRequest->return_photo_path)
                        <img src="{{ asset('storage/' . $bookRequest->return_photo_path) }}" alt="Foto do livro devolvido"
                            class="max-w-xs rounded-lg shadow-md object-cover">
                    @else
                        <p class="text-red-500">Nenhuma foto enviada.</p>
                    @endif
                </div>
            </div>

            <!-- Observações -->
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Observações do Usuário</h3>
                <p class="bg-base-200 p-4 rounded-lg">
                    {{ $bookRequest->notes ?? 'Nenhuma observação' }}
                </p>
            </div>

            <!-- Condição do livro -->
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Condição do livro</h3>
                <select name="book_condition" class="select select-bordered w-full" required form="approveReturnForm">
                    <option value="">Selecione</option>
                    <option value="Excellent">Excelente</option>
                    <option value="Good">Bom</option>
                    <option value="Bad">Mau</option>
                    <option value="Damaged">Danificado</option>
                </select>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <form id="approveReturnForm" action="{{ route('requests.approveReturn', $bookRequest) }}" method="POST"
                    class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-success w-full text-white">
                        <i class="fas fa-check mr-2"></i> Aprovar Devolução
                    </button>
                </form>

                <form action="{{ route('requests.rejectReturn', $bookRequest) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-error w-full text-white">
                        <i class="fas fa-times mr-2"></i> Rejeitar Devolução
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-app-layout>