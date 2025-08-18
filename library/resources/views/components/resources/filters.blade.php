@props([
    'action' => '',
    'method' => 'GET',
    'clearUrl' => null,
])

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 mb-6">
    <form method="{{ $method }}" action="{{ $action }}" class="flex flex-wrap gap-4 items-end">
        {{ $slot }}

     
        <x-resources.button-search :clear-url="$clearUrl"><i class="fas fa-search mr-2"></i>Pesquisar</x-resources.button-search>
    </form>
</div>
