<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white flex items-center">
                <i class="fa fa-circle-check mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
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
                    <h1 class="text-3xl font-bold text-base-content">Gestão de Requisições</h1>
                </div>

                <x-resources.filters action="{{ route('requests.index') }}" clearUrl="{{ route('requests.index') }}">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">Pesquisar</span>
                        </label>
                        <input type="text" name="search" placeholder="Número" value="{{ request('search') }}"
                            class="input input-bordered w-full">
                    </div>
                </x-resources.filters>

                @if($bookRequests->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhuma requisição encontrada.</span>
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
                                            Número</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Livro</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data Requisição</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Devolução Prevista</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookRequests as $bookRequest)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $bookRequest->number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($bookRequest->book->cover_image)
                                                        <img src="{{  $bookRequest->book->cover_image }}"
                                                            alt="{{ $bookRequest->book->name }}"
                                                            class="w-12 h-15 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/46x70" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $bookRequest->book->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $bookRequest->request_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $bookRequest->expected_return_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$bookRequest->status" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('requests.show', $bookRequest) }}"
                                                    class="btn btn-sm btn-outline">
                                                    <i class="fas fa-eye"></i>
                                                    Ver
                                                </a>
                                                @if(auth()->user()->isAdmin() && $bookRequest->status === 'pending')
                                                    <form class="inline" action="{{ route('requests.approve', $bookRequest) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success ml-2 text-white">
                                                            Aprovar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 px-4 pb-4 sm:px-6">
                            {{ $bookRequests->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>