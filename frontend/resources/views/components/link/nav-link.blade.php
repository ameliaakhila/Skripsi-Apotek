<li>
    <a href="{{ $route }}" aria-expanded="false">
        @if($icon && $label)
            <i class="fs-7 {{ $icon }}"></i>
            <span class="nav-text fs-16">{{ $label }}</span>
        @elseif($label)
            {{ $label }}
        @endif
    </a>
</li>