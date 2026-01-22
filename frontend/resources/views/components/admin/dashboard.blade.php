<x-layout title="Dashboard">
    {{-- @include('components.admin.dashboard') --}}
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">
                <button class="btn btn-danger mb-3 d-flex align-item-center justify-content-center"
                    onclick="window.location.href='{{ url('obat-kadaluarsa') }}'">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <strong class="mt-1 fs-4">Obat Kedaluwarsa!</strong>
                </button>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Prediksi Obat</h4>
                        </div>
                        <div class="card-body m-xl-5">
                            <canvas id="barChart_1" style="max-heightt: 100vh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--
    <script>
        function konfirmasiPemusnahan(batch) {
            Swal.fire({
                title: 'Pemusnahan Obat',
                html: `
                <p><strong>Paracetamol (batch ${batch})</strong></p>
                <p><strong>Ibuprofen (batch ${batch})</strong></p>
                <p><strong>Cetirizine (batch ${batch})</strong></p>
                <p>telah kedaluwarsa.</p>
                <p>Apakah Anda yakin ingin <strong>memusnahkan</strong> obat ini?</p>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Musnahkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: arahkan ke route / submit form
                    Swal.fire(
                        'Berhasil!',
                        'Obat telah dimusnahkan.',
                        'success'
                    );
                }
            });
        }
    </script> --}}
    {{-- Kirim data chart dari route ke JS global --}}
    <script>
        window.chartData = @json($chartData);
    </script>
</x-layout>