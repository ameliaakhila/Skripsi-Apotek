<!DOCTYPE html>
<html lang="id">

<x-head />

<body>
    <x-preloader />

    <div id="main-wrapper">
        <x-navbar />

        @auth
            @include('components.layouts.sidebar-' . Auth::user()->role)
            {{-- <x-sidebar- :role="Auth::user()->role" /> --}}
        @endauth

        <div class="content-body">
            <div class="container-fluid">
                {{ $slot }}
            </div>
            </div>

        <x-footer />
    </div>

    <x-script />
    @stack('scripts')
    @stack('extra_js')
</body>

</html>
