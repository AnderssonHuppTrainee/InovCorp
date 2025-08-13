<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Avaliação de Devolução</h2>

        <div class="bg-white p-6 rounded shadow">
            <p><strong>Usuário:</strong> {{ $bookRequest->user->name }}</p>
            <p><strong>Livro:</strong> {{ $bookRequest->book->name }}</p>
            <p><strong>Data de requisição:</strong> {{ $bookRequest->request_date->format('d/m/Y') }}</p>
            <p><strong>Data prevista de devolução:</strong> {{ $bookRequest->expected_return_date->format('d/m/Y') }}
            </p>

            <div class="mt-4">
                <strong>Foto enviada pelo cidadão:</strong>
                <div class="mt-2">
                    @if($bookRequest->return_photo_path)
                        <img src="{{ asset('storage/' . $bookRequest->return_photo_path) }}" alt="Foto do livro devolvido"
                            class="max-w-md rounded shadow">
                    @else
                        <p class="text-red-600">Nenhuma foto enviada.</p>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Observações do Usuário</h3>
                <p class="bg-base-200 p-4 rounded-lg">{{ 'Nenhuma observação' }}</p>
            </div>

            <div class="mt-4">
                <h3 class="font-semibold mb-2">Condição do livro</h3>
                <select name="book_condition" class="select select-bordered w-full" required form="approveReturnForm">
                    <option value="">Selecione</option>
                    <option value="Excellent">Excelente</option>
                    <option value="Good">Bom</option>
                    <option value="Bad">Mau</option>
                    <option value="Damaged">Danificado</option>
                </select>
            </div>

            <div class="flex space-x-4 mt-6">
                <form id="approveReturnForm" action="{{ route('requests.approveReturn', $bookRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Aprovar Devolução
                    </button>
                </form>

                <form action="{{ route('requests.rejectReturn', $bookRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Rejeitar Devolução
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-app-layout>