<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>.:<?php echo e(config('app.name')); ?> - <?php echo $__env->yieldContent('title'); ?>:.</title>
    <meta content="" name="descriptison">
    <meta content="" name="keywords">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Favicons -->
    <link href="<?php echo e(asset('images/favicon.png')); ?>" rel="icon">
    <link href="<?php echo e(asset('images/favicon.png')); ?>" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Muli:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?php echo e(asset('assets/vendor/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/icofont/icofont.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/boxicons/css/boxicons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/animate.css/animate.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/venobox/venobox.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/owl.carousel/assets/owl.carousel.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/vendor/aos/aos.css')); ?>" rel="stylesheet">
    <link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
        crossorigin="anonymous" />
    <script src="<?php echo e(asset('js/sweetalert.min.js')); ?>"></script>

    <!-- Template Main CSS File -->
    <link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">

    <!-- =======================================================
    * Template Name: Medilab - v2.0.0
    * Template URL: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>
    <section id="topbar" class="d-none d-lg-block">
        <div class="container d-flex">
            <div class="contact-info mr-auto">
                <i class="icofont-envelope"></i><a href="mailto:runclean.id@gmail.com">marketing.mkt@samudera.id</a>
                <i class="icofont-phone"></i> (021) 29088220
            </div>
            <div class="social-links">
                <a href="http://www.facebook.com/samuderaID" target="_blank" class="facebook"><i
                        class="icofont-facebook"></i></a>
                <a href="http://www.twitter.com/samudera_ind" target="_blank" class="twitter"><i
                        class="icofont-twitter"></i></a>
                <a href="http://www.instagram.com/samudera.id" target="_blank" class="instagram"><i
                        class="icofont-instagram"></i></a>
            </div>
        </div>
    </section>

    <header id="header">
        <div class="container d-flex">
            <div class="logo mr-auto">
                <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('images/logos.png')); ?>" alt=""
                        class="img-fluid"></a>
            </div>

            <nav class="nav-menu d-none d-lg-block">
                <ul>
                    <li <?php if(Request::segment(1) == 'home'): ?> class="active" <?php endif; ?>><a href="<?php echo e(route('home')); ?>">
                            <i class="fas fa-home"></i> Home</a></li>
                    <li <?php if(Request::segment(1) == 'about'): ?> class="active" <?php endif; ?>><a
                            href="<?php echo e(route('profile.about')); ?>">About</a></li>
                    <li <?php if(Request::segment(1) == 'services'): ?> class="active" <?php endif; ?>><a
                            href="<?php echo e(route('profile.services')); ?>">Services</a></li>
                    <li <?php if(Request::segment(1) == 'contact'): ?> class="active" <?php endif; ?>><a
                            href="<?php echo e(route('profile.contact')); ?>">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

    <!-- Vendor JS Files -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="<?php echo e(asset('assets/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/jquery.easing/jquery.easing.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/php-email-form/validate.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/jquery-sticky/jquery.sticky.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/isotope-layout/isotope.pkgd.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/venobox/venobox.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/waypoints/jquery.waypoints.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/owl.carousel/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/aos/aos.js')); ?>"></script>
    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"
        integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.js"
        integrity="sha256-siqh9650JHbYFKyZeTEAhq+3jvkFCG8Iz+MHdr9eKrw=" crossorigin="anonymous"></script>

    <!-- Template Main JS File -->
    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>

    <!-- Page Specific JS File -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/layouts/error.blade.php ENDPATH**/ ?>