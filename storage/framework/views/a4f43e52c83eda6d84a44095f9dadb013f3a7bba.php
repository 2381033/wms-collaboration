

<?php $__env->startSection('title'); ?>
    Product
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Product</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Product</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Principal Name</label>
                        <select class="custom-select" id="principal_filter" name="principal_filter">
                            <?php $__currentLoopData = Auth::user()->principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add"><i
                                class="fas fa-plus"></i> <span>Add New</span></a>
                    </div>
                    <div class="float-right">
                        <a href="#modal-upload" data-toggle="modal" class="btn btn-success btn-md"><i
                                class="fas fa-file-excel"></i>
                            <span>
                                Upload
                                Excel
                            </span>
                        </a>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-entry">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-entry" name="form-entry" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Principal Name</label>
                                    <select class="custom-select" id="principal_id" name="principal_id">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = Auth::user()->principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Group Name</label>
                                    <select class="custom-select" id="group_id" name="group_id">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Brand Name</label>
                                    <select class="custom-select" id="brand_id" name="brand_id">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category Name</label>
                                    <select class="custom-select" id="category_id" name="category_id">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Manufactur Name</label>
                                    <select class="custom-select" id="manufactur_id" name="manufactur_id">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pick Criteria</label>
                                    <select class="custom-select" id="pick_criteria" name="pick_criteria">
                                        <option value="">.:Select:.</option>
                                        <option value="FEFO">First Expiry First Out (FEFO)</option>
                                        <option value="FIFO">First In First Out (FIFO)</option>
                                        <option value="LEFO">Last Expiry First Out (LEFO)</option>
                                        <option value="LIFO">Last In First Out (LIFO)</option>
                                        <option value="FMFO">First Manufacturer First Out (FMFO)</option>
                                        <option value="BATCH">Lot No / Batch No</option>
                                        <option value="DOCREF">Document Reference</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" autocomplete="off" id="product_code" name="product_code"
                                        class="form-control">
                                    <?php if($errors->has('product_code')): ?>
                                        <span class="help-block"><?php echo e($errors->first('product_code')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" autocomplete="off" id="product_name" name="product_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit Level</label>
                                    <select class="custom-select" id="unit_level" name="unit_level">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3" selected>3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>UPPP</label>
                                    <input type="text" autocomplete="off" id="uppp" name="uppp"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>1st Unit</label>
                                    <select class="custom-select" id="puom" name="puom">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Middle UPPP</label>
                                    <input type="text" autocomplete="off" id="muppp" name="muppp"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>2nd Unit</label>
                                    <select class="custom-select" id="muom" name="muom">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>3rd Unit</label>
                                    <select class="custom-select" id="buom" name="buom">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Length</label>
                                    <input type="text" autocomplete="off" id="length" name="length"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Width</label>
                                    <input type="text" autocomplete="off" id="width" name="width"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Height</label>
                                    <input type="text" autocomplete="off" id="height" name="height"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <select class="custom-select" id="dimensions_unit" name="dimensions_unit">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Temperature</label>
                                    <input type="text" autocomplete="off" id="temperature" name="temperature"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Shelf Life</label>
                                    <input type="text" autocomplete="off" id="shelf_life" name="shelf_life"
                                        value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Volume</label>
                                    <input type="text" autocomplete="off" id="volume" name="volume"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <select class="custom-select" id="volume_unit" name="volume_unit">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Gross Weight</label>
                                    <input type="text" autocomplete="off" id="gross_weight" name="gross_weight"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Net Weight</label>
                                    <input type="text" autocomplete="off" id="net_weight" name="net_weight"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <select class="custom-select" id="weight_unit" name="weight_unit">
                                        <option value="">.:Select:.</option>
                                        <?php $__currentLoopData = $unit_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->code); ?>"><?php echo e($item->uom_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Auto Freeze Day</label>
                                    <input type="text" autocomplete="off" id="freeze_day" name="freeze_day"
                                        value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Base Price</label>
                                    <input type="text" autocomplete="off" id="base_price" name="base_price"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Lot No Required</label>
                                    <select class="custom-select" id="batch_flag" name="batch_flag">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mfg / Exp Date Required</label>
                                    <select class="custom-select" id="expired_flag" name="expired_flag">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Auto Freeze on Inbound</label>
                                    <select class="custom-select" id="freeze_flag" name="freeze_flag">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="custom-select" id="active" name="active">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save"><i class="fas fa-save"></i>
                            <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="pallet-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pallet Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="pallet_table" class="table table-striped table-bordered table-sm"
                                    style="width:100%;" cellspacing="0" width="100%">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Location Type</th>
                                            <th>Pallet Qty</th>
                                            <th>Unit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary btn-sm" id="add-pallet-btn"><i
                            class="fas fa-plus"></i> <span>Add</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="add-pallet-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-pallet" name="form-pallet" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Pallet Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <?php echo csrf_field(); ?>
                            <div class="col-md-8">
                                <input type="hidden" id="product_unit" name="product_unit">
                                <input type="hidden" id="pallet_id" name="pallet_id">
                                <div class="form-group">
                                    <label>Location Type</label>
                                    <select name="type_id" id="type_id" class="custom-select">
                                        <?php $__currentLoopData = $location_type_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->description); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pallet Qty</label>
                                    <input type="text" autocomplete="off" id="pallet_qty" name="pallet_qty"
                                        value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-save-pallet"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-upload">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="post-excel" action="<?php echo e(route('upload-product-master')); ?>" method="post"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">FORM UPLOAD EXCEL
                            <a href="javascript:void(0)" onclick="downloadExcel()" class="btn btn-md btn-dark"><i
                                    class="fas fa-download"></i>
                                Template</a>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select id="my-select" class="form-control" name="principal" required>
                                        <option value="" selected disabled>PRINCIPAL</option>
                                        <?php $__currentLoopData = Auth::user()->principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">PILIH FILE</span>
                                    </div>
                                    <input class="form-control" type="file" name="file"
                                        placeholder="Recipient's text" aria-label="Recipient's "
                                        aria-describedby="my-addon" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        
                        <button type="submit" class="btn btn-success btn-md"><i class="fas fa-upload"></i>
                            <span>Upload</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function downloadExcel() {
            var url = "<?php echo e(asset('assets/template-upload-product.xlsx')); ?>";
            window.open(url, '_blank');
        }

        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            load_data();

            function load_data(principal = '') {
                $('#table_list').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?php echo e(route('product.index')); ?>",
                        type: "GET",
                        data: {
                            principal_id: $('#principal_filter').val()
                        }
                    },
                    columns: [{
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            $('#refresh').click(function() {
                var principal_id = $('#principal_filter').val();

                if (principal_id != '' && principal_id > 0) {
                    $('#table_list').DataTable().destroy();
                    load_data();
                } else {
                    swal({
                        icon: "error",
                        text: "Principal name cannot be empty."
                    });
                }
            });

            $('#principal_id').on('change', function() {
                var principal_id = this.value;
                $("#category_id").html('');
                $("#group_id").html('');
                $("#manufactur_id").html('');
                $.ajax({
                    url: "<?php echo e(route('product.reference')); ?>",
                    type: "GET",
                    data: {
                        principal_id: principal_id,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#category_id').html('<option value="">.:Select:.</option>');
                        $.each(result.category_list, function(key, value) {
                            $("#category_id").append('<option value="' + value.id +
                                '">' + value.category_name + '</option>');
                        });
                        $('#group_id').html('<option value="">.:Select:.</option>');
                        $.each(result.group_list, function(key, value) {
                            $("#group_id").append('<option value="' + value.id + '">' +
                                value.group_name + '</option>');
                        });
                        $('#manufactur_id').html('<option value="">.:Select:.</option>');
                        $.each(result.manufactur_list, function(key, value) {
                            $("#manufactur_id").append('<option value="' + value.id +
                                '">' + value.manufactur_name + '</option>');
                        });
                    }
                });
            });

            $('#group_id').on('change', function() {
                var principal_id = $('#principal_id').val();
                var group_id = this.value;
                $("#brand_id").html('');
                $.ajax({
                    url: "<?php echo e(route('product.brand')); ?>",
                    type: "GET",
                    data: {
                        principal_id: principal_id,
                        group_id: group_id,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#brand_id').html('<option value="">.:Select:.</option>');
                        $.each(result.brand_list, function(key, value) {
                            $("#brand_id").append('<option value="' + value.id + '">' +
                                value.brand_name + '</option>');
                        });
                    }
                });
            });

            $('#unit_level').on('change', function() {
                var unit_level = this.value;

                if (unit_level == 1) {
                    document.getElementById("muom").className = 'disabled custom-select';
                    document.getElementById("buom").className = 'disabled custom-select';

                    $("#muom").find("option").prop("hidden", true);
                    $("#buom").find("option").prop("hidden", true);
                } else if (unit_level == 2) {
                    document.getElementById("muom").className = 'disabled custom-select';
                    document.getElementById("buom").className = 'custom-select';

                    $("#muom").find("option").prop("hidden", true);
                    $("#buom").find("option").prop("hidden", false);
                } else {
                    document.getElementById("muom").className = 'custom-select';
                    document.getElementById("buom").className = 'custom-select';

                    $("#muom").find("option").prop("hidden", false);
                    $("#buom").find("option").prop("hidden", false);
                }
            });

            $('#puom').on('change', function() {
                var unit_level = $('#unit_level').val();
                var puom = this.value;

                if (unit_level == 1) {
                    $('#muom').val(puom);
                    $('#buom').val(puom);
                } else {
                    $('#muom').val('');
                    $('#buom').val('');
                }
            });

            $('#buom').on('change', function() {
                var unit_level = $('#unit_level').val();
                var buom = this.value;

                if (unit_level == 2) {
                    $('#muom').val(buom);
                }
            });

            $('#btn-add').click(function() {
                $('#id').val('');
                $('#form-entry').trigger("reset");
                $('#modal-title').html("Add New");
                $('#modal-entry').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $(document).on('click', '.delete', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('product')
                $('#modal-konfirmasi').modal('show');
            });

            $('body').on('click', '.edit-data', function() {
                var data_id = $(this).data('id');
                $.get('product/' + data_id + '/edit', function(data) {
                    $('#modal-title').html("Edit");
                    $('#btn-save').val("Edit");
                    $('#modal-entry').modal('show');


                    $('#category_id').html('<option value="">.:Select:.</option>');
                    $.each(data.category_list, function(key, value) {
                        $("#category_id").append('<option value="' + value.id + '">' + value
                            .category_name + '</option>');
                    });
                    $('#group_id').html('<option value="">.:Select:.</option>');
                    $.each(data.group_list, function(key, value) {
                        $("#group_id").append('<option value="' + value.id + '">' + value
                            .group_name + '</option>');
                    });
                    $('#manufactur_id').html('<option value="">.:Select:.</option>');
                    $.each(data.manufactur_list, function(key, value) {
                        $("#manufactur_id").append('<option value="' + value.id + '">' +
                            value.manufactur_name + '</option>');
                    });
                    $('#brand_id').html('<option value="">.:Select:.</option>');
                    $.each(data.brand_list, function(key, value) {
                        $("#brand_id").append('<option value="' + value.id + '">' + value
                            .brand_name + '</option>');
                    });

                    var unit_level = data.edit_view.unit_level;

                    if (unit_level == 1) {
                        document.getElementById("muom").className = 'disabled custom-select';
                        document.getElementById("buom").className = 'disabled custom-select';

                        $("#muom").find("option").prop("hidden", true);
                        $("#buom").find("option").prop("hidden", true);
                    } else if (unit_level == 2) {
                        document.getElementById("muom").className = 'disabled custom-select';
                        document.getElementById("buom").className = 'custom-select';

                        $("#muom").find("option").prop("hidden", true);
                        $("#buom").find("option").prop("hidden", false);
                    } else {
                        document.getElementById("muom").className = 'custom-select';
                        document.getElementById("buom").className = 'custom-select';

                        $("#muom").find("option").prop("hidden", false);
                        $("#buom").find("option").prop("hidden", false);
                    }

                    $('#id').val(data.edit_view.id);
                    $('#principal_id').val(data.edit_view.principal_id);
                    $('#product_code').val(data.edit_view.product_code);
                    $('#product_name').val(data.edit_view.product_name);
                    $('#group_id').val(data.edit_view.group_id);
                    $('#brand_id').val(data.edit_view.brand_id);
                    $('#category_id').val(data.edit_view.category_id);
                    $('#manufactur_id').val(data.edit_view.manufactur_id);
                    $('#pick_criteria').val(data.edit_view.pick_criteria);
                    $('#unit_level').val(data.edit_view.unit_level);
                    $('#uppp').val(data.edit_view.uppp);
                    $('#muppp').val(data.edit_view.muppp);
                    $('#puom').val(data.edit_view.puom).trigger('change');
                    $('#muom').val(data.edit_view.muom);
                    $('#buom').val(data.edit_view.buom);
                    $('#length').val(data.edit_view.length);
                    $('#width').val(data.edit_view.width);
                    $('#height').val(data.edit_view.height);
                    $('#dimensions_unit').val(data.edit_view.dimensions_unit);
                    $('#volume').val(data.edit_view.volume);
                    $('#volume_unit').val(data.edit_view.volume_unit);
                    $('#gross_weight').val(data.edit_view.gross_weight);
                    $('#net_weight').val(data.edit_view.net_weight);
                    $('#weight_unit').val(data.edit_view.weight_unit);
                    $('#temperature').val(data.edit_view.temperature);
                    $('#shelf_life').val(data.edit_view.shelf_life);
                    $('#freeze_day').val(data.edit_view.freeze_day);
                    $('#base_price').val(data.edit_view.base_price);
                    $('#batch_flag').val(data.edit_view.batch_flag);
                    $('#expired_flag').val(data.edit_view.expired_flag);
                    $('#freeze_flag').val(data.edit_view.freeze_flag);
                    $('#active').val(data.edit_view.active);
                })
            });

            if ($("#form-entry").length > 0) {
                $("#form-entry").validate({
                    submitHandler: function(form) {
                        var actionType = $('#btn-save').val();
                        $('#btn-save').html('Sending..');

                        $.ajax({
                            data: $('#form-entry').serialize(),
                            url: "<?php echo e(route('product.store')); ?>",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-entry').trigger("reset");
                                    $('#modal-entry').modal('hide');
                                    $('#btn-save').html('Save');
                                    var oTable = $('#table_list').dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
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
                                    $('#btn-save').html('Save');
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $('#btn-save').html('Save');
                            }
                        });
                    }
                })
            }

            $('#btn-delete').click(function() {
                var action = $('#action-delete').val();
                var requestUrl = "";

                if (action == 'product') {
                    requestUrl = "product/" + dataId;
                } else if (action == 'pallet') {
                    requestUrl = "pallet-unit/" + dataId;
                }

                $.ajax({
                    url: requestUrl,
                    type: 'delete',
                    data: {
                        "_token": "<?php echo e(csrf_token()); ?>"
                    },
                    beforeSend: function() {
                        $('#btn-delete').text('Sending..');
                    },
                    success: function(data) {
                        setTimeout(function() {
                            $('#modal-konfirmasi').modal('hide');

                            var oTable = "";
                            if (action == 'product') {
                                oTable = $('#table_list').dataTable();
                            } else if (action == 'pallet') {
                                oTable = $('#pallet_table').dataTable();
                            }

                            oTable.fnDraw(false);
                        });

                        if ($.isEmptyObject(data.error)) {
                            swal({
                                icon: "success",
                                text: "Data Successfully Deleted."
                            });
                        } else {
                            swal({
                                icon: "error",
                                text: data.error
                            });
                        }
                    },
                    error: function(data) {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                })
            });

            $(document).on('click', '.pallet', function() {
                dataId = $(this).attr('id');
                $('#product_unit').val(dataId);
                $('#pallet_table').DataTable().destroy();
                $('#pallet_table').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    paging: false,
                    info: false,
                    ajax: {
                        url: "<?php echo e(route('pallet-unit.index')); ?>",
                        type: "GET",
                        data: {
                            product_id: dataId
                        }
                    },
                    columns: [{
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'pallet_qty',
                            name: 'pallet_qty'
                        },
                        {
                            data: 'uom',
                            name: 'uom'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });

                $('#pallet-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $(document).on('click', '.delete-pallet', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('pallet');
                $('#modal-konfirmasi').modal('show');
            });

            $('#add-pallet-btn').click(function() {
                $('#pallet_id').val('');
                $('#add-pallet-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('body').on('click', '.edit-pallet', function() {
                var data_id = $(this).data('id');
                $.get('pallet-unit/' + data_id + '/edit', function(data) {
                    $('#btn-save-pallet').val("Edit");
                    $('#add-pallet-modal').modal('show');

                    $('#pallet_id').val(data.id);
                    $('#principal_id').val(data.principal_id);
                    $('#product_unit').val(data.product_id);
                    $('#type_id').val(data.type_id);
                    $('#pallet_qty').val(data.pallet_qty);
                })
            });

            if ($("#form-pallet").length > 0) {
                $("#form-pallet").validate({
                    submitHandler: function(form) {
                        var actionType = $('#btn-save-pallet').val();
                        $('#btn-save-pallet').html('Sending..');

                        $.ajax({
                            data: $('#form-pallet').serialize(),
                            url: "<?php echo e(route('pallet-unit.store')); ?>",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-pallet').trigger("reset");
                                    $('#add-pallet-modal').modal('hide');
                                    $('#btn-save-pallet').html('Simpan');
                                    var oTable = $('#pallet_table').dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "error",
                                        text: "Data Successfully Saved."
                                    });
                                } else {
                                    var pesan = data.error;

                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = pesan;
                                    swal({
                                        icon: "error",
                                        content: wrapper
                                    });
                                    $('#btn-save-pallet').html('Save');
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $('#btn-save-pallet').html('Save');
                            }
                        });
                    }
                })
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/master/product.blade.php ENDPATH**/ ?>