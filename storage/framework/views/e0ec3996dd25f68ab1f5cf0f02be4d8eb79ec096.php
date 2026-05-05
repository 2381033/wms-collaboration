

<?php $__env->startSection('title'); ?>
    Home
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section id="hero">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active" style="background-image: url(images/MKT1.jpg);">
                    <div class="carousel-container">
                        <div class="carousel-content animate__animated animate__fadeInUp">
                            <h2>Welcome to <span>PT Masaji Kargosentra Tama</span></h2>
                            <h4>
                                <p>Connecting Indonesia</p>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/home.blade.php ENDPATH**/ ?>