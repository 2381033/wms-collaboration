<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <!-- <base href="{{ url('/') }}"> -->
    <meta charset="utf-8" />
    <title>MKT - Faktur Pajak</title>
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
        ::-webkit-scrollbar {
            -webkit-appearance: none;
        }

        ::-webkit-scrollbar:vertical {
            width: 12px;
        }

        ::-webkit-scrollbar:horizontal {
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, .2);
            border-radius: 10px;
            border: 2px solid #ffffff;
        }

        ::-webkit-scrollbar-track {
            border-radius: 10px;
            background-color: #ffffff;
        }

        .hide {
            display: none;
        }

        .table-thin tr td,
        .table-thin tr th {
            padding: 5px !important
        }

        .print-area {
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
            background-color: #fff;
        }

        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            box-shadow: 2px 2px 3px #999;
        }

        .my-float {
            margin-top: 22px;
        }

        @media (max-width:576px) {
            .container h1 {
                font-size: 2rem;
            }

        }

        /* Full-screen overlay with a semi-transparent background */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black background */
            display: flex;
            /* Use flexbox to center the spinner */
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
            display: none;
            /* Hidden by default */
            z-index: 9999;
            /* Ensures overlay is on top */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/new/css/loading.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-mobile-fixed page-loading">
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed hide-print"
        style="right: 50px">
        <a href="{{ url('/') }}">
            <img alt="Logo" src="{{ url('/') }}/images/logos.png" style="width: 140px;" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                <span></span>
            </button>
        </div>
    </div>

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
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="card card-custom card-stretch gutter-b" style="border-radius: 20px;">
                                        <div class="card-body d-flex p-0">
                                            <div class="flex-grow-1 p-8 card-rounded bgi-no-repeat d-flex align-items-center"
                                                style="background-color: #C9F7F5; background-position: left bottom; background-size: auto 90%; background-image: url('{{ asset('assets/new/media/icons/pdf.png') }} ')">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                    </div>
                                                    <div class="col-sm-7">
                                                        <h4 class="text-danger font-weight-bolder">Upload PDF ZIP</h4>
                                                        <p class="text-dark-50 my-5 font-size-xl font-weight-bold">
                                                            Download file pdf coretax, kumpulkan pdf menjadi zip dan
                                                            pastikan file yang akan di upload berformat ZIP
                                                        </p>
                                                        <a href="#"
                                                            class="btn btn-danger font-weight-bold py-2 px-6"
                                                            id="kt_demo_panel_toggle">Upload ZIP
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="table_list">
                                            <thead>
                                                <tr class="text-center">
                                                    <th colspan="5">{{ Str::Upper(date('F')) }} PDF FILE</th>
                                                </tr>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>File</th>
                                                    <th>NPWP</th>
                                                    <th>Faktur Pajak</th>
                                                    <th>Referensi</th>
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
                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer" style="background-color: #f0eee4">
                    <div
                        class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-dark font-weight-bold mr-2"><span>
                                    2022 &copy;</span></strong>. All Rights Reserved</span>
                        </div>
                        <div class="nav nav-dark">
                            <a class="btn btn-block btn-outline-danger logout"><i class="fas fa-power-off"></i>
                                logout</a>
                        </div>
                        <div class="nav nav-dark">
                            <span class="nav-link text-dark-75">PT Masaji Kargosentra Tama</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="kt_demo_panel" class="offcanvas offcanvas-right p-10">
        <div id="overlay">
        </div>
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7" kt-hidden-height="47"
            style="">
            <h4 class="font-weight-bold m-0">
                Upload File ZIP
            </h4>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_demo_panel_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="offcanvas-content">
            <div class="offcanvas-wrapper mb-5 scroll-pull scroll" style="height: auto; overflow: hidden;">
                <form action="{{ url('tax/uploadzip') }}" method="POST" method="post"
                    enctype="multipart/form-data" id="upload">
                    @csrf
                    <div class="form-group">
                        <label for=""></label>
                        <input type="file" name="file" id="" class="form-control" placeholder=""
                            aria-describedby="helpId" required accept=".zip">
                    </div>
                    <button type="submit" class="btn btn-primary btn-md btn-upload"> Upload</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="{{ asset('assets/new/plugins/global/plugins.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5') }}"></script>
    <script src="{{ asset('assets/new/js/scripts.bundle.js?v=7.0.5') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        load_data();

        function load_data() {
            $('#table_list').DataTable().destroy();
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('tax/getList') }}",
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data, type, row) {
                            var filename = row.file;
                            var url = `{{ asset('public/tax/pdf/') }}/${filename}`;
                            return `<a href="${url}" target="_blank" class="btn btn-dark btn-sm"> Download</a>`;
                        }
                    },
                    {
                        data: 'npwp',
                        name: 'npwp'
                    },
                    {
                        data: 'fp',
                        name: 'fp'
                    },
                    {
                        data: 'kw',
                        name: 'kw'
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: '_all', // This applies to all columns
                    className: 'text-center' // Applying the class 'text-center' for centering text
                }]
            });
        }

        function Checktoken() {
            if (!sessionStorage.getItem('token')) {
                window.history.back();
            }
        }
        $(document).ready(function() {
            if (!sessionStorage.getItem('token')) {
                window.history.back();
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ajaxSend(function(event, jqxhr, settings) {
            $('#overlay').fadeIn(); // Show the overlay
        });

        $(document).ajaxComplete(function(event, jqxhr, settings) {
            $('#overlay').fadeOut(); // Hide the overlay
        });

        $(document).ajaxError(function(event, jqxhr, settings) {
            $('#overlay').fadeOut(); // Hide the overlay
            $('.loading-overlay').removeClass('d-flex');
        });
        $(".logout").click(function() {
            location.href = "{{ url('tax/home') }}"
            sessionStorage.removeItem('token');
        });

        $("#upload").on('submit', function() {
            $('.btn-upload').attr('disabled', true);
        });
    </script>
    @stack('scripts')
</body>
<!--end::Body-->

</html>
