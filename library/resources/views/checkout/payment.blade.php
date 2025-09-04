<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- progresso -->
        <ul class="steps w-full mb-8">
            <li class="step step-success">Carrinho</li>
            <li class="step step-success">Morada</li>
            <li class="step step-primary">Pagamento</li>
        </ul>

        <!-- card de checkout -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl font-bold mb-4">
                    Pagamento — Encomenda #{{ $order->id }}
                </h2>

                <div class="flex justify-between items-center mb-6">
                    <span class="text-lg font-medium">Valor a pagar:</span>
                    <span class="text-2xl font-bold text-success">
                        {{ number_format($order->total, 2, ',', '.') }} €
                    </span>
                </div>

                <!-- stripe form -->
                <form id="payment-form" class="space-y-4">
                    <div id="payment-element" class="p-4 border rounded-md bg-base-200">
                        <!-- Stripe Elements -->
                    </div>

                    <div class="card-actions justify-end mt-6">
                        <button id="submit" class="btn btn-success text-white w-full">
                            Pagar agora
                        </button>
                    </div>
                    <div id="error-message" role="alert" class="mt-2 text-error text-sm"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- stripe js -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ $stripeKey }}");
        const clientSecret = "{{ $clientSecret }}";

        const elements = stripe.elements({ clientSecret });

        const paymentElement = elements.create('payment', {
            layout: "tabs", // pode ser 'tabs', 'accordion' ou 'auto'
            appearance: {
                theme: 'flat',
                labels: 'floating',
                variables: {
                    colorPrimary: '#22c55e', // verde daisyUI success
                    colorBackground: '#ffffff',
                    colorText: '#111827',
                }
            }
        });
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitBtn = document.getElementById('submit');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerText = "Processando...";

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: "{{ route('checkout.success', $order) }}"
                },
            });

            if (error) {
                errorMessage.textContent = error.message;
                submitBtn.disabled = false;
                submitBtn.innerText = "Pagar agora";
            }
        });
    </script>
</x-app-layout>