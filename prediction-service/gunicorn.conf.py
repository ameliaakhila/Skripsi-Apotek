# --- Pengaturan Gunicorn ---
workers = 4  # Sesuaikan dengan jumlah core CPU Anda (misal: 2 * jumlah_core + 1)
bind = "0.0.0.0:7860" # Port yang digunakan, sesuai dengan Hugging Face Spaces
timeout = 120 # Timeout untuk worker
loglevel = "info" # Level logging