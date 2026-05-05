<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>.:{{ config('app.name') }} - Tracking Cargo Export:.</title>
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
    </style>

    <!-- Page Specific JS File -->
    @stack('styles')
</head>

<body>
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
                    <div class="carousel-item active" style="background-image: url(images/MKT1.jpg);">
                        <div class="carousel-container text-center"
                            style="justify-content: end; width: 45%; margin-left: 15%;">
                            <div class="carousel-content">
                                <h4>
                                    <p class="text-center">Tracking Cargo Export</p>
                                </h4>
                                <form action="" method="post" id="form-search">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="peb_no" id="pebNo"
                                            aria-describedby="helpId" placeholder="PEB Number" autocomplete="off"
                                            autofocus required>
                                    </div>
                                    {{-- <div class="form-group">
                                        <input type="text" class="form-control pebNo" name="vehicle_number"
                                            id="" aria-describedby="helpId" placeholder="Vehcile Number"
                                            autocomplete="off" required>
                                    </div> --}}
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row p-5">
                        <div class="col-sm-12" style="outline: #F03C02 solid 2px; border-radius: 5px;">
                            <div class="appendTable">

                            </div>
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
        $('#modal-result').on('shown.bs.modal', function() {
            $('#pebNo').focus();
        });
        $('#modal-result').on('hidden.bs.modal', function() {
            $('#pebNo').focus();
        })

        const username = localStorage.getItem('USERNAME');

        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            $('.btnSearch').attr('disabled', true);
            $('.appendTable').html("");
            $.ajax({
                url: "{{ route('tracingCustExport') }}",
                data: $('#form-search').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    $('.btnSearch').attr('disabled', false);
                    if (response.length > 0) {
                        $('#modal-result').modal('show');
                        appendTable(response);
                        $("#pebNo").val("").focus();
                    } else {
                        swal({
                            title: "Data not found!",
                            icon: "warning",
                            dangerMode: true,
                        }).then((result) => {
                            $("#pebNo").val("").focus();
                        });
                    }
                },
                error: function(error) {
                    $("#pebNo").val("").focus();
                    $('.btnSearch').attr('disabled', false);
                    console.log('error');
                }
            });
        });

        function appendTable(params) {
            $.each(params, function(key, val) {
                $('.appendTable').append(`
                <table class="table table-hover table-borderless">
                        <thead>
                            <tr>
                                <th>PEB </th>
                                <th>: </th>
                                <th>${val.peb_no}</th>
                            </tr>
                            <tr>
                                <th>SHIPPER </th>
                                <th>: </th>
                                <th>${val.shipper_name}</th>
                            </tr>
                            <tr>
                                <th>GATE IN </th>
                                <th>: </th>
                                <th>${val.gate_in}</th>
                            </tr>
                            <tr>
                                <th>VEHICLE NO </th>
                                <th>: </th>
                                <th>${val.vehicle_no}</th>
                            </tr>
                        </thead>
                    </table>
                    `);
                $('.appendTable').append("<hr>");
            });
        }
    </script>
</body>

</html>
