<li>
    <a class="has-arrow" href="#javascript:void(0)" aria-expanded="false">
        <i class="{{ $icon }}"></i>
        <span class="nav-text">{{ $label }}</span>
    </a>
    @if($slot->isNotEmpty())
        <ul>
            {{ $slot }}
        </ul>
    @endif
</li>

{{-- <li><a href="/data-kategori">Kategori Obat</a></li>
<li><a href="/obat-masuk">Obat Masuk</a></li> --}}
