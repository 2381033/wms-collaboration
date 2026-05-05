
<?php $__env->startSection('title', 'MKT - Inbound Planning DC'); ?>

<?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(url('/')); ?>assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="main-body">
            <div class="card card-custom" style="border-radius: 15px;" id="kt_card_3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 mb-4">
                            <div class="float-left">
                                <a href="<?php echo e(url('warehouse/inboundPlanningDC/downloadTemplate')); ?>"
                                    class="btn btn-md text-white" style="background-color: green;"> <i
                                        class="fas fa-file-excel text-white"></i>
                                    Download Template
                                </a>
                            </div>
                            <div class="float-right">
                                <input type="file" id="excel-upload" class="form-control-file" accept=".xlsx, .xls">
                                <button id="btn-upload" class="btn btn-dark mt-2"> <i class="fas fa-upload"></i> Upload
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tbl-stock" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Product Code</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Uom</th>
                                        <?php for($i = 0; $i < 4; $i++): ?>
                                            <th class="text-center" style="background-color: yellow">IP <?php echo e($i + 1); ?>

                                            </th>
                                            <th class="text-center" style="background-color: yellow">Week
                                                <?php echo e($i + 1); ?>

                                            </th>
                                        <?php endfor; ?>
                                        <th class="text-center">#</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(url('/')); ?>/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script>
        $(document).ready(function() {
            $('#btn-upload').click(function() {
                let file = $('#excel-upload').prop('files')[0];
                if (!file) {
                    Swal.fire("Error", "Pilih file terlebih dahulu", "error");
                    return;
                }

                let formData = new FormData();
                formData.append('file', file);
                formData.append('_token', "<?php echo e(csrf_token()); ?>");

                $.ajax({
                    url: "<?php echo e(url('warehouse/inboundPlanningDC/upload')); ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        Swal.fire('Success', res.message, 'success');
                        loadData();
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON;
                        Swal.fire('Error', err.message, 'error');
                    }
                });
            });

            loadData();

            function getRowStatus(row) {
                let values = [
                    row.ip_1, row.week_1,
                    row.ip_2, row.week_2,
                    row.ip_3, row.week_3,
                    row.ip_4, row.week_4
                ];

                let filled = values.filter(v => v && v > 0).length;
                let total = values.length;

                let hasError = values.some(v => v < 0);

                if (hasError) return 'error';
                if (filled === 0) return 'empty';
                if (filled < total) return 'partial';
                return 'filled';
            }

            function loadData() {
                let table = $('#tbl-stock').DataTable({
                    ordering: false,
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    ajax: {
                        url: "<?php echo e(url('warehouse/inboundPlanningDC/getListStock')); ?>",
                        type: "GET",
                        dataSrc: function(res) {
                            return res.data.map(row => {
                                for (let i = 1; i <= 4; i++) {
                                    row['ip_' + i] = '';
                                    row['week_' + i] = '';
                                }
                                let planning = row.planning;
                                if (typeof planning === 'string') {
                                    try {
                                        planning = JSON.parse(planning);
                                    } catch (e) {
                                        planning = [];
                                    }
                                }
                                if (Array.isArray(planning)) {
                                    planning.forEach((plan, index) => {
                                        if (index < 4) {
                                            row['ip_' + (index + 1)] = plan.ip;
                                            row['week_' + (index + 1)] = plan.week;
                                        }
                                    });
                                }

                                return row;
                            });
                        }
                    },

                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: 'product_code'
                        },
                        {
                            data: 'product_name'
                        },
                        {
                            data: 'qtys'
                        },
                        {
                            data: 'puom'
                        },

                        ...[1, 2, 3, 4].flatMap(i => ([{
                                data: 'ip_' + i,
                                render: (data, type, row) => `
                        <input type="number" min="1"
                            style="width:80px"
                            class="form-control form-control-sm text-center ip-input"
                            data-id="${row.id}"
                            value="${data ?? ''}">
                    `
                            },
                            {
                                data: 'week_' + i,
                                render: (data, type, row) => `
                        <input type="number" min="1"
                            style="width:80px"
                            class="form-control form-control-sm text-center week-input"
                            data-id="${row.id}"
                            value="${data ?? ''}">
                    `
                            }
                        ])),

                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                        <div class="text-center">
                            <button class="btn btn-sm btn-dark save-row"
                                data-id="${row.id}">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    `;
                            }
                        }
                    ]
                });
            }

            $(document).on('input', '.ip-input, .week-input', function() {
                let v = $(this).val();
                v = v.replace('-', '');
                $(this).val(v);
            });

            $(document).on('click', '.save-row', function() {

                let btn = $(this);
                let tr = btn.closest('tr');
                let id = btn.data('id');

                let plans = [];

                for (let i = 1; i <= 4; i++) {

                    let ip = tr.find('.ip-input').eq(i - 1).val();
                    let week = tr.find('.week-input').eq(i - 1).val();

                    if (ip && week) {
                        plans.push({
                            ip: parseInt(ip),
                            week: parseInt(week)
                        });
                    }
                }
                if (plans.length === 0) {
                    Swal.fire('Warning', 'Minimal 1 data harus diisi', 'warning');
                    return;
                }
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url: "<?php echo e(route('updateInboundPlanningDC')); ?>",
                    type: "POST",
                    data: {
                        id: id,
                        plans: plans,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function() {
                        Swal.fire('Success', 'Data updated!', 'success');
                        loadData();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false)
                            .html('<i class="fas fa-save"></i>');
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.new.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/new/InboundPlanningDC/index.blade.php ENDPATH**/ ?>