<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6 text-white flex items-center">
                <i class="fa fa-circle-check mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ url()->previous()}}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>


        <div class="card bg-base-100">
            <div class="card-body p-4">

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="card-title text-2xl md:text-3xl">
                            Requisição #{{ $bookRequest->number }}
                            <x-status-badge :status="$bookRequest->status"></x-status-badge>
                        </h1>
                        <div class="text-sm text-gray-500 mt-1">
                            Criada em: {{ $bookRequest->created_at?->format('d/m/Y H:i') ?? 'Sem data'}}
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div class="flex justify-center">
                        <div
                            class="bg-base-200 rounded-lg shadow-md overflow-hidden w-40 h-60 flex items-center justify-center">
                            @if($bookRequest->book->cover_image)
                                <img src="{{ $bookRequest->book->cover_image }}" class="w-full h-full object-cover" />
                            @else
                                <div class="text-center p-4">
                                    <i class="fas fa-book-open text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">Sem imagem</p>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="space-y-4">
                        <h2 class="text-xl font-bold">{{ $bookRequest->book?->name }}</h2>
                        <div>
                            <label class="text-sm font-semibold text-gray-500">ISBN:</label>
                            <p>{{ $bookRequest->book->isbn ?? 'Não informado' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-500">Editora:</label>
                            <p>{{ $bookRequest->book->publisher->name ?? 'Não informada' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-500">Autores:</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($bookRequest->book->authors as $author)
                                    <span class="badge badge-primary">
                                        {{ $author->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>


                <div class="divider"></div>
                <h2 class="text-lg font-semibold mb-4">Foto de Identificação</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- foto enviada -->
                    <div class="flex justify-center">
                        <div
                            class="bg-base-200 rounded-lg shadow-md overflow-hidden w-48 h-64 flex items-center justify-center">
                            @if($bookRequest->photo_path)
                                <img src="{{ Storage::url($bookRequest->photo_path) }}" alt="Foto de identificação"
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
                                    <p>{{ $bookRequest->request_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Devolução Prevista:</label>
                                    <p @class([
                                        'font-bold' => $bookRequest->isOverdue(),
                                        'text-error' => $bookRequest->isOverdue(),

                                    ])>
                 {{ $bookRequest->expected_return_date->format('d/m/Y') }}
                                        @if($bookRequest->isOverdue())
                                            <span class="badge badge-error ml-2 text-white">Atrasado</span>
                                        @endif
                                    </p>
                                </div>
                                @if($bookRequest->returned_at)
                                    <div>
                                        <label class="text-sm font-semibold text-gray-500">Devolvido em:</label>
                                        <p>{{ $bookRequest->returned_at->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="divider"></div>
                            <h3 class="card-title text-lg mb-4">Requisitante</h3>
                            <div class="flex items-center gap-4">
                                <div class="avatar">
                                    <div class="w-12 rounded-full">
                                        <img src="{{ $bookRequest->user->profile_photo_url }}"
                                            alt="{{ $bookRequest->user->name }}" />
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $bookRequest->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $bookRequest->user->email }}</p>
                                </div>
                            </div>


                            @if($bookRequest->status === 'pending')
                                <div class="divider"></div>
                                <div class="flex gap-3">
                                    <form method="POST" action="{{ route('requests.approve', $bookRequest) }}"
                                        class="w-full">
                                        @csrf
                                        <button type="submit" class="btn btn-success text-white w-full gap-2">
                                            <i class="fas fa-check"></i>
                                            Aprovar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('requests.reject', $bookRequest) }}"
                                        class="w-full">
                                        @csrf
                                        <button type="submit" class="btn btn-error text-white w-full gap-2"
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
    </div>

</x-app-layout>