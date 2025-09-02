<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-3">
            <a href="#" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="container mx-auto p-4 ">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Morada de Entrega</h1>
            </div>
            <x-form action="{{ route('checkout.storeAddress') }}" method="POST" enctype="multipart/form-data"
                submitText="Continuar" color="success">
                <x-input name="address_line1" label="Morada" required placeholder="Ex: Rua 25 de Abril" />
                <x-input name="address_line2" label="Complemento" />
                <x-input name="city" label="Cidade" required placeholder="Lisboa" />
                <x-input name="postal_code" label="Código Postal" required placeholder="1100-000" />
                <x-input name="country" label="País" required />
            </x-form>

        </div>
    </div>
</x-app-layout>