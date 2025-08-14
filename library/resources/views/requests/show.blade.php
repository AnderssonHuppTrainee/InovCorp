<x-app-layout>
    @php
        $statusLabels = [
            'approved' => 'Aprovada',
            'pending' => 'Pendente',
            'pending_returned' => 'Devolução pendente',
            'returned' => 'Devolvida',
            'rejected' => 'Rejeitada',
        ];
    @endphp


    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
    </div>


    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="card-title text-2xl md:text-3xl">
                        Requisição #{{ $request->number }}
                        <span class="badge badge-lg ml-2 gap-1
                            @if($request->status === 'approved') badge-primary
                            @elseif(in_array($request->status, ['pending', 'pending_returned'])) badge-warning
                            @elseif($request->status === 'returned') badge-success
                            @elseif($request->status === 'rejected') badge-error
                            @else badge-neutral 
                            @endif">

                            @if($request->status === 'approved')
                                <i class="fas fa-check-circle"></i>
                            @elseif(in_array($request->status, ['pending', 'pending_returned']))
                                <i class="fas fa-clock"></i>
                            @elseif($request->status === 'returned')
                                <i class="fas fa-undo"></i>
                            @elseif($request->status === 'rejected')
                                <i class="fas fa-times-circle"></i>
                            @endif

                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </h1>
                    <div class="text-sm text-gray-500 mt-1">
                        Criada em: {{ $request->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="flex justify-center">
                    <div
                        class="bg-base-200 rounded-lg shadow-md overflow-hidden w-40 h-60 flex items-center justify-center">
                        @if($request->book->cover_image)
                            <x-image-book class="w-full h-full object-cover" />
                        @else
                            <div class="text-center p-4">
                                <i class="fas fa-book-open text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500">Sem imagem</p>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="space-y-4">
                    <h2 class="text-xl font-bold">{{ $request->book->name }}</h2>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">ISBN:</label>
                        <p>{{ $request->book->isbn ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Editora:</label>
                        <p>{{ $request->book->publisher->name ?? 'Não informada' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Autores:</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($request->book->authors as $author)
                                <span class="badge badge-primary">
                                    {{ $author->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Sinopse:</label>
                        <p class="mt-1 text-justify">
                            {{ $request->book->bibliography ?? 'Nenhuma sinopse disponível.' }}
                        </p>
                    </div>
                </div>
            </div>


            <div class="divider"></div>
            <h2 class="text-lg font-semibold mb-4">Foto de Identificação</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Foto enviada -->
                <div class="flex justify-center">
                    <div
                        class="bg-base-200 rounded-lg shadow-md overflow-hidden w-48 h-64 flex items-center justify-center">
                        @if($request->photo_path)
                            <img src="{{ Storage::url($request->photo_path) }}" alt="Foto de identificação"
                                class="w-full h-full object-cover">
                        @else
                            <div class="text-center p-6">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500">Foto não enviada</p>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="card bg-base-200">
                    <div class="card-body p-4">
                        <h3 class="card-title text-lg mb-4">Datas</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-500">Requisição:</label>
                                <p>{{ $request->request_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-500">Devolução Prevista:</label>
                                <p @class([
                                    'font-bold' => $request->isOverdue(),
                                    'text-error' => $request->isOverdue()
                                ])>
                                    {{ $request->expected_return_date->format('d/m/Y') }}
                                    @if($request->isOverdue())
                                        <span class="badge badge-error ml-2">Atrasado</span>
                                    @endif
                                </p>
                            </div>
                            @if($request->returned_at)
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Devolvido em:</label>
                                    <p>{{ $request->returned_at->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="divider"></div>
                        <h3 class="card-title text-lg mb-4">Requisitante</h3>
                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="{{ $request->user->profile_photo_url }}"
                                        alt="{{ $request->user->name }}" />
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold">{{ $request->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $request->user->email }}</p>
                            </div>
                        </div>


                        @if($request->status === 'pending')
                            <div class="divider"></div>
                            <div class="flex gap-3">
                                <form method="POST" action="{{ route('requests.approve', $request) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-full gap-2">
                                        <i class="fas fa-check"></i>
                                        Aprovar
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('requests.reject', $request) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="btn btn-error w-full gap-2"
                                        onclick="return confirm('Tem certeza que deseja rejeitar esta requisição?')">
                                        <i class="fas fa-times"></i>
                                        Rejeitar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>