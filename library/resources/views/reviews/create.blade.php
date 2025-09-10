<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-2">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <div class="card  bg-base-100 mx-auto">
            <div class="card-body p-4">
                <h1 class="card-title text-3xl font-bold mb-6">
                    <i class="fas fa-comments mr-2"></i>
                    Deixar uma Avaliação
                </h1>

                <form action="{{ route('reviews.store', $bookRequest) }}" method="POST">
                    @csrf
                    <div class="form-control mb-4">
                        <label class="label" for="rating">
                            <span class="label-text font-semibold">Avaliação:</span>
                        </label>
                        <div class="rating rating-lg rating-half">
                            <input type="radio" name="rating" value="0.5"
                                class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="rating" value="1"
                                class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="rating" value="1.5"
                                class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="rating" value="2"
                                class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="rating" value="2.5"
                                class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="rating" value="3"
                                class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="rating" value="3.5"
                                class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="rating" value="4"
                                class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="rating" value="4.5"
                                class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="rating" value="5"
                                class="mask mask-star-2 mask-half-2 bg-orange-400" checked />
                        </div>
                    </div>
                    <div class="form-control mb-4">
                        <label class="label" for="comment">
                            <span class="label-text font-semibold">Comentário:</span>
                        </label>
                        <textarea name="comment" id="comment" rows="3" class="textarea textarea-bordered w-full"
                            placeholder="Partilhe a sua experiência com este livro..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="fas fa-paper-plane mr-2"></i> Avaliar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>