

<?php $__env->startSection('title'); ?>
    CY Booking
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Booking</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>CY Booking</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
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
                            <option value="Open">Open</option>
                            <option value="Cancel">Cancel</option>
                            <option value="Confirmed">Confirmed</option>
                        </select>
                    </div>
                </div>    
                <div class="col-md-6"> 
                    <div class="form-group">
                        <label for="branch_id">Branch Name</label>
                        <select name="branch_id" id="branch_id" class="custom-select">
                            <?php $__currentLoopData = Auth::user()->branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>" <?php if(isset($header->job_date)): ?> <?php if( $item->id == $header->branch_id ): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($item->branch_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>       
            </div>
            <div class="row">                
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="<?php echo e(url('/cy/booking/create/0')); ?>" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
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
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Booking No</th>
                                    <th>Booking Date</th>
                                    <th>Company Name</th>
                                    <th>Reference No</th>
                                    <th>Driver Name</th>
                                    <th>Vehicle No</th>
                                    <th>Container No</th>
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
    $( function() {
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
    } );

    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        load_data();

        $('#refresh').click(function () {
            $('#table_list').DataTable().destroy();
            load_data();
        });

        function load_data(forwarder = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "<?php echo e(route('cy-booking.index')); ?>",
                    type : "GET",
                    data : { 
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        status_code: $('#status_code').val(),
                        branch_id: $('#branch_id').val()
                    } 
                },
                columns : [
                    { data:'booking_no', name:'booking_no' },
                    { data:'booking_date', name:'booking_date' },
                    { data:'forwarder_name', name:'forwarder_name' },
                    { data:'reference_no', name:'reference_no' },
                    { data:'driver_name', name:'driver_name' },
                    { data:'vehicle_no', name:'vehicle_no'},
                    { data:'container_no', name:'container_no'}
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/transaction/cy/booking/index.blade.php ENDPATH**/ ?>