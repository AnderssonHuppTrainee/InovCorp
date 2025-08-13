<x-app-layout>
    <div class="mb-2">
        <a href="{{ route('books.index') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
    </div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold ">Detalhes da Requisição #{{ $request->number }}</h1>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/3 p-6 flex justify-center">
                    @if($request->book->cover_image)
                        <div class="mt-4">
                            <!--<img src="{{ Storage::url($request->book->cover_image) }}"
                                                                                                                                                    alt="Capa do livro {{ $request->book->name }}" class="max-w-xs rounded shadow">-->
                            <div class="w-full max-w-xs aspect-[2/3] overflow-hidden rounded-lg shadow-md">
                                <x-image-book class="w-full h-full" />
                            </div>
                        </div>
                    @endif
                </div>
                <div class="md:w-2/3 p-6">
                    <h2 class="text-xl font-semibold mb-4">Livro: {{ $request->book->name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p><strong>ISBN:</strong> {{ $request->book->isbn ?? 'Não informado' }}</p>
                        </div>
                        <div>
                            <p><strong>Data da requisição:</strong> {{ $request->request_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <!-- Autores -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Autores</h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($request->book->authors as $author)
                                    <span class="badge badge-primary">{{ $author->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <!-- Sinopse -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Sinopse</h3>
                            <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ $request->book->bibliography ?? 'Nenhuma sinopse disponível.' }}
                            </p>
                        </div>

                        <div>
                            <p><strong>Data prevista para devolução:</strong>
                                {{ $request->expected_return_date->format('d/m/Y') ?? 'Não definida' }}
                            </p>
                        </div>
                        <div>
                            <p><strong>Status:</strong> <span
                                    class="font-semibold">{{ ucfirst($request->status) }}</span>
                            </p>
                        </div>
                    </div>
                    <hr class="my-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Foto enviada na requisição</h3>
                        @if($request->photo_path)
                            <img src="{{ Storage::url($request->photo_path) }}" alt="Foto de identificação"
                                class="max-w-xs rounded shadow">
                        @else
                            <p>Foto não enviada.</p>
                        @endif
                    </div>
                    <div>

                        @if(auth()->user()->isAdmin() && $request->status === 'pending')
                            <div class="mt-6 flex justify-between">

                                <form method="POST" action="{{ route('requests.approve', $request) }}">
                                    @csrf
                                    <x-button type="submit" class="btn btn-sucess">
                                        {{ __('Aprovar Requisição') }}
                                    </x-button>
                                </form>

                                <form method="POST" action="{{ route('requests.approve', $request) }}">
                                    @csrf
                                    <x-button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Tem certeza que deseja rejeitar esta requisição?')">
                                        {{ __('Rejeiar Requisição') }}
                                    </x-button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
</x-app-layout>