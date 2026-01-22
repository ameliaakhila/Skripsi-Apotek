<!DOCTYPE html>
<html lang="en">

<x-head />

<body>
    {{-- ? Preloader --}}
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>

    {{-- ? Header --}}
    <div id="main-wrapper">
        <x-header-admin />

        {{-- ? Slot --}}
        {{ $slot }}

        <x-footer />
    </div>
    <x-script />
</body>

</html>