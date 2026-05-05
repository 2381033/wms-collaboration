

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/' . $css . '.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <table class="table-header-kerangka">
        <tr>
            <td>
                <?php if(isset($headerOne)): ?>
                    <table class="table-header">
                        <?php echo $headerOne; ?>

                    </table>
                <?php endif; ?>
            </td>
            <td>
                <?php if(isset($headerTwo)): ?>
                    <table class="table-header">
                        <?php echo $headerTwo; ?>

                    </table>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <table class="table">
        <thead class="thead-dark">
            <?php if(isset($headOne)): ?>
                <tr>
                    <?php for($i = 0; $i < count($headOne); $i++): ?>
                        <th rowspan="<?php echo e($headOne[$i]['rowspan']); ?>" colspan="<?php echo e($headOne[$i]['colspan']); ?>">
                            <?php echo e($headOne[$i]['name']); ?></th>
                    <?php endfor; ?>
                </tr>
            <?php endif; ?>
            <?php if(isset($headTwo)): ?>
                <tr>
                    <?php for($i = 0; $i < count($headTwo); $i++): ?>
                        <th><?php echo e($headTwo[$i]['name']); ?></th>
                    <?php endfor; ?>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php for($i = 0; $i < count($listData); $i++): ?>
                <tr>
                    <?php for($r = 0; $r < count($bodyOne); $r++): ?>
                        <td class="<?php echo e($bodyOne[$r]['class']); ?>"
                            <?php if(isset($bodyOne[$r]['colspan'])): ?> colspan="<?php echo e($bodyOne[$r]['colspan']); ?>" <?php endif; ?>
                            <?php if(isset($bodyOne[$r]['rowspan'])): ?> rowspan="<?php echo e($bodyOne[$r]['rowspan']); ?>" <?php endif; ?>>
                            <?php echo e($listData[$i][$bodyOne[$r]['field_name']]); ?></td>
                    <?php endfor; ?>
                </tr>
                <?php if(isset($bodyTwo)): ?>
                    <tr>
                        <?php for($r = 0; $r < count($bodyTwo); $r++): ?>
                            <td class="<?php echo e($bodyTwo[$r]['class']); ?>"><?php echo e($listData[$i][$bodyTwo[$r]['field_name']]); ?></td>
                        <?php endfor; ?>
                    </tr>
                <?php endif; ?>
                <?php if(isset($bodyThree)): ?>
                    <tr>
                        <?php for($r = 0; $r < count($bodyThree); $r++): ?>
                            <td class="<?php echo e($bodyThree[$r]['class']); ?>"><?php echo e($listData[$i][$bodyThree[$r]['field_name']]); ?>

                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if($title === 'Product Wise - Stock Report ( Summary )' && $principal->multi_level == 'No'): ?>
                <?php $__currentLoopData = $stockKosong; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="text-align: left"><?php echo e($item->principal_name); ?></td>
                        <td style="text-align: left"><?php echo e($item->product_code); ?></td>
                        <td style="text-align: left"><?php echo e($item->product_name); ?></td>
                        <td style="text-align: right"><?php echo e('0'); ?></td>
                        <td style="text-align: right"><?php echo e('0'); ?></td>
                        <td style="text-align: right"><?php echo e('0'); ?></td>
                        <td style="text-align: center"><?php echo e($item->puom); ?></td>
                        <?php if($isVendor): ?>
                            <?php for($i = 1; $i <= 4; $i++): ?>
                                <td style="text-align: right">
                                    <?php echo e($item->{'ip_' . $i} ?? ''); ?>

                                </td>
                                <td style="text-align: right">
                                    <?php echo e($item->{'week_' . $i} ?? ''); ?>

                                </td>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <tr>
                <td class="center" colspan="<?php echo e($columnCount); ?>">End Of Report</td>
            </tr>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('signature'); ?>
    <?php if(isset($signature)): ?>
        <table class="table">
            <?php echo $signature; ?>

        </table>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.report', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/report.blade.php ENDPATH**/ ?>