Berikut contoh **SRS (Software Requirements Specification)** khusus untuk **fitur prediksi stok obat** pada proyek Laravel kamu, sekaligus menjelaskan **cara penerapan model Decision Tree**, **alur data**, dan **bagaimana model belajar dari histori (obat masuk, keluar, dan stok opname)**.

Aku buat dengan gaya **rapih, akademik, dan siap masuk skripsi (BAB 3 / Analisis Sistem)** ✨

---

# 📘 SRS FITUR PREDIKSI STOK OBAT

## 1. Pendahuluan

Fitur prediksi stok obat merupakan bagian dari sistem informasi persediaan yang bertujuan untuk membantu pengguna dalam memperkirakan kebutuhan stok obat di masa mendatang. Prediksi dilakukan menggunakan algoritma **Decision Tree** berdasarkan data historis transaksi obat, sehingga keputusan pengadaan dapat dilakukan secara lebih akurat dan berbasis data.

---

## 2. Tujuan Sistem Prediksi

* Mengurangi risiko **keMenipisan stok (stockout)**
* Menghindari **penumpukan obat**
* Membantu **pengambilan keputusan restock**
* Menghasilkan **status stok (Aman, Menipis, Habis)**

---

## 3. Ruang Lingkup

Fitur prediksi mencakup:

* Pengolahan data historis:

  * obat masuk
  * obat keluar
  * stok opname
* Proses training model Decision Tree
* Penyediaan hasil prediksi melalui API (Flask)
* Integrasi hasil prediksi ke Laravel

---

# 🧠 4. Sumber Data (Dataset)

Model akan belajar dari beberapa tabel utama:

---

## 📥 4.1 Data Obat Masuk (`tb_obat_masuk`)

Digunakan untuk mengetahui:

* jumlah stok masuk
* frekuensi pengadaan

---

## 📤 4.2 Data Obat Keluar (`tb_obat_keluar` / penjualan)

DIGUNAKAN SEBAGAI CORE DATA

Digunakan untuk:

* mengetahui pola konsumsi obat
* menentukan tren permintaan

---

## 📊 4.3 Data Stok Opname (`tb_stok_opname`)

Digunakan untuk:

* validasi kondisi stok riil
* koreksi selisih stok

👉 Ini penting untuk:
**mengurangi bias data sistem**

---

## 📦 4.4 Data Stok Batch (`tb_stok_batch`)

Digunakan untuk:

* stok saat ini
* kondisi aktual per batch

---

# ⚙️ 5. Fitur Input Model (Feature Engineering)

Data akan diolah menjadi fitur seperti:

| Fitur               | Keterangan                     |
| ------------------- | ------------------------------ |
| total_keluar        | jumlah obat keluar per periode |
| total_masuk         | jumlah obat masuk              |
| stok_saat_ini       | stok terakhir                  |
| selisih_opname      | selisih stok fisik vs sistem   |
| frekuensi_transaksi | jumlah transaksi               |
| bulan / periode     | waktu                          |

---

# 🎯 6. Output Model

Model akan menghasilkan:

### 📌 1. Kategori Status Stok

* Aman
* Menipis
* Habis

---

### 📌 2. Prediksi Jumlah Stok

* estimasi kebutuhan 1 bulan ke depan

---

# 🤖 7. Metode yang Digunakan

Menggunakan:

👉 **Decision Tree Classifier**

Alasan:

* mudah dipahami
* cocok untuk klasifikasi stok
* ringan untuk integrasi

---

# 🔁 8. Alur Sistem Prediksi

```text
Laravel (Data) 
   ↓
Export Dataset
   ↓
Google Colab (Training Model)
   ↓
Model (.pkl)
   ↓
Flask API
   ↓
Laravel (Consume API)
   ↓
Tampilkan hasil prediksi
```

---

# 🧪 9. Proses Training Model

## Langkah:

1. Ambil data dari database Laravel
2. Export ke CSV
3. Upload ke Google Colab
4. Preprocessing:

   * cleaning
   * grouping per bulan
5. Training Decision Tree
6. Evaluasi (accuracy)
7. Simpan model (`model.pkl`)

---

# 🔗 10. Integrasi dengan Flask API

## Endpoint contoh:

```http
POST /predict
```

### Request:

```json
{
  "total_keluar": 120,
  "stok_saat_ini": 30,
  "frekuensi": 15
}
```

### Response:

```json
{
  "status": "Menipis",
  "prediksi": 100
}
```

---

# 🔌 11. Integrasi ke Laravel

### Flow:

1. Laravel ambil data obat
2. Kirim ke API Flask
3. Terima hasil prediksi
4. Simpan ke tabel / tampilkan

---

# 🗃️ 12. Tabel Tambahan (Opsional tapi disarankan)

```php
tb_prediksi
- id
- id_obat
- hasil_status
- jumlah_prediksi
- periode
- created_at
```

---

# ⚠️ 13. Aturan Sistem

* Prediksi hanya berdasarkan data historis
* Minimal data: 1–3 bulan
* Data opname digunakan sebagai koreksi
* Tidak menggantikan keputusan manusia (support system)

---

# 💡 14. Bagaimana Model “Belajar”

Ini bagian penting buat kamu pahami:

👉 Model belajar dari pola:

Contoh:

| Obat | Keluar | Stok   | Status  |
| ---- | ------ | ------ | ------- |
| A    | tinggi | rendah | Habis   |
| B    | sedang | sedang | Aman    |
| C    | tinggi | rendah | Menipis |

---

Model akan menemukan pola seperti:

```text
IF stok rendah AND penjualan tinggi → HABIS
IF stok cukup AND penjualan stabil → AMAN
```

---

# 🔥 PERAN STOK OPNAME

Tanpa opname:
❌ data bisa bias
❌ stok tidak akurat

Dengan opname:
✅ data real
✅ model lebih akurat

---

# 🚀 KESIMPULAN

Fitur prediksi dalam sistem ini:

* Menggunakan data nyata (masuk, keluar, opname)
* Diproses dengan Decision Tree
* Diintegrasikan via Flask API
* Digunakan sebagai **decision support system**
