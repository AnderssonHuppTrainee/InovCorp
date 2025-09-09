<div class="container mx-auto px-4 py-8">
    <div class="mb-3">
        <a href="{{ route('public.books.index') }}" class="btn btn-ghost gap-2">
            <i class="fas fa-arrow-left"></i>
            Continuar a comprar
        </a>
    </div>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6"><i class="fa fa-cart-shopping mr-2"></i>Meu
            Carrinho</h1>

        @if($cart->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="table w-full table-zebra">
                    <thead>
                        <tr class="border-b-2">
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Livro
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Preço
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Quantidade
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Subtotal
                            </th>
                            <th class="py-3 px-6"></th>
                        </tr>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                            <tr class="border-b-2">
                                <td class="flex items-center space-x-3 py-3">
                                    <img src="{{ $item->book->cover_image ?? 'https://via.placeholder.com/48x72' }}"
                                        alt="{{ $item->book->name }}" class="w-16 h-20 object-cover rounded">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $item->book->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->book->authors->pluck('name')->join(', ') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-3 whitespace-nowrap">€ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td class="py-3 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="decrementQuantity({{ $item->id }})"
                                            class="btn btn-sm btn-outline">-</button>
                                        <span class="w-8 text-center">{{ $item->quantity }}</span>
                                        <button wire:click="incrementQuantity({{ $item->id }})"
                                            class="btn btn-sm btn-outline">+</button>
                                    </div>
                                </td>
                                <td class="py-3 whitespace-nowrap">€
                                    {{ number_format($item->quantity * $item->price, 2, ',', '.') }}
                                </td>
                                <td class="py-3 ">
                                    <button wire:click="removeItem({{ $item->id }})" class="btn btn-sm btn-error text-white">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <div class="flex justify-end mt-6">
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Total: € {{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->price), 2, ',', '.') }}
                        </p>
                        <a href="{{ route('checkout.index', $cart) }}" class="mt-3 btn btn-md  btn-success text-white">
                            Ir para o Checkout
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">O seu carrinho está vazio.</p>
                <a href="{{ route('public.books.index') }}" class="mt-6 btn btn-primary">Ver Livros</a>
            </div>
        @endif
    </div>
</div>