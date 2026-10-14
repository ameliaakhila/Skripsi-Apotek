# Rencana Refactor View Layer - Class Components, Views, dan Blade

## Konteks dan Tujuan
Dokumen ini fokus pada refactor layer tampilan (View) agar lebih mudah di-maintain tanpa mengubah styling/isi visual halaman.

Target utama:
- struktur folder Blade lebih jelas dan konsisten
- class View Components sinkron dengan file Blade
- duplikasi view berMenipis
- naming convention seragam (role, file, namespace)
- alur render (layout, component, modal, script) lebih rapi dan terprediksi

## Hasil Analisa Struktur Saat Ini

### Baseline file (hasil audit)
- Total file Blade: 66
- Area `resources/views/admin`: 12
- Area `resources/views/apoteker`: 5
- Area `resources/views/manager`: 4
- Area `resources/views/shared`: 5
- Area `resources/views/components`: 27
- Area `resources/views/layouts`: 9

### Temuan utama
1. Ada duplikasi halaman antara role-pages dan folder komponen.
   Contoh: halaman mirip ada di `resources/views/admin/*` dan juga `resources/views/components/admin/*`.
2. Naming belum konsisten.
   Contoh campuran: `data-kategori.blade.php` dan `data_kategori.blade.php`.
3. Role naming masih campur.
   Ada penggunaan `Apoteker` (huruf besar), `apoteker` (huruf kecil), dan historis `pharmacist`.
4. Class View Components sebagian belum sinkron dengan path Blade yang benar.
   Ada beberapa `render()` yang menunjuk view yang tidak ada.
5. Pola layout campur antara `@extends('layouts.app')` dan `<x-app>` di banyak halaman.
6. Modal lintas role masih memakai namespace komponen `x-admin.*`, sehingga coupling antarmodul masih tinggi.
7. Script global pada satu file besar membuat maintenance behavior UI (modal/select/delete alert) jadi sulit ditelusuri per halaman.

## Prinsip Refactor View Layer
1. Tidak mengubah tampilan visual dan konten bisnis di halaman.
2. Fokus pada konsistensi struktur, naming, dan pemisahan tanggung jawab.
3. Migrasi bertahap dengan backward compatibility.
4. Satu sumber kebenaran untuk setiap halaman (hindari file duplikat aktif).

## Target Struktur Folder (Disarankan)

```text
resources/views/
  layouts/
    app.blade.php
    auth.blade.php
    components/
      head.blade.php
      navbar.blade.php
      footer.blade.php
      sidebar-admin.blade.php
      sidebar-apoteker.blade.php
      sidebar-manager.blade.php
      script.blade.php

  pages/
    admin/
      dashboard.blade.php
      data-obat.blade.php
      data-kategori.blade.php
      data-satuan.blade.php
      data-supplier.blade.php
      data-cabang.blade.php
      kelola-akun.blade.php
    apoteker/
      dashboard.blade.php
      obat-masuk.blade.php
      obat-keluar.blade.php
    manager/
      dashboard.blade.php
      laporan.blade.php
      prediksi-obat.blade.php
      obat-kedaluwarsa.blade.php
    shared/
      stok-cabang.blade.php
      mutasi-stok.blade.php
      stok-batch-kedaluwarsa.blade.php
      stok-opname.blade.php

  components/
    ui/
      alert.blade.php
      table-action.blade.php
      empty-state.blade.php
    link/
      nav-link.blade.php
      nav-group.blade.php
    forms/
      select-cabang.blade.php
      select-obat.blade.php
    modals/
      data-obat-modal.blade.php
      data-kategori-modal.blade.php
      data-satuan-modal.blade.php
      data-supplier-modal.blade.php
      data-cabang-modal.blade.php
      obat-masuk-modal.blade.php
      obat-keluar-modal.blade.php
      mutasi-stok-modal.blade.php

  errors/
  auth/
```

Catatan penting:
- Folder `resources/views/components/admin/*` dijadikan legacy lalu dipensiunkan bertahap.
- Halaman aktif dipusatkan di `resources/views/pages/*`.

## Target Struktur Class View Components

```text
app/View/Components/
  Layout/
    AppShell.php
    AuthShell.php
  Link/
    NavLink.php
    NavGroup.php
  Ui/
    Alert.php
    TableAction.php
  Forms/
    SelectCabang.php
    SelectObat.php
  Modals/
    DataObatModal.php
    DataKategoriModal.php
    DataSatuanModal.php
    DataSupplierModal.php
    DataCabangModal.php
    ObatMasukModal.php
    ObatKeluarModal.php
    MutasiStokModal.php
```

Konvensi mapping wajib:
- `App\View\Components\Modals\ObatMasukModal` -> `resources/views/components/modals/obat-masuk-modal.blade.php`
- `App\View\Components\Link\NavLink` -> `resources/views/components/link/nav-link.blade.php`

