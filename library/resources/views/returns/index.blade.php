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
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($return->book->cover_image)
                                                        <img src="{{  $return->book->cover_image }}" alt="{{ $return->book->name }}"
                                                            class="w-12 h-15 mr-3 object-cover">
                                                    @else
                                                        <img src="https://placehold.co/46x70" class="mr-3" />
                                                    @endif
                                                    <div>
                                                        <div class="font-bold">{{ $return->book->name }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap">
                                                {{ $return->returned_date->format('d/m/Y') }}

                                            </td>
                                            <td class="whitespace-nowrap">
                                                {{ $return->expected_return_date->format('d/m/Y') }}

                                            </td>

                                            <td>
                                                {{ round($return->actual_days) }} dias
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$return->status" />
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <a href="{{ route('returns.reviewReturn', $return) }}"
                                                    class="btn btn-sm btn-outline">
                                                    <i class="fa fa-eye"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class=" mt-4 px-4 pb-4 sm:px-6">
                            {{ $returns->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>