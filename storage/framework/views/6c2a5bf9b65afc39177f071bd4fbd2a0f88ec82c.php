

<?php $__env->startSection('title'); ?>
    Not Found
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Not Found</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Not Found</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs -->

    <section id="about-us" class="about-us">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12">
                    <h1>
                        Oops!</h1>
                    <h2>
                        404 Not Found</h2>

                    <p>
                        The page you were looking for could not be found.
                    </p>
                    <br>
                    <p>
                        <h2><?php echo e($exception->getMessage()); ?></h2>
                    </p>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/errors/404.blade.php ENDPATH**/ ?>