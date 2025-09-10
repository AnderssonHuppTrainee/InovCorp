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
                    <h1 class="text-3xl font-bold text-base-content">Gestão de Pedidos</h1>
                </div>

                <x-resources.filters action="{{ route('orders.index') }}" clearUrl="{{ route('orders.index') }}">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">Pesquisar</span>
                        </label>
                        <input type="text" name="search" placeholder="Número" value="{{ request('search') }}"
                            class="input input-bordered w-full">
                    </div>
                </x-resources.filters>

                @if($orders->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhum pedido encontrado.</span>
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
                                            Número
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Cliente
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data de pedido
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
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap ">
                                                #{{ $order->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap ">
                                                {{ $order->user->name}}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $order->created_at->format('d/m/Y') }}
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$order->status" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline">
                                                    <i class="fa fa-eye"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 pb-4 sm:px-6">
                            {{ $orders->links() }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>