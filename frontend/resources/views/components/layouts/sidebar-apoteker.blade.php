{{-- ? Apoteker (Apoteker) Sidebar Menu --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            {{-- Dashboard --}}
            <x-link.nav-link route="dashboard" icon="fas fa-home" label="Dashboard" />

            {{-- Transaksi Stok (Apoteker Primary) --}}
            <x-link.nav-group icon="fas fa-exchange-alt" label="Transaksi Stok">
                <x-link.nav-link route="obat-masuk" label="Obat Masuk" />
                <x-link.nav-link route="obat-keluar" label="Obat Keluar" />
                <x-link.nav-link route="mutasi-stok" label="Mutasi Stok" />
            </x-link.nav-group>

            {{-- Monitoring Stok --}}
            <x-link.nav-group icon="fas fa-clipboard-list" label="Monitoring Stok">
                <x-link.nav-link route="stok-cabang" label="Stok Cabang" />
                <x-link.nav-link route="stok-batch-kedaluwarsa" label="Batch & Kedaluwarsa" />
                <x-link.nav-link route="stok-opname" label="Stok Opname" />
            </x-link.nav-group>
        </ul>
    </div>
</div>
{{-- ? Apoteker Sidebar Menu End --}}
