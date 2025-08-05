@props([
    'width' => 200,
    'height' => 300,
])

<img src="https://picsum.photos/seed/{{ rand(0, 1000000) }}/{{ $width }}/{{ $height }}" alt="Imagem de teste" class="">
