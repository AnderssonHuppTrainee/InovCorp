<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">Meu Carrinho</h1>

        @if($cart->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="table w-full table-zebra">
                    <thead>
                        <tr>
                            <th class="text-center">Livro</th>
                            <th class="text-center">Preço</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-center">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                            <tr>

                                <td class="flex items-center space-x-3">
                                    <img src="{{ $item->book->cover_image ?? 'https://via.placeholder.com/80' }}"
                                        alt="{{ $item->book->name }}" class="w-16 h-20 object-cover rounded">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->book->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->book->authors->pluck('name')->join(', ') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="text-center">€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('cart.index', $item) }}" method="POST"
                                            class="flex items-center space-x-2">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" name="quantity" value="{{ max($item->quantity - 1, 1) }}"
                                                class="btn btn-sm btn-outline">-</button>
                                            <span class="w-14 text-center">{{ $item->quantity }}</span>
                                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                                class="btn btn-sm btn-outline">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="text-center">€ {{ number_format($item->quantity * $item->price, 2, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('cart.index', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error"><i
                                                class="fa fa-trash text-white"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col md:flex-row justify-end items-end md:items-center mt-6 gap-4">
                <p class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Total: € {{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->price), 2, ',', '.') }}
                </p>
                <a href="{{ route('cart.index') }}" class="btn btn-success">Finalizar Compra</a>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">O seu carrinho está vazio.</p>
                <a href="{{ route('public.books.index') }}" class="mt-6 btn btn-primary">Ver Livros</a>
            </div>
        @endif
    </div>
</x-app-layout>