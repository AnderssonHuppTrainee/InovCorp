<span class="badge badge-sm ml-2 gap-1 {{ $classes() }}">
    @if($icon())
        <i class="{{ $icon() }}"></i>
    @endif
    {{ $label() }}
</span>