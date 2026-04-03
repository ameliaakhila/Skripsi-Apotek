{{-- ? Manager (Manajer) Sidebar Menu --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            {{-- Dashboard --}}
            <x-link.nav-link route="dashboard" icon="fas fa-home" label="Dashboard" />

            {{-- Monitoring Stok --}}
            <x-link.nav-group icon="fas fa-clipboard-list" label="Monitoring Stok">
                <x-link.nav-link route="stok-cabang.index" label="Stok Cabang" />
                <x-link.nav-link route="stok-batch-kedaluwarsa.index" label="Batch & Kedaluwarsa" />
                <x-link.nav-link route="stok-opname.index" label="Stok Opname" />
            </x-link.nav-group>

            {{-- Analisis & Prediksi --}}
            <x-link.nav-group icon="fas fa-chart-line" label="Analisis & Prediksi">
                <x-link.nav-link route="prediksi.index" label="Prediksi Stok Obat" />
                <x-link.nav-link route="obat-kedaluwarsa.index" label="Peringatan Kedaluwarsa" />
            </x-link.nav-group>

            {{-- Laporan --}}
            <x-link.nav-group icon="fas fa-file-medical" label="Laporan">
                <x-link.nav-link route="laporan.index" label="Laporan Komprehensif" />
                <x-link.nav-link route="laporan-obat.index" label="Detail Laporan Obat" />
                <x-link.nav-link route="laporan-penjualan.index" label="Laporan Penjualan" />
                <x-link.nav-link route="laporan-prediksi.index" label="Laporan Prediksi" />
            </x-link.nav-group>
        </ul>
    </div>
</div>
{{-- ? Manager Sidebar Menu End --}}
