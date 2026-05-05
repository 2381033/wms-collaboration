<table>
    <tr>
        <td colspan="10"
            style="height:50px; border-top:2px solid black; border-left:2px solid black; border-right:2px solid black;">
        </td>
    </tr>
    <tr>
        <td colspan="10"
            style="font-size:16px; font-weight:bold; text-align:center; padding-top:15px; border-left:2px solid black; border-right:2px solid black;">
            TALLY SHEET
        </td>
    </tr>
    <tr>
        <td colspan="10"
            style="font-size:12px; text-align:center; border-left:2px solid black; border-right:2px solid black; border-bottom: 2px solid black;">
            RECEIVING EXPORT
        </td>
    </tr>
</table>
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="font-weight:bold; border-left:2px solid black;">SHIPPER</td>
        <td colspan="4">: <?php echo e($data['header']->shipper_name); ?></td>

        <td style="font-weight:bold;">PRINCIPAL</td>
        <td colspan="4" style="border-right:2px solid black;">: <?php echo e($data['header']->forwarder_name); ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold; border-left:2px solid black;">TOTAL QTY</td>
        <td colspan="4">: <?php echo e($data['detail']->sum('quantity') . ' ' . $data['detail']->first()->unit); ?></td>

        <td style="font-weight:bold;">TRUCK NO</td>
        <td colspan="4" style="border-right:2px solid black;">: <?php echo e($data['header']->vehicle_no); ?>

            (<?php echo e(DB::table('ex_gate_in_cargo')->where('vehicle_number', $data['header']->vehicle_no)->value('vehicle_type')); ?>)
        </td>
    </tr>
    <tr>
        <td style="font-weight:bold; border-left:2px solid black;">TOTAL VOLUME(ACTUAL)</td>
        <td colspan="4">: <?php echo e($data['cbm_total']); ?></td>

        <td style="font-weight:bold;">DATE IN</td>
        <td colspan="4" style="border-right:2px solid black;">:
            <?php echo e(\Carbon\Carbon::parse($data['header']->created_at)->format('d-m-Y H:i')); ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold; border-left:2px solid black;">TOTAL VGM</td>
        <td colspan="4">: <?php echo e($data['vgm_total']); ?></td>

        <td style="font-weight:bold;">UNLOADING START</td>
        <td colspan="4" style="border-right:2px solid black;">:
            <?php echo e(\Carbon\Carbon::parse($data['unloading_start']->created_at)->format('d-m-Y H:i') ?? '-'); ?>

        </td>
    </tr>
    <tr>
        <td style="font-weight:bold;">DESTINATION</td>
        <td colspan="4" style="border-right:2px solid black;">: <?php echo e($data['header']->destination ?? '-'); ?></td>

        <td style="font-weight:bold;">UNLOADING FINISH</td>
        <td colspan="4" style="border-right:2px solid black;">:
            <?php echo e(\Carbon\Carbon::parse($data['unloading_finish']->created_at)->format('d-m-Y H:i') ?? '-'); ?>

        </td>
    </tr>
    <tr>
        <td style="font-weight:bold; border-left:2px solid black; border-bottom:2px solid black;">FINAL DESTINATION</td>
        <td colspan="4" style="border-bottom:2px solid black;">: <?php echo e($data['header']->final_destination ?? '-'); ?>

        </td>

        <td style="font-weight:bold; border-left:2px solid black; border-bottom:2px solid black;">BUYER</td>
        <td colspan="4" style="border-bottom:2px solid black;">: <?php echo e($data['header']->consignee_name ?? '-'); ?></td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th rowspan="2" style="font-weight:bold; border:2px solid black;">PEB NO.</th>
            <th rowspan="2" style="font-weight:bold; border:2px solid black;">PALLET ID</th>
            <th rowspan="2" style="font-weight:bold; border:2px solid black;">MARKING &amp; PO</th>
            <th rowspan="2" style="font-weight:bold; border:2px solid black;">PKGS</th>
            <th rowspan="2" style="font-weight:bold; border:2px solid black;">QTY CARGO</th>
            <th colspan="5" style="font-weight:bold; border:2px solid black; text-align: center;">MEASUREMENT</th>
        </tr>
        <tr>
            <th style="font-weight:bold; border:2px solid black; text-align: center">L</th>
            <th style="font-weight:bold; border:2px solid black; text-align: center">W</th>
            <th style="font-weight:bold; border:2px solid black; text-align: center">H</th>
            <th style="font-weight:bold; border:2px solid black; text-align: center">VGM</th>
            <th style="font-weight:bold; border:2px solid black; text-align: center">CBM</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $data['detail']->groupBy('pallet_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $items->groupBy(function ($item) {
        return explode('-', $item->serial_no)[0];
    }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber => $groupedByPo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $pebNo = $data['header']->peb_no == 0 ? '-' : $data['header']->peb_no;
                ?>

                <?php $__currentLoopData = $groupedByPo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($pebNo); ?></td>
                        <td><?php echo e($val->pallet_id); ?></td>
                        <td><?php echo e(strtoupper($poNumber)); ?></td>
                        <td><?php echo e(optional($groupedByPo->first())->unit); ?></td>
                        <td><?php echo e($val->quantity); ?></td>
                        <td><?php echo e($val->length); ?></td>
                        <td><?php echo e($val->width); ?></td>
                        <td><?php echo e($val->height); ?></td>
                        <td><?php echo e($val->weight == 0 ? '-' : $val->weight); ?></td>
                        <td>
                            <?php echo e(number_format((($val->length * $val->width * $val->height) / 1000000) * $val->quantity, 3, '.', '')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">TOTAL</th>
            <th><?php echo e($data['qtyTotal']); ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th><?php echo e($data['vgm_total']); ?></th>
            <th><?php echo e(number_format($data['cbm_total'], 3, '.', '')); ?></th>
        </tr>
    </tfoot>

</table>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/transaction/export/inbound/tally_sheet_download.blade.php ENDPATH**/ ?>