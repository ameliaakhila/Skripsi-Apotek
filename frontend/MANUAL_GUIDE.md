# 📦 Manual Guide: Sistem Prediksi Stok Apotek (Skripsi-Apotek)

Selamat datang di panduan penggunaan sistem **Skripsi-Apotek**. Sistem ini terdiri dari dua komponen utama:
1.  **Frontend (Laravel)**: Dashboard manajemen apotek (inventori, transaksi, laporan).
2.  **Backend (Python/Flask)**: Mesin kecerdasan buatan (AI) untuk prediksi stok menggunakan metode *Decision Tree Regressor (CARD)*.

---

## 🛠️ Persyaratan Sistem

Pastikan perangkat Anda sudah terinstal:
-   **PHP 8.2+** & **Composer**
-   **Node.js & NPM**
-   **Python 3.8+** & **PIP**
-   **SQLite** (Sudah termasuk dalam PHP)

---

## 🏗️ Bagian 1: Setup Frontend (Laravel)

Frontend berfungsi sebagai antarmuka pengguna untuk mengelola data obat, transaksi, dan melihat hasil prediksi.

### 1. Persiapan File
Buka terminal di folder `frontend`:
```bash
cd frontend
cp .env.example .env
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Database & Keamanan
Sistem ini menggunakan SQLite agar mudah dijalankan tanpa perlu setup database server (MySQL).
```bash
# Buat file database kosong
touch database/database.sqlite

# Generate kunci aplikasi
php artisan key:generate

# Jalankan migrasi dan isi data awal (seeding)
php artisan migrate --seed
```

### 4. Build Aset Frontend
```bash
npm run build
```

### 5. Jalankan Server Frontend
```bash
php artisan serve
```
Aplikasi sekarang dapat diakses di: `http://localhost:8000`

---

## 🤖 Bagian 2: Setup Backend (Python AI)

Backend berfungsi untuk memproses data histori dan memberikan prediksi kebutuhan stok obat.

### 1. Instalasi Dependensi Python
Buka terminal baru di folder `backend`:
```bash
cd backend
pip install -r requirements.txt
```

### 2. Inisialisasi Model AI
Sebelum sistem prediksi bisa digunakan, kita perlu mengekspor data dari Laravel dan melatih model AI:
```bash
# Jalankan pipeline lengkap: Ekspor Data -> Persiapan -> Training Model
python app.py --full
```
*Proses ini akan otomatis memanggil perintah di Laravel untuk mengekspor data ke file CSV dan melatih model.*

### 3. Jalankan Server API (Flask)
```bash
python app.py --api
```
Server API akan berjalan di: `http://localhost:5000`. **Pastikan terminal ini tetap terbuka.**

---

## 🔗 Bagian 3: Integrasi & Penggunaan

### 🔑 Akun Login (Default)
Terdapat 3 level akses yang sudah dibuat secara otomatis:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@gmail.com` | `admin` |
| **Apoteker** | `apotek@gmail.com` | `apotek` |
| **Manajer** | `manajer@gmail.com` | `manajer` |

### 🚀 Menjalankan Prediksi Stok
1.  Login sebagai **Manajer**.
2.  Buka menu **Prediksi Obat** (sidebar).
3.  Sistem akan secara otomatis mengirim data fitur obat ke Backend Python.
4.  Hasil prediksi akan muncul di tabel, mencakup:
    -   **Stok Saat Ini**: Jumlah stok fisik di gudang.
    -   **Prediksi Dibutuhkan**: Estimasi jumlah yang harus tersedia.
    -   **Kepercayaan (Confidence)**: Seberapa akurat model AI menilai data tersebut.
    -   **Rekomendasi**: Saran tindakan (misal: "Urgent Restock").

---

## 📊 Alur Kerja Sistem (Workflow)

1.  **Input Data**: Apoteker memasukkan data obat masuk dan keluar di Frontend.
2.  **Ekspor Data**: Sistem (melalui Backend) mengambil data histori transaksi dari database Laravel.
3.  **Pelatihan (Training)**: Model AI mempelajari pola penjualan obat (bulanan/mingguan).
4.  **Prediksi**: Saat Manajer membuka dashboard, Frontend meminta hasil prediksi ke Backend berdasarkan kondisi stok terkini.
5.  **Keputusan**: Manajer melakukan pengadaan obat berdasarkan hasil prediksi.

---

## ❓ Troubleshooting (Masalah Umum)

| Masalah | Solusi |
| :--- | :--- |
| **Halaman Prediksi Kosong/Error** | Pastikan server Backend Python sudah jalan (`python app.py --api`). |
| **Model Not Found** | Jalankan `python app.py --train` di folder backend untuk membuat file model. |
| **Database Lock (SQLite)** | Pastikan tidak ada aplikasi lain yang sedang mengunci file `database.sqlite`. |
| **Vite/CSS Tidak Muncul** | Jalankan `npm run dev` atau `npm run build` di folder frontend. |

---

## 📂 Struktur Penting Proyek

-   `frontend/app/Services/ML/`: Logika integrasi ke API Python.
-   `frontend/database/seeders/`: Data awal untuk pengujian.
-   `backend/dataset/`: File CSV hasil ekspor data.
-   `backend/model/`: File `.pkl` yang merupakan otak dari AI.
-   `backend/api/app.py`: Endpoint API Flask.

---
*Dibuat untuk keperluan Skripsi - Sistem Manajemen Apotek Pintar.*
