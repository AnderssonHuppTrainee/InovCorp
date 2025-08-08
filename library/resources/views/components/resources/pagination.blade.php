@props(['items'])

<div class="mt-4">
    {{ $items->withQueryString()->links() }}
</div>