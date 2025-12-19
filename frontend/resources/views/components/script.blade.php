{{-- ! JAVA SCRIPT LINK --}}
<!-- Required vendors -->
<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>

<!-- Apex Chart -->
{{--
<script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script> --}}


<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}"></script>

<!-- Dashboard 1 -->
{{--
<script src="{{ asset('assets/js/dashboard/dashboard-1.js') }}"></script> --}}

<script src="{{ asset('assets/vendor/owl-carousel/owl.carousel.js') }}"></script>

<script src="{{ asset('assets/js/custom.min.js') }}"></script>
<script src="{{ asset('assets/js/dlabnav-init.js') }}"></script>
<script src="{{ asset('assets/js/chart-bulanan.js') }}"></script>


<!-- data table -->
<script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>

<!-- Modal -->
<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>

<!-- awal date pikcer -->
<!-- Daterangepicker -->
<!-- momment js is must -->
<script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- pickdate -->
<script src="{{ asset('assets/vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('assets/vendor/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('assets/vendor/pickadate/picker.date.js') }}"></script>

<!-- Pickdate -->
<script src="{{ asset('assets/js/plugins-init/pickadate-init.js') }}"></script>

{{-- select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#datepicker2').pickadate({
            format: 'dd mmmm yyyy'
        });
    });
</script>

<!-- akhir datepicker -->

<!-- select js awal -->
{{--
<script>
    $(document).ready(function () {
        //change selectboxes to selectize mode to be searchable
        $("select").select2();
    });
</script> --}}
<!-- select js akhir -->

{{-- Ajax simpan penjualan obat --}}
<script>
    $(document).ready(function () {
        // menjadikan text placeholder
        $('.js-example-data-array').select2({
            placeholder: "Masukan Nama Obat | Kode Obat",
            allowClear: true
        });

        // Setup CSRF token untuk semua AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // Menambahkan event listener 'change' pada elemen dengan id 'id_obat_masuk'
        $('#id_obat_masuk').on('change', function () {

            // Mengambil nilai (value) dari select/input yang berubah
            let id_obat_masuk = $(this).val();

            // Melakukan request AJAX ke server
            $.ajax({
                // URL tujuan request (route Laravel)
                url: "{{ url('/penjualan-obat-tambah') }}",

                // Tipe request HTTP yang digunakan
                type: "POST",

                // Data yang dikirim ke server
                data: {
                    // Mengirim id_obat_masuk ke controller
                    id_obat_masuk: $(this).val()
                },

                // Fungsi yang dijalankan jika request berhasil
                success: function (res) {
                    // Menampilkan response dari server ke console browser
                    console.log(res);
                },

                // Fungsi yang dijalankan jika request gagal / error
                error: function (xhr) {
                    // Menampilkan pesan error dari server
                    console.error(xhr.responseText);
                }
            });
        });

        // Fungsi untuk menambahkan opsi obat ke dalam <select>
        function appendOpsiObat(data) {

            // Mengambil elemen <select> dengan id "id_obat"
            let select = $('#id_obat');

            // Membuat elemen <option> baru (placeholder)
            let option = `
            <option value="">
                Masukan Nama obat | kode obat
            </option>
        `;

            // Menambahkan option ke dalam <select>
            select.append(option);

            // Mengatur ulang nilai select ke kosong
            // dan memicu event 'change' (berguna jika pakai Select2)
            select.val(null).trigger('change');
        }

    });
</script>
{{-- Ajax simpan penjualan obat --}}

<script>
    function cardsCenter() {

        /*  testimonial one function by = owl.carousel.js */

        jQuery('.card-slider').owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            //center:true,
            slideSpeed: 3000,
            paginationSpeed: 3000,
            dots: false,
            navText: ['', ''],
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                800: {
                    items: 2
                },
                991: {
                    items: 2
                },
                1200: {
                    items: 3
                },
                1600: {
                    items: 4
                }
            }
        })
    }

    jQuery(window).on('load', function () {
        setTimeout(function () {
            cardsCenter();
        }, 1000);
    });

</script>

{{-- ! JAVA SCRIPT LINK END --}}
