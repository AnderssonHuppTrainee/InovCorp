<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="card mx-auto">
            <div class="card-body p-4">
                <div class="card-title mb-6">
                    <h1 class="text-3xl font-bold text-base-content">Minhas Multas</h1>
                </div>
                <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                    <table class="table table-zebra w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Livro
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Motivo
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Opções
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fines as $fine)
                                <tr>
                                    <td>{{ $fine->bookRequest->book->name ?? 'Livro removido' }}</td>
                                    <td>{{ $fine->reason }}</td>
                                    <td class="whitespace-nowrap">€ {{ number_format($fine->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @if($fine->status === 'pending')
                                            <span class="badge badge-warning text-white">
                                                <i class="fas fa-clock"></i>
                                                Pendente
                                            </span>
                                        @else
                                            <span class="badge badge-success text-white">
                                                <i class="fas fa-check-circle"></i>
                                                Paga
                                            </span>
                                        @endif
                                    </td>
                                    <td>

                                        @if($fine->status === 'pending')
                                            <form action="{{ route('fines.pay', $fine) }}" method="POST">
                                                @csrf
                                                <x-button class="btn btn-sm btn-success ">
                                                    {{ __('Pagar') }}
                                                </x-button>
                                            </form>
                                        @else
                                            <span>-</span>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhuma multa encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>