

<?php $__env->startSection('title'); ?>
    Export - Inbound
<?php $__env->stopSection(); ?>

<style>
    .page {
        width: 125mm;
        min-height: 148mm;
        padding: 3mm;
        margin: 5mm auto;
        border: 1px #333 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        page-break-after: always;
        page-break-inside: avoid;
    }

    .container {
        width: 100%;
        margin: 0px auto;
    }

    .row:after {
        content: "";
        display: grid;
        clear: both;
    }

    .column {
        float: left;
        padding-bottom: 5px;
    }

    .col-100 {
        width: 100%;
    }

    .center {
        text-align: center;
    }

    .hide {
        display: none;
    }

    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #0C9;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
    }

    .my-float {
        margin-top: 22px;
    }

    .float .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        top: -5%;
        right: 105%;
        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .float:hover .tooltiptext {
        visibility: visible;
    }

    #ajax-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: black;
        /* Ganti ke white jika mau putih */
        opacity: 0.8;
        z-index: 9999;
    }

    #modal-images .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        grid-gap: 8px;
        padding: 5px;
    }

    #modal-images .gallery img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        transition: .3s;
    }

    #modal-images .gallery img:hover {
        transform: scale(1.05);
    }
</style>

<?php $__env->startSection('content'); ?>
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - Inbound</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Export - Inbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-job" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="id" name="id"
                    <?php if(isset($header->id)): ?> value="<?php echo e($header->id); ?>" <?php endif; ?>>
                <div class="row info-wrap p-3 m-3" style="border-radius: 13px; text-shadow: 13px;">
                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_no">Job Number</label>
                                    <input type="text" autocomplete="off" id="Job_no" name="job_no"
                                        value="<?php echo e($header->job_no); ?>" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_date">Job Date</label>
                                    <input type="text" autocomplete="off" id="job_date" name="job_date"
                                        value="<?php echo e(\Carbon\Carbon::parse($header->job_date)->format('d-m-Y')); ?>"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select id="my-select" required class="form-control" name="branch_id" readonly>
                                        <option value="<?php echo e($branchme->id); ?>" readonly><?php echo e($branchme->branch_name); ?> </option>
                                    </select>
                                </div>
                            </div>
                            <?php if($detail->count() > 0 and $header->status_flag == 'Open'): ?>
                                <div class="col-sm-6 mb-2">
                                    <a class="btn btn-info btn-block text-white" href="#addQty" data-toggle="modal"><i
                                            class="fas fa-plus-circle"></i> Weight
                                    </a>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <a onclick="showImages()" class="btn btn-primary btn-block text-white"><i
                                            class="fas fa-images"></i> Image
                                    </a>
                                </div>
                                <?php if($header->checker_flag == 'Confirmed'): ?>
                                    <div class="col-sm-12">
                                        <a onclick="backtoChecker()"
                                            class="btn btn-danger text-white btn-block mb-3 mt-2"><i class="fas fa-reply"
                                                style="border-radius: 12px;"></i> Return to checker
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="col-sm-12">
                                <a id="pallet-print" class="btn btn-dark text-white btn-block"><i class="fas fa-print"
                                        style="border-radius: 12px;"></i> Pallet Tag Print
                                </a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('button-confirm-export', 'button-confirm-export')): ?>
                                    <?php if($header->status_flag == 'Confirmed'): ?>
                                        <a href="javascript:void(0)" onclick="editPalletize('<?php echo e($header->id); ?>')"
                                            class="btn btn-secondary btn-block"><i class="fas fa-clipboard-list"></i> <span>
                                                Edit
                                                Palletize</span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <hr>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('button-confirm-export', 'button-confirm-export')): ?>
                                    <?php if($detail->count() > 0 and $header->status_flag == 'Open'): ?>
                                        <?php if($header->checker_flag == 'Confirmed'): ?>
                                            <?php if(!is_null($header->remarks)): ?>
                                                <a href="javascript:void(0)" onclick="submitData()"
                                                    class="btn btn-success btn-block"><i class="fas fa-check-circle"></i> <span>
                                                        Konfirm</span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($header->status_flag == 'Confirmed'): ?>
                                    <a href="javascript:void(0)" id="tally-print-detail" class="btn btn-block text-white"
                                        style="background-color: #DD1C21"><i class="fas fa-file-pdf"></i> <span>
                                            Tally Sheet (PDF)</span>
                                    </a>
                                    <a href="javascript:void(0)" id="tally-print-download"
                                        class="btn btn-block btn-success text-white"><i class="fas fa-file-excel"></i>
                                        <span>
                                            Tally Sheet (Excel)
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="hidden" id="forwarder_id" name="forwarder_id"
                                        <?php if(isset($header->forwarder_id)): ?> value="<?php echo e($header->forwarder_id); ?>" <?php endif; ?>>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.."
                                        id="forwarder_name" name="forwarder_name" class="form-control" required
                                        <?php if(isset($header->forwarder_name)): ?> value="<?php echo e($header->forwarder_name); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?>  <?php endif; ?>
                                        <?php if($header->status_flag == 'Confirmed'): ?> readonly <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Shipper Name</label>
                                    <input type="hidden" id="shipper_id" name="shipper_id"
                                        <?php if(isset($header->shipper_id)): ?> value="<?php echo e($header->shipper_id); ?>" <?php endif; ?>>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="shipper_name" name="shipper_name" class="form-control"
                                        <?php if(isset($header->shipper_name)): ?> value="<?php echo e($header->shipper_name); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php endif; ?>
                                        <?php if($header->status_flag == 'Confirmed'): ?> readonly <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Consignee Name</label>
                                    <input type="hidden" id="consignee_id" name="consignee_id"
                                        <?php if(isset($header->consignee_id)): ?> value="<?php echo e($header->consignee_id); ?>" <?php endif; ?>>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="consignee_name" name="consignee_name" class="form-control"
                                        <?php if(isset($header->consignee_name)): ?> value="<?php echo e($header->consignee_name); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php endif; ?>
                                        <?php if($header->status_flag == 'Confirmed'): ?> readonly <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>PEB No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="peb_no"
                                        name="peb_no" class="form-control" required value="<?php echo e($header->peb_no); ?>"
                                        <?php if($header->status_flag == 'Confirmed'): ?> readonly <?php endif; ?> />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>AJU No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="aju_no"
                                        name="aju_no" class="form-control" required value="<?php echo e($header->aju_no); ?>"
                                        <?php if($header->status_flag == 'Confirmed'): ?> readonly <?php endif; ?> />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="vehicle_no">Vehicle No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="vehicle_no" value="<?php echo e($header->vehicle_no); ?>"
                                        class="form-control" <?php if(isset($header)): ?> readonly <?php endif; ?> />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>VGM (kg)</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vgm" name="vgm" class="form-control"
                                        value="<?php echo e($header->vgm); ?>" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="destination" name="destination" class="form-control"
                                        value="<?php echo e($header->destination); ?>" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Final Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="final_destination" name="final_destination" class="form-control"
                                        value="<?php echo e($header->final_destination); ?>" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Checker</label>
                                    <select id="checkerName" required class="form-control" name="pic_name"
                                        style="background-color: bisque;">
                                        <?php if(!is_null($header->pic_name)): ?>
                                            <option value="<?php echo e(Str::upper($header->pic_name)); ?>" selected>
                                                <?php echo e(Str::upper($header->pic_name)); ?>

                                            <?php else: ?>
                                            <option value="" selected disabled> Silahkan Pilih </option>
                                        <?php endif; ?>
                                        <?php $__currentLoopData = $checker; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->username); ?>"><?php echo e($item->name); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <?php if($header->status_flag == 'Confirmed'): ?>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Stapel</label>
                                        <select class="form-control" name="stapel_name"
                                            onchange="updateStaple(this.value)" style="background-color: bisque;"
                                            id="stapelSelect">
                                            <?php if(is_null($header->stapel_name)): ?>
                                                <option value="" disabled selected>Silahkan Pilih</option>
                                            <?php else: ?>
                                                <option value="<?php echo e(Str::upper($header->stapel_name)); ?>" selected>
                                                    <?php echo e(Str::upper($header->stapel_name)); ?>

                                            <?php endif; ?>
                                            <?php $__currentLoopData = $stapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($item->username); ?>"><?php echo e($item->name); ?>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <a class="btn btn-link" data-toggle="collapse" data-target="#draft"
                                                    aria-expanded="true" aria-controls="draft">
                                                    CRS Draft Version <i class="fa fa-arrow-circle-down"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="draft" class="collapse show" aria-labelledby="headingOne"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No.</th>
                                                            <th>PO No.</th>
                                                            <th>Qty</th>
                                                            <th>Vol. CBM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $row_count = count($draft['po_number']);
                                                            $totalQty = 0;
                                                            $totalVol = 0;
                                                        ?>
                                                        <?php for($i = 0; $i < $row_count; $i++): ?>
                                                            <tr class="text-center">
                                                                <td><?php echo e($i + 1); ?></td>
                                                                <td><?php echo e($draft['po_number'][$i]); ?></td>
                                                                <td><?php echo e($draft['qty_cargo'][$i]); ?></td>
                                                                <td><?php echo e($draft['cbm'][$i]); ?></td>
                                                                <?php
                                                                    $totalQty += $draft['qty_cargo'][$i];
                                                                    $totalVol += $draft['cbm'][$i];
                                                                ?>
                                                            </tr>
                                                        <?php endfor; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="text-center">
                                                            <td colspan="2"><strong>SUMMARY</strong></td>
                                                            <td><strong><?php echo e($totalQty); ?></strong></td>
                                                            <td><strong><?php echo e(number_format($totalVol, 3, '.', '')); ?></strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <a class="btn btn-link" data-toggle="collapse" data-target="#checker"
                                                    aria-expanded="true" aria-controls="checker">
                                                    CRS Checker Version <i class="fa fa-arrow-circle-down"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="checker" class="collapse show" aria-labelledby="headingOne"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Pallet ID</th>
                                                            <th>PO No.</th>
                                                            <th>Qty</th>
                                                            <th>P</th>
                                                            <th>L</th>
                                                            <th>T</th>
                                                            <th>Vol. CBM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $groupedItems = $detail
                                                                ->sortBy('pallet_id')
                                                                ->groupBy('pallet_id')
                                                                ->map(function ($items) {
                                                                    return $items->groupBy(function ($item) {
                                                                        return explode('-', $item->serial_no)[0];
                                                                    });
                                                                });

                                                            $totalQuantity = 0;
                                                            $totalVolume = 0;
                                                        ?>

                                                        <?php $__currentLoopData = $groupedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletId => $poGroups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php $__currentLoopData = $poGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $rowspan = $items->count();
                                                                    $groupQuantity = 0;
                                                                    $groupVolume = 0;
                                                                ?>

                                                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $volume =
                                                                            (($item->length *
                                                                                $item->width *
                                                                                $item->height) /
                                                                                1000000) *
                                                                            $item->quantity;
                                                                        $groupQuantity += $item->quantity;
                                                                        $groupVolume += $volume;
                                                                        $totalQuantity += $item->quantity;
                                                                        $totalVolume += $volume;
                                                                    ?>
                                                                    <tr class="text-center">
                                                                        <?php if($index == 0): ?>
                                                                            <td rowspan="<?php echo e($rowspan); ?>">
                                                                                <?php echo e($palletId); ?></td>
                                                                            <td rowspan="<?php echo e($rowspan); ?>">
                                                                                <?php echo e(strtoupper($poNumber)); ?></td>
                                                                        <?php endif; ?>
                                                                        <td><?php echo e($item->quantity . ' ' . $item->unit); ?></td>
                                                                        <td><?php echo e($item->length); ?></td>
                                                                        <td><?php echo e($item->width); ?></td>
                                                                        <td><?php echo e($item->height); ?></td>
                                                                        <td><?php echo e(number_format($volume, 3, '.', '')); ?></td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="text-center">
                                                            <td colspan="2"><strong>SUMMARY</strong></td>
                                                            <td><strong><?php echo e($totalQuantity); ?></strong></td>
                                                            <td colspan="3"><strong></td>
                                                            <td><strong><?php echo e(number_format($totalVolume, 3, '.', '')); ?></strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" role="dialog" id="addQty">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php if(isset($header)): ?>
                                <section id="contact" class="contact">
                                    <form action="<?php echo e(url('export/inbound/updateWeight')); ?>" method="post">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" id="job_id" name="job_id"
                                            <?php if(isset($header->id)): ?> value="<?php echo e($header->id); ?>" <?php endif; ?> />
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-sm" style="width:100%;">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Pallet ID</th>
                                                        <th>Quantity</th>
                                                        <th>Weight</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr class="text-center">
                                                            <td class="text-center"><?php echo e($value->pallet_id); ?></td>
                                                            <td class="text-center"><?php echo e($value->quantity); ?></td>
                                                            <td class="text-center">
                                                                <input type="number" autocomplete="off" name="weight[]"
                                                                    value="<?php echo e($value->weight); ?>" class="form-control"
                                                                    required>
                                                                <input type="hidden" autocomplete="off"
                                                                    name="id_detail_inbound[]" value="<?php echo e($value->id); ?>"
                                                                    class="form-control">
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if($header->status_flag == 'Open' and $detail->count() > 0): ?>
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-md btn-success"><i
                                                        class="fas fa-save"></i> Submit
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </section>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="pallet_tag_preview">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5> Pallet Tag Preview </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page">
                                <div class="container">
                                    <div class="row">
                                        <div class="column col-100">
                                            <table class="table">
                                                <tr>
                                                    <td colspan="2">
                                                        <img alt="image" src="<?php echo e(asset('images/logos.png')); ?>"
                                                            alt="" height="25pt">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="center">
                                                        <img src="data:image/png;base64,<?php echo e(DNS2D::getBarcodePNG('Preview', 'QRCODE', 4, 4)); ?>"
                                                            alt="barcode" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Forwarder Name</small><br>
                                                        <b class="forwardertext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Shipper Name</small><br>
                                                        <b class="shippertext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>PO Number</small>
                                                        <br>
                                                        <b class="POtext"></b></span>
                                                    </td>
                                                    <td>
                                                        <small>PEB Number</small><br>
                                                        <b class="pebtext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Consignee Name</small>
                                                        <br>
                                                        <b class="consigneetext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Destination</small><br>
                                                        <span style="font-size: 20px;">
                                                            <b class="destinationtext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>Quantity</small><br>
                                                        <b class="qty_actualtext"></b> <b>of</b> <b
                                                            class="qty_cargotext"></b>
                                                    </td>
                                                    <td>
                                                        <small>Total Pallet</small><br>
                                                        <b class="total_pallettext"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>Checker</small><br>
                                                        <b class="checkertext"></b>
                                                    </td>
                                                    <td>
                                                        <small>Tanggal Bongkar</small><br>
                                                        <b class="tanggal_bongkartext"></b>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table>
                                                <tr style="border-style : hidden;">
                                                    <td colspan="2">
                                                        <span style="font-size: 9px; margin-left: 21em;"><b>PT. MASAJI
                                                                KARGOSENTRA TAMA<b></small>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <a data-dismiss="modal" class="btn btn-primary btn-sm text-white" onclick="klikOK()"><i
                            class="fas fa-check"></i>
                        <span>OK</span></a>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="update_palletize" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <section id="contact" class="contact">
                                <form action="<?php echo e(url('export/inbound/update_palletize')); ?>" method="post"
                                    id="updatePalletize">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="job_id" name="job_id" value="<?php echo e($header->id); ?>" />
                                    <input type="hidden" id="job_id" name="peb_no"
                                        value="<?php echo e($header->peb_no); ?>" />
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm" style="width:100%;"
                                            id="table_palletize">
                                            <div class="mb-2">
                                                <button type="button" class="btn btn-sm btn-primary btn-add"
                                                    onclick="addRow()" style="display: none;">
                                                    <i class="fas fa-plus"></i> Add Row
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark" onclick="coLoud()">
                                                    <i class="fas fa-pencil-alt"></i> Make Coloud
                                                </button>
                                            </div>
                                            <thead>
                                                <tr class="text-center">
                                                    <th>PO No</th>
                                                    <th>Pallet ID</th>
                                                    <th>Quantity</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>H</th>
                                                    <th>Uom</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-md btn-success btn-update"><i
                                                class="fas fa-save"></i>
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-images" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-body">
                        <div class="gallery"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($header != null): ?>
        <a href="javascript:void(0)" class="float" onclick="updateHeader()">
            <i class="fa fa-edit my-float"></i>
            <span class="tooltiptext">Update Header</span>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function setQtyCargo(val) {
            $('#qty_cargo').val(val);
        }

        function setQtyActual(value) {
            $('#qty_actual').val(value);
        }

        $('#updatePalletize').on('submit', function(e) {
            e.preventDefault(); // Hindari reload form default
            $('.btn-update').hide(); // Sembunyikan tombol saat submit
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: form.attr('action'), // pastikan action sudah di-set di form
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#ajax-overlay').show(); // Munculkan overlay
                },
                success: function(response) {
                    if (response.success) {
                        // Reload halaman jika sukses
                        location.reload();
                    } else if (response.error) {
                        alert('Error: ' + response.error);
                        $('.btn-update').show(); // Tampilkan kembali tombol
                    }
                },
                complete: function() {
                    $('#ajax-overlay').hide(); // Sembunyikan overlay setelah selesai
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan pada server.');
                    $('.btn-update').show(); // Tampilkan kembali tombol
                }
            });
        });

        function showImages() {
            var job_id = '<?php echo e($header->id); ?>';
            $.ajax({
                url: "<?php echo e(url('/export/inbound/showImages')); ?>/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {

                    let container = $('#modal-images .modal-body .gallery');
                    container.empty();

                    $.each(response.data, function(index, item) {
                        var url =
                            `<?php echo e(asset('public/foto/warehouse-export/inbound-cargo/')); ?>/${item.file}`;
                        container.append(`
                            <img src="${url}" data-id="${item.id}" style="cursor:pointer;max-width:130px;margin:5px;border-radius:6px;"
                                onclick="deleteImage(${item.id})">
                        `);
                    });
                    $('#modal-images').modal('show');
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function deleteImage(id) {

            if (!confirm('Yakin hapus foto ini?')) return;
            $.ajax({
                url: "<?php echo e(url('/export/inbound/deleteImage')); ?>/" + id,
                type: "GET",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                },
                success: function(res) {
                    showImages();
                },
                error: function() {
                    alert("Gagal hapus");
                }
            })
        }


        function backtoChecker() {
            confirmation = confirm("Are you sure want to return this job to checker?");
            if (confirmation) {
                executeBacktoChecker();
            } else {
                return false;
            }

        }

        function executeBacktoChecker() {
            var job_id = '<?php echo e($header->id); ?>';
            $.ajax({
                url: "<?php echo e(url('/export/inbound/backtoChecker')); ?>/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    alert('Job has been returned to checker successfully.');
                    location.reload();
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function editPalletize(job_id) {
            $.ajax({
                url: "<?php echo e(url('/export/inbound/getPalletize')); ?>/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#table_palletize tbody').empty();

                    $.each(response, function(index, item) {
                        var row = `
                    <tr class="text-center">
                          <td>
                            <input type="text" class="form-control" hidden name="location_id[]" value="${item.location_id ?? 0}">
                            <input type="text" class="form-control" hidden name="location_code[]" value="${item.location_code ?? 0}">
                            <select id="my-select" required class="form-control" name="po_number[]">
                                <option value="${item.po_number}">${item.po_number}</option>
                                <?php $__currentLoopData = $po_number; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($key); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="pallet_id[]" value="${item.pallet_id ?? ''}"></td>
                        <td><input type="number" class="form-control" name="quantity[]" value="${item.quantity ?? ''}"></td>
                        <td><input type="number" class="form-control" name="length[]" value="${item.length ?? ''}"></td>
                        <td><input type="number" class="form-control" name="width[]" value="${item.width ?? ''}"></td>
                        <td><input type="number" class="form-control" name="height[]" value="${item.height ?? ''}"></td>
                        <td>
                            <select id="my-select" required class="form-control" name="unit[]">
                                <option value="${item.unit}">${item.unit}</option>
                                <?php $__currentLoopData = $uom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `;
                        $('#table_palletize tbody').append(row);
                    });

                    $('#update_palletize').modal('show');
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function deleteRow(job_id, id_detail) {
            // Hapus baris (tr) dari tabel
            $(button).closest('tr').remove();
        }

        function coLoud() {
            swal({
                    title: "Apakah kamu yakin?",
                    text: "Simpan Tally yang lama sebelum melakukan aksi ini, karena data lama akan di hapus!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Belum, Saya simpan dulu",
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, saya sudah simpan",
                            closeModal: true,
                        },
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        deleteStock();
                    } else {
                        return false;
                    }
                });
        }

        function deleteStock() {
            var job_id = '<?php echo e($header->id); ?>';
            $.ajax({
                url: "<?php echo e(url('/export/inbound/deleteStock')); ?>/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.btn-add').show();
                    $('#update_palletize').modal('hide');
                    editPalletize(job_id);
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function addRow() {
            var row = `
        <tr class="text-center">
            <td><input type="text" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="po_number[]" value=""></td>
            <td><input type="text" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="pallet_id[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="quantity[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="length[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="width[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="height[]" value=""></td>
            <td>
                <select id="my-select" required class="form-control" name="unit[]">
                    <?php $__currentLoopData = $uom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
            $('#table_palletize tbody').append(row);
        }

        function deleteRow(button) {
            $(button).closest('tr').remove();
        }


        function updateHeader() {
            var qtyCargo = $('#qty_cargo').val();
            var qtyActual = $('#qty_actual').val();
            $.ajax({
                data: $('#form-job').serialize(),
                url: "<?php echo e(route('export-inbound.store')); ?>",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    if ($.isEmptyObject(data.error)) {
                        swal({
                            icon: "success",
                            text: "Data Successfully Saved."
                        });

                        window.open(data.success, '_top');
                    } else {
                        var pesan =
                            "<div class='text-left alert alert-danger'>";
                        for (var i = 0; i < data.error.length; i++) {
                            pesan += data.error[i] + '</br>';
                        }
                        pesan += '</div>';

                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = pesan;
                        swal({
                            icon: "error",
                            content: wrapper
                        });
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    $("#loader").hide();
                }
            });
        }

        function klikOK() {
            $('.saveJob').removeClass('hide');
        }

        function updateStaple(value) {
            var job_id = '<?php echo e($header->id); ?>';
            let urlRequest = "<?php echo e(url('export/inbound/updateStaple')); ?>/" + job_id + "/" + value;
            if (confirm("Are You sure?") == true) {
                location.href = urlRequest;
            } else {
                $('#stapelSelect').val("")
                return false;
            }
        }

        function previewPalletTag() {
            $('#pallet_tag_preview').modal('show')
            var peb = $('#peb_no').val();
            var aju = $('#aju_no').val();
            var pebText = '';
            if (peb == 0) {
                var pebText = aju
            } else {
                var pebText = peb
            }

            $('.forwardertext').text($('#forwarder_name').val());
            $('.shippertext').text($('#shipper_name').val());
            $('.potext').text($('#po_number').val());
            $('.pebtext').text(pebText);
            $('.consigneetext').text($('#consignee_name').val());
            $('.destinationtext').text($('#destination').val());
            $('.qty_actualtext').text($('#qty_actual').val());
            $('.qty_cargotext').text($('#qty_cargo').val());
            $('.total_pallettext').text($('#total_pallet').val());
            $('.checkertext').text($('#checkerName').val());
            $('.tanggal_bongkartext').text($('#tglBongkar').val());
        }

        function qtyActual() {

        }
        $(function() {
            var d = new Date();
            d.setDate(d.getDate());
            $('#eta').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#forwarder_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "<?php echo e(route('export.getForwarder')); ?>",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                service_name: "Export",
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#forwarder_id').val(ui.item.forwarder_id);
                        $('#forwarder_name').val(ui.item.forwarder_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.forwarder_name + "</div>")
                        .appendTo(ul);
                };

            $("#shipper_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "<?php echo e(route('export.getShipper')); ?>",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#shipper_id').val(ui.item.shipper_id);
                        $('#shipper_name').val(ui.item.shipper_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.shipper_name + "</div>")
                        .appendTo(ul);
                };

            $("#consignee_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "<?php echo e(route('export.getConsignee')); ?>",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#consignee_id').val(ui.item.consignee_id);
                        $('#consignee_name').val(ui.item.consignee_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.consignee_name + "</div>")
                        .appendTo(ul);
                };



            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        console.log($('#form-job').serialize());
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "<?php echo e(route('export-inbound.store')); ?>",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });

                                    window.open(data.success, '_top');
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
                                    }
                                    pesan += '</div>';

                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = pesan;
                                    swal({
                                        icon: "error",
                                        content: wrapper
                                    });
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            if ($("#form-detail").length > 0) {
                $("#form-detail").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-detail').serialize(),
                            url: "<?php echo e(route('export-detail.store')); ?>",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-detail').trigger("reset");

                                    var oTable = $('#table-detail').dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                    location.reload();
                                } else {
                                    swal({
                                        icon: "error",
                                        text: data.error
                                    });
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }
        });

        $('body').on('click', '#pallet-print', function() {
            var data_id = $('#id').val();

            window.open("<?php echo e(url('/export/inbound/pallet-tag/')); ?>" + "/" + data_id, 'palletExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-detail', function() {
            var data_id = $('#id').val();

            window.open("<?php echo e(url('/export/inbound/tally_sheet/detail/')); ?>" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-download', function() {
            var data_id = $('#id').val();
            window.location.href = "<?php echo e(url('export/inbound/tally_sheet/download')); ?>/" + data_id;
        });

        $('body').on('click', '#tally-download-detail', function() {
            var data_id = $('#id').val();

            window.open("<?php echo e(url('/export/inbound/tally_sheet/detail/')); ?>" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-summary', function() {
            var data_id = $('#id').val();

            window.open("<?php echo e(url('/export/inbound/tally_sheet/summary/')); ?>" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        function submitData() {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this data!",
                icon: "warning",
                buttons: [
                    'No, cancel it!',
                    'Yes, I am sure!'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        data: {
                            job_id: $("#id").val()
                        },
                        url: "<?php echo e(route('export-inbound.submit')); ?>",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            $("#loader").show();
                        },
                        success: function(data) {
                            $("#loader").hide();
                            if ($.isEmptyObject(data.error)) {
                                swal({
                                    icon: "success",
                                    text: "Data was processed successfully."
                                });

                                window.location.reload();
                            } else {
                                swal({
                                    icon: "error",
                                    text: data.error
                                });
                            }
                        },
                        error: function(data) {
                            console.log(data);
                            $("#loader").hide();
                        }
                    });
                } else {

                }
            })
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/transaction/export/inbound/show.blade.php ENDPATH**/ ?>