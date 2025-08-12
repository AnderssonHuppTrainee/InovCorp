<div class="card bg-white dark:bg-gray-800 shadow-lg">
    <div class="card-body">
        <h2 class="text-xl font-semibold mb-4">Últimas Requisições</h2>
        @if($requests->isEmpty())
            <div class="alert alert-info shadow-lg">
                <div>
                    <i class="fas fa-info-circle"></i>
                    <span>Nenhuma requisição encontrada.</span>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Livro</th>
                            <th>Data Requisição</th>
                            <th>Devolução Prevista</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr>
                                <td>{{ $request->number }}</td>
                                <td>{{ $request->book->title }}</td>
                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                <td>{{ $request->expected_return_date->format('d/m/Y') }}</td>
                                <td>
                                    <span
                                        class="badge {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="flex space-x-2">
                                    <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline">
                                        Ver
                                    </a>
                                    @if(auth()->user()->isAdmin() && $request->status === 'pending')
                                        <form class="inline" action="{{ route('requests.approve', $request) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
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
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>