## Konvensi Penamaan (Wajib Seragam)

### Folder
- role: `admin`, `apoteker`, `manager`, `shared` (lowercase)
- hindari variasi huruf besar/kecil untuk nama role

### File Blade
- gunakan `kebab-case`
- contoh benar: `data-kategori.blade.php`
- contoh yang harus dihindari: `data_kategori.blade.php`

### Class Component
- gunakan `PascalCase`
- suffix jelas untuk komponen domain tertentu: `...Modal`, `...Shell`, `...Select`

### Namespace
- ikuti struktur folder class
- hindari class di folder `Admin` yang me-render path non-admin atau path yang tidak ada

## Rencana Penerapan Refactor (Step by Step)

## Fase 1 - Stabilkan Naming dan Path
1. Samakan role folder ke lowercase seluruhnya: `admin`, `apoteker`, `manager`, `shared`.
2. Samakan referensi `view('...')` di controller agar sesuai path final.
3. Samakan semua referensi include/component ke lowercase konsisten.
4. Ubah file snake_case menjadi kebab-case untuk halaman utama dan komponen utama.

Output fase 1:
- tidak ada path `view(...)` yang mengarah ke file non-eksis
- tidak ada campuran `Apoteker` vs `apoteker`

## Fase 2 - Rapikan Class Component Mapping
1. Audit seluruh `render()` di `app/View/Components/*`.
2. Perbaiki semua render target yang tidak ada filenya.
3. Pindahkan class yang tidak dipakai ke area `Legacy` sementara atau hapus bertahap.
4. Pastikan komponen reusable ada di `components/ui`, `components/forms`, `components/modals`.

Output fase 2:
- 100% class component punya target view valid
- tidak ada class component orphan

## Fase 3 - Hilangkan Duplikasi Halaman
1. Pilih satu sumber halaman aktif: `resources/views/pages/*`.
2. Tandai `resources/views/components/admin/*` sebagai legacy.
3. Migrasikan pemanggilan lama ke halaman aktif satu per satu.
4. Setelah semua route/controller stabil, hapus file duplikat yang tidak dipakai.

Output fase 3:
- setiap fitur hanya punya satu file halaman aktif
- beban maintenance turun signifikan

## Fase 4 - Standardisasi Layout dan Script
1. Tentukan satu pola utama: `<x-app>` atau `@extends`, pilih satu untuk konsistensi.
2. Pisahkan script global vs script halaman:
   - global di `layouts/components/script.blade.php`
   - script spesifik halaman lewat `@push('scripts')`.
3. Rapikan script modal agar tidak bergantung pada selector yang ambigu.

Output fase 4:
- lifecycle modal/select2/sweetalert lebih stabil
- debugging front-end lebih mudah

## Fase 5 - Refactor Modal Berbasis Domain
1. Kelompokkan modal per domain di `components/modals/*`.
2. Hindari namespace `x-admin.*` untuk modul shared/apoteker/manager.
3. Standarkan kontrak data modal (atribut `data-*`, action URL, method).

Output fase 5:
- modal reusable lintas role
- coupling role berMenipis

## Checklist Teknis Verifikasi
1. Semua path `view('...')` valid.
2. Semua `<x-...>` resolve ke class/view yang benar.
3. Tidak ada file halaman duplikat yang masih dipanggil route aktif.
4. Tidak ada referensi file snake_case jika standar sudah `kebab-case`.
5. Route list tetap hijau dan seluruh halaman utama bisa dibuka.
6. Tidak ada perubahan styling/markup visual selain kebutuhan rename variabel/path.

## Aturan Implementasi Aman
1. Jangan ubah CSS class, struktur DOM, dan konten visual halaman.
2. Perubahan di Blade hanya untuk:
   - rename path
   - rename variabel agar sinkron dengan controller/service
   - ekstraksi partial/component tanpa mengubah output UI.
3. Gunakan adapter/alias sementara saat transisi nama supaya halaman lama tetap jalan.

## Prioritas Eksekusi Disarankan
1. Naming role dan path view (impact besar, risiko rendah).
2. Mapping class component yang invalid (stabilitas tinggi).
3. Eliminasi duplikasi halaman admin/components.
4. Standardisasi script dan modal contracts.
5. Pembersihan final file legacy.

## Catatan Integrasi dengan Refactor Controller/Service yang Sudah Berjalan
- View harus mengikuti naming variabel dari controller tipis yang baru.
- Jika ada perubahan key data dari service, update binding Blade secara minimal.
- Gunakan route name helper (`route(...)`) bertahap untuk menurunkan hardcoded URL.

## Hasil Akhir yang Ditargetkan
- Struktur view dan component bersih, konsisten, dan mudah dibaca.
- Class component tidak lagi memiliki render path invalid.
- Tidak ada duplikasi file halaman aktif.
- Perubahan fitur baru dapat dilakukan lebih cepat dengan risiko regressi lebih rendah.
