
<?php $__env->startSection('title', 'MKT - OB Export'); ?>
<?php $__env->startPush('styles'); ?>
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
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card konten" style="outline: solid; black; border-radius: 15px;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required value="<?php echo e(date('Y-m-01')); ?>"
                                                id="startDate">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required value="<?php echo e(date('Y-m-t')); ?>"
                                                id="endDate">
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <select id="statusJob" class="form-control">
                                                    <option value="open" selected>Open</option>
                                                    <option value="confirmed">Confirmed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <a href="#" onclick="searchData()" class="btn btn-block btn-dark"><i
                                                    class="fas fa-search"></i>
                                            </a>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="float-right">
                                                <a href="<?php echo e(url('export/ob/create')); ?>" class="btn btn-lg btn-light-info"><i
                                                        class="fas fa-add"></i> Add
                                                    New
                                                    Job</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tableList">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Job No</th>
                                                            <th>PEB No</th>
                                                            <th>PIC Penerima</th>
                                                            <th>Destination</th>
                                                            <th>Remarks</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(url('/assets/new/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>

    <script type="text/javascript">
        $('#start').datepicker();
        $('#end').datepicker();


        function searchData() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var statusJob = $('#statusJob').val();

            $('#tableList').DataTable().clear().destroy()
            $('#tableList').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ordering: false,
                paging: false,
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                ajax: {
                    url: "<?php echo e(url('export/ob/searchData')); ?>/" + startDate + "/" + endDate + "/" + statusJob,
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a href="<?php echo e(url('export/ob/show/${data.id}')); ?>">${data.job_no}</a>`;
                        },
                    },
                    {
                        data: 'peb_no',
                        name: 'peb_no'
                    },
                    {
                        data: 'pic_name',
                        name: 'pic_name'
                    },
                    {
                        data: 'destination',
                        name: 'destination'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.confirmed_flag == 'Open') {
                                var status =
                                    '<span class = "badge badge-primary">Open</span>'
                            } else {
                                var status =
                                    '<span class="badge badge-success">Confirmed</span>'
                            }
                            return status;
                        },
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.new.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/new/OBExport/index.blade.php ENDPATH**/ ?>