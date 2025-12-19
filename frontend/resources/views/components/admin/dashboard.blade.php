<x-layout :title="'Dashboard - Apotek Sena Farma'">
    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="row">
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
    {{-- Kirim data chart dari route ke JS global --}}
    <script>
        window.chartData = @json($chartData);
    </script>
</x-layout>
