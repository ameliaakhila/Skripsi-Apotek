{{-- ? Admin Sidebar Menu --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            {{-- Dashboard --}}
            <x-link.nav-link route="dashboard" icon="fas fa-home" label="Dashboard" />

            {{-- Master Data (Admin Only) --}}
            <x-link.nav-group icon="fas fa-database" label="Master Data">
                <x-link.nav-link route="data-obat" label="Data Obat" />
                <x-link.nav-link route="data-satuan" label="Data Satuan" />
                <x-link.nav-link route="data-kategori" label="Data Kategori" />
                <x-link.nav-link route="data-supplier" label="Data Supplier" />
                <x-link.nav-link route="data-cabang" label="Data Cabang" />
            </x-link.nav-group>

            {{-- Transaksi Stok --}}
            <x-link.nav-group icon="fas fa-exchange-alt" label="Transaksi Stok">
                <x-link.nav-link route="obat-masuk" label="Obat Masuk" />
                <x-link.nav-link route="mutasi-stok" label="Mutasi Stok" />
                <x-link.nav-link route="obat-keluar" label="Obat Keluar" />
            </x-link.nav-group>

            {{-- Monitoring Stok --}}
            <x-link.nav-group icon="fas fa-clipboard-list" label="Monitoring Stok">
                <x-link.nav-link route="stok-cabang" label="Stok Cabang" />
                <x-link.nav-link route="stok-batch-kedaluwarsa" label="Batch & Kedaluwarsa" />
                <x-link.nav-link route="stok-opname" label="Stok Opname" />
            </x-link.nav-group>

            {{-- Prediksi & Laporan --}}
            <x-link.nav-link route="prediksi" icon="fas fa-chart-line" label="Prediksi Obat" />

            <x-link.nav-link  icon="fas fa-file-medical"  route="laporan" label="Laporan Full" />

            {{-- Kelola Akun --}}
            <x-link.nav-link route="kelola-akun" icon="fas fa-users" label="Kelola Akun" />
        </ul>
    </div>
</div>
{{-- ? Admin Sidebar Menu End --}}
