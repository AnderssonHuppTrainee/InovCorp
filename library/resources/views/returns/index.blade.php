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
                    <h1 class="text-3xl font-bold text-base-content">Gestão de Devoluções</h1>
                </div>

                <x-resources.filters action="{{ route('returns.index') }}" clearUrl="{{ route('returns.index') }}">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">Pesquisar</span>
                        </label>
                        <input type="text" name="search" placeholder="Número" value="{{ request('search') }}"
                            class="input input-bordered w-full">
                    </div>
                </x-resources.filters>

                @if($returns->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <i class="fas fa-info-circle"></i>
                        <p>Nenhuma devolução encontrada.</p>
                    </div>
                @else

                    <div class="bg-white  shadow-md sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Número
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Livro
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Utilizador
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data de devolução
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Devolução Prevista
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dias
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>

                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($returns as $return)
                                        <tr class="hover">
                                            <td class="whitespace-nowrap ">{{ $return->number }}</td>
                                            <td class="whitespace-wrap">
                                                <div class="flex items-center gap-3">
                                                    @if($return->book->cover_image)
                                                        <img src="{{ asset('storage/' . $return->book->cover_image) }}"
                                                            alt="{{ $return->book->name }}" class="w-10 h-10 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/48x72" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-bold">{{ Str::limit($return->book->name, 30) }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{  Str::limit($return->book->authors->pluck('name')->join(', '), 30) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">{{ $return->user->name }}</td>
                                            <td>
                                                {{ $return->returned_date->format('d/m/Y') }}

                                            </td>
                                            <td>
                                                {{ $return->expected_return_date->format('d/m/Y') }}

                                            </td>

                                            <td>
                                                {{ round($return->actual_days) }} dias
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="badge badge-lg ml-2 gap-1 text-white
                                                                        @if($return->status === 'approved') badge-success
                                                                        @elseif(in_array($return->status, ['pending', 'pending_returned'])) badge-warning
                                                                        @elseif($return->status === 'returned') badge-success
                                                                        @elseif($return->status === 'rejected') badge-error
                                                                        @else badge-neutral 
                                                                        @endif">

                                                    @if($return->status === 'approved')
                                                        <i class="fas fa-check-circle"></i> Aprovado
                                                    @elseif($return->status === 'pending')
                                                        <i class="fas fa-clock"></i> Pendente
                                                    @elseif($return->status === 'pending_returned')
                                                        <i class="fas fa-clock"></i> Devolução Pendente
                                                    @elseif($return->status === 'returned')
                                                        <i class="fas fa-undo"></i> Devolvido
                                                    @elseif($return->status === 'rejected')
                                                        <i class="fas fa-times-circle"></i> Rejeitado
                                                    @elseif($return->status === 'cancelled')
                                                        <i class="fas fa-ban"></i> Cancelado
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $return->status)) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <a href="{{ route('returns.reviewReturn', $return) }}"
                                                    class="btn btn-sm btn-outline">
                                                    Ver
                                                </a>
                                                @if(auth()->user()->isAdmin() && $return->status === 'pending_returned')
                                                    <form class="inline" action="{{ route('returns.approveReturn', $return) }}"
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
                            {{ $returns->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>