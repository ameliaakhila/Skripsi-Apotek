<x-auth-layout :title="'Login - Sistem Apotek Sena Farma'">
    <div class="pharmacy-login-page">
        <!-- Animated Background Layer -->
        <div class="pharmacy-bg-container">
            <div class="pharmacy-animated-bg">
                <div class="bg-icon bg-icon-1"><i class="fas fa-pills"></i></div>
                <div class="bg-icon bg-icon-2"><i class="fas fa-capsules"></i></div>
                <div class="bg-icon bg-icon-3"><i class="fas fa-droplet"></i></div>
                <div class="bg-icon bg-icon-4"><i class="fas fa-prescription-bottle-medical"></i></div>
                <div class="bg-icon bg-icon-5"><i class="fas fa-syringe"></i></div>
                <div class="bg-icon bg-icon-6"><i class="fas fa-pills"></i></div>
                <div class="bg-icon bg-icon-7"><i class="fas fa-heartbeat"></i></div>
                <div class="bg-icon bg-icon-8"><i class="fas fa-capsules"></i></div>
            </div>
            <div class="pharmacy-bg-overlay"></div>
        </div>

        <!-- Login Container -->
        <div class="pharmacy-login-wrapper">
            <!-- Left Side - Brand Section (Desktop Only) -->
            <div class="pharmacy-brand-section">
                <div class="brand-content">
                    <div class="brand-icon-wrapper">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h2 class="brand-title">Apotek Sena Farma</h2>
                    <p class="brand-subtitle">Sistem Manajemen Terpadu</p>
                    <ul class="brand-features">
                        <li><i class="fas fa-check-circle"></i> <span>Kelola Inventaris dengan Mudah</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Tracking Stok Real-time</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Laporan Penjualan Otomatis</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Kompatibel Semua Perangkat</span></li>
                    </ul>
                    <div class="brand-illustration">
                        <i class="fas fa-prescription-bottle-medical"></i>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form Section -->
            <div class="pharmacy-form-section">
                <div class="form-card">
                    <!-- Logo -->
                    <div class="form-logo">
                        <img src="{{ asset('assets/images/logosf2.png') }}" alt="Apotek Sena Farma">
                    </div>

                    <!-- Form Header -->
                    <div class="form-header">
                        <h1 class="form-title">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </h1>
                        <p class="form-description">Silakan login ke akun Anda</p>
                    </div>

                    <!-- Error Alert -->
                    @if ($errors->any())
                        <div class="alert-pharmacy alert-danger-pharmacy fade-in">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-message">
                                <strong>Gagal Login!</strong>
                                @foreach ($errors->all() as $error)
                                    <div class="error-item">{{ $error }}</div>
                                @endforeach
                            </div>
                            <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.process') }}" class="pharmacy-form">
                        @csrf

                        <!-- Username Field -->
                        <div class="form-group-pharmacy">
                            <div class="input-group-pharmacy">
                                <span class="input-icon-left">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="username"
                                    placeholder=""
                                    class="input-pharmacy @error('username') is-invalid-pharmacy @enderror"
                                    value="{{ old('username') }}" required autofocus>
                                <label class="input-label-pharmacy">Username</label>
                            </div>
                            @error('username')
                                <span class="input-error-pharmacy">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group-pharmacy">
                            <div class="input-group-pharmacy">
                                <span class="input-icon-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password"
                                    placeholder=""
                                    class="input-pharmacy @error('password') is-invalid-pharmacy @enderror"
                                    required>
                                <label class="input-label-pharmacy">Password</label>
                            </div>
                            @error('password')
                                <span class="input-error-pharmacy">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-login-pharmacy">
                            <span class="btn-text">
                                <i class="fas fa-arrow-right"></i>
                                Masuk
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="form-footer">
                        <p class="footer-info">
                            <i class="fas fa-shield-alt"></i> Login aman dan terenkripsi
                        </p>
                        <p class="footer-copyright">©{{ now()->year }} Apotek Sena Farma</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
