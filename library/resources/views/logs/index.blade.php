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
                    <h1 class="text-3xl font-bold mb-6">Registo de Atividades
                        <i class="fa fa-clock-rotate-left ml-3"></i>
                    </h1>
                </div>

                <x-resources.filters action="{{ route('logs.index') }}" clearUrl="{{ route('logs.index') }}">
                    <div class="form-control w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">Pesquisar</span>
                        </label>
                        <input type="text" name="search" placeholder="Nome do utilizador"
                            value="{{ request('search') }}" class="input input-bordered w-full">
                    </div>
                </x-resources.filters>

                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-gray-50">
                            <tr>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Utilizador</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Modulo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    ID do Objeto</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Alteração</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    IP</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Browser</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Data</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr>

                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->user->name ?? 'Sistema' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->module }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->object_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->changes }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->ip}}</td>
                                    <td class="px-6 py-4 whitespace-wrap">{{ $log->browser }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class=" text-center py-4">
                                        <i class="fas fa-info-circle"></i>
                                        Nenhum log encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>