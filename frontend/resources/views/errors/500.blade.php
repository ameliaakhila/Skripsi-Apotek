<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Kesalahan Server</title>
    <link rel="icon" type="image/png" href="./assets/images/logosf.png">
    <link href="{{ asset('build/assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="{{ asset('assets/css/errors.css') }}" rel="stylesheet">
</head>

<body class="error-page"
    style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="error-container">
        <div class="error-card error-500">
            <div class="error-content">
                <!-- Logo -->
                <img src="{{ asset('assets/images/logosf2.png') }}" alt="Logo" class="error-logo">

                <!-- Error Icon -->
                <i class="fas fa-exclamation-triangle error-icon"></i>

                <!-- Decoration Line -->
                <div class="error-decoration"></div>

                <!-- Error Code -->
                <div class="error-code">500</div>

                <!-- Error Title -->
                <h1 class="error-title">Kesalahan Server</h1>

                <!-- Error Description -->
                <p class="error-description">
                    Maaf, terjadi kesalahan pada server kami.<br>
                    Tim kami telah diberitahu dan akan segera memperbaikinya.
                </p>

                <!-- Action Buttons -->
                <div class="error-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="error-btn error-btn-primary">
                            <i class="fas fa-home"></i> Kembali ke Dashboard
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="error-btn error-btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Kembali ke Login
                        </a>
                    @endauth
                    <a href="javascript:location.reload()" class="error-btn error-btn-secondary">
                        <i class="fas fa-redo"></i> Coba Lagi
                    </a>
                </div>

                <!-- Footer -->
                <div class="error-footer">
                    <small>
                        Sistem Apotek Sena Farma © 2026 |
                        <a href="mailto:support@apotek.com">Hubungi Support</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('build/assets/js/plugins.js') }}"></script>
</body>

</html>
