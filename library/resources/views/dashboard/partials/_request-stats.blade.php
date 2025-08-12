<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card bg-white dark:bg-gray-800 shadow-lg">
        <div class="card-body">
            <h2 class="text-xl font-semibold">Requisições Ativas</h2>
            <p class="text-3xl font-bold">{{ $activeRequestsCount }}</p>
        </div>
    </div>
    <div class="card bg-white dark:bg-gray-800 shadow-lg">
        <div class="card-body">
            <h2 class="text-xl font-semibold">Últimos 30 dias</h2>
            <p class="text-3xl font-bold">{{ $recentRequestsCount }}</p>
        </div>
    </div>
    <div class="card bg-white dark:bg-gray-800 shadow-lg">
        <div class="card-body">
            <h2 class="text-xl font-semibold">Entregues Hoje</h2>
            <p class="text-3xl font-bold">{{ $returnedTodayCount }}</p>
        </div>
    </div>
</div>