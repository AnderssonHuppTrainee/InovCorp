@props([
    'method' => 'POST',
    'action',
    'enctype' => 'application/x-www-form-urlencoded',
    'submitText' => 'Salvar',
    'color' => 'primary'
])
<form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" enctype="{{ $enctype }}"
{{ $attributes }}>
    @csrf
    
    @if(!in_array($method, ['GET', 'POST']))
        @method($method)
    @endif

    <div class="space-y-4">
        {{ $slot }}
    </div>

    <div class="mt-6 flex justify-end space-x-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline">
            Cancelar
        </a>
        <button type="submit" class="btn btn-{{ $color }} text-white">
            {{ $submitText }}
        </button>
    </div>
</form>