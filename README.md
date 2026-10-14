# Sistem Informasi Inventory & Manajemen Apotek

Aplikasi berbasis web untuk pengelolaan inventaris, transaksi, laporan, dan prediksi stok obat (Studi Kasus: Apotek Sena Farma).

---

## Cara Instalasi & Menjalankan Project

### 1. Setup Backend / Frontend (Folder `frontend/`)
```bash
cd frontend
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database (.env)
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

### 2. Setup Layanan Prediksi (Folder `apotek-stock-prediction/`)
```bash
cd apotek-stock-prediction
python -m venv .venv
# Windows:
.venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

---

##  Struktur Folder Project

```text
A:\Skripsi-Apotek\
├── apotek-dataset/
│   ├── drifted_data.csv
│   ├── train.csv
│   ├── training_data.csv
│   ├── training_data_from_db.csv
│   ├── training_data_latest.csv
│   └── training_data_latest_export.csv
│
├── apotek-stock-prediction/
│   ├── .venv/
│   ├── model/
│   │   └── stock_prediction_model.pkl
│   ├── Dockerfile
│   ├── app.py
│   ├── gunicorn.conf.py
│   ├── requirements.txt
│   └── README.md
│
├── frontend/
│   ├── api/
│   ├── app/
│   │   ├── Console/
│   │   ├── Exports/
│   │   ├── Helpers/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/
│   │   │   │   ├── Apoteker/
│   │   │   │   ├── Manager/
│   │   │   │   └── Shared/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   ├── Providers/
│   │   └── View/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── docs/
│   ├── jmeter/
│   ├── public/
│   ├── resources/
│   │   ├── css/
│   │   ├── js/
│   │   └── views/
│   ├── storage/
│   ├── tests/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   ├── composer.json
│   ├── package.json
│   └── vite.config.js
│
├── .gitignore
└── README.md
```

---

## Fungsi Utama Tiap Folder

- **`apotek-dataset/`**: Menyimpan file dataset CSV untuk pelatihan dan pengujian model Machine Learning.
- **`apotek-stock-prediction/`**: Layanan Machine Learning berbasis Python Flask yang menyediakan API untuk prediksi stok obat.
- **`frontend/`**: Aplikasi utama Laravel yang menangani backend, database, manajemen user, transaksi, hingga antarmuka web.
- **Root Files**: Berisi skrip pengujian performa (`JMeter`), konfigurasi git, dan utilitas pendukung project.
