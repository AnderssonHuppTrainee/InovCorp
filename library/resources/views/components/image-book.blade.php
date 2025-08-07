@props([
    'width' => null,  // Remova os valores padrão
    'height' => null, // para tornar mais flexível
])

<img {{ $attributes->merge(['class' => 'object-cover w-full h-full']) }} 
     src="https://picsum.photos/seed/{{ rand(0, 1000000) }}/200/300" 
     alt="{{ $attributes->get('alt', 'Capa do livro') }}">