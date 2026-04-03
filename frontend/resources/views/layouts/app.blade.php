<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Apotek Sena Farma')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.components.head')

    @yield('extra_css')
</head>

<body>
    {{-- Preloader --}}
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>

    <div id="main-wrapper" class="show">
        {{-- Navbar (Header Brand & Top Bar) --}}
        @include('layouts.components.navbar')

        {{-- Sidebar (Role-Based Navigation) --}}
        @auth
            @include('layouts.components.sidebar-' . Auth::user()->role)
        @endauth

        {{-- Main Content Area --}}
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        {{-- Footer --}}
        @include('layouts.components.footer')
    </div>

    @include('layouts.components.script')

    @yield('extra_js')
