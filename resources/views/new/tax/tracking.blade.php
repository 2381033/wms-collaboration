<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>MKT - E-FAKTUR ONLINE</title>
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
                                    <p class="text-center">E-FAKTUR ONLINE</p>
                                </h4>
                                <form action="" method="post" id="form-search">
                                    @csrf
                                    <div class="form-group">
                                        <input type="number" class="form-control" name="npwp" id="npwp"
                                            aria-describedby="helpId" placeholder="MASUKAN NPWP (16 DIGIT)"
                                            autocomplete="off" autofocus required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="kw" id="kw"
                                            aria-describedby="helpId" placeholder="NOMOR KWITANSI" autocomplete="off"
                                            required>
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

    <footer id="footer">
        <div class="container d-md-flex py-4">
            <div class="mr-md-auto text-center text-md-left">
                <i class="icofont-envelope mr-2 text-white"></i><a class="text-white"
                    href="mailto:rahmat.fitrah@samudera.id">rahmat.fitrah@samudera.id</a>
                <label for="" class="text-center ml-4 mr-4"> | </label>
                <i class="icofont-brand-whatsapp text-white"></i> <label class="text-white">0813-1832-8167
                    (Rahmat)</label>
                <label for="" class="text-center ml-4 mr-4"> | </label>
                <i class="icofont-envelope mr-2 text-white"></i><a class="text-white"
                    href="mailto:nur.hamzah@samudera.id">nur.hamzah@samudera.id</a>
                <label for="" class="text-center ml-4 mr-4"> | </label>
                <i class="icofont-brand-whatsapp text-white"></i> <label class="text-white">0813-8248-7910
                    (Hamzah)</label>
                <div class="copyright">
                    &copy; <strong><span>PT Masaji Kargosentra Tama 2021</span></strong>.
                </div>
            </div>
            <div class="social-links text-center text-md-right pt-3 pt-md-0">
                <a href="http://www.facebook.com/samuderaID" target="_blank" class="facebook"><i
                        class="icofont-facebook"></i></a>
                <a href="http://www.twitter.com/samudera_ind" target="_blank" class="twitter"><i
                        class="icofont-twitter"></i></a>
                <a href="http://www.instagram.com/samudera.id" target="_blank" class="instagram"><i
                        class="icofont-instagram"></i></a>
            </div>
        </div>
    </footer><!-- End Footer -->


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
            $('#npwp').val("").focus();
        })

        $('#npwp').on('input', function() {
            $('#npwp').on('input', function() {
                var value = $(this).val();
                $(this).val(value.replace(/[^0-9]/g, ''));
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().substring(0, 16));
                }
            });
        });
        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            $('.btnSearch').attr('disabled', true);
            $('#appendTable').html("");
            var npwp = $('#npwp').val();
            var kw = $('#kw').val();
            if (npwp == "" && kw == "") {
                swal({
                    title: "NPWP or Kwitansi is required",
                    icon: "warning",
                    dangerMode: true,
                }).then((result) => {
                    $("#npwp").val("").focus();
                });
                $('.btnSearch').attr('disabled', false);
            } else {
                $.ajax({
                    url: "{{ url('tax/postTracking') }}",
                    data: $('#form-search').serialize(),
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        $('.btnSearch').attr('disabled', false);
                        if (response.data == 'null') {
                            swal({
                                title: "Data not found",
                                icon: "warning",
                                dangerMode: true,
                            }).then((result) => {
                                $("#npwp").val("").focus();
                            });
                        } else {
                            var filename = response.data.file;
                            var url = `{{ asset('public/tax/pdf/') }}/${filename}`;
                            window.open(
                                url, '_blank'
                            );
                        }
                    },
                    error: function(error) {
                        $("#npwp").val("").focus();
                        $('.btnSearch').attr('disabled', false);
                        console.log('error');
                    }
                });
            }
        });
    </script>
</body>

</html>
