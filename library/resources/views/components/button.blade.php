@props([
    'type' => 'submit',
    'color' => 'success', // pode ser: primary, secondary, accent, info, success, warning, error
])

<button 
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "btn btn-$color inline-flex items-center px-4 py-2 border border-transparent rounded-md 
                    font-semibold text-xs uppercase tracking-widest
                    focus:outline-none focus:ring-2 focus:ring-offset-2 
                    disabled:opacity-50 transition ease-in-out duration-150 text-white"
    ]) }}
>
    {{ $slot }}
</button>
