import ssl
import os

# --- Konfigurasi Jalur Sertifikat dan Kunci ---
# Pastikan jalur ini benar dan dapat diakses oleh Gunicorn.
# Disarankan menggunakan variabel lingkungan untuk jalur sensitif di produksi.
# Contoh: os.environ.get('SSL_CERTFILE', 'path/to/your/certificate.pem')
#         os.environ.get('SSL_KEYFILE', 'path/to/your/private_key.pem')
certfile = "path/to/your/certificate.pem"  # Ganti dengan jalur sertifikat Anda yang sebenarnya
keyfile = "path/to/your/private_key.pem"    # Ganti dengan jalur kunci privat Anda yang sebenarnya
ca_certs = None  # Opsional: Jalur ke bundel sertifikat CA jika Anda memerlukan otentikasi klien (mTLS)
                 # atau jika sertifikat Anda ditandatangani oleh CA kustom.

# --- Pengaturan Gunicorn Lainnya (Contoh) ---
workers = 4  # Sesuaikan dengan jumlah core CPU Anda (misal: 2 * jumlah_core + 1)
bind = "0.0.0.0:7860" # Port yang digunakan, sesuai dengan Hugging Face Spaces
timeout = 120 # Timeout untuk worker
loglevel = "info" # Level logging

# --- Fungsi Kustom untuk Konteks SSL yang Aman ---
# Fungsi ini akan dipanggil oleh Gunicorn untuk membuat dan mengkonfigurasi SSLContext.
# Ini adalah cara yang direkomendasikan untuk mengamankan SSL di Gunicorn.
def ssl_context(conf, default_ssl_context_factory):
    """
    Mengkonfigurasi SSLContext untuk Gunicorn dengan pengaturan keamanan yang lebih ketat.
    """
    context = default_ssl_context_factory()

    # 1. Menegakkan Versi TLS Minimum
    # TLS 1.0 dan TLS 1.1 sudah tidak aman. TLS 1.2 adalah minimum yang direkomendasikan,
    # dan TLS 1.3 adalah yang terbaru dan paling aman.
    # Kita akan mencoba mengatur minimum ke TLS 1.2 atau lebih tinggi.
    if hasattr(ssl, 'TLSVersion'):
        # Python 3.7+ menggunakan enum TLSVersion
        if hasattr(ssl.TLSVersion, 'TLSv1_3'):
            context.minimum_version = ssl.TLSVersion.TLSv1_3
            conf.logger.info("SSL: Minimum TLS version set to TLS 1.3.")
        elif hasattr(ssl.TLSVersion, 'TLSv1_2'):
            context.minimum_version = ssl.TLSVersion.TLSv1_2
            conf.logger.info("SSL: Minimum TLS version set to TLS 1.2.")
        else:
            conf.logger.warning("SSL: Python version does not support TLSv1_2 or TLSv1_3 enum. "
                                "Cannot enforce minimum TLS version via TLSVersion.")
    elif hasattr(ssl, 'PROTOCOL_TLSv1_2'):
        # Python versi lama mungkin menggunakan PROTOCOL_TLSv1_2
        context.protocol = ssl.PROTOCOL_TLSv1_2
        conf.logger.info("SSL: Minimum TLS version set to PROTOCOL_TLSv1_2.")
    else:
        conf.logger.warning("SSL: Could not set minimum TLS version. Ensure your Python environment "
                            "supports modern TLS versions (1.2 or 1.3).")

    # 2. Menggunakan Cipher Suites yang Kuat
    # Daftar cipher ini memprioritaskan cipher modern dan kuat, serta menghindari yang lemah/rusak.
    # Selalu merujuk pada rekomendasi keamanan terbaru (misalnya, Mozilla SSL Configuration Generator).
    # Pastikan cipher ini kompatibel dengan klien yang akan terhubung.
    secure_ciphers = (
        "ECDHE+AESGCM:ECDHE+CHACHA20:DHE+AESGCM:DHE+CHACHA20:"
        "!aNULL:!eNULL:!EXPORT:!DES:!RC4:!3DES:!MD5:!PSK:!DSS:!SRP:!CAMELLIA"
    )
    context.set_ciphers(secure_ciphers)
    conf.logger.info(f"SSL: Cipher suites set to: {secure_ciphers}")

    # 3. Opsional: Memerlukan Sertifikat Klien (Mutual TLS / mTLS)
    # Jika Anda ingin server memverifikasi sertifikat klien, aktifkan baris ini.
    # Anda juga perlu menyediakan ca_certs yang berisi sertifikat CA yang menandatangani sertifikat klien.
    # context.verify_mode = ssl.CERT_REQUIRED
    # context.load_verify_locations(cafile="path/to/client_ca_certs.pem")
    # conf.logger.info("SSL: Client certificate verification (mTLS) is REQUIRED.")

    conf.logger.info("✅ SSL context configured for enhanced security.")
    return context