<x-app-layout>
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <div class="card-title mb-6">
                <h1 class="text-3xl font-bold text-base-content">Todas as Devoluções</h1>
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

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Livro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data de devolução</th>
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
                                @foreach($returns as $return)
                                    <tr class="hover">
                                        <td>{{ $return->number }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                @if($return->book->cover_image)
                                                    <div class="avatar">
                                                        <div class="mask mask-squircle w-12 h-12">
                                                            <img src="{{ asset('storage/' . $return->book->cover_image) }}"
                                                                alt="{{ $return->book->name }}" />
                                                        </div>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-bold">{{ $return->book->name }}</div>
                                                    <div class="text-sm opacity-50">
                                                        {{ $return->book->authors->pluck('name')->join(', ') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $return->user->name }}</td>
                                        <td>
                                            {{ $return->returned_date->format('d/m/Y') }}

                                        </td>

                                        <td>
                                            {{ $return->actual_days }} dias
                                        </td>
                                        <td>
                                            <a href="{{ route('requests.reviewReturn', $return) }}"
                                                class="btn btn-sm btn-outline">
                                                Ver
                                            </a>
                                            @if(auth()->user()->isAdmin() && $return->status === 'pending_returned')
                                                <form class="inline" action="{{ route('requests.approveReturn', $return) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ml-2">
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
</x-app-layout>