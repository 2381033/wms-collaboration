

<?php $__env->startSection('title'); ?>
    Outbound
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Outbound</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Outbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Branch Name</label>
                        <select class="custom-select" id="branch_id" name="branch_id">
                            <?php $__currentLoopData = Auth::user()->branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->branch_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Principal Name</label>
                        <select class="custom-select" id="principal_id" name="principal_id">
                            <?php $__currentLoopData = Auth::user()->principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="text" id="date_from" name="date_from" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="text" id="date_to" name="date_to" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="custom-select" id="status_code" name="status_code">
                            <option value="O">Open</option>
                            <option value="A">Confirmed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gate-access', 'warehouse/outbound')): ?>
                            <a href="<?php echo e(url('/warehouse/outbound/create/0')); ?>" class="btn btn-primary btn-sm" id="btn-add"><i
                                    class="fas fa-plus"></i> <span>Add New Job</span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Outbound Number</th>
                                    <th>Outbound Created At</th>
                                    <th>Outbound Confirm Date</th>
                                    <th>Description</th>
                                    <th>Vehicle No</th>
                                    <th>Status</th>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function kasihNol(data) {
            if (data < 10) {
                return '0' + data;
            } else {
                return data;
            }
        }

        function formatTanggalIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

        $(function() {
            var today = getFirstDate(),
                lastDay = getLastDate();

            $('#date_from').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", today);

            $('#date_to').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", lastDay);
        });

        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            load_data();

            $('#refresh').click(function() {
                var principal_id = $('#principal_id').val();

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

            function load_data(principal = '') {
                $('#table_list').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?php echo e(route('outbound-job.index')); ?>",
                        type: "GET",
                        data: {
                            branch_id: $('#branch_id').val(),
                            principal_id: $('#principal_id').val(),
                            date_from: $('#date_from').val(),
                            date_to: $('#date_to').val(),
                            status_code: $('#status_code').val()
                        }
                    },
                    columns: [{
                            data: 'job_no',
                            name: 'job_no'
                        },
                        {
                            data: 'job_date',
                            name: 'job_date',
                            sortable: false,
                            render: function(job_date) {
                                return formatTanggalIndonesia2(job_date);
                            }
                        },
                        {
                            data: 'confirmed_date',
                            name: 'confirmed_date',
                            sortable: false,
                            render: function(confirmed_date) {
                                if (confirmed_date == null) {
                                    var confirm = '-';
                                } else {
                                    var confirm = formatTanggalIndonesia2(confirmed_date);
                                }
                                return confirm;
                            }
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'vehicle_no',
                            name: 'vehicle_no'
                        },
                        {
                            data: 'confirmed_flag',
                            name: 'confirmed_flag'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/transaction/outbound/index.blade.php ENDPATH**/ ?>