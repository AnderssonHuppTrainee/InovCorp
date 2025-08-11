<x-app-layout>
  

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6">
                @if($requests->isEmpty())
                    <p>Nenhuma requisição encontrada.</p>
                @else
                    <!-- Indicadores -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium">Requisições Ativas</h3>
                            <p class="text-2xl">{{ $activeRequestsCount }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium">Últimos 30 dias</h3>
                            <p class="text-2xl">{{ $recentRequestsCount }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium">Entregues Hoje</h3>
                            <p class="text-2xl">{{ $returnedTodayCount }}</p>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
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
                                                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->number }}</td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->book->title }}</td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            {{ $request->request_date->format('d/m/Y') }}</td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            {{ $request->expected_return_date->format('d/m/Y') }}</td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            <span
                                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' :
                                            ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                                                {{ ucfirst($request->status) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                                            <a href="{{ route('requests.show', $request) }}"
                                                                                class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                                                            @if(auth()->user()->isAdmin() && $request->status === 'pending')
                                                                                <form class="inline" action="{{ route('requests.store', $request) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <button type="submit"
                                                                                        class="text-green-600 hover:text-green-900 ml-2">Aprovar</button>
                                                                                </form>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                @endif
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>