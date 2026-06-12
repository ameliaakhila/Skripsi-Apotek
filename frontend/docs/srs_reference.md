# 📚 SRS Reference - Pharmacy Management System (Sena Farma)

**Document Version:** 1.0  
**Last Updated:** April 27, 2026  
**Status:** ✅ Production-Ready Reference Document

---

## 📖 Table of Contents

1. [System Overview](#-system-overview)
2. [Database Schema & Tables](#-database-schema--tables)
3. [Table Relationships](#-table-relationships)
4. [System Flow Architecture](#-system-flow-architecture)
5. [UI/Menu Structure Alignment](#-uimenu-structure-alignment)
6. [Feature Access Matrix](#-feature-access-matrix)
7. [Prediction Module - Feature Engineering](#-prediction-module---feature-engineering)
8. [Data Flow Examples](#-data-flow-examples)
9. [Implementation Checklist](#-implementation-checklist)

---

## 🎯 System Overview

The **Pharmacy Management System (Sena Farma)** is a Laravel-based pharmacy inventory management system designed for multi-branch pharmacy networks. The system manages:

- **Master Data**: Drugs, Categories, Suppliers, Units, Branches, Users
- **Stock Transactions**: Incoming stock (Obat Masuk), Outgoing stock (Obat Keluar), Stock mutations between branches
- **Stock Monitoring**: Real-time stock status, batch expiry tracking, physical stock verification
- **Predictive Analytics**: Machine learning-based stock level forecasting using Decision Tree model
- **Reporting**: Comprehensive reports across all operational areas

**Role-Based Access:**
- **Admin** (Administrator): Master data, system configuration, user management
- **Apoteker** (Pharmacist): Daily transactions, stock monitoring
- **Manager** (Manajer): Analytics, predictions, reporting

---

## 🗃️ Database Schema & Tables

### Core Master Data Tables

| Table Name | Primary Key | Purpose | Key Columns |
|-----------|------------|---------|------------|
| `tb_obat` | `id_obat` | Drug/Medicine master data | `nama_obat`, `safety_stock`, `id_kategori`, `id_supplier`, `id_satuan` |
| `tb_kategori` | `id_kategori` | Drug categories | `nama_kategori` |
| `tb_satuan` | `id_satuan` | Units of measurement | `nama_satuan` |
| `tb_supplier` | `id_supplier` | Supplier information | `nama_supplier`, `alamat`, `kontak` |
| `tb_cabang` | `id_cabang` | Branch/Location data | `kode_cabang`, `nama_cabang`, `tipe` (pusat/cabang), `alamat_cabang` |
| `users` | `id_user` | User accounts | `username`, `email`, `password`, `role` (admin/apoteker/manajer) |

### Stock Management Tables

| Table Name | Primary Key | Purpose | Key Columns |
|-----------|------------|---------|------------|
| `tb_obat_masuk` | `id_obat_masuk` | Incoming stock records | `kode_batch`, `tanggal_masuk`, `tanggal_kedaluwarsa`, `stok_masuk`, `id_obat` |
| `tb_stok_batch` | `id_stok_batch` | Batch-level stock tracking | `kode_batch`, `tanggal_kedaluwarsa`, `stok_masuk`, `stok_keluar`, `stok_sisa`, `status_stok`, `id_obat`, `id_cabang` |
| `tb_obat_keluar` | `id_obat_keluar` | Outgoing stock/sales | `tanggal_keluar`, `stok_keluar`, `id_stok_batch`, `id_cabang` |
| `tb_mutasi_stok` | `id_mutasi` | Stock mutations between branches | `id_stok_batch`, `id_cabang_asal`, `id_cabang_tujuan`, `tanggal_mutasi`, `jumlah_mutasi` |
| `tb_stok_opname` | `id_stok_opname` | Physical inventory verification | `tanggal_opname`, `stok_sistem`, `stok_fisik`, `selisih`, `id_stok_batch`, `id_cabang` |

### Column Details

#### `tb_obat` (Drugs/Medicines)
```sql
id_obat (BIGINT, PRIMARY KEY)
nama_obat (VARCHAR)
safety_stock (INT) -- Minimum safe stock level
id_kategori (BIGINT, FK → tb_kategori)
id_supplier (BIGINT, FK → tb_supplier)
id_satuan (BIGINT, FK → tb_satuan)
created_at, updated_at (TIMESTAMPS)
```

#### `tb_stok_batch` (Stock Batch - Core Inventory)
```sql
id_stok_batch (BIGINT, PRIMARY KEY)
kode_batch (VARCHAR) -- Batch code (e.g., "BATCH-2026-001")
tanggal_kedaluwarsa (DATE) -- Expiry date (FEFO basis)
stok_masuk (INT) -- Initial incoming quantity
stok_keluar (INT, DEFAULT 0) -- Total outgoing quantity
stok_sisa (INT, DEFAULT 0) -- Calculated: stok_masuk - stok_keluar
status_stok (ENUM: 'Aman', 'Menipis', 'Habis') -- Stock status
tujuan_penyesuaian (VARCHAR) -- Adjustment purpose/reason
catatan (TEXT) -- Notes
id_obat (BIGINT, FK → tb_obat)
id_cabang (BIGINT, FK → tb_cabang)
created_at, updated_at (TIMESTAMPS)
```

#### `tb_obat_masuk` (Incoming Stock)
```sql
id_obat_masuk (BIGINT, PRIMARY KEY)
kode_batch (VARCHAR) -- Batch identifier
tanggal_masuk (DATE) -- Receiving date
tanggal_kedaluwarsa (DATE) -- Expiry date
stok_masuk (INT) -- Quantity received
id_obat (BIGINT, FK → tb_obat)
created_at, updated_at (TIMESTAMPS)
```

#### `tb_obat_keluar` (Outgoing Stock/Sales)
```sql
id_obat_keluar (BIGINT, PRIMARY KEY)
tanggal_keluar (DATE) -- Sale/distribution date
stok_keluar (INT) -- Quantity sold/distributed
id_obat_masuk (BIGINT, FK → tb_obat_masuk)
id_stok_batch (BIGINT, FK → tb_stok_batch)
id_cabang (BIGINT, FK → tb_cabang)
created_at, updated_at (TIMESTAMPS)
```

#### `tb_mutasi_stok` (Stock Transfer Between Branches)
```sql
id_mutasi (BIGINT, PRIMARY KEY)
id_stok_batch (BIGINT, FK → tb_stok_batch)
id_cabang_asal (BIGINT, FK → tb_cabang) -- Source branch
id_cabang_tujuan (BIGINT, FK → tb_cabang) -- Destination branch
tanggal_mutasi (DATE) -- Transfer date
jumlah_mutasi (INT) -- Quantity transferred
keterangan (TEXT) -- Notes/reason
created_at, updated_at (TIMESTAMPS)
```

#### `tb_stok_opname` (Physical Inventory Verification)
```sql
id_stok_opname (BIGINT, PRIMARY KEY)
kode_opname (VARCHAR, 10) -- Opname code
tanggal_opname (DATE) -- Verification date
stok_sistem (INT) -- System stock
stok_fisik (INT) -- Physical count
selisih (INT) -- Difference: stok_fisik - stok_sistem
keterangan (TEXT) -- Notes
id_stok_batch (BIGINT, FK → tb_stok_batch)
id_cabang (BIGINT, FK → tb_cabang)
id_user (BIGINT, FK → users) -- Person who verified
id_obat (BIGINT, FK → tb_obat)
created_at, updated_at (TIMESTAMPS)
```

---

## 🔗 Table Relationships

### Entity Relationship Diagram (Text Format)

```
┌─────────────┐
│   users     │
└──────┬──────┘
       │ id_user
       │
       └─────────────────┐
                         │
    ┌────────────────────┴────────────────────┐
    │                                         │
    ▼                                         ▼
┌─────────────┐                     ┌──────────────────┐
│  tb_stok_   │                     │   tb_stok_       │
│  opname     │                     │   opname (user)  │
└─────────────┘                     └──────────────────┘

┌─────────────────┐     id_kategori    ┌──────────────┐
│   tb_kategori   │◄────────────────────│   tb_obat    │
└─────────────────┘                    └──────┬───────┘
                                               │
                                    ┌──────────┴──────────┐
                                    │                    │
                        id_satuan   │    id_supplier     │
                            │       │                    │
                            ▼       ▼                    ▼
                    ┌──────────────┐   ┌──────────────┐
                    │  tb_satuan   │   │ tb_supplier  │
                    └──────────────┘   └──────────────┘

tb_obat ◄──────── tb_obat_masuk
  │ id_obat       id_obat │
  │                        │
  │                        ▼
  │              ┌──────────────────┐
  │              │   tb_stok_batch  │◄──── tb_obat_keluar
  │◄─────────────│ (batch level)    │       (id_stok_batch)
  id_obat        └────────┬─────────┘
                          │ id_cabang
                          │
                          ▼
                   ┌──────────────┐
                   │  tb_cabang   │
                   └──────────────┘
                          ▲
                ┌─────────┴─────────┐
                │                   │
        (id_cabang_asal)    (id_cabang_tujuan)
                │                   │
                └─────────┬─────────┘
                          │
                    ┌─────▼────────┐
                    │ tb_mutasi_   │
                    │   stok       │
                    └──────────────┘
```

### Key Relationship Definitions

**1. Obat (Drug) - Hub Model**
```
tb_obat
  ├─ hasMany(tb_obat_masuk)        # Incoming records
  ├─ hasMany(tb_stok_batch)        # Batches of this drug
  ├─ belongsTo(tb_kategori)        # Category
  ├─ belongsTo(tb_supplier)        # Supplier
  └─ belongsTo(tb_satuan)          # Unit of measurement
```

**2. StokBatch (Stock Batch) - Core Inventory Model**
```
tb_stok_batch
  ├─ belongsTo(tb_obat)            # What drug
  ├─ belongsTo(tb_cabang)          # Where stored
  ├─ hasMany(tb_obat_keluar)       # Outgoing records
  ├─ hasMany(tb_stok_opname)       # Inventory checks
  ├─ hasOne(tb_mutasi_stok)        # Transfers initiated
  └─ hasMany(tb_mutasi_stok)       # Via id_stok_batch
```

**3. ObatKeluar (Outgoing) - Transaction Model**
```
tb_obat_keluar
  ├─ belongsTo(tb_obat_masuk)      # Source batch
  ├─ belongsTo(tb_stok_batch)      # Stock batch reference
  └─ belongsTo(tb_cabang)          # Branch location
```

**4. MutasiStok (Transfer) - Movement Model**
```
tb_mutasi_stok
  ├─ belongsTo(tb_stok_batch)      # Batch being transferred
  ├─ belongsTo(tb_cabang, id_cabang_asal)  # Source
  ├─ belongsTo(tb_cabang, id_cabang_tujuan) # Destination
```

**5. User & Authorization**
```
users
  └─ hasMany(tb_stok_opname)       # Inventory verifications
```

---

## 🔄 System Flow Architecture

### Overall Data Flow

```
INPUT STAGE
    │
    ├─── OBAT MASUK (Incoming Stock)
    │    └─► tb_obat_masuk → tb_stok_batch (stok_masuk++)
    │
    ├─── MUTASI STOK (Branch Transfers)
    │    └─► tb_mutasi_stok → tb_stok_batch (both branches)
    │
    └─── STOK OPNAME (Physical Verification)
         └─► tb_stok_opname → Validation

    │
    ▼

PROCESSING STAGE (Core Business Logic)
    │
    ├─── Stock Calculation (ALWAYS)
    │    Formula: stok_sisa = stok_masuk - stok_keluar
    │
    ├─── Status Assignment
    │    • Aman: stok_sisa ≥ 120% safety_stock
    │    • Menipis: 30-120% safety_stock
    │    • Habis: < 30% safety_stock
    │
    └─── FEFO Selection (First Expiry, First Out)
         └─► Auto-select batch with earliest expiry date

    │
    ▼

OUTPUT STAGE (Operations)
    │
    ├─── OBAT KELUAR (Sales/Distribution)
    │    └─► tb_obat_keluar → tb_stok_batch (stok_keluar++)
    │
    ├─── MONITORING STOK (Visibility)
    │    └─► Dashboard views, Reports
    │
    └─── PREDIKSI STOK (ML Forecasting)
         └─► Decision Tree Model → Predictions

```

### Stage-by-Stage Flow

#### **Stage 1: Master Data Setup (Admin)**
```
1. Create Drug (tb_obat)
   └─ Define: name, safety_stock, category, supplier, unit

2. Create/Update Branch (tb_cabang)
   └─ Define: code, name, type (pusat/cabang), address

3. Define Stock Parameters
   └─ safety_stock per drug (minimum safe level)
```

#### **Stage 2: Stock Receiving (Admin/Apoteker)**
```
1. Receive Incoming Stock (Obat Masuk)
   └─ Input: drug, batch code, quantity, expiry date, date

2. Create tb_obat_masuk record
   └─ Stores: kode_batch, stok_masuk, tanggal_kedaluwarsa

3. Create tb_stok_batch record (per branch)
   └─ Initial state:
      • stok_masuk = received quantity
      • stok_keluar = 0
      • stok_sisa = stok_masuk - 0
      • status_stok = calculated
      • id_cabang = destination branch
```

#### **Stage 3: Stock Transfer (Admin/Apoteker)**
```
1. Initiate Mutation (Mutasi Stok)
   └─ Select: source batch, qty, source branch, destination branch

2. FEFO Auto-Selection (if batch not specified)
   └─ System finds batch with earliest expiry date

3. Update Source Batch
   └─ stok_keluar += transferred_qty
   └─ stok_sisa = stok_masuk - stok_keluar (recalc)

4. Create/Update Destination Batch
   └─ New batch created if destination branch doesn't have this drug
   └─ stok_masuk += transferred_qty
   └─ stok_sisa = stok_masuk - stok_keluar (recalc)

5. Record tb_mutasi_stok
   └─ Audit trail: from → to, date, qty, notes
```

#### **Stage 4: Stock Monitoring (Admin/Apoteker/Manager)**
```
1. View Stok Cabang (Branch Stock)
   └─ Real-time: sum of all batches per branch

2. Monitor Batch & Expiry (Batch & Kedaluwarsa)
   └─ Batch detail, expiry dates, status

3. Physical Inventory Check (Stok Opname)
   └─ Input: system stock vs physical count
   └─ Record: selisih (difference)
   └─ Store: tb_stok_opname with verification date & person

4. Check Expiring Drugs (Obat Kedaluwarsa)
   └─ Alert: drugs expiring within 30 days or already expired
```

#### **Stage 5: Stock Outgoing (Admin/Apoteker)**
```
1. Record Sale/Distribution (Obat Keluar)
   └─ Input: drug, batch, quantity, branch, date

2. Update tb_obat_keluar
   └─ Records: stok_keluar, date, branch

3. Update Source tb_stok_batch
   └─ stok_keluar += sold_qty
   └─ stok_sisa = stok_masuk - stok_keluar (recalc)
   └─ status_stok = recalculate status
```

#### **Stage 6: Predictive Analytics (Manager)**
```
1. Generate Export Dataset
   └─ Collect: 12 months historical data
   └─ Data includes: obat masuk, obat keluar, stok opname
   └─ Features: total_keluar, stok_saat_ini, frekuensi_transaksi, etc.

2. Train Decision Tree Model
   └─ Using: Google Colab or similar ML environment
   └─ Output: model.pkl (trained classifier)
   └─ Save: Flask API deployment

3. Consume API Predictions
   └─ Send: features to Flask API /predict endpoint
   └─ Receive: status (Aman/Menipis/Habis), recommended qty
   └─ Display: in manager dashboard & reports
```

---

## 🎨 UI/Menu Structure Alignment

### Admin Sidebar Menu (Full Access)

```
Dashboard (dashboard)
│
├─ Master Data (icon: database)
│  ├─ Data Obat (data-obat)
│  ├─ Data Satuan (data-satuan)
│  ├─ Data Kategori (data-kategori)
│  ├─ Data Supplier (data-supplier)
│  └─ Data Cabang (data-cabang)
│
├─ Transaksi Stok (icon: exchange-alt)
│  ├─ Obat Masuk (obat-masuk)
│  ├─ Mutasi Stok (mutasi-stok)
│  └─ Obat Keluar (obat-keluar)
│
├─ Monitoring Stok (icon: clipboard-list)
│  ├─ Stok Cabang (stok-cabang)
│  ├─ Batch & Kedaluwarsa (stok-batch-kedaluwarsa)
│  └─ Stok Opname (stok-opname)
│
├─ Prediksi Obat (icon: chart-line) [Master access to prediction]
│
├─ Laporan (icon: file-medical)
│  ├─ Laporan Full (laporan)
│  ├─ Laporan Obat (laporan-obat)
│  ├─ Laporan Transaksi Stok (laporan-penjualan)
│  ├─ Laporan Monitoring Stok (laporan-penjualan)
│  └─ Laporan Prediksi (laporan-prediksi)
│
└─ Kelola Akun (icon: users) [Unique to Admin]
```

### Apoteker (Pharmacist) Sidebar Menu

```
Dashboard (dashboard)
│
├─ Transaksi Stok (icon: exchange-alt) [Primary focus]
│  ├─ Obat Masuk (obat-masuk)
│  ├─ Obat Keluar (obat-keluar)
│  └─ Mutasi Stok (mutasi-stok)
│
├─ Monitoring Stok (icon: clipboard-list) [Secondary]
│  ├─ Stok Cabang (stok-cabang)
│  ├─ Batch & Kedaluwarsa (stok-batch-kedaluwarsa)
│  └─ Stok Opname (stok-opname)
│
└─ Obat Kedaluwarsa (icon: exclamation-circle) [Alert feature]
```

### Manager (Manajer) Sidebar Menu

```
Dashboard (dashboard)
│
├─ Monitoring Stok (icon: clipboard-list) [Read-only view]
│  ├─ Stok Cabang (stok-cabang)
│  ├─ Batch & Kedaluwarsa (stok-batch-kedaluwarsa)
│  └─ Stok Opname (stok-opname)
│
├─ Analisis & Prediksi (icon: chart-line) [Primary focus]
│  ├─ Prediksi Stok Obat (prediksi)
│  └─ Peringatan Kedaluwarsa (obat-kedaluwarsa)
│
└─ Laporan (icon: file-medical) [Analytics focus]
   ├─ Laporan Komprehensif (laporan)
   ├─ Detail Laporan Obat (laporan-obat)
   ├─ Laporan Penjualan (laporan-penjualan)
   └─ Laporan Prediksi (laporan-prediksi)
```

### Route-to-Controller-to-View Mapping

| Menu Item | Route | Controller | View | Access |
|-----------|-------|-----------|------|--------|
| Data Obat | `/data-obat` | `Admin\DataObatController@index` | `admin/data-obat.blade.php` | Admin only |
| Obat Masuk | `/obat-masuk` | `Apoteker\ObatMasukController@index` | `apoteker/obat-masuk.blade.php` | Admin, Apoteker |
| Obat Keluar | `/obat-keluar` | `Apoteker\ObatKeluarController@index` | `apoteker/obat-keluar.blade.php` | Admin, Apoteker |
| Mutasi Stok | `/mutasi-stok` | `Shared\MutasiStokController@index` | `shared/mutasi-stok.blade.php` | All (view), Admin/Apoteker (edit) |
| Stok Cabang | `/stok-cabang` | `Shared\StokCabangController@index` | `shared/stok-cabang.blade.php` | All roles |
| Batch & Kedaluwarsa | `/stok-batch-kedaluwarsa` | `Shared\StokBatchKedaluwarsaController@index` | `shared/stok-batch-kedaluwarsa.blade.php` | All roles |
| Stok Opname | `/stok-opname` | `Shared\StokOpnameController@index` | `shared/stok-opname.blade.php` | All roles |
| Prediksi | `/prediksi` | `Manager\PrediksiController@index` | `manager/prediksi-obat.blade.php` | Admin, Manager |
| Obat Kedaluwarsa | `/obat-kedaluwarsa` | `Manager\PrediksiController@obat_kedaluwarsa` | `manager/obat-kedaluwarsa.blade.php` | Admin, Manager |
| Laporan | `/laporan` | `Manager\LaporanController@index` | `manager/laporan.blade.php` | Admin, Manager |

---

## 📊 Feature Access Matrix

| Feature Module | View | Create | Edit | Delete | Admin | Apoteker | Manager |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Master Data** |
| Data Obat | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Data Kategori | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Data Satuan | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Data Supplier | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Data Cabang | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Stock Transactions** |
| Obat Masuk | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Obat Keluar | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Mutasi Stok | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Stock Monitoring** |
| Stok Cabang | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| Batch & Kedaluwarsa | ✅ | ❌ | ✅ | ❌ | ✅ | ✅ | ✅ |
| Stok Opname | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Predictive Analytics** |
| Prediksi Stok | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Obat Kedaluwarsa | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| **Reporting** |
| Laporan Full | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Laporan Obat | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Laporan Penjualan | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Laporan Prediksi | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| **System Admin** |
| Kelola Akun | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Dashboard | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |

---

## 🤖 Prediction Module - Feature Engineering

### Data Sources for ML Model

The Decision Tree model will be trained using historical data from these tables:

#### **Primary Data Source: tb_stok_batch**
Contains the core inventory metrics for each batch per branch:

| Feature Name | Data Column | Aggregation | Purpose |
|---|---|---|---|
| `total_masuk` | `stok_masuk` | SUM per drug | Incoming quantity trend |
| `total_keluar` | `stok_keluar` | SUM per drug | Outgoing/consumption trend |
| `stok_saat_ini` | `stok_sisa` | Latest value | Current stock level |
| `status_stok` | `status_stok` | Most recent | Current category |
| `frekuensi_transaksi` | COUNT(*) | Group by id_obat | Number of transactions |
| `kedaluwarsa_upcoming` | `tanggal_kedaluwarsa` | Count < 30 days | Risk of waste |

#### **Secondary Data Source: tb_obat_keluar (Sales/Distribution)**
Historical sales data for consumption pattern analysis:

| Feature Name | Data Column | Aggregation | Purpose |
|---|---|---|---|
| `total_penjualan_bulan` | `stok_keluar` | SUM by month | Monthly sales volume |
| `rata_rata_harian` | `stok_keluar` | AVG by day | Daily consumption rate |
| `puncak_penjualan` | `stok_keluar` | MAX by period | Peak demand |
| `variasi_penjualan` | `stok_keluar` | STDEV | Demand volatility |

#### **Tertiary Data Source: tb_stok_opname (Verification Data)**
Physical inventory validation to reduce data bias:

| Feature Name | Data Column | Aggregation | Purpose |
|---|---|---|---|
| `selisih_rata_rata` | `selisih` | AVG per batch | Inventory accuracy |
| `pola_discrepancy` | `selisih` trend | Trend over time | System vs reality gap |
| `frekuensi_opname` | COUNT(*) | Frequency | Verification rigor |

#### **Supporting Data: tb_obat (Drug Master)**
Drug-level attributes for context:

| Feature Name | Data Column | Purpose |
|---|---|---|
| `safety_stock` | `safety_stock` | Minimum threshold |
| `kategori_obat` | `id_kategori` | Drug type context |
| `supplier_reliability` | Supplier performance | Source consistency |

### Feature Engineering Process

#### **Step 1: Data Extraction (SQL Query)**
```sql
-- PREDICTION FEATURES QUERY
SELECT 
  o.id_obat,
  o.nama_obat,
  o.safety_stock,
  -- Historical aggregates (12 months)
  SUM(CASE WHEN sb.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
      THEN sb.stok_masuk ELSE 0 END) as total_masuk_12m,
  SUM(CASE WHEN sb.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
      THEN sb.stok_keluar ELSE 0 END) as total_keluar_12m,
  -- Current state
  (SELECT SUM(stok_sisa) FROM tb_stok_batch WHERE id_obat = o.id_obat) as stok_saat_ini,
  -- Transaction frequency
  (SELECT COUNT(*) FROM tb_obat_keluar ok 
   JOIN tb_stok_batch sb ON ok.id_stok_batch = sb.id_stok_batch 
   WHERE sb.id_obat = o.id_obat) as frekuensi_transaksi,
  -- Expiry risk
  (SELECT COUNT(*) FROM tb_stok_batch WHERE id_obat = o.id_obat 
   AND tanggal_kedaluwarsa <= DATE_ADD(NOW(), INTERVAL 30 DAY)) as kedaluwarsa_risk,
  -- Inventory accuracy
  (SELECT AVG(ABS(selisih)) FROM tb_stok_opname 
   WHERE id_obat = o.id_obat) as accuracy_deviation
FROM tb_obat o
WHERE o.safety_stock IS NOT NULL
ORDER BY o.id_obat;
```

#### **Step 2: Data Normalization**
Normalize features to 0-1 range for ML model:

```python
# Example normalization (in Python/Google Colab)
from sklearn.preprocessing import MinMaxScaler

scaler = MinMaxScaler()
features_normalized = scaler.fit_transform(raw_features)
```

#### **Step 3: Target Variable Encoding**
Encode stock status as target classes:

```python
# Target mapping
status_mapping = {
    'Aman': 0,      # Safe stock
    'Menipis': 1,   # Low stock
    'Habis': 2      # Out of stock
}
```

#### **Step 4: Model Training**
Train Decision Tree Classifier:

```python
from sklearn.tree import DecisionTreeClassifier

# Initialize classifier
dt_model = DecisionTreeClassifier(
    max_depth=5,
    min_samples_split=5,
    random_state=42
)

# Train on historical data
dt_model.fit(X_train, y_train)

# Save model
import pickle
with open('model.pkl', 'wb') as f:
    pickle.dump(dt_model, f)
```

### Feature Selection Summary

**Primary Features (Most Important)**
1. `total_keluar` - Historical outgoing quantity
2. `stok_saat_ini` - Current stock level  
3. `safety_stock` - Minimum threshold
4. `frekuensi_transaksi` - Transaction frequency

**Secondary Features (Supporting)**
5. `kedaluwarsa_upcoming` - Expiry risk
6. `accuracy_deviation` - System accuracy
7. `total_masuk` - Incoming trend
8. `variasi_penjualan` - Demand volatility

**Excluded Features (Not Suitable)**
- Drug names (categorical, needs encoding)
- Date fields (temporal, use aggregations instead)
- Raw timestamps (use period aggregations)

### Prediction Output Examples

| Input Features | Expected Output |
|---|---|
| `total_keluar: 150, stok_saat_ini: 80, frekuensi: 12, safety_stock: 100` | `Status: Menipis, Rekomendasi: Restock 200 unit` |
| `total_keluar: 5, stok_saat_ini: 500, frekuensi: 2, safety_stock: 50` | `Status: Aman, Rekomendasi: Monitor` |
| `total_keluar: 100, stok_saat_ini: 10, frekuensi: 20, safety_stock: 80` | `Status: Habis, Rekomendasi: Urgent Restock 300 unit` |

---

## 📈 Data Flow Examples

### Example 1: Complete Transaction Flow (Obat Masuk → Obat Keluar)

```
Time: 2026-04-01

STEP 1: Receive Stock (Obat Masuk)
├─ Action: Apoteker receives 500 units of "Paracetamol 500mg" from Supplier
├─ Input:
│  └─ Drug: Paracetamol 500mg (id_obat: 5)
│  └─ Batch: BATCH-2026-PAR-001
│  └─ Quantity: 500 units
│  └─ Received: 2026-04-01
│  └─ Expiry: 2027-04-01
├─ Database Updates:
│  └─ tb_obat_masuk INSERT:
│     id_obat_masuk: 101
│     kode_batch: "BATCH-2026-PAR-001"
│     tanggal_masuk: 2026-04-01
│     tanggal_kedaluwarsa: 2027-04-01
│     stok_masuk: 500
│     id_obat: 5
│
│  └─ tb_stok_batch INSERT:
│     id_stok_batch: 250
│     kode_batch: "BATCH-2026-PAR-001"
│     tanggal_kedaluwarsa: 2027-04-01
│     stok_masuk: 500
│     stok_keluar: 0
│     stok_sisa: 500
│     status_stok: "Aman" (500 >= 120% of safety_stock)
│     id_obat: 5
│     id_cabang: 1 (Pusat)
│
└─ Result: Drug available for distribution

STEP 2: Check Stock Position
├─ Query: Stok Cabang for "Paracetamol 500mg"
├─ Result:
│  └─ Total stok_sisa: 500 units
│  └─ Location: Pusat (id_cabang: 1)
│  └─ Status: Aman (Safe)
│
└─ System: Ready for patient sales

STEP 3: Transfer to Branch (Mutasi Stok)
├─ Action: Manager transfers 200 units from Pusat to Cabang Bandung
├─ Input:
│  └─ Batch: BATCH-2026-PAR-001
│  └─ From: Pusat (id_cabang: 1)
│  └─ To: Cabang Bandung (id_cabang: 2)
│  └─ Quantity: 200 units
│  └─ Date: 2026-04-05
├─ Database Updates:
│  └─ tb_mutasi_stok INSERT:
│     id_mutasi: 45
│     id_stok_batch: 250
│     id_cabang_asal: 1
│     id_cabang_tujuan: 2
│     tanggal_mutasi: 2026-04-05
│     jumlah_mutasi: 200
│
│  └─ tb_stok_batch UPDATE (Pusat):
│     WHERE id_stok_batch = 250
│     stok_keluar: 0 → 200
│     stok_sisa: 500 → 300
│     status_stok: "Aman" → "Aman"
│
│  └─ tb_stok_batch INSERT (Cabang Bandung):
│     id_stok_batch: 251
│     kode_batch: "BATCH-2026-PAR-001"
│     stok_masuk: 200 (from transfer)
│     stok_keluar: 0
│     stok_sisa: 200
│     id_cabang: 2
│
└─ Result: Stock distributed across branches

STEP 4: Patient Purchase (Obat Keluar)
├─ Action: Patient buys 25 units from Cabang Bandung
├─ Input:
│  └─ Drug: Paracetamol 500mg
│  └─ Batch: BATCH-2026-PAR-001 (Cabang Bandung)
│  └─ Quantity: 25 units
│  └─ Date: 2026-04-10
├─ Database Updates:
│  └─ tb_obat_keluar INSERT:
│     id_obat_keluar: 502
│     tanggal_keluar: 2026-04-10
│     stok_keluar: 25
│     id_stok_batch: 251 (Cabang Bandung batch)
│     id_cabang: 2
│
│  └─ tb_stok_batch UPDATE (Cabang Bandung):
│     WHERE id_stok_batch = 251
│     stok_keluar: 0 → 25
│     stok_sisa: 200 → 175
│     status_stok: "Aman" → "Aman"
│
└─ Result: Inventory reduced, patient served

STEP 5: System Generates Prediction
├─ Action: Manager runs prediction model
├─ Input features extracted:
│  └─ Drug: Paracetamol 500mg
│  └─ total_keluar (12m): 487 units
│  └─ stok_saat_ini: 475 units (300 Pusat + 175 Bandung)
│  └─ frekuensi_transaksi: 18
│  └─ safety_stock: 100
├─ Model prediction:
│  └─ Next 30 days: Expected 120 units sold
│  └─ Status prediction: "Aman"
│  └─ Recommendation: "Monitor, restock within 30 days"
│
└─ Result: Decision support for manager
```

### Example 2: Inventory Verification Flow (Stok Opname)

```
Time: 2026-04-15 (Monthly verification)

STEP 1: Physical Count
├─ Action: Pharmacist counts actual stock in Pusat
├─ Finding:
│  └─ Drug: Paracetamol 500mg, Batch: BATCH-2026-PAR-001
│  └─ System shows (stok_sisa): 300 units
│  └─ Physical count: 295 units
│  └─ Difference: -5 units (shortage)
│
├─ Investigation:
│  └─ Possible cause: Damaged/spilled during handling
│  └─ Notes: "5 units found damaged during shelf arrangement"
│
└─ Record verification

STEP 2: Record Opname
├─ Input:
│  └─ Date: 2026-04-15
│  └─ Batch: BATCH-2026-PAR-001
│  └─ System stock: 300
│  └─ Physical count: 295
│  └─ Difference: -5
│  └─ Notes: "5 units damaged"
├─ Database Updates:
│  └─ tb_stok_opname INSERT:
│     id_stok_opname: 78
│     kode_opname: "OP-2026-04"
│     tanggal_opname: 2026-04-15
│     stok_sistem: 300
│     stok_fisik: 295
│     selisih: -5
│     keterangan: "5 units damaged during handling"
│     id_stok_batch: 250
│     id_cabang: 1
│     id_user: 2 (Pharmacist ID)
│     id_obat: 5
│
└─ Opname recorded

STEP 3: Correction Entry
├─ Action: Admin corrects system stock to match physical
├─ Adjustment reason: "Damaged stock adjustment"
├─ Update tb_stok_batch:
│  └─ stok_masuk: 500 → 495
│  └─ stok_keluar: 200 → 200
│  └─ stok_sisa: 300 → 295
│  └─ tujuan_penyesuaian: "Damaged stock"
│  └─ catatan: "Opname correction 2026-04-15"
│
└─ System aligned with physical reality

STEP 4: Data Impact on Prediction Model
├─ Next ML prediction will use:
│  └─ Corrected stok_sisa: 295 (not 300)
│  └─ Accuracy metric improved: MAPE reduced
│  └─ Training data quality enhanced
│
└─ Result: More accurate future predictions
```

### Example 3: Prediction Model Training & Deployment

```
Dataset Collection Phase
├─ Historical period: 2025-01-01 to 2026-04-01 (12 months+)
├─ Drugs included: All active drugs (>5 transactions/year)
├─ Records: ~5,000 transactions
├─ Data sources:
│  └─ tb_stok_batch: 2,000 batch records
│  └─ tb_obat_keluar: 2,500 sales records
│  └─ tb_stok_opname: 500 verification records
│
├─ Features generated:
│  └─ Drug ID, total_masuk, total_keluar, stok_saat_ini
│  └─ safety_stock, frekuensi_transaksi, kedaluwarsa_risk
│  └─ accuracy_deviation
│
└─ Dataset ready: 500 drugs × 8 features = 4,000 training samples

Model Training Phase (Google Colab)
├─ Data import:
│  └─ CSV file: training_data_prediksi_stok_2026-04-01.csv
│  └─ Records: 4,000 rows
│
├─ Preprocessing:
│  └─ Remove nulls: 4,000 → 3,950 (50 invalid records)
│  └─ Normalize: MinMaxScaler() on all features
│  └─ Target encoding: Aman=0, Menipis=1, Habis=2
│  └─ Train/test split: 70% train, 30% test
│
├─ Model training:
│  └─ Algorithm: DecisionTreeClassifier
│  └─ Hyperparameters: max_depth=5, min_samples_split=5
│  └─ Training samples: 2,765 records
│
├─ Model evaluation:
│  └─ Test samples: 1,185 records
│  └─ Accuracy: 87.3%
│  └─ Precision: 0.85
│  └─ Recall: 0.84
│  └─ F1-Score: 0.84
│
├─ Model export:
│  └─ Format: Pickle (.pkl)
│  └─ File: model_dt_stok_prediksi_2026-04-01.pkl
│  └─ Size: ~2.5 MB
│
└─ Result: Model ready for deployment

Flask API Deployment Phase
├─ API endpoint: http://localhost:5000/predict
├─ Flask application receives:
│  └─ POST request with JSON features
│  └─ Example: {
│       "total_keluar": 150,
│       "stok_saat_ini": 80,
│       "safety_stock": 100,
│       "frekuensi_transaksi": 15
│     }
│
├─ API processes:
│  └─ Load model from pickle file
│  └─ Normalize input features
│  └─ Predict using model.predict()
│  └─ Return class probability
│
├─ Response format:
│  └─ {
│       "status": "Menipis",
│       "confidence": 0.89,
│       "recommendation": "Restock recommended",
│       "suggested_qty": 250
│     }
│
└─ Result: API ready for Laravel integration

Laravel Integration Phase
├─ PrediksiController receives request:
│  └─ Collects drug features from database
│  └─ Sends to Flask API /predict endpoint
│  └─ Receives prediction response
│
├─ Display in Manager Dashboard:
│  └─ Table showing: Drug, Current Stock, Prediction, Recommendation
│  └─ Color coding: Green (Aman), Yellow (Menipis), Red (Habis)
│  └─ Export to Excel option
│
└─ Result: Manager can make informed decisions
```

---

## 📋 Implementation Checklist

### ✅ Database & Models (Completed)
- [x] Create all 13 tables (master data, transactions, monitoring, users)
- [x] Define foreign key relationships
- [x] Create Model classes for each table
- [x] Add relationships in Model classes
- [x] Create migrations with indexes
- [x] Seed basic master data (categories, suppliers, branches)

### ✅ Controllers (Completed)
- [x] Admin controllers (6 master data CRUDs)
- [x] Pharmacist controllers (ObatMasuk, ObatKeluar)
- [x] Shared controllers (MutasiStok, StokCabang, StokOpname)
- [x] Manager controllers (PrediksiController, LaporanController)
- [x] Implement RBAC middleware checks
- [x] Add validation rules for all inputs

### ✅ Views & UI (Completed)
- [x] Create Blade templates for all main features
- [x] Create role-specific sidebars (admin, apoteker, manager)
- [x] Create modal components for CRUD operations
- [x] Create dashboard views per role
- [x] Implement DataTables for large datasets
- [x] Add form validation feedback
- [x] Add success/error toast notifications

### ✅ Routes (Completed)
- [x] Define all RESTful routes
- [x] Define legacy routes for backward compatibility
- [x] Apply middleware for role-based access
- [x] Implement named routes for views

### ⏳ Prediction Module (In Progress)
- [ ] Create export command: `php artisan prediksi:export-data`
- [ ] Generate 12-month historical CSV
- [ ] Train Decision Tree model in Google Colab
- [ ] Save model.pkl to project
- [ ] Implement Flask API wrapper
- [ ] Integrate API calls in PrediksiController
- [ ] Display predictions in prediksi-obat view
- [ ] Add prediction caching strategy
- [ ] Document model training process
- [ ] Create model retraining schedule (monthly/quarterly)

### 📝 Documentation (This Document)
- [x] Database schema reference
- [x] Table relationships
- [x] System flow architecture
- [x] UI/menu structure mapping
- [x] Feature access matrix
- [x] Prediction features definition
- [x] Data flow examples
- [ ] API documentation (separate file)
- [ ] Deployment guide (separate file)
- [ ] Training data export guide (separate file)

---

## 🔑 Key Business Rules

### Stock Calculation Formula (CRITICAL)
```
stok_sisa = stok_masuk - stok_keluar

This formula MUST be applied:
• After receiving stock (Obat Masuk)
• After stock transfer (Mutasi Stok)
• After stock sale (Obat Keluar)
• During inventory verification (Stok Opname)
```

### Stock Status Assignment
```
IF stok_sisa >= (safety_stock × 1.20) THEN status = "Aman" (Safe)
ELSE IF stok_sisa >= (safety_stock × 0.30) THEN status = "Menipis" (Low)
ELSE status = "Habis" (Out of Stock)
```

### FEFO (First Expiry, First Out)
```
When selecting batch for transfer or sale:
1. Query all batches of drug with stok_sisa > 0
2. Order by tanggal_kedaluwarsa ASC (earliest first)
3. Select first result (oldest expiry date)
4. Use this batch for transaction
```

### Safety Stock Threshold
```
• Defined per drug (tb_obat.safety_stock column)
• Represents minimum safe level to prevent stockout
• Used for status calculation
• Used for reorder point in predictions
• Typical value: 50-100 units (depends on drug usage)
```

### Expiry Warnings
```
• Alert if within 30 days: "Mendekati Kedaluwarsa" (Near expiry)
• Alert if already passed: "Kedaluwarsa" (Expired)
• Show in Obat Kedaluwarsa section
• Prevent sale of expired drugs (system enforcement needed)
```

---

## 🎓 Quick Reference: File Locations

### Controllers
```
app/Http/Controllers/
├── Admin/
│   ├── DataObatController.php
│   ├── DataCabangController.php
│   ├── DataKategoriController.php
│   ├── DataSatuanController.php
│   ├── DataSupplierController.php
│   ├── KelolaAkunController.php
│   └── MutasiStokController.php
├── Apoteker/
│   ├── ObatMasukController.php
│   └── ObatKeluarController.php
├── Manager/
│   ├── PrediksiController.php
│   └── LaporanController.php
├── Shared/
│   ├── AuthController.php
│   ├── StokCabangController.php
│   ├── StokBatchKedaluwarsaController.php
│   └── StokOpnameController.php
└── Controller.php
```

### Models
```
app/Models/Admin/
├── Obat.php
├── DataCabang.php
├── DataKategori.php
├── DataSatuan.php
├── DataSupplier.php
├── ObatMasuk.php
├── ObatKeluar.php
├── StokBatch.php
├── StokOpname.php
├── MutasiStok.php
├── KelolaAkun.php
├── Laporan.php
└── StokBatchKedaluwarsa.php
```

### Views
```
resources/views/
├── admin/
│   ├── dashboard.blade.php
│   ├── data-obat.blade.php
│   ├── data-cabang.blade.php
│   ├── data-kategori.blade.php
│   ├── data-satuan.blade.php
│   ├── data-supplier.blade.php
│   ├── kelola-akun.blade.php
│   └── modals/
│       ├── modal-obat.blade.php
│       ├── modal-cabang.blade.php
│       └── ...
├── apoteker/
│   ├── dashboard.blade.php
│   ├── obat-masuk.blade.php
│   ├── obat-keluar.blade.php
│   └── modals/
├── manager/
│   ├── dashboard.blade.php
│   ├── prediksi-obat.blade.php
│   ├── laporan.blade.php
│   ├── obat-kedaluwarsa.blade.php
│   └── modals/
├── shared/
│   ├── mutasi-stok.blade.php
│   ├── stok-cabang.blade.php
│   ├── stok-batch-kedaluwarsa.blade.php
│   ├── stok-opname.blade.php
│   └── modals/
├── components/
│   └── layouts/
│       ├── app.blade.php
│       ├── sidebar-admin.blade.php
│       ├── sidebar-apoteker.blade.php
│       ├── sidebar-manajer.blade.php
│       ├── navbar.blade.php
│       └── ...
├── auth/
│   └── login.blade.php
└── errors/
    ├── 403.blade.php
    ├── 404.blade.php
    └── 500.blade.php
```

### Migrations
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 2025_02_12_050304_create_data_cabangs_table.php
├── 2025_02_12_051837_create_data_satuans_table.php
├── 2025_12_11_091427_create_data_suppliers_table.php
├── 2025_12_11_091428_create_tb_data_kategori_table.php
├── 2025_12_11_091429_create_tb_obat_table.php
├── 2025_12_11_095117_create_tb_obat_masuk_table.php
├── 2026_02_12_072735_table_tb_stok_batch.php
├── 2026_02_12_080152_table_tb_mutasi_stok.php
├── 2026_02_12_080658_table_tb_stok_opname.php
├── 2026_03_02_060347_create_tb_obat_keluar.php
├── 2026_03_11_045510_add_role_to_users_table.php
└── 2026_03_12_add_performance_indexes.php
```

---

## 📞 Support & Notes

**Database Consistency**
- Always verify `stok_sisa = stok_masuk - stok_keluar` after any transaction
- Use transactions for multi-step operations
- Clear cache after data modifications

**Performance Optimization**
- Use eager loading (with()) to prevent N+1 queries
- Implement caching for read-heavy operations
- Add database indexes on frequently queried columns

**Security**
- CSRF protection on all forms
- Role-based middleware on protected routes
- Input validation and sanitization
- SQL injection prevention via ORM

**Future Enhancements**
- Mobile app for field pharmacists
- SMS/Email notifications for low stock
- Integration with supplier ordering system
- Advanced ML models (ARIMA, Prophet) for seasonal forecasting
- Real-time dashboard with WebSocket updates
- Barcode scanning for faster data entry

---

**Document Prepared By:** Development Team  
**For:** Skripsi - Pharmacy Management System with ML-Based Stock Prediction  
**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** April 27, 2026
