<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('users.index') }}" class="btn btn-ghost">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                @if($user->profile_photo_path)
                    <div class="flex-shrink-0">
                        <div class="avatar placeholder w-32 h-32 ">
                            <img src="{{$user->profile_photo_path}}" alt="{{ $user->name }}"
                                class=" rounded-full object-cover shadow-md">
                        </div>
                    </div>
                @else
                    <div class="flex-shrink-0">
                        <div class="avatar placeholder w-32 h-32 ">
                            <img src="https://avatar.iran.liara.run/public" class=" rounded-full object-cover shadow-md">
                        </div>
                    </div>
                @endif

                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <div class="mt-4">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary text-white">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <!--lista de REQUESTS-->
            <div class=" bg-white dark:bg-gray-800  p-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Histórico de Requisições</h2>

                @if($requests->count())
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Data da Solicitação</th>
                                    <th>Data Esperada Devolução</th>
                                    <th>Status</th>
                                    <th>Condição (Devolução)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td>{{ $req->book->name ?? 'Livro removido' }}</td>
                                        <td>{{ $req->request_date->format('d/m/Y') }}</td>
                                        <td>{{ $req->expected_return_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            <x-status-badge :status="$req->status"></x-status-badge>
                                        </td>
                                        <td>{{ $req->book_condition ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma requisição encontrada para este utilizador.</p>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>