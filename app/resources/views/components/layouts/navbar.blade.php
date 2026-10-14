{{-- ? Navbar Brand & Header Start --}}
<div class="nav-header">
    <a href="{{ route('dashboard') }}" class="brand-logo">
        <img src="{{ asset('assets/images/logosf.png') }}" class="logo-apotek" alt="logo-apotek">
        <div class="brand-title">
            <h2>Sistem</h2>
            <span class="brand-sub-title fs-4">Apotek Sena Farma</span>
        </div>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </div>
    </div>
</div>
{{-- ? Navbar Brand & Header End --}}

{{-- ? Header Top Bar Start --}}
<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar text-primary">
                        {{ $title ?? 'Dashboard' }}
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    @auth
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <img src="{{ asset('assets/images/logosf.png') }}" width="20" alt="logo-apotek" />
                                <div class="header-info ms-3">
                                    <span class="fs-18 font-w500 mb-2">
                                        {{ Auth::user()->username }}
                                    </span>
                                    <small class="fs-12 font-w400">
                                        {{ Auth::user()->email }}
                                    </small>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('logout') }}" class="dropdown-item ai-icon"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                        height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="ms-2">Logout</span>
                                </a>
                            </div>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>
    </div>
</div>
{{-- ? Header Top Bar End --}}

{{-- Hidden Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
