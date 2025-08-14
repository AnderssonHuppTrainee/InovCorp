<x-mail::layout>
    <x-mail::header :url="config('app.url')">
        Amazing Library
    </x-mail::header>

    <x-mail::panel>
        Sua requisição de livro foi registrada com sucesso!
    </x-mail::panel>

    <p class="text-lg font-semibold mb-2">{{ $request->book->name }}</p>

    <x-mail::table>
        | Detalhe | Valor |
        |-----------------------|--------------------------------|
        | Número da Requisição | #{{ $request->number }} |
        | Status | {{ ucfirst($request->status) }} |
        | Data da Requisição | {{ $request->request_date->format('d/m/Y H:i') }} |
        | Devolução Prevista | {{ $request->expected_return_date->format('d/m/Y') }} |
    </x-mail::table>

    <p class="mt-4">
        Você receberá uma nova notificação quando sua requisição for aprovada.
    </p>

    <x-mail::button :url="route('dashboard', $request)">
        Acompanhar Minha Requisição
    </x-mail::button>

    <x-mail::footer />
</x-mail::layout>