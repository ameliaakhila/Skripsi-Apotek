{{-- ! JAVA SCRIPT LINK - Performance Optimized --}}

{{-- ! Critical JS (must load synchronously) --}}
<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>

{{-- ! Non-critical JS (deferred for better performance) --}}

<!-- Chart library -->
<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}" defer></script>

<!-- Select2 and related -->
<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}" defer></script>
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js')}}" defer></script>
<script src="{{ asset('assets/js/plugins-init/select2-init.js')}}" defer></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}" defer></script>

<!-- Carousel -->
<script src="{{ asset('assets/vendor/owl-carousel/owl.carousel.js') }}" defer></script>

<!-- Custom scripts -->
<script src="{{ asset('assets/js/custom.min.js') }}" defer></script>
<script src="{{ asset('assets/js/dlabnav-init.js') }}" defer></script>
<script src="{{ asset('assets/js/chart-bulanan.js') }}" defer></script>

<!-- DataTable -->
<script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}" defer></script>
<script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}" defer></script>

<!-- Date/Time Pickers -->
<script src="{{ asset('assets/vendor/moment/moment.min.js') }}" defer></script>
<script src="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.js') }}" defer></script>
<script src="{{ asset('assets/vendor/pickadate/picker.js') }}" defer></script>
<script src="{{ asset('assets/vendor/pickadate/picker.time.js') }}" defer></script>
<script src="{{ asset('assets/vendor/pickadate/picker.date.js') }}" defer></script>
<script src="{{ asset('assets/js/plugins-init/pickadate-init.js') }}" defer></script>


<script defer>
    $(document).ready(function () {
        $('#datepicker2').pickadate({
            format: 'dd mmmm yyyy'
        });
    });
</script>

<!-- select js awal -->
<script defer>
    // $(document).ready(function () {
    //     //change selectboxes to selectize mode to be searchable
    //     $("select").select2();
    // });
</script>
<!-- select js akhir -->

<script defer>
    document.addEventListener('submit', function (e) {
        if (e.target.classList.contains('delete-confirm')) {
            e.preventDefault();

            const text = e.target.getAttribute('data-text');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });
</script>

{{-- Sweet alert Script Start --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<script defer>
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-delete');
        if (!button) return;

        e.preventDefault();

        const id = button.dataset.id;
        const nama = button.dataset.nama || '';
        const entity = button.dataset.entity || 'data';

        Swal.fire({
            title: `Yakin hapus ${entity} ini?`,
            html: `<strong>${nama}</strong> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        });
    });

</script>
{{-- Sweet alert Script End --}}

{{-- Ajax simpan penjualan obat --}}
<script defer>
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

<script defer>
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

{{-- ! SCRIPT MODAL START --}}
<script defer>
    document.addEventListener('DOMContentLoaded', function () {

        function ensureModalOnBody(modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        }

        function tagLatestBackdrop() {
            document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                backdrop.classList.remove('modal-dynamic-backdrop');
            });

            var backdrops = document.querySelectorAll('.modal-backdrop');
            var latestBackdrop = backdrops[backdrops.length - 1];

            if (latestBackdrop) {
                latestBackdrop.classList.add('modal-dynamic-backdrop');
            }
        }

        function syncBodyModalState() {
            var hasOpenModal = document.querySelector('.modal-dynamic.show') !== null;
            document.body.classList.toggle('modal-active', hasOpenModal);
        }

        document.querySelectorAll('.modal-dynamic').forEach(function (modal) {

            modal.addEventListener('show.bs.modal', function (event) {

                ensureModalOnBody(modal);
                window.setTimeout(tagLatestBackdrop, 0);
                syncBodyModalState();

                var button = event.relatedTarget;
                if (!button) return;

                // isi semua input
                modal.querySelectorAll('[data-field]').forEach(function (element) {

                    let field = element.dataset.field;
                    let value = button.dataset[field] ?? '';

                    // Kalau input atau select
                    if (element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                        element.value = value;
                    }
                    // Kalau span / div / td (detail mode)
                    else {
                        // Special handling untuk status_stok dengan badge
                        if (field === 'status_stok') {
                            let badgeClass = '';
                            if (value === 'Aman') {
                                badgeClass = 'bg-success';
                            } else if (value === 'Menipis') {
                                badgeClass = 'bg-warning text-dark';
                            } else if (value === 'Habis') {
                                badgeClass = 'bg-danger';
                            }
                            element.innerHTML = `<span class="text-white badge ${badgeClass}">${value}</span>`;
                        } else {
                            element.textContent = value;
                        }
                    }

                });

                // Set form action (khusus edit)
                let form = modal.querySelector('form');
                if (form && button.dataset.action) {
                    form.action = button.dataset.action;
                }

            });

            modal.addEventListener('shown.bs.modal', function () {

                $(modal).find('select[id^="single-select-"]').each(function () {
                    // Prevent double initialization - only initialize if not already a Select2 instance
                    if (!$(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2({
                            dropdownParent: $(modal),
                            width: '100%'
                        });
                    }
                });

                tagLatestBackdrop();
                syncBodyModalState();

            });

            modal.addEventListener('hidden.bs.modal', function () {
                tagLatestBackdrop();
                syncBodyModalState();

            });

        });

    });
</script>
{{-- ! SCRIPT MODAL END --}}
