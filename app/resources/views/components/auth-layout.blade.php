<!DOCTYPE html>
<html lang="id">
@include('components.layouts.head', ['title' => $title ?? 'Login - Sistem Apotek Sena Farma'])

<body class="v-light vertical-nav sidebarlaststatus" data-nav-layout="vertical" data-nav-style="dark">
    <div id="main-wrapper">
        {{ $slot }}
    </div>

    @include('components.layouts.script')
    @stack('scripts')
</body>

</html>
