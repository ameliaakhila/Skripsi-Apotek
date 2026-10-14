<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Workload : Workload Project Management Admin  Bootstrap 5 Template" />
    <meta property="og:title" content="Workload : Workload Project Management Admin  Bootstrap 5 Template" />
    <meta property="og:description" content="Workload : Workload Project Management Admin  Bootstrap 5 Template" />
    <meta property="og:image" content="https:/workload.dexignlab.com/xhtml/social-image.png" />
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="Sistem Apotek Sena Farma" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title === 'Apotek Sena Farma' ? $title : $title . ' | Apotek Sena Farma' }}</title>
    <link rel="icon" type="image/png" href="./assets/images/favicon.ico">

    {{-- ! System Fonts Only - No external CDN (local development) --}}
    <style>
        :root {
            --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        * {
            font-family: var(--font-sans) !important;
        }

        .material-icons {
            font-family: 'Material Icons' !important;
        }

        /* Font Awesome inline fallback */
        .fa,
        .fas,
        .far,
        .fal,
        .fab {
            font-family: 'Font Awesome 5 Free' !important;
            font-weight: 900;
        }

        .far {
            font-weight: 400;
        }
    </style>

    {{-- ! Tailwind CSS via Vite (Critical) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ! Font Awesome - CRITICAL (icons must load immediately) --}}
    <link href="{{ asset('assets/icons/font-awesome/css/all.min.css')}}" rel="stylesheet">

    {{-- ! Vendor CSS - Async loaded (non-critical) --}}
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/owl-carousel/owl.carousel.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet"
        media="print" onload="this.media='all'">
    <link href="{{ asset('assets/vendor/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet"
        media="print" onload="this.media='all'">
    <link href="{{ asset('assets/vendor/jquery-asColorPicker/css/asColorPicker.min.css')}}" rel="stylesheet"
        media="print" onload="this.media='all'">
    <link href="{{ asset('assets/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}"
        rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{ asset('assets/vendor/pickadate/themes/default.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/pickadate/themes/defau--lt.date.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/animate/animate.min.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="{{ asset('assets/vendor/aos/css/aos.min.css')}}" rel="stylesheet" media="print"
        onload="this.media='all'">
    {{-- ! Main theme CSS - CRITICAL (must load immediately for styling) --}}
    <link href="{{ asset('assets/css/style.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css')}}" rel="stylesheet">

    {{-- ! Helper script to load media="print" CSS on DOM ready --}}
    <script>
        (function () {
            // Load print media CSS as soon as DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', loadPrintCSS);
            } else {
                loadPrintCSS();
            }

            function loadPrintCSS() {
                var links = document.querySelectorAll('link[media="print"]');
                links.forEach(function (link) {
                    link.media = 'all';
                });
            }
        })();
    </script>
</head>
