<x-app-layout>

    <div class="container mx-auto px-4 py-6">
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

                @if($requests->isEmpty())
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
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($requests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap ">
                                                {{ $request->number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-wrap">
                                                <div class="flex items-center">
                                                    @if($request->book->cover_image)
                                                        <img src="{{  $request->book->cover_image }}"
                                                            alt="{{ $request->book->name }}" class="w-10 h-15 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/48x72" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $request->book->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $request->book->authors->pluck('name')->join(', ') }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $request->request_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $request->expected_return_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="badge badge-lg ml-2 gap-1 text-white
                                                         @if($request->status === 'approved') badge-success
                                                         @elseif(in_array($request->status, ['pending', 'pending_returned'])) badge-warning
                                                          @elseif($request->status === 'returned') badge-success
                                                         @elseif($request->status === 'rejected') badge-error
                                                          @else badge-neutral 
                                                        @endif">

                                                    @if($request->status === 'approved')
                                                        <i class="fas fa-check-circle"></i> Aprovado
                                                    @elseif($request->status === 'pending')
                                                        <i class="fas fa-clock"></i> Pendente
                                                    @elseif($request->status === 'pending_returned')
                                                        <i class="fas fa-clock"></i> Devolução Pendente
                                                    @elseif($request->status === 'returned')
                                                        <i class="fas fa-undo"></i> Devolvido
                                                    @elseif($request->status === 'rejected')
                                                        <i class="fas fa-times-circle"></i> Rejeitado
                                                    @elseif($request->status === 'cancelled')
                                                        <i class="fas fa-ban"></i> Cancelado
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline">
                                                    Ver
                                                </a>
                                                @if(auth()->user()->isAdmin() && $request->status === 'pending')
                                                    <form class="inline" action="{{ route('requests.approve', $request) }}"
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
                        <div class="px-4 pb-4 sm:px-6">
                            {{ $requests->links() }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>