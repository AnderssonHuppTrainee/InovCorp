<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Detalhes da Requisição #{{ $request->number }}</h1>

        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Livro: {{ $request->book->name }}</h2>
            <p><strong>Autor:</strong> {{ $request->book->author ?? 'Não informado' }}</p>
            <p><strong>ISBN:</strong> {{ $request->book->isbn ?? 'Não informado' }}</p>
            <p><strong>Sinopse:</strong> {{ $request->book->bibliography ?? 'Não disponível' }}</p>

            @if($request->book->cover_image)
                <div class="mt-4">
                    <img src="{{ Storage::url($request->book->cover_image) }}"
                        alt="Capa do livro {{ $request->book->title }}" class="max-w-xs rounded shadow">
                </div>
            @endif

            <hr class="my-6">

            <p><strong>Data da requisição:</strong> {{ $request->request_date->format('d/m/Y') }}</p>
            <p><strong>Data prevista para devolução:</strong>
                {{ $request->expected_return_date->format('d/m/Y') ?? 'Não definida' }}</p>
            <p><strong>Status:</strong> <span class="font-semibold">{{ ucfirst($request->status) }}</span></p>

            <hr class="my-6">

            <h3 class="text-lg font-semibold mb-2">Foto enviada na requisição</h3>
            @if($request->photo_path)
                <img src="{{ Storage::url($request->photo_path) }}" alt="Foto de identificação"
                    class="max-w-xs rounded shadow">
            @else
                <p>Foto não enviada.</p>
            @endif

            <div>

                @if(auth()->user()->isAdmin() && $request->status === 'pending')
                    <div class="mt-6 flex justify-between">
                        <div>
                            <a href="{{ route('requests.index') }}" class="text-blue-600 hover:underline">Voltar para lista
                                de
                                requisições</a>
                        </div>
                        <form method="POST" action="{{ route('requests.approve', $request) }}">
                            @csrf
                            <x-button type="submit" class="bg-green-600 hover:bg-green-700">
                                {{ __('Aprovar Requisição') }}
                            </x-button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>