@props([
    'action' => '',
    'method' => '',
    'clearUrl' => '',
    ])

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">
        <form method="{{ $method }}" action="{{ $action }}" class="flex flex-wrap gap-4 items-end">
                {{ $slot }}

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                @if($clearUrl)
                    <div class="form-control">
                        <a href="{{ $clearUrl}}" class="btn btn-outline">Limpar</a>
                    </div>
                @endif
        </form>
</div>
