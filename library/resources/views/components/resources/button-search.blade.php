@props([
    'type' => 'submit',   
    'clearUrl' => null,   
])

<div class="flex gap-2">
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-primary']) }}>
        {{ $slot }}
    </button>

    @isset($clearUrl)
        <a href="{{ $clearUrl }}"
           class="btn btn-outline @if(request()->query->count() === 0) hidden @endif">
            Limpar
        </a>
    @endisset
</div>
