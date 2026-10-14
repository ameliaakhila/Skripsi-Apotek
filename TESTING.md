# Panduan Lengkap Performance Testing (JMeter + InfluxDB + Grafana) — Apotek Sena Farma

Dokumen ini berisi panduan teknis langkah demi langkah (*step-by-step*) dari awal instalasi, konfigurasi integrasi 3 tools pengujian (**Apache JMeter 5.6.3**, **InfluxDB v2**, dan **Grafana OSS**), pembuatan *Test Plan*, penulisan *Query Flux*, hingga eksekusi pengujian beban (*Load Testing*) untuk skripsi aplikasi web **Apotek Sena Farma** (Laravel 11).

> **Catatan**: Tutorial ini merupakan adaptasi dari template *WedBook* (Next.js + Express) ke project **Apotek Sena Farma (Laravel 11)**. Perbedaan utama ada pada **mekanisme autentikasi** (Laravel menggunakan *session-based* login + **CSRF token**, bukan JWT/JSON API), serta **port server** target.

---

## DAFTAR ISI
1. [Prasyarat Sistem](#1-prasyarat-sistem)
2. [Instalasi & Konfigurasi Tools di Windows](#2-instalasi--konfigurasi-tools-di-windows)
   - [A. Java Development Kit (JDK 17+)](#a-java-development-kit-jdk-17)
   - [B. Apache JMeter 5.6.3](#b-apache-jmeter-563)
   - [C. InfluxDB v2.7.x](#c-influxdb-v27x)
   - [D. Grafana OSS (Solusi Konflik Port 3000)](#d-grafana-oss-solusi-konflik-port-3000)
3. [Menjalankan Aplikasi Laravel (Target Pengujian)](#3-menjalankan-aplikasi-laravel-target-pengujian)
4. [Membuat Test Plan di GUI Apache JMeter](#4-membuat-test-plan-di-gui-apache-jmeter)
5. [Menghubungkan JMeter dengan InfluxDB v2](#5-menghubungkan-jmeter-dengan-influxdb-v2)
6. [Menghubungkan InfluxDB dengan Grafana](#6-menghubungkan-influxdb-dengan-grafana)
7. [Membuat Dashboard & Query Flux di Grafana](#7-membuat-dashboard--query-flux-di-grafana)
8. [Prosedur Eksekusi Load Testing (10, 25, 50, 100 Users)](#8-prosedur-eksekusi-load-testing-10-25-50-100-users)
9. [Panduan Troubleshooting Error Umum](#9-panduan-troubleshooting-error-umum)

---

## 1. PRASYARAT SISTEM

- **Sistem Operasi**: Windows 10 / 11 (64-bit).
- **Aplikasi Target**: Laravel 11 Web App (**Production di Vercel**: `https://skripsi-apotek-iyaf.vercel.app`).
- **Database**: TiDB Cloud (MySQL-compatible) — sudah dikonfigurasi di `frontend/.env`.
- **Akun Login Default (DatabaseSeeder)**:
  | Role | Username | Password |
  | :--- | :--- | :--- |
  | **Admin** | `admin` | `admin` |
  | **Apoteker** | `apotek` | `apotek` |
  | **Manajer** | `manajer` | `manajer` |

- **Pemetaan Port Lingkungan Pengujian**:

  | Service / Tool | Port Default | Status & Penyesuaian Port |
  | :--- | :--- | :--- |
  | **Laravel Frontend (Apotek Sena Farma)** | `443` (HTTPS) | Target Pengujian (Production Vercel) |
  | **InfluxDB v2 (TSDB)** | `8086` | Tempat Penyimpanan Metrik Real-Time |
  | **Grafana OSS** | `3000` ➜ **`3001`** | **Diubah ke 3001** (cegah konflik bila port 3000 terpakai aplikasi lain) |

> **File JMeter Test Plan siap pakai**: `frontend/jmeter/Apotek_Sena_Farma_Load_Test.jmx`

---

## 2. INSTALASI & KONFIGURASI TOOLS DI WINDOWS

### A. Java Development Kit (JDK 17+)
JMeter membutuhkan Java Runtime (minimal JDK 11 atau 17 LTS).
1. Unduh **JDK 17 LTS** (seperti OpenJDK / Eclipse Temurin installer `.msi`).
2. Jalankan instalasi hingga selesai.
3. Buka PowerShell / Command Prompt (CMD), ketik perintah verifikasi:
   ```powershell
   java -version
   ```
   *Pastikan versi Java 17 atau di atasnya muncul tanpa error.*

---

### B. Apache JMeter 5.6.3
1. Unduh zip resmi **Apache JMeter 5.6.3** dari website Apache JMeter.
2. Ekstrak file zip ke direktori lokal (misal: `C:\apache-jmeter-5.6.3\`).
3. Untuk membuka GUI JMeter, buka folder `C:\apache-jmeter-5.6.3\bin\` dan klik ganda file **`jmeter.bat`**.

---

### C. InfluxDB v2.7.x
1. Unduh **InfluxDB v2.7.x (Windows Zip Binary)**.
2. Ekstrak isi file zip ke folder `C:\influxdb\`.
3. Buka CMD / PowerShell pada folder `C:\influxdb\`, lalu jalankan perintah:
   ```powershell
   .\influxd.exe
   ```
4. Buka browser dan akses **`http://localhost:8086`**.
5. Selesaikan setup awal di browser:
   - **Username**: `admin`
   - **Password**: `Admin123`
   - **Initial Organization Name**: `skripsi_apotek_org`
   - **Initial Bucket Name**: `jmeter_db`
6. **Membuat API Token All-Access di InfluxDB**:
   - Di sidebar kiri InfluxDB, klik menu **Load Data** (ikon panah/kotak) ➜ tab **API Tokens**.
   - Klik tombol **Generate API Token** ➜ pilih **All Access API Token**.
   - Beri nama token (misal `jmeter-token`), lalu klik **Save**.
   - Salin (*copy*) token tersebut untuk digunakan di JMeter dan Grafana.

> **Simpan token Anda**: ganti nilai `PASTE_ALL_ACCESS_API_TOKEN_ANDA` pada:
> - Variable `INFLUXDB_TOKEN` di file `frontend/jmeter/Apotek_Sena_Farma_Load_Test.jmx`
> - Konfigurasi *Data Source* Grafana (lihat [Bagian 6](#6-menghubungkan-influxdb-dengan-grafana))

---

### D. Grafana OSS (Solusi Konflik Port 3000)
1. Unduh **Grafana OSS Windows Standalone (.zip)** atau **Installer (.msi)**.
2. Ekstrak ke `C:\grafana\` (di project ini sudah ada di `C:\Users\ASUS\Downloads\grafana-13.1.2`).
3. **Mencegah Konflik Port 3000**:
   - Buka file `C:\grafana\conf\defaults.ini` (atau buat file `custom.ini` di folder `conf`).
   - Cari baris `http_port = 3000`.
   - Ubah menjadi:
     ```ini
     http_port = 3001
     ```
   - Simpan file tersebut.
4. Jalankan Grafana dari folder install Grafana:
   ```powershell
   .\bin\grafana-server.exe
   ```
   Atau gunakan **script `start-grafana.bat`** yang sudah disediakan di root project:
   ```cmd
   start-grafana.bat
   ```
   (Script ini otomatis menjalankan Grafana dengan `--http-port 3001`.)
5. Buka browser dan akses **`http://localhost:3001`**.
6. Login menggunakan akun default:
   - **Username**: `admin`
   - **Password**: `admin` (lalu ubah password saat diminta).

---

## 3. MENJALANKAN APLIKASI LARAVEL (TARGET PENGUJIAN)

Target pengujian adalah **Production Vercel**: `https://skripsi-apotek-iyaf.vercel.app` — **tidak perlu** menjalankan server lokal.

Sebelum memulai load test, pastikan:
- Aplikasi production dapat diakses publik (buka `https://skripsi-apotek-iyaf.vercel.app` di browser).
- Database TiDB Cloud dapat diakses dari production (cek `frontend/.env` — `DB_HOST=gateway01.ap-northeast-1.prod.aws.tidbcloud.com`).
- Seeder akun sudah dijalankan di production:
  ```cmd
  php artisan db:seed
  ```
- Verifikasi login manual di browser production menggunakan salah satu akun (`admin/admin`).

> **⚠️ Perhatian Vercel (Serverless)**: Vercel menjalankan aplikasi sebagai *serverless function* dengan batas waktu eksekusi per request (tergantung plan, umumnya 10–60 detik). Gunakan skenario beban yang wajar dan perhatikan bahwa session cookie tetap bekerja secara stateless. Jika muncul error `504`/`Timeout` saat beban tinggi, itu adalah batasan serverless — bukan bug aplikasi.

---

## 4. MEMBUAT TEST PLAN DI GUI APACHE JMETER

### Alur Struktur Test Plan (User Journey)

```text
Apotek Sena Farma Load Test Plan
└── Thread Group (Skenario Beban User)
    ├── User Defined Variables (BASE_URL, USERNAME, PASSWORD, INFLUXDB_URL, INFLUXDB_TOKEN)
    ├── HTTP Request Defaults (Protocol: https, Host: skripsi-apotek-iyaf.vercel.app, Port: 443)
    ├── HTTP Cookie Manager (Menyimpan session cookie Laravel)
    │
    ├── [Step 1] GET / → Ambil CSRF Token
    │   └── Regex Extractor (Variable: csrf_token, Regex: name="_token" value="([^"]+)")
    │
    ├── [Step 2] POST /login-process → Login (Form: _token, username, password)
    │
    ├── [Step 3] GET /dashboard → Dashboard
    ├── [Step 4] GET /stok-cabang → Stok Cabang
    ├── [Step 5] GET /data-obat → Data Obat (Admin)
    ├── [Step 6] GET /obat-masuk → Obat Masuk
    ├── [Step 7] GET /prediksi → Prediksi Stok
    ├── [Step 8] GET /laporan → Laporan
    │
    ├── Backend Listener (Menghubungkan ke InfluxDB: http://localhost:8086)
    ├── View Results Tree (Listener Debugging - HANYA untuk Uji 1 User)
    └── Summary Report (Listener Ringkasan statistik)
```

> **💡 Catatan Khusus Laravel**: Berbeda dengan tutorial WedBook yang memakai `Authorization: Bearer ${token}` (JWT), aplikasi Laravel ini menggunakan **session cookie** untuk autentikasi. Oleh karena itu:
> - Tidak perlu `JSON Extractor` untuk token JWT.
> - Tambahkan **HTTP Cookie Manager** agar JMeter otomatis menyimpan dan mengirim kembali session cookie (`laravel_session`).
> - Gunakan **Regex Extractor** untuk mengambil **CSRF token** dari halaman login, lalu kirim sebagai `_token` di form login.

### Langkah demi Langkah Pembuatan di GUI JMeter:

1. **Thread Group**:
   - Klik kanan **Test Plan** ➜ **Add** ➜ **Threads (Users)** ➜ **Thread Group**.
   - Set awal untuk pengujian 1 user (debugging): `Number of Threads = 1`, `Ramp-Up = 1`, `Loop Count = 1`.

2. **User Defined Variables**:
   - Klik kanan **Test Plan** ➜ **Add** ➜ **Config Element** ➜ **User Defined Variables**.
   - Tambahkan variabel:
     - `BASE_URL` = `https://skripsi-apotek-iyaf.vercel.app`
     - `USERNAME` = `admin`
     - `PASSWORD` = `admin`
     - `INFLUXDB_URL` = `http://localhost:8086/api/v2/write?org=skripsi_apotek_org&bucket=jmeter_db`
     - `INFLUXDB_TOKEN` = `PASTE_ALL_ACCESS_API_TOKEN_ANDA`

3. **HTTP Request Defaults**:
   - Klik kanan **Thread Group** ➜ **Add** ➜ **Config Element** ➜ **HTTP Request Defaults**.
   - **Protocol**: `https`
   - **Server Name or IP**: `skripsi-apotek-iyaf.vercel.app`
   - **Port Number**: `443`

4. **HTTP Cookie Manager**:
   - Klik kanan **Thread Group** ➜ **Add** ➜ **Config Element** ➜ **HTTP Cookie Manager**.
   - Biarkan pengaturan default (kebijakan `standard`).
   - Fungsi: menyimpan session `laravel_session` dari login untuk request berikutnya.

5. **Autentikasi: GET / (Ambil CSRF Token)**
   - Tambahkan **HTTP Request**: Name `1. GET / - Halaman Login`, Method `GET`, Path `/`.
   - Klik kanan pada request ini ➜ **Add** ➜ **Post Processors** ➜ **Regex Extractor**.
   - **Name of created variable**: `csrf_token`
   - **Regular Expression**: `name="_token" value="([^"]+)"`
   - **Template**: `$1$`
   - **Match No**: `1`
   - **Default Value**: `csrf_NOT_FOUND`

6. **POST /login-process (Login Session-Based)**
   - Tambahkan **HTTP Request**: Name `2. POST /login-process - Login`, Method `POST`, Path `/login-process`.
   - Centang **Use multipart/form-data** atau isi parameter sebagai form data:
     - `_token` = `${csrf_token}`
     - `username` = `${USERNAME}`
     - `password` = `${PASSWORD}`
   - **Follow Redirects**: ✅ (untuk mendarat di `/dashboard` setelah login).

7. **HTTP Request Alur Bisnis (Semua GET):**
   - Tambahkan Samplers sesuai alur pada struktur di atas:
     - `3. GET /dashboard - Dashboard`
     - `4. GET /stok-cabang - Stok Cabang`
     - `5. GET /data-obat - Data Obat (Admin)`
     - `6. GET /obat-masuk - Obat Masuk`
     - `7. GET /prediksi - Prediksi Stok`
     - `8. GET /laporan - Laporan`

8. **Verifikasi Debugging (1 User)**:
   - Tambahkan **View Results Tree**, klik tombol **Play (Green Button)**.
   - Pastikan semua respon berwarna hijau (status `200` / `302` redirect yang wajar).
   - Jika gagal pada langkah login (`419 Page Expired`), artinya CSRF token tidak ter-extract dengan benar.

### 4.9 (Alternatif) Mengonversi Postman Collection ke JMeter

> ⚠️ **Perhatian**: JMeter **TIDAK memiliki fitur bawaan** "Import from Postman" di menu File. Konversi Postman collection ke JMeter harus dilakukan melalui alat eksternal, lalu hasilnya dibuka di JMeter.

> **File collection**: `frontend/postman/Apotek-Stock-Management.postman_collection.json`

#### Opsi A — Online Converter (Paling Mudah): BlazeMeter Converter
1. Buka browser, akses **https://converter.blazemeter.com/**.
2. Klik tombol **"Choose File"** dan pilih `frontend/postman/Apotek-Stock-Management.postman_collection.json`.
3. Klik **"Convert"**.
4. Setelah selesai, klik **"Download Jmx"** untuk mengunduh file `.jmx` hasil konversi.
5. Buka file `.jmx` tersebut di JMeter (**File ➜ Open**).

#### Opsi B — CLI Tool (npx, Tanpa Instalasi Global)
1. Pastikan **Node.js** sudah terinstal.
2. Buka Command Prompt di folder project `a:\Skripsi-Apotek`:
   ```cmd
   npx postman-to-jmeter --output frontend/jmeter/postman_converted.jmx frontend/postman/Apotek-Stock-Management.postman_collection.json
   ```
3. Buka hasil konversi `frontend/jmeter/postman_converted.jmx` di JMeter (**File ➜ Open**).

#### ⚠️ Penyesuaian WAJIB Setelah Konversi (khusus Laravel):

Karena project Laravel memakai **session-based auth + CSRF token**, hasil konversi TIDAK langsung bisa dijalankan. Lakukan penyesuaian berikut:

| No | Hal yang Perlu Disesuaikan | Alasannya |
| :--- | :--- | :--- |
| 1 | Tambahkan **HTTP Cookie Manager** di dalam Thread Group | Menyimpan & mengirim ulang cookie session `laravel_session` setelah login. Konversi dari Postman **tidak** menambahkan ini secara otomatis. |
| 2 | Ganti **semua** sintaks variabel Postman `{{nama}}` menjadi sintaks JMeter `${nama}` | Postman memakai `{{skripsi-apotek}}`, `{{csrf_token}}`, `{{id_obat}}`, dst. JMeter memakai `${nama}`. |
| 3 | Tambah **User Defined Variables**: `skripsi-apotek` = `https://skripsi-apotek-iyaf.vercel.app`, `csrf_token` = (kosong), `id_obat` = `1`, dst. | Nilai variabel Postman tidak ikut ter-konversi otomatis. |
| 4 | Tambah **Regex Extractor** pada `GET /` (di folder Authentication) untuk menangkap CSRF token dari HTML | Collection hanya berisi `{{csrf_token}}` yang harus diisi manual dari cookie `XSRF-TOKEN`. Pola regex: `name="_token" value="([^"]+)"` dengan variable `${csrf_token}`. |
| 5 | Perbaiki nilai **password** di request `POST /login-process` | Collection memakai `password123`, sedangkan seeder project memakai `admin`/`apotek`/`manajer`. |
| 6 | Hapus / Disable request **GET /login-process (redirect saja)** | Endpoint ini hanya redirect, tidak berguna untuk load test. |
| 7 | Hapus / Disable request **POST /logout** | Jika logout dijalankan di tengah iterasi, session hilang dan request berikutnya akan gagal (redirect ke login). |
| 8 | Tambahkan **User Defined Variables INFLUXDB_URL & INFLUXDB_TOKEN** + **Backend Listener** | Konversi Postman TIDAK menyertakan koneksi InfluxDB. Panduan lengkap di [Bagian 5](#5-menghubungkan-jmeter-dengan-influxdb-v2). |

#### Rekomendasi:
Jika Anda hanya butuh *load testing* alur utama (login → dashboard → stok → prediksi → laporan), **gunakan file test plan yang sudah disiapkan**:

```text
frontend/jmeter/Apotek_Sena_Farma_Load_Test.jmx
```

File tersebut sudah mencakup semuanya (CSRF token otomatis, HTTP Cookie Manager, Backend Listener InfluxDB, alur user journey), sehingga Anda **tidak perlu** mengonversi Postman manual terlebih dahulu.

---

## 5. MENGHUBUNGKAN JMETER DENGAN INFLUXDB V2

1. Klik kanan **Thread Group** ➜ **Add** ➜ **Listener** ➜ **Backend Listener**.
2. Pada dropdown **Backend Listener implementation**, pilih:
   `org.apache.jmeter.visualizers.backend.influxdb.InfluxdbBackendListenerClient`
3. Atur parameter pada tabel **Parameters**:

   | Parameter Name | Value / Isian | Deskripsi / Penjelasan |
   | :--- | :--- | :--- |
   | `influxdbMetricsSender` | `org.apache.jmeter.visualizers.backend.influxdb.HttpMetricsSender` | Sender standar HTTP InfluxDB |
   | `influxdbUrl` | `http://localhost:8086/api/v2/write?org=skripsi_apotek_org&bucket=jmeter_db` | Endpoint API Write resmi InfluxDB v2 |
   | `influxdbToken` | `PASTE_ALL_ACCESS_API_TOKEN_ANDA` | Token InfluxDB v2 All Access |
   | `application` | `ApotekSenaFarma` | Nama identifikasi aplikasi |
   | `measurement` | `jmeter` | Nama tabel measurement di InfluxDB |
   | `summaryOnly` | `false` | Mengirim data sampel detail per transaksi |
   | `samplersRegex` | `.*` | Mengirimkan data seluruh HTTP Request |
   | `percentiles` | `90;95;99` | Persentil yang dihitung & dikirim |

4. Klik **Save** (`Ctrl + S`).
5. Jalankan tes 1 user di JMeter. Buka `http://localhost:8086` ➜ **Data Explorer**, pastikan data `_measurement == "jmeter"` sudah masuk.

---

## 6. MENGHUBUNGKAN INFLUXDB DENGAN GRAFANA

1. Buka browser: **`http://localhost:3001`**.
2. Klik menu (ikon garis tiga) ➜ **Connections** ➜ **Data sources**.
3. Klik tombol **Add data source** ➜ pilih **InfluxDB**.
4. Isi parameter konfigurasi:
   - **Query Language**: Pilih **`Flux`** (Wajib Flux untuk InfluxDB v2).
   - **HTTP -> URL**: `http://localhost:8086`
   - **InfluxDB Details**:
     - **Organization**: `skripsi_apotek_org`
     - **Token**: *Paste* **All Access API Token** InfluxDB Anda.
     - **Default Bucket**: `jmeter_db`
5. Gulir ke bawah, lalu klik **Save & Test**.
6. Pastikan muncul notifikasi hijau: **"3 buckets found"** atau **"Data source is working"**.

---

## 7. MEMBUAT DASHBOARD & QUERY FLUX DI GRAFANA

Buat **Dashboard Baru** di Grafana (`http://localhost:3001`), lalu tambahkan 4 panel utama berikut:

### Panel 1: Average Response Time
- **Visualisasi**: `Time series`
- **Title**: `Average Response Time`
- **Unit**: `Standard options` ➜ `Unit` ➜ `Time` ➜ `milliseconds (ms)`
- **Query Flux**:
  ```flux
  from(bucket: "jmeter_db")
    |> range(start: v.timeRangeStart, stop: v.timeRangeStop)
    |> filter(fn: (r) => r._measurement == "jmeter" and r._field == "avg")
    |> aggregateWindow(every: v.windowPeriod, fn: mean, createEmpty: false)
  ```

### Panel 2: Throughput / Requests Per Second (RPS)
- **Visualisasi**: `Time series`
- **Title**: `Throughput / Requests Per Second (RPS)`
- **Unit**: `Throughput` ➜ `Requests/sec (rps)`
- **Query Flux**:
  ```flux
  from(bucket: "jmeter_db")
    |> range(start: v.timeRangeStart, stop: v.timeRangeStop)
    |> filter(fn: (r) => r._measurement == "jmeter" and r._field == "count")
    |> aggregateWindow(every: 1s, fn: sum, createEmpty: false)
  ```

### Panel 3: 90th Percentile Response Time (p90)
- **Visualisasi**: `Stat`
- **Title**: `p90 Response Time`
- **Unit**: `milliseconds (ms)`
- **Query Flux**:
  ```flux
  from(bucket: "jmeter_db")
    |> range(start: v.timeRangeStart, stop: v.timeRangeStop)
    |> filter(fn: (r) => r._measurement == "jmeter" and r._field == "pct90.0")
    |> mean()
  ```

### Panel 4: Error Rate (%)
- **Visualisasi**: `Stat`
- **Title**: `Error Rate`
- **Unit**: `Percent (0-100)`
- **Value mappings / No value**: Pada panel sebelah kanan `Standard options` ➜ **`No value`** ➜ Isi `0%` (agar saat 0 error menampilkan `0%` hijau bukan "No data").
- **Query Flux**:
  ```flux
  from(bucket: "jmeter_db")
    |> range(start: v.timeRangeStart, stop: v.timeRangeStop)
    |> filter(fn: (r) => r._measurement == "jmeter" and r._field == "error_pct")
    |> mean()
  ```

---

## 8. PROSEDUR EKSEKUSI LOAD TESTING (10, 25, 50, 100 USERS)

### Matriks Pengaturan Skenario Beban

| Skenario | Virtual Users (`Number of Threads`) | Ramp-Up Period | Durasi Steady State |
| :--- | :---: | :---: | :---: |
| **Skenario 1** | `10` | `10` Detik | 60 Detik |
| **Skenario 2** | `25` | `25` Detik | 60 Detik |
| **Skenario 3** | `50` | `50` Detik | 60 Detik |
| **Skenario 4** | `100` | `100` Detik | 60 Detik |

### Prosedur Eksekusi GUI Mode:
1. Matikan (*Disable*) listener **View Results Tree** sebelum menguji beban tinggi.
2. Ubah `Number of Threads` dan `Ramp-Up` di Thread Group sesuai tabel di atas.
3. Jalankan pengujian satu per satu.
4. **Beri jeda istirahat 30–60 detik antar skenario** agar server Laravel dan database cloud kembali *idle*.
5. Di Grafana, ubah pemilih waktu di kanan atas ke **`Last 5 minutes`** saat tes berjalan, lalu ambil *Screenshot* grafik dashboard.

### Eksekusi via CLI Mode (Non-GUI Mode):
Buka Command Prompt di folder `apache-jmeter-5.6.3\bin`:
```cmd
jmeter -n -t "a:\Skripsi-Apotek\frontend\jmeter\Apotek_Sena_Farma_Load_Test.jmx" -l "a:\Skripsi-Apotek\frontend\jmeter\results_100u.jtl" -e -o "a:\Skripsi-Apotek\frontend\jmeter\report_100u"
```

> **Tips**: Untuk skenario CLI, ubah dulu `Number of Threads` & `Ramp-Up` di file `.jmx` (bisa edit langsung dengan teks editor), atau gunakan parameter berikut:
> ```cmd
> jmeter -n -JThreads=100 -JrampUp=100 -t "Ap..._Load_Test.jmx" ...
> ```
> *(Perlu menambahkan `${__P(Threads,1)}` di Thread Group agar parameter ini aktif.)*

---

## 9. PANDUAN TROUBLESHOOTING ERROR UMUM

### 1. Error `419 Page Expired` / Gagal Login di JMeter
- **Penyebab**: CSRF token tidak berhasil diambil dari halaman login, atau session cookie tidak dikirim kembali.
- **Solusi**:
  1. Pastikan **Regex Extractor** pada `GET /` memiliki regex: `name="_token" value="([^"]+)"`.
  2. Pastikan parameter `_token = ${csrf_token}` dikirim dalam **body form** `POST /login-process`.
  3. Pastikan **HTTP Cookie Manager** sudah ditambahkan di dalam Thread Group.
  4. Buka **View Results Tree** dan periksa response body `GET /` — cari nilai `_token`.

### 2. Error `419 Session Expired` Saat Beban Tinggi
- **Penyebab**: SESSION_DRIVER=file menyebabkan bottleneck saat ribuan session ditulis ke file secara bersamaan.
- **Solusi**: Pertimbangkan mengganti SESSION_DRIVER ke `database` atau `redis` di `frontend/.env` untuk pengujian beban tinggi, atau batasi skenario sesuai kapasitas server.

### 3. Error `403 Forbidden: insufficient permissions for write` di JMeter Log
- **Penyebab**: API Token di parameter `influxdbToken` JMeter tidak memiliki akses tulis ke bucket `jmeter_db`.
- **Solusi**: Buka InfluxDB `http://localhost:8086` ➜ **Load Data** ➜ **API Tokens** ➜ **Generate All Access API Token**, salin token baru dan paste ke `influxdbToken` di JMeter.

### 4. Error `Data is missing a time field` di Grafana
- **Penyebab**: Fungsi `mean()` menghapus kolom waktu `_time` sehingga panel grafik garis (*Time series*) tidak bisa menggambar titik waktu.
- **Solusi**: Gunakan `aggregateWindow(every: v.windowPeriod, fn: mean, createEmpty: false)` atau ubah tipe visualisasi ke **Stat**.

### 5. Tampilan "No Data" Tiba-Tiba di Grafana
- **Penyebab**: JMeter sedang berhenti (*stopped/idle*) sehingga tidak ada data metrik baru dalam rentang waktu terpasang (*Last 15 minutes*).
- **Solusi**: Ubah *time range* di kanan atas Grafana ke **`Last 1 hour`** atau **`Last 3 hours`**, atau tekan tombol **Play (Run)** kembali di JMeter.

### 6. Grafana Tidak Bisa Dibuka di `localhost:3001`
- **Penyebab**: Port 3001 belum di-set di konfigurasi Grafana.
- **Solusi**:
  1. Pastikan file `conf/defaults.ini` (atau `custom.ini`) memiliki `http_port = 3001`.
  2. Atau jalankan lewat script `start-grafana.bat` yang sudah otomatis menambahkan `--http-port 3001`.

### 7. JMeter Tidak Bisa Connect ke InfluxDB
- **Penyebab**: InfluxDB belum berjalan, atau URL org/bucket salah.
- **Solusi**: Pastikan `influxd.exe` berjalan, dan URL di `influxdbUrl` menggunakan:
  ```
  http://localhost:8086/api/v2/write?org=skripsi_apotek_org&bucket=jmeter_db
  ```

---

## REFERENSI FILE

| File | Lokasi | Keterangan |
| :--- | :--- | :--- |
| **JMeter Test Plan** | `frontend/jmeter/Apotek_Sena_Farma_Load_Test.jmx` | Test plan siap pakai (buka di JMeter GUI) |
| **Script Grafana** | `start-grafana.bat` | Jalankan Grafana otomatis di port 3001 |
| **Konfigurasi DB** | `frontend/.env` | Koneksi database TiDB Cloud |
| **Seeder Akun** | `frontend/database/seeders/DatabaseSeeder.php` | Akun default (admin/admin, apotek/apotek, manajer/manajer) |
| **Routes** | `frontend/routes/web.php` | Daftar endpoint aplikasi |
| **Login Controller** | `frontend/app/Http/Controllers/Shared/AuthController.php` | Proses login session-based |