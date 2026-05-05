<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <!-- <base href="{{ url('/') }}"> -->
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/new/css/fonts.css') }}" />
    <link href="{{ asset('/assets/new/plugins/global/plugins.bundle.css?v=7.0.5') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/new/css/style.bundle.css?v=7.0.5') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/new/css/themes/layout/header/base/light.css?v=7.0.5') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('/assets/new/css/themes/layout/header/menu/light.css?v=7.0.5') }}" rel="stylesheet"
        type="text/css" />
    <link rel="shortcut icon" href="{{ url('/') }}/images/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style type="text/css">
        .hide {
            display: none;
        }

        @media print {
            .hide-print {
                display: none !important;
            }

            .print-area {
                display: block;
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        .loading-overlay {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.is-active {
            display: flex;
        }

        .code {
            font-family: monospace;
            color: #dd4a68;
            background-color: rgb(238, 238, 238);
            padding: 0 3px;
        }

        * {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 18px;
            background-color: #ffffff;
        }

        #clock-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            margin-bottom: 20px;
        }

        #clock {
            font-family: 'Orbitron', sans-serif;
            font-size: 36px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            padding: 12px 28px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
            letter-spacing: 2.5px;
            transition: all 0.3s ease-in-out;
        }

        #clock:hover {
            transform: scale(1.08);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.35);
        }

        .badge-status {
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .badge-done {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/new/css/loading.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body id="kt_body" class="page-loading">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="loading-overlay justify-content-center">
                            <div class="drawing">
                                <div class="loading-dot"></div>
                            </div>
                        </div>
                        <div class="container-fluid" style="zoom: 110%;">
                            <div class="main-body">
                                <div class="card" style="border-radius: 15px;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-center w-100">
                                            <div id="clock-wrapper">
                                                <div id="clock"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-5 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="my-addon">Tanggal
                                                            Mulai</span>
                                                    </div>
                                                    <input class="form-control tglMulai" type="date" name=""
                                                        placeholder="Recipient's text" aria-label="Recipient's "
                                                        aria-describedby="my-addon" value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-5 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="my-addon">Tanggal
                                                            Selesai</span>
                                                    </div>
                                                    <input class="form-control tglSelesai" type="date" name=""
                                                        placeholder="Recipient's text" aria-label="Recipient's "
                                                        aria-describedby="my-addon" value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <a href="javascript:void(0)" onclick="cariData()"
                                                    class="btn btn-block btn-dark btn-search">
                                                    <i class="fas fa-search"></i> Cari
                                                </a>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mt-4" id="listTable">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>NO</th>
                                                                <th>NAMA SUPIR</th>
                                                                <th>NO POLISI</th>
                                                                <th>JENIS MOBIL</th>
                                                                <th>TRANSPORTER</th>
                                                                <th>SHIPPER</th>
                                                                <th>JAM MASUK</th>
                                                                <th>JAM KELUAR</th>
                                                                <th>JUMLAH BARANG</th>
                                                                <th>ID VISITOR</th>
                                                                <th>STATUS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/new/plugins/global/plugins.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/js/scripts.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        cariData();

        // Trigger ulang setiap 30 menit
        setInterval(() => {
            console.log("Auto-refresh triggered");
            cariData();
        }, 30 * 60 * 1000);


        function kasihNol(data) {
            if (data < 10) {
                return '0' + data;
            } else {
                return data;
            }
        }

        function kasihNol(i) {
            return (i < 10) ? "0" + i : i;
        }

        function updateClock() {
            const now = new Date();
            const hours = kasihNol(now.getHours());
            const minutes = kasihNol(now.getMinutes());
            const seconds = kasihNol(now.getSeconds());

            document.getElementById('clock').innerHTML = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateClock, 1000); // update setiap detik
        updateClock(); // panggil pertama kali


        $(".datepicker-year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        $(document).ajaxSend(function(event, jqxhr, settings) {
            // Start pace
            $('.loading-overlay').addClass('d-flex');
            // $('#clock-loader').fadeIn('normal');
        });

        $(document).ajaxComplete(function(event, jqxhr, settings) {
            // Stop pace
            $('.loading-overlay').removeClass('d-flex');
        });

        $(document).ajaxError(function(event, jqxhr, settings) {
            // Stop pace
            $('.loading-overlay').removeClass('d-flex');
        });


        function cariData() {
            var tgl_mulai = $('.tglMulai').val();
            var tgl_selesai = $('.tglSelesai').val();
            const branchSegment = window.location.pathname.split('/')[3] || ''; // pastikan ada fallback
            const baseUrl = "{{ url('getListGateIn') }}/" + branchSegment;
            $('.btn-search').hide();
            const url = baseUrl + "/" + tgl_mulai + "/" + tgl_selesai;

            // destroy table sebelumnya jika ada
            if ($.fn.DataTable.isDataTable('#listTable')) {
                $('#listTable').DataTable().clear().destroy();
            }

            $('#listTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: url,
                    dataSrc: function(json) {
                        // jika backend mengembalikan { data: [...] }
                        if (!json) return [];
                        if (json.data) return json.data;
                        // fallback: kalau backend return array langsung
                        return Array.isArray(json) ? json : [];
                    },
                    error: function(xhr, status, error) {
                        console.error('DataTables AJAX error:', status, error);
                        $('.btn-search').show();
                        // beri informasi ke user
                        toastr?.error?.('Gagal memuat data. Cek console untuk detail.');
                    }
                },
                columns: [{
                        data: null,
                        name: 'number',
                        sortable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'driver_name',
                        name: 'driver_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'vehicle_number',
                        name: 'vehicle_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'vehicle_type',
                        name: 'vehicle_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'transporter_name',
                        name: 'transporter_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'shipper_name',
                        name: 'shipper_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        defaultContent: '-'
                    },
                    {
                        data: 'jam_keluar',
                        name: 'jam_keluar',
                        defaultContent: '-'
                    }, // waktu keluar / '-' jika belum
                    {
                        data: 'qty_total',
                        name: 'qty_total',
                        defaultContent: '0'
                    },
                    {
                        data: 'id_visitor',
                        name: 'id_visitor',
                        defaultContent: '-'
                    },
                    {
                        data: 'confirmed_flag', // gunakan field confirmed_flag untuk status
                        name: 'confirmed_flag',
                        render: function(data, type, row) {
                            // beberapa backend mengirim 1/0, true/false atau 'Y'/'N' — tangani fleksibel
                            const val = String(data).toLowerCase();
                            if (val === '1' || val === 'true' || val === 'y' || val === 'yes') {
                                return `<span class="badge-status badge-done"> Done</span>`;
                            }
                            // jika jam_keluar ada, anggap done juga
                            if (row.jam_keluar && row.jam_keluar !== '-') {
                                return `<span class="badge-status badge-done"> Done</span>`;
                            }
                            return `<span class="badge-status badge-pending"> Pending</span>`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                bDestroy: true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    },
                    'copy', 'excel'
                ],
                initComplete: function() {
                    $('.btn-search').show();
                }
            });
        }
    </script>
</body>
<!--end::Body-->

</html>
