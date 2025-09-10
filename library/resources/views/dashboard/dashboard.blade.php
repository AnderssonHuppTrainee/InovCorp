<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white flex items-center">
                <i class="fa fa-circle-check mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Administrativo</h1>
                <p class="text-gray-500">Visão geral do sistema</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reviews.index') }}" class="btn btn-accent btn-sm gap-2 text-white">
                    <i class="fas fa-star"></i> Moderar Avaliações
                </a>
                <a href="{{ route('requests.index') }}" class="btn btn-secondary btn-sm gap-2 text-white">
                    <i class="fas fa-hand"></i> Gerir Requisições
                </a>
                <a href="{{ route('returns.index') }}" class="btn btn-neutral btn-sm gap-2 text-white">
                    <i class="fas fa-undo"></i> Gerir Devoluções
                    <a href="{{ route('books.import') }}" class="btn btn-info btn-sm gap-2 text-white">
                        <i class="fas fa-download"></i> Importar Livros
                    </a>
                </a>
                 <a href="{{ route('logs.index') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-clock-rotate-left"></i> Gerir Atividades
                </a>
            </div>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body flex items-center gap-4">
                    <div class="rounded-full bg-primary/20 p-3 text-primary">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total de Livros</p>
                        <p class="text-2xl font-bold">{{ $stats['books'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg">
                <div class="card-body flex items-center gap-4">
                    <div class="rounded-full bg-secondary/20 p-3 text-secondary">
                        <i class="fas fa-user-edit text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total de Autores</p>
                        <p class="text-2xl font-bold">{{ $stats['authors'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg">
                <div class="card-body flex items-center gap-4">
                    <div class="rounded-full bg-accent/20 p-3 text-accent">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total de Editoras</p>
                        <p class="text-2xl font-bold">{{ $stats['publishers'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg">
                <div class="card-body flex items-center gap-4">
                    <div class="rounded-full bg-success/20 p-3 text-success">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Encomendas Pendentes</p>
                        <p class="text-2xl font-bold">{{ $pendingOrdersCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- graficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
            <div class="card bg-base-100 shadow-lg p-4 h-73">
                <h2 class="text-xl font-semibold mb-2">Status das Encomendas</h2>
                <canvas id="ordersChart" class="w-full h-full"></canvas>
            </div>

            <div class="card bg-base-100 shadow-lg  p-4 h-73">
                <h2 class="text-xl font-semibold mb-2">Vendas Mensais</h2>
                <canvas id="ordersChart2" class="w-full h-full"></canvas>
            </div>
        </div>
        <!-- ultimas encomendas -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">
                    Últimas Encomendas
                    <i class="fa fa-truck ml-2"></i>
                </h2>
                @if($orders->isEmpty())
                    <div class="alert alert-info text-white">Nenhuma encomenda encontrada.</div>
                @else
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
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Data
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>€ {{ number_format($order->total, 2, ',', '.') }}</td>
                                        <td>
                                            <x-status-badge :status="$order->status" />
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href={{ route('orders.show', $order)}} class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i>Ver</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end mt-2">
                        <a href="{{ route('orders.index') }}" class="link-info">Ver todas as encomendas...</a>
                    </div>
                @endif
            </div>
        </div>
        <!--Ultimas Requisições-->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Últimas Requisições
                    <i class="fa fa-hand ml-2"></i>
                </h2>
                @if($bookRequests->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhuma requisição encontrada.</span>
                        </div>
                    </div>
                @else
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
                                @foreach ($bookRequests as $bookRequest)
                                    <tr>
                                        <td class="whitespace-nowrap ">
                                            {{ $bookRequest->number }}
                                        </td>
                                        <td class="whitespace-wrap">
                                            <div class="flex items-center">
                                                @if($bookRequest->book->cover_image)
                                                    <img src="{{  $bookRequest->book->cover_image }}" alt="{{ $bookRequest->book->name }}"
                                                        class="w-12 h-15 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/46x70" class="mr-3" />
                                                @endif
                                                <div>
                                                    <div class="font-medium">{{ $bookRequest->book->name }}</div>

                                                </div>

                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ $bookRequest->request_date->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ $bookRequest->expected_return_date->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <x-status-badge :status="$bookRequest->status" />
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <a href="{{ route('requests.show', $bookRequest) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 sm:px-6">
                        {{ $bookRequests->links() }}
                    </div>
                    <div class="flex justify-end mt-2">
                        <a href="{{ route('requests.index') }}" class="link-info">Ver todas as requisições...</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">
                    Devoluções Pendentes
                    <i class="fa fa-undo ml-2"></i>
                </h2>

                @if($returns->isEmpty())
                    <div class="alert alert-info m-4 sm:m-6">
                        <i class="fas fa-info-circle"></i>
                        <p>Nenhuma devolução encontrada.</p>
                    </div>
                @else
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
                                                    <img src="{{  $return->book->cover_image }}" alt="{{ $return->book->name }}"
                                                        class="w-12 h-15 mr-3 object-cover">
                                                @else
                                                    <img src="https://placehold.co/46x70" class="mr-3" />
                                                @endif
                                                <div>
                                                    <div class="font-bold">{{ Str::limit($return->book->name, 30) }}</div>
                                                </div>
                                            </div>
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
                                                <i class="fas fa-eye"></i>Ver
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 sm:px-6">
                        {{ $returns->links() }}
                    </div>
                    <div class="flex justify-end mt-2">
                        <a href="{{ route('returns.index') }}" class="link-info">Ver todas as devoluções...</a>
                    </div>
                @endif
            </div>
        </div>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('ordersChart').getContext('2d');
            const ordersChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pendentes', 'Pagas'],
                    datasets: [{
                        data: [{{ $pendingOrdersCount ?? 0 }}, {{ $paidOrdersCount ?? 0 }}],
                        backgroundColor: ['oklch(82% .189 84.429)', 'oklch(76% 0.177 163.223)'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 20, padding: 15 }
                        },
                        tooltip: { enabled: true }
                    }
                }
            });

            const ctx2 = document.getElementById('ordersChart2').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [
                        {
                            label: 'Encomendas Pendentes',
                            data: {!! json_encode($monthlyPending) !!},
                            borderColor: 'oklch(82% .189 84.429)',
                            backgroundColor: 'rgba(251, 191, 36, 0.2)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Encomendas Pagas',
                            data: {!! json_encode($monthlyPaid) !!},
                            borderColor: 'oklch(76% 0.177 163.223)',
                            backgroundColor: 'rgba(34, 197, 94, 0.2)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#E5E7EB' } }
                    }
                }
            });
        </script>
</x-app-layout>