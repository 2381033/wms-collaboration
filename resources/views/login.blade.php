<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>.:{{ config('app.name') }} - Login:.</title>
    <meta content="" name="descriptison">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('images/favicon.png') }}" rel="icon">
    <link href="{{ asset('images/favicon.png') }}" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/vendor/icofont/icofont.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/venobox/venobox.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet"> --}}
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    {{-- <script src="{{ asset('js/sweetalert.min.js') }}"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

    <style>
        .loader {
            width: 18px;
            height: 18px;
            border: 5px dotted rgb(0, 0, 0);
            border-radius: 50%;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            animation: rotation 2s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes rotationBack {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-360deg);
            }
        }
    </style>
</head>

<body>
    @include('layouts.navbar')

    <main id="main">
        <!-- ======= Breadcrumbs ======= -->
        <section id="breadcrumbs" class="breadcrumbs">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Login</h2>
                    <ol>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li>Login</li>
                    </ol>
                </div>
            </div>
        </section><!-- End Breadcrumbs -->

        <section id="blog" class="blog">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-6"></div>
                    <div class="col-md-4 col-sm-6">
                        <article class="entry" style="border-radius: 30px;">
                            <h5 class="entry-title text-center">
                                Form Login
                            </h5>

                            @if (session('sukses'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('sukses') }}
                                </div>
                            @endif

                            <div class="entry-content">
                                <form method="POST" id="form-login" class="needs-validation" novalidate="">
                                    @csrf
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input id="username" type="text" class="form-control" name="username"
                                            value="{{ old('username') }}" required autofocus autocomplete="off">
                                        @if ($errors->has('username'))
                                            <small class="text-info mt-2">{{ $errors->first('username') }}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                        </div>
                                        <input id="password" type="password" class="form-control Password"
                                            name="password" required autocomplete="off">
                                        @if ($errors->has('password'))
                                            <small class="text-info mt-2">{{ $errors->first('password') }}</small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="showPass"
                                                        aria-label="Checkbox for following text input">
                                                    <label class="ml-2 mt-2">Show Password</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-outline-dark btn-lg btn-block"
                                            tabindex="4">
                                            <div class="appendLoader">
                                                Login
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="whole-page-overlay" id="loader">
        <img class="center-loader" style="height:100px;" src="{{ asset('images/loading.gif') }}" />
    </div>

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor/jquery-sticky/jquery.sticky.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/venobox/venobox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/owl.carousel/owl.carousel.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"
        integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/date.js') }}"></script>

    <!-- Page Specific JS File -->

    <script>
        $(document).ready(function() {

            $('#showPass').on('click', function() {
                var passInput = $(".Password");
                if (passInput.attr('type') === 'password') {
                    passInput.attr('type', 'text');
                } else {
                    passInput.attr('type', 'password');
                }
            });

            if ($("#form-login").length > 0) {
                $("#form-login").validate({
                    submitHandler: function(form) {
                        $('.appendLoader').html("")
                        $('.appendLoader').append('<span class="loader mr-2"></span>Loading')
                        $('.btn-block').attr('disabled', true);
                        $.ajax({
                            data: $('#form-login').serialize(),
                            url: "{{ route('login.post') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    toastr.success(data.message)
                                    location.href = "{{ route('home') }}"
                                } else {
                                    $('.appendLoader').html("")
                                    $('.appendLoader').append('Login')
                                    $('.btn-block').attr('disabled', false);

                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error
                                        .length; i++) {
                                        pesan += data.error[i] + '</br>';
                                    }
                                    pesan += '</div>';

                                    const wrapper = document.createElement(
                                        'div');
                                    wrapper.innerHTML = pesan;
                                    toastr.error(pesan)
                                    // swal({
                                    //     icon: "error",
                                    //     content: wrapper
                                    // });
                                }
                            },
                            error: function(data) {
                                $('.appendLoader').html("")
                                $('.appendLoader').append('Login')
                                $('.btn-block').attr('disabled', false);
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }
        });
    </script>
</body>

</html>
