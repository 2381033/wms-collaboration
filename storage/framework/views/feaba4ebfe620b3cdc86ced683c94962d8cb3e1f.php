

<?php $__env->startSection('title'); ?>
    Menu
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Menu</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>Menu</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></a>
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
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Url</th>
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
    <div class="modal-dialog" role="document">
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
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" autocomplete="off" id="name" name="name" class="form-control">
                                <?php if($errors->has('name')): ?>
                                    <span class="help-block"><?php echo e($errors->first('name')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Parent ID</label>
                                <select class="custom-select" id="parent_id" name="parent_id">
                                    <option value="0">Menu Utama</option>
                                    <?php $__currentLoopData = $parent_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" autocomplete="off" id="url" name="url" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Icon</label>
                                <input type="text" autocomplete="off" id="icon" name="icon" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-3">
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
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save"><i class="fas fa-save"></i> <span>Save</span></button>
                </div>
            </form>
        </div>
    </div>
</div>         
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>        
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $('#table_list').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            destroy: true,
            scrollx: true,
            ajax : {
                url : "<?php echo e(route('menu.index')); ?>",
                type : "GET"
            },
            columns : [
                { data:'name', name:'name' },
                { data:'url', name:'url' },
                { data:'active', name:'active' },
                { data: 'action', name: 'action' }
            ],
            order : [
                [0, 'asc']
            ]
        });

        $('#btn-add').click(function () {
            $('#id').val(''); 
            $('#form-entry').trigger("reset"); 
            $('#modal-title').html("Add New");
            $('#modal-entry').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });

        $(document).on('click', '.delete', function () {
            dataId = $(this).attr('id');
            $('#modal-konfirmasi').modal('show');
        });

        $('body').on('click', '.edit-data', function () {
            var data_id = $(this).data('id');
            $.get('menu/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#parent_id').val(data.parent_id);
                $('#url').val(data.url);
                $('#icon').val(data.icon);
                $('#active').val(data.active);
            })
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    var actionType = $('#btn-save').val();
                    $('#btn-save').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "<?php echo e(route('menu.store')); ?>", 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
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
                                var pesan = "<div class='text-left alert alert-danger'>";
                                for (var i = 0; i < data.error.length; i++) {                                            
                                    pesan += data.error[i]+'</br>'; 
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
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#btn-save').html('Save');
                        }
                    });
                }
            })
        }

        $('#btn-delete').click(function () {
            $.ajax({
                url: "menu/" + dataId,
                type: 'delete',
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>"
                },
                beforeSend: function () {
                    $('#btn-delete').text('Sending..');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#modal-konfirmasi').modal('hide');
                        var oTable = $('#table_list').dataTable();
                        oTable.fnDraw(false);
                    });
                    
                    if($.isEmptyObject(data.error)){
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
                error: function (data) {
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            })
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/admin/menu.blade.php ENDPATH**/ ?>