# Rencana Refactor Laravel - Aplikasi Apotek

## Tujuan Refactor
Merapikan arsitektur aplikasi agar:
- CRUD mengikuti standar RESTful
- duplikasi logika di controller berMenipis
- penamaan route konsisten
- penamaan file MVC menggunakan Bahasa Indonesia dan selaras dengan file yang sudah ada

## 1) Rekomendasi Struktur Folder dan Penamaan MVC (Sesuai Kondisi Proyek)

Penamaan mengikuti pola yang saat ini sudah dipakai: `DataObatController`, `ObatMasukController`, `LaporanController`, file view seperti `data-obat.blade.php`, `obat-masuk.blade.php`.

```text
app/
	Http/
		Controllers/
			Admin/
				DataObatController.php
				DataKategoriController.php
				DataSatuanController.php
				DataSupplierController.php
				DataCabangController.php
				KelolaAkunController.php
			Apoteker/
				ObatMasukController.php
				ObatKeluarController.php
			Manager/
				LaporanController.php
				PrediksiController.php
			Shared/
				StokCabangController.php
				MutasiStokController.php
				StokBatchKedaluwarsaController.php
				StokOpnameController.php
				AuthController.php
		Requests/
			Admin/
				SimpanDataObatRequest.php
				UbahDataObatRequest.php
			Apoteker/
				SimpanObatMasukRequest.php
				SimpanObatKeluarRequest.php
			Shared/
				SimpanMutasiStokRequest.php
	Models/
		Admin/
			DataObat.php
			DataKategori.php
			DataSatuan.php
			DataSupplier.php
			DataCabang.php
			ObatMasuk.php
			ObatKeluar.php
			MutasiStok.php
			StokBatch.php
			StokBatchKedaluwarsa.php
			StokOpname.php
			Laporan.php
			KelolaAkun.php
	Services/
		Admin/
			DataObatService.php
			DataKategoriService.php
			DataSatuanService.php
			DataSupplierService.php
			DataCabangService.php
		Apoteker/
			ObatMasukService.php
			ObatKeluarService.php
		Shared/
			StokCabangService.php
			MutasiStokService.php
			AlokasiBatchService.php
			StokOpnameService.php
		Manager/
			LaporanService.php
			PrediksiService.php
```

Untuk view, pertahankan pola folder yang sudah ada agar aman dan minim perubahan:

```text
resources/views/
	admin/
		dashboard.blade.php
		data-obat.blade.php
		data-kategori.blade.php
		data-satuan.blade.php
		data-supplier.blade.php
		data-cabang.blade.php
		kelola-akun.blade.php
		modals/
			modal-data-obat.blade.php
			modal-data-kategori.blade.php
			modal-data-satuan.blade.php
			modal-data-supplier.blade.php
			modal-data-cabang.blade.php
	Apoteker/
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
```

Catatan penamaan:
- Class MVC: PascalCase Bahasa Indonesia, contoh `DataObatController`, `MutasiStokService`.
- File blade: kebab-case Bahasa Indonesia, contoh `data-obat.blade.php`.
- Suffix class tetap standar Laravel: `Controller`, `Service`, `Request`.

## 2) Strategi Refactor Controller (Menipisi Duplikasi + SOLID)

### Prinsip utama
- Controller tipis: validasi request, autorisasi, panggil service, response.
- Service tebal: business logic, transaksi database, orkestrasi antar model.
- Policy + middleware: seluruh akses role dipusatkan.

### Langkah implementasi
1. Standarisasi action CRUD ke resource Laravel:
	 `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
2. Pindahkan validasi ke Form Request berbahasa Indonesia, contoh:
	 `SimpanDataObatRequest`, `UbahDataObatRequest`, `SimpanObatKeluarRequest`.
3. Gunakan route model binding untuk menghilangkan pengulangan `findOrFail`.
4. Pindahkan query filter/sort/pagination ke service.
5. Semua proses tulis data dipindah ke service dengan `DB::transaction`.
6. Hilangkan role check berulang di controller, gantikan dengan middleware + policy.
7. Gunakan event/listener untuk side-effect (sinkron stok, log aktivitas, notifikasi).

### Dampak ke SOLID
- SRP: controller hanya urusan HTTP.
- OCP: strategi FIFO/FEFO dapat ditambah tanpa ubah controller utama.
- DIP: controller bergantung pada kontrak service.

## 3) Contoh Refactor Route (Before vs After, RESTful)

### Contoh A - Data Obat

Before (sekarang):

```php
Route::get('/data-obat', [DataObatController::class, 'index']);
Route::post('/data-obat-tambah', [DataObatController::class, 'store']);
Route::put('/data-obat-edit/{id_obat}', [DataObatController::class, 'update']);
Route::delete('/data-obat-hapus/{id_obat}', [DataObatController::class, 'destroy']);
```

After (RESTful, nama tetap Indonesia):

```php
Route::middleware(['auth', 'admin'])
		->prefix('admin')
		->as('admin.')
		->group(function () {
				Route::resource('data-obat', DataObatController::class)
						->parameters(['data-obat' => 'obat']);
		});
```

### Contoh B - Obat Masuk dan Obat Keluar

Before:

```php
Route::get('/obat-masuk', [ObatMasukController::class, 'index']);
Route::post('/obat-masuk-tambah', [ObatMasukController::class, 'insert']);
Route::put('/obat-masuk-edit/{id_obat_masuk}', [ObatMasukController::class, 'update']);
Route::delete('/obat-masuk-hapus/{id_obat_masuk}', [ObatMasukController::class, 'destroy']);

Route::get('/obat-keluar', [ObatKeluarController::class, 'index']);
Route::post('/obat-keluar-tambah', [ObatKeluarController::class, 'insert']);
Route::put('/obat-keluar-edit/{id_obat_keluar}', [ObatKeluarController::class, 'update']);
Route::delete('/obat-keluar-hapus/{id_obat_keluar}', [ObatKeluarController::class, 'destroy']);
```

After:

```php
Route::middleware(['auth', 'role:admin,apoteker'])
		->group(function () {
				Route::resource('obat-masuk', ObatMasukController::class)
						->parameters(['obat-masuk' => 'obatMasuk']);

				Route::resource('obat-keluar', ObatKeluarController::class)
						->parameters(['obat-keluar' => 'obatKeluar']);
		});
```

Konvensi nama route yang disarankan:
- `admin.data-obat.index`
- `admin.data-obat.store`
- `obat-masuk.index`
- `obat-keluar.store`
- `prediksi.index`
- `laporan.index`

## 4) Saran Service Layer (Pisahkan Business Logic dari Controller)

### Pembagian service dengan nama Indonesia
- Admin: `DataObatService`, `DataKategoriService`, `DataSatuanService`, `DataSupplierService`, `DataCabangService`.
- Transaksi: `ObatMasukService`, `ObatKeluarService`.
- Stok: `StokCabangService`, `MutasiStokService`, `AlokasiBatchService`, `StokOpnameService`.
- Manajer: `LaporanService`, `PrediksiService`.

### Pola implementasi
1. Definisikan kontrak di `app/Contracts/Services`.
2. Binding kontrak ke implementasi pada Service Provider.
3. Gunakan DTO/array tervalidasi dari Form Request.
4. Proses mutasi stok wajib transaksi atomik.
5. Simpan riwayat mutasi per batch untuk audit.

### Alur method controller yang ditargetkan
1. `authorize()`
2. validasi Form Request
3. panggil service
4. return response/redirect

## 5) Catatan Opsional: Integrasi Prediksi ML + FIFO/FEFO

### Integrasi prediksi
- Buat kontrak `GatewayPrediksi` agar sumber model fleksibel (service Python/API/antrian job).
- Jalankan prediksi berat via queue.
- Simpan metadata run (versi model, horizon prediksi, waktu proses).
- Simpan snapshot hasil agar dashboard tetap cepat.

### Integrasi FIFO/FEFO berbasis batch
- Gunakan Strategy Pattern:
	`StrategiAlokasiBatch`, `StrategiFifo`, `StrategiFefo`.
- `ObatKeluarService` meminta rencana alokasi batch dari strategi, lalu commit mutasi.
- Terapkan guard rule: tidak boleh stok minus, rollback penuh bila alokasi gagal.

## Rencana Implementasi Bertahap (Aman)
1. Samakan penamaan class MVC ke Bahasa Indonesia (tanpa ubah perilaku).
2. Rapikan route ke bentuk resource RESTful bertahap per modul.
3. Migrasi validasi ke Form Request dan akses ke policy.
4. Ekstrak business logic dari controller ke service.
5. Kunci mutasi stok dalam transaksi + ledger batch.
6. Isolasi modul prediksi via gateway + queue.
7. Bersihkan endpoint/controller lama setelah pengujian lulus.
