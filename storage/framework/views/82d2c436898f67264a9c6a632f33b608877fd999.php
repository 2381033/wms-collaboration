<!DOCTYPE html>
<html>

<head>
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="<?php echo e(asset('images/logos.png')); ?>" />
        </div>
        <table class="table-template">
            <thead>
                <tr>
                    <td>
                        <div class="header-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="title">
                            <h3 class="title-header">
                                <?php echo $__env->yieldContent('title'); ?>
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="signature">
                            <?php echo $__env->yieldContent('signature'); ?>
                            <!-- <div class="footer-space">&nbsp;</div> -->
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="footer">
        Print Date : <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?>, Print By <?php echo e(Auth::user()->username); ?>

    </div>
    <script>
        document.title = "<?php echo e($title ?? ''); ?>"
        // window.print()
    </script>
</body>

</html>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/layouts/report.blade.php ENDPATH**/ ?>