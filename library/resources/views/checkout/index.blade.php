<x-app-layout>
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <div class="flex justify-center mb-6">
            <h1 class="text-3xl text-gray-800 font-bold ">Checkout</h1>
        </div>
        <!-- steps -->
        <ul class="steps steps-vertical lg:steps-horizontal w-full mb-8">
            <li class="step step-primary cursor-pointer" id="step-address">Morada</li>
            <li class="step cursor-pointer" id="step-payment">Pagamento</li>
        </ul>


        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">

                <!-- endereço -->
                <div id="address-section">
                    <h2 class="card-title text-2xl font-bold mb-4">Morada de Entrega</h2>
                    <form id="address-form" class="space-y-4">
                        @csrf
                        <div>
                            <label class="label">Rua</label>
                            <input type="text" name="address_line1" class="input input-bordered w-full" required>
                        </div>
                        <div>
                            <label class="label">Complemento</label>
                            <input type="text" name="address_line2" class="input input-bordered w-full" required>
                        </div>
                        <div>
                            <label class="label">Cidade</label>
                            <input type="text" name="city" class="input input-bordered w-full" required>
                        </div>
                        <div>
                            <label class="label">Código Postal</label>
                            <input type="text" name="postal_code" class="input input-bordered w-full" required>
                        </div>
                        <div>
                            <label class="label">País</label>
                            <input type="text" name="country" class="input input-bordered w-full" required>
                        </div>
                        <div class="card-actions justify-end mt-6">
                            <button type="submit" class="btn btn-primary text-white">Continuar para Pagamento</button>
                        </div>
                    </form>
                </div>


                <div id="payment-section" class="hidden">
                    <h2 class="card-title text-2xl font-bold mb-4">Pagamento</h2>
                    <p class="mb-4">
                        Valor a pagar:
                        <span id="order-total" class="text-2xl font-bold text-success"></span>
                    </p>

                    <form id="payment-form" class="space-y-4">
                        <div id="payment-element" class="p-4 border rounded-md bg-base-200">

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
    </div>

    <!-- Stripe -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const addressForm = document.getElementById('address-form');
        const addressSection = document.getElementById('address-section');
        const paymentSection = document.getElementById('payment-section');
        const stepAddress = document.getElementById('step-address');
        const stepPayment = document.getElementById('step-payment');

        function showStep(step) {
            if (step === 'address') {
                addressSection.classList.remove('hidden');
                paymentSection.classList.add('hidden');

                stepAddress.classList.add('step-primary');
                stepAddress.classList.remove('step-success');
                stepPayment.classList.remove('step-primary');
            }

            if (step === 'payment') {
                addressSection.classList.add('hidden');
                paymentSection.classList.remove('hidden');

                stepAddress.classList.remove('step-primary');
                stepAddress.classList.add('step-success');
                stepPayment.classList.add('step-primary');
            }
        }


        stepAddress.addEventListener('click', () => showStep('address'));
        stepPayment.addEventListener('click', () => showStep('payment'));
        addressForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(addressForm);

            const response = await fetch("{{ route('checkout.address') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: formData
            });

            const result = await response.json();

            if (result.success) {

                document.getElementById('order-total').innerText = (result.total).toFixed(2).replace('.', ',') + " €";


                addressSection.classList.add('hidden');
                paymentSection.classList.remove('hidden');
                stepAddress.classList.remove('step-primary');
                stepAddress.classList.add('step-success');
                stepPayment.classList.add('step-primary');


                const stripe = Stripe(result.stripe_key);
                const elements = stripe.elements({ clientSecret: result.client_secret });

                const paymentElement = elements.create('payment', {
                    layout: 'tabs',
                    appearance: {
                        theme: 'flat',
                        variables: { colorPrimary: '#22c55e' }
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
                            return_url: "{{ route('checkout.success', 'ORDER_ID') }}".replace('ORDER_ID', result.order_id)
                        },
                    });

                    if (error) {
                        errorMessage.textContent = error.message;
                        submitBtn.disabled = false;
                        submitBtn.innerText = "Pagar agora";
                    }
                });
            }
        });
    </script>
</x-app-layout>