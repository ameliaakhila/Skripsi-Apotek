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
    <link rel="icon" type="image/png" href="./assets/images/logosf.png">

    {{-- ! CSS LINK --}}
    <!~-- FAVICONS ICON -->
        <link rel="shortcut icon" type="image/png" href="images/favicon.png" />
        <link href="{{ asset('assets/vendor/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/vendor/owl-carousel/owl.carousel.css')}}" rel="stylesheet">
        <link href="{{ asset('assets/css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('assets/css/custom.css')}}" rel="stylesheet">

        <!-- Datatable -->
        <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">

        <!-- Style css -->
        <link href="{{ asset('assets/css/style.css')}}" rel="stylesheet">

        <!-- Daterange picker -->
        <link href="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
        <!-- Clockpicker -->
        <link href="{{ asset('assets/vendor/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet">
        <!-- asColorpicker -->
        <link href="{{ asset('assets/vendor/jquery-asColorPicker/css/asColorPicker.min.css')}}" rel="stylesheet">
        <!-- Material color picker -->
        <link
            href="{{ asset('assets/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
            rel="stylesheet">

        <!-- Pick date -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.date.css') }}">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        {{-- Realtime Ui Package --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- ! CSS LINK END --}}
</head>