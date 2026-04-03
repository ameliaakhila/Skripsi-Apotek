@php
$href = $route;

if (!\Illuminate\Support\Str::startsWith($route, ['http://', 'https://', 'mailto:', 'tel:', '#', 'javascript:'])) {
    $href = \Illuminate\Support\Facades\Route::has($route) ? route($route) : url($route);
}
@endphp

<li>
    <a href="{{ $href }}" aria-expanded="false">
        @if($icon && $label)
            <i class="fs-7 {{ $icon }}"></i>
            <span class="nav-text fs-16">{{ $label }}</span>
        @elseif($label)
            {{ $label }}
        @endif
    </a>
    </li>