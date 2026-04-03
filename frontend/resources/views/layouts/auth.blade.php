<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login - Sistem Apotek Sena Farma')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logosf2.png') }}">
    @include('layouts.components.head')
</head>

<body class="v-light vertical-nav sidebarlaststatus" data-nav-layout="vertical" data-nav-style="dark">
    {{-- Main Content Area (Full Width for Auth) --}}
    <div id="main-wrapper">
        @yield('content')
    </div>

    @include('layouts.components.script')
</body>

</html>
