<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success mb-4 text-white">{{ session('success') }}</div>
        @endif


        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Administrativo</h1>
                <p class="text-gray-500">Visão geral do sistema</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('books.import') }}" class="btn btn-info btn-sm gap-2 text-white">
                    <i class="fas fa-download"></i> Importar Livros
                </a>
                <a href="{{ route('reviews.index') }}" class="btn btn-accent btn-sm gap-2 text-white">
                    <i class="fas fa-star"></i> Moderar Avaliações
                </a>
                <a href="{{ route('export.books') }}" class="btn btn-outline btn-sm gap-2">
                    <i class="fas fa-file-excel"></i> Exportar Livros
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
            <div class="card bg-base-100 shadow-lg p-4">
                <h2 class="text-xl font-semibold mb-4">Status das Encomendas</h2>
                <canvas id="ordersChart" class="w-full h-48"></canvas>
            </div>

            <div class="card bg-base-100 shadow-lg  p-4">
                <h2 class="text-xl font-semibold mb-4">Vendas Mensais</h2>
                <canvas id="ordersChart2" class="w-full h-48"></canvas>
            </div>
        </div>
        <!-- ultimas encomendas -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Últimas Encomendas</h2>
                @if($orders->isEmpty())
                    <div class="alert alert-info text-white">Nenhuma encomenda encontrada.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>€ {{ number_format($order->total, 2, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge 
                                                                                                                                                                                                                                                                    {{ $order->status === 'pending' ? 'badge-warning text-white' : 'badge-success text-white' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href={{ route('orders.show', $order)}} class="btn btn-sm btn-outline"><i
                                                    class="fas fa-eye"></i>Ver</a>
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

        <!-- ultimos livros adicionados -->
        <div class="card bg-white dark:bg-gray-800 shadow-lg mb-8">
            <div class="card-body">
                <h2 class="text-xl font-semibold mb-4">Últimos Livros Adicionados</h2>
                @if($stats['latestBooks']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Título</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Autor(es)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Editora</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ISBN</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stats['latestBooks'] as $book)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="flex items-center gap-2">
                                            @if($book->cover_image)
                                                <img src="{{ $book->cover_image }}" class="w-10 h-14 object-cover rounded" />
                                            @endif
                                            {{ $book->name }}
                                        </td>
                                        <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                                        <td>{{ $book->publisher->name ?? '-' }}</td>
                                        <td>{{ $book->isbn }}</td>
                                        <td>{{ $book->created_at->format('d/m/Y') }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="flex justify-end m-4">
                            <a href="{{ route('books.index') }}" class="link-info">Ver todos os livros...</a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info m-4 sm:m-6">
                        <div>
                            <i class="fas fa-info-circle"></i>
                            <span>Nenhum livro cadastrado ainda.</span>
                        </div>
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