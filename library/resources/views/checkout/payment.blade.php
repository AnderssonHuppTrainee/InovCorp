<x-app-layout>


    <div class="container mx-auto px-4 py-6">
        <div class="container p-4">
            <h1 class="text-2xl mb-4">Pagamento — Encomenda #{{ $order->id }}</h1>

            <p>Valor: <strong>{{ number_format($order->total, 2, ',', '.') }} €</strong></p>


            <form id="payment-form">
                <div id="payment-element"><!-- Stripe will mount Payment Element here --></div>
                <div class="flex justify-end mt-4">
                    <button id="submit" class="btn btn-success text-white">
                        Pagar
                    </button>
                </div>
                <div id="error-message" role="alert" class="mt-2 text-red-600"></div>
            </form>
        </div>
    </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ $stripeKey }}");
        const clientSecret = "{{ $clientSecret }}";

        const elements = stripe.elements({ clientSecret });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitBtn = document.getElementById('submit');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;
            errorMessage.textContent = '';

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    // depois do 3DS/checkout, a Stripe redireciona para aqui
                    return_url: "{{ route('checkout.success', $order) }}"
                },
            });

            if (error) {
                errorMessage.textContent = error.message;
                submitBtn.disabled = false;
            }
        });
    </script>

</x-app-layout>