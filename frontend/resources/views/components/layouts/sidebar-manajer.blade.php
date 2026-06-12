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
            </x-link.nav-group>

            {{-- Analisis & Prediksi --}}
            <x-link.nav-link route="prediksi" icon="fas fa-chart-line" label="Prediksi Obat" />

            {{-- Laporan --}}
            <x-link.nav-link icon="fas fa-file-medical" route="laporan" label="Laporan Full" />
        </ul>
    </div>
</div>
{{-- ? Manager Sidebar Menu End --}}
