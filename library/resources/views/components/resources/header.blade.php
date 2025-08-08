@props([
'title' => '',
'createRoute' => '',
'createLabel' => 'Novo'
])

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $title }}</h1>
    @if($createRoute)
    <a href="{{ $createRoute }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> {{ $createLabel }}
    </a>
    @endif
</div>