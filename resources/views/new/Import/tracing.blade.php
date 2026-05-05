<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>.:{{ config('app.name') }} - Monitoring Cargo Import:.</title>
    <meta content="" name="descriptison">
    <meta content="" name="keywords">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ asset('images/favicon.png') }}" rel="icon">
    <link href="{{ asset('images/favicon.png') }}" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/icofont/icofont.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/venobox/venobox.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-datepicker/css/datepicker3.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css') }}" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
        crossorigin="anonymous" />

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-smartwizard/css/smart_wizard.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-smartwizard/css/smart_wizard_arrows.min.css') }}" />

    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
    <style type="text/css">
        .hide {
            display: none;
        }

        .hiddenRow {
            padding: 0 !important;
        }
    </style>

    <!-- Page Specific JS File -->
    @stack('styles')
</head>

<body oncontextmenu = "return false" oncopy="return false" oncut="return false">
    <header id="header">
        <div class="container d-flex">
            <div class="logo mr-auto">
                <a href="#"><img src="{{ asset('images/logos.png') }}" alt="" class="img-fluid"></a>
            </div>
        </div>
    </header>

    <main id="main">
        <section id="hero">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active" style="background-image: url('{{ asset('images/MKT1.jpg') }}');">
                        <div class="carousel-container text-center"
                            style="justify-content: end; width: 45%; margin-left: 15%;">
                            <div class="carousel-content">
                                <h4>
                                    <p class="text-center">Monitoring Cargo Import</p>
                                </h4>
                                <form action="" method="post" id="form-search">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="housebl" id="houseBL"
                                            aria-describedby="helpId" placeholder="House BL..." autocomplete="off"
                                            autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="container_no" id="containerNo"
                                            aria-describedby="helpId" placeholder="Container Number.."
                                            autocomplete="off">
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-block text-white btnSearch"
                                                style="background-color: #F03C02"><i
                                                    class="fas fa-search text-white mr-2"></i>
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
            </div>
        </section>
    </main>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-result" data-backdrop="false" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Images</th>
                                        <th>Principal</th>
                                        <th>Consignee</th>
                                        <th>Stripping Date</th>
                                        <th>Master BL</th>
                                        <th>House BL</th>
                                        <th>Container No</th>
                                        <th>ETA</th>
                                        <th>QTY</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="appendTable">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fas fa-window-close"></i>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

    <!-- Vendor JS Files -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    <script>
        $(function() {
            $('#modal-result').modal({
                show: false,
                keyboard: false,
                backdrop: 'static'
            });
        });
        $('#modal-result').on('hidden.bs.modal', function() {
            $('#houseBL').val("").focus();
        })

        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            $('.btnSearch').attr('disabled', true);
            $('#appendTable').html("");
            var housebl = $('#houseBL').val();
            var container = $('#containerNo').val();
            if (housebl == "" && container == "") {
                swal({
                    title: "House BL or Container is required",
                    icon: "warning",
                    dangerMode: true,
                }).then((result) => {
                    $("#houseBL").val("").focus();
                });
                $('.btnSearch').attr('disabled', false);
            } else {
                $.ajax({
                    url: "{{ url('foto-management/postTracing') }}",
                    data: $('#form-search').serialize(),
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        $('.btnSearch').attr('disabled', false);
                        if (response.data == 'required') {
                            swal({
                                title: "House BL is required",
                                icon: "warning",
                                dangerMode: true,
                            }).then((result) => {
                                $("#houseBL").val("").focus();
                            });
                        } else if (response.data == 'null') {
                            swal({
                                title: "Data not found",
                                icon: "warning",
                                dangerMode: true,
                            }).then((result) => {
                                $("#houseBL").val("").focus();
                            });
                        } else {
                            $('#modal-result').modal('show');
                            appendTable(response.data, response.foto);
                        }
                    },
                    error: function(error) {
                        $("#houseBL").val("").focus();
                        $('.btnSearch').attr('disabled', false);
                        console.log('error');
                    }
                });
            }
        });

        function appendTable(params, images) {
            $.each(params, function(key, val) {
                $('#appendTable').append(`
                <tr>
                    <td>
                        <a href="#" onclick="downloadFoto('${val.token}')" class="btn btn-info btn-md mt-1"><i class="fas fa-download"></i>
                        </a>
                    </td>
                    <td>${val.principal}</td>
                    <td>${val.consignee}</td>
                    <td>${val.confirmed_at}</td>
                    <td>${val.masterbl}</td>
                    <td>${val.housebl}</td>
                    <td>${val.container}</td>
                    <td>${val.eta}</td>
                    <td>${val.qty} ${val.package}</td>
                    <td>${val.remarks}</td>
                </tr>
                <tr>
                    <td colspan="12" class="hiddenRow">
                        <div class="accordian-body collapse m-4" id="demo_${key+1}">
                            <div class="appendImage">
                            </div>
                        </div>
                    </td>
                </tr>
                    `);
            });


        }

        function previewFoto(token) {
            $.ajax({
                url: "{{ url('foto-management/previewFoto') }}/" + token,
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    $('.appendImage').html("");
                    $.each(response.data, function(key, value) {
                        $('.appendImage').append(
                            `<img class="activator m-3" src="{{ url('foto/warehouse-import/foto-management/${value}') }}" style="width: 200px;height: 200px; border-radius: 20px;">`
                        );
                    });
                },
                error: function(error) {
                    console.log('error');
                }
            });
        }

        function downloadFoto(token) {
            let text = token;
            let result = token.replace(/\//g, '-');
            location.href = "{{ url('foto-management/downloadFoto/') }}/" + result;
        }
    </script>
</body>

</html>
