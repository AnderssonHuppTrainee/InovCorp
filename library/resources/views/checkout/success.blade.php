<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="flex items-center justify-center px-4">
            <div class="card bg-base-100 shadow-xl w-full max-w-md text-center">
                <div class="card-body items-center">
                    <!-- Ícone de sucesso -->
                    <div class="text-green-500 text-6xl mb-4">
                        <i class="fas fa-check-circle"></i>
                    </div>

                    <h1 class="card-title text-2xl font-bold">Obrigado pelo seu pedido!</h1>
                    <p class="text-gray-600 mt-2">
                        A sua compra foi concluída com sucesso.
                        Se tiver alguma dúvida, entre em contato:
                    </p>
                    <a href="mailto:orders@example.com" class="link link-primary mt-1">
                        amazing.library@example.com
                    </a>

                    <div class="divider"></div>


                    <div class="flex gap-3 mt-4">
                        <a href="{{ route('public.books.index') }}" class="btn btn-primary text-white">
                            Explorar mais livros
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline">
                            Ver meus pedidos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>