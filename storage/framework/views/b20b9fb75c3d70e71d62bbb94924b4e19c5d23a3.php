

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

    .table {
        font-size: 18px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid rgb(0, 0, 0);
    }

    .table tbody tr td {
        text-align: left;
        padding: 5px;
        border: 1px solid rgb(0, 0, 0);
    }

    .table .center {
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
                                <div class="text-left">
                                    <a href="<?php echo e(url('/export/inbound/create/0')); ?>" class="btn btn-primary btn-sm"><i
                                            class="fas fa-plus"></i> <span>Add New Job</span>
                                    </a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_no">Job Number</label>
                                    <input type="text" autocomplete="off" id="Job_no" name="job_no"
                                        <?php if(isset($header->job_no)): ?> value="<?php echo e($header->job_no); ?>" <?php endif; ?>
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_date">Job Date</label>
                                    <input type="text" autocomplete="off" id="job_date" name="job_date"
                                        <?php if(isset($header->job_date)): ?> value="<?php echo e(\Carbon\Carbon::parse($header->job_date)->format('d-m-Y')); ?>" <?php endif; ?>
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select id="my-select" required class="form-control" name="branch_id">
                                        <?php if($header != null): ?>
                                            <option value="<?php echo e($branchme->id); ?>" readonly>
                                                <?php echo e($branchme->branch_name); ?>

                                            </option>
                                        <?php else: ?>
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                            <?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->branch_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <?php if(!isset($header->id)): ?>
                                <div class="col-sm-12 mb-2" style="border-radius: 17px; outline-color: black solid">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <a href="javascript:void(0)" onclick="previewPalletTag()" class="text-dark">
                                                    <i class="fas fa-eye"></i> Preview Pallet
                                                    Tag</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-12">
                                <?php if(isset($header->id)): ?>
                                    <a id="pallet-print" class="btn btn-dark text-white btn-block"><i class="fas fa-print"
                                            style="border-radius: 12px;"></i> Pallet Tag
                                        Print</a>
                                <?php else: ?>
                                    <button type="submit" id="btn-save-job"
                                        class="btn btn-success btn-block rounded-2 hide saveJob"><i class="fas fa-save"></i>
                                        <span>Save</span></button>
                                <?php endif; ?>
                                <?php if(isset($header)): ?>
                                    <?php if($header->status_flag == 'Open'): ?>
                                        <a class="btn btn-info btn-block text-white" href="#addQty" data-toggle="modal"><i
                                                class="fas fa-plus"></i> Add/Update Qty</a>
                                        <hr>
                                        <a href="javascript:void(0)" onclick="submitData()" class="btn btn-success btn-block"><i
                                                class="fas fa-check-circle"></i> <span>
                                                Konfirm</span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-8">
                        <div class="row ">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="hidden" id="forwarder_id" name="forwarder_id"
                                        <?php if(isset($header->forwarder_id)): ?> value="<?php echo e($header->forwarder_id); ?>" <?php endif; ?>>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.."
                                        id="forwarder_name" name="forwarder_name" class="form-control" required
                                        <?php if(isset($header->forwarder_name)): ?> value="<?php echo e($header->forwarder_name); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
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
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
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
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="po_number">PO Number</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="po_number" name="po_number"
                                        <?php if(isset($header->po_number)): ?> value="<?php echo e($header->po_number); ?>" <?php endif; ?>
                                        class="form-control"
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PEB No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="peb_no"
                                        name="peb_no" class="form-control" required
                                        <?php if(isset($header->peb_no)): ?> value="<?php echo e($header->peb_no); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>AJU No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="aju_no"
                                        name="aju_no" class="form-control" required
                                        <?php if(isset($header->aju_no)): ?> value="<?php echo e($header->aju_no); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="vehicle_no">Vehicle No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="vehicle_no"
                                        <?php if(isset($header->vehicle_no)): ?> value="<?php echo e($header->vehicle_no); ?>" <?php endif; ?>
                                        class="form-control"
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="destination" name="destination" class="form-control"
                                        <?php if(isset($header->destination)): ?> value="<?php echo e($header->destination); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Checker</label>
                                    <select id="checkerName" style="width: 100%;" required class="form-control"
                                        name="pic_name">
                                        <?php if($header != null): ?>
                                            <option value="<?php echo e($header->pic_name); ?>" selected><?php echo e($header->pic_name); ?>

                                                <?php $__currentLoopData = $checker; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            </option>
                                            <option value="<?php echo e($item->name); ?>"><?php echo e($item->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <option value="" disabled selected>SILAHKAN PILIH</option>
                                        <?php $__currentLoopData = $checker; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->name); ?>"><?php echo e($item->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Document</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="qty_cargo" name="qty_cargo" class="form-control"
                                        <?php if(isset($header->qty_cargo)): ?> value="<?php echo e($header->qty_cargo); ?>" onchange="setQtyCargo(this.value)" <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Actual</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="qty_actual" name="qty_actual" class="form-control"
                                        <?php if(isset($header->qty_actual)): ?> value="<?php echo e($header->qty_actual); ?>" onchange="setQtyActual(this.value)" <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Volume ( CBM )</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="cbm"
                                        name="cbm" class="form-control" required
                                        <?php if(isset($header->cbm)): ?> value="<?php echo e($header->cbm); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Weight ( Kg )</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="weight"
                                        name="weight" class="form-control" required
                                        <?php if(isset($header->weight)): ?> value="<?php echo e($header->weight); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Total Pallet</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.."
                                        id="total_pallet" name="total_pallet" class="form-control" required
                                        <?php if(isset($header->total_pallet)): ?> value="<?php echo e($header->total_pallet); ?>" <?php endif; ?>
                                        <?php if(isset($header->id)): ?> <?php if($header->status_flag !== 'Open'): ?> disabled <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">GATE IN </span>
                                    </div>
                                    <input class="form-control floating-label" type="text" name="gate_in" required
                                        placeholder="Click for input" id="gate_in"
                                        <?php if(isset($header->gate_in)): ?> <?php if(!is_null($header->gate_in)): ?> value="<?php echo e(\Carbon\Carbon::parse($header->gate_in)->format('d/m/Y H:i')); ?>" <?php endif; ?> <?php endif; ?>>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">Tanggal Bongkar</span>
                                    </div>
                                    <input class="form-control" type="date" name="tgl_bongkar" required
                                        placeholder="" id="tglBongkar" aria-label="Recipient's "
                                        <?php if($header != null): ?> value="<?php echo e($header->tgl_bongkar); ?>" <?php else: ?> value="<?php echo e(date('Y-m-d')); ?>" <?php endif; ?>>
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
                                    <form id="form-detail" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" id="job_id" name="job_id"
                                            <?php if(isset($header->id)): ?> value="<?php echo e($header->id); ?>" <?php endif; ?> />
                                        <div class="table-responsive">
                                            <table id="table-detail" class="table table-striped table-bordered table-sm"
                                                style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Pallet ID</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <?php if(isset($header->id)): ?>
                                            <?php if($header->status_flag == 'Open'): ?>
                                                <div class="float-right">
                                                    <button type="submit" id="btn-update" class="btn btn-success btn-sm"><i
                                                            class="fas fa-save"></i>
                                                        <span>Update</span></button>
                                                </div>
                                            <?php endif; ?>
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

    <?php if($header != null): ?>
        <?php if($header->status_flag != 'Confirmed'): ?>
            <a href="javascript:void(0)" class="float" onclick="updateHeader()">
                <i class="fa fa-edit my-float"></i>
                <span class="tooltiptext">Update Header</span>
            </a>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $('#gate_in').bootstrapMaterialDatePicker({
            format: 'DD/MM/YYYY HH:mm'
        });
        $('#checkerName').select2();

        function setQtyCargo(val) {
            $('#qty_cargo').val(val);
        }

        function setQtyActual(value) {
            $('#qty_actual').val(value);
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

            load_detail();

            function load_detail() {
                var job_id = $('#job_id').val();

                $('#table-detail').DataTable().destroy();
                $('#table-detail').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    searching: false,
                    destroy: true,
                    info: false,
                    ajax: {
                        url: "<?php echo e(route('export-detail.index')); ?>",
                        type: "GET",
                        data: {
                            job_id: job_id
                        }
                    },
                    columns: [{
                            data: 'pallet_id',
                            name: 'pallet_id'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
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

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi - Copy\resources\views/transaction/export/inbound/create.blade.php ENDPATH**/ ?>