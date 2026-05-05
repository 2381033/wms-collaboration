
<?php $__env->startSection('title', 'MKT - Freeze Stock DC'); ?>
<?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(url('/')); ?>assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <form action="" method="post" id="formFreezeStock">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select class="form-control" name="branch_id" required id="">
                                        <?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->branch_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Principal</label>
                                    <select class="form-control" name="principal_id" required id="principalSelect">
                                        <?php $__currentLoopData = $principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Freeze Activity</label>
                                    <select class="form-control" name="activity" required>
                                        <option value="INBOUND">INBOUND</option>
                                        <option value="OUTBOUND">OUTBOUND</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Reason/Body Email</label>
                                    <textarea class="form-control" name="body_email" id="" rows="5" required>Dear Operasional Team,</textarea>
                                </div>
                                <div class="float-right">
                                    <button class="btn btn-md btn-dark">
                                        <i class="fas fa-save"></i>
                                        Submit And Freeze Now!
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <hr>
            <div class="card">
                <div class="card-body">
                    <table class="table table-vertical-center">
                        <thead>
                            <tr>
                                <th colspan="3">Data Freeze Stock DC</th>
                            </tr>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Branch</th>
                                <th>Principal</th>
                                <th>Freeze Date</th>
                                <th>Activity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="text-center">
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($item->branch_name); ?></td>
                                    <td><?php echo e($item->principal_name); ?></td>
                                    <td><?php echo e(Carbon\Carbon::parse($item->created_at)->format('d-m-Y')); ?></td>
                                    <td>
                                        <span class="badge badge-danger"> <i class="fas fa-lock text-white"></i> Freeze
                                        </span>
                                    </td>
                                    <td><?php echo e($item->freeze_activity); ?></td>
                                    <td>
                                        <a href="#"
                                            onclick="unfreezeNow('<?php echo e($item->principal_id); ?>', '<?php echo e($item->branch_id); ?>', '<?php echo e($item->id); ?>')"
                                            class="btn btn-sm btn-success" id="unfreezeBtn">
                                            <i class="fas fa-unlock"></i> Unfreeze Now!
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-unfreeze" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form method="post" id="formUnfreezeStock">
                    <?php echo csrf_field(); ?>
                    <div>
                        <div class="modal-body">
                            <input type="hidden" name="branch_id_unf" id="branch_id_unf">
                            <input type="hidden" name="principal_id_unf" id="principal_id_unf">
                            <input type="hidden" name="id_unf" id="id_unf">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="">Reason/Body Email</label>
                                        <textarea class="form-control" name="mail_body" id="" rows="5" required>Dear Operasional Team,</textarea>
                                    </div>
                                    <div class="float-right">
                                        <button class="btn btn-md btn-success btn-un">
                                            <i class="fas fa-save"></i>
                                            Submit And Unreeze Now!
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(url('/')); ?>/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#principalSelect').select2({
            width: '100%',
        });

        function unfreezeNow(principal_id, branch_id, id) {
            $('#modal-unfreeze').modal('show')
            $('#principal_id_unf').val(principal_id)
            $('#branch_id_unf').val(branch_id)
            $('#id_unf').val(id)
        }

        $('#formUnfreezeStock').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin melakukan unfreeze stock ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('unfreezeStockDC')); ?>",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('.btn-un').attr('disabled', 'disabled');
                            $('.btn-un').html(
                                '<i class="fa fa-spinner fa-spin"></i> Please wait...');
                        },
                        success: function(response) {
                            if (response.message == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText,
                            });
                        },
                        complete: function() {
                            $('.btn-un').removeAttr('disabled');
                            $('.btn-un').html(
                                '<i class="fas fa-save"></i> Submit And UnFreeze Now!');
                        }
                    });
                }
            });
        });

        $('#formFreezeStock').on('submit', function(e) {
            e.preventDefault();
            let dataForm = new FormData(this);
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin melakukan freeze stock ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('freezeStockDC')); ?>",
                        data: dataForm,
                        processData: false,
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('.btn').attr('disabled', 'disabled');
                            $('.btn').html(
                                '<i class="fa fa-spinner fa-spin"></i> Please wait...');
                        },
                        success: function(response) {
                            if (response.message == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText,
                            });
                        },
                        complete: function() {
                            $('.btn').removeAttr('disabled');
                            $('.btn').html(
                                '<i class="fas fa-save"></i> Submit And Freeze Now!');
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.new.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/new/FreezeStockDC/index.blade.php ENDPATH**/ ?>