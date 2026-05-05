

<?php $__env->startSection('title'); ?>
    User
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>User</h2>
                <ol>
                    <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li>User</li>
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
                                    <th>Name</th>
                                    <th>User Name</th>
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
            <form id="form-entry" method="post">
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
                                <label>User Name</label>
                                <input type="text" autocomplete="off" id="username" name="username" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Role Name</label>
                                <select class="custom-select" id="role_id" name="role_id">
                                    <option value="">.:Select:.</option>
                                    <?php $__currentLoopData = $role_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->role_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" autocomplete="off" id="email" name="email" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Menu Autorization Same With</label>
                                <select class="custom-select" id="user_akses" name="user_akses">
                                    <option value="">.:Select:.</option>
                                    <?php $__currentLoopData = $user_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                    
                                </select>
                            </div>
                        </div>
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

<div class="modal fade" tabindex="-1" role="dialog" id="site-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Site Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="user_id_site">
                        <div class="table-responsive">
                            <table id="site_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>   
                                        <th>Site Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-primary btn-sm" id="add-site-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>  

<div class="modal fade" tabindex="-1" role="dialog" id="add-site-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-site" name="form-site" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Add Site Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo csrf_field(); ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="hidden" id="user_site" name="user_site">
                            <select name="site_id" id="site_id" class="form-control">
                                <?php $__currentLoopData = $site_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->site_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="submit" class="btn btn-success btn-sm" id="save-site-btn"><i class="fas fa-save"></i> <span>Save</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
            </form>
        </div>
    </div>
</div>     

<div class="modal fade" tabindex="-1" role="dialog" id="principal-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Principal Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="user_id_principal">
                        <div class="table-responsive">
                            <table id="principal_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>   
                                        <th>Principal Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-primary btn-sm" id="add-principal-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>  

<div class="modal fade" tabindex="-1" role="dialog" id="add-principal-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-principal" name="form-principal" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Add Principal Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo csrf_field(); ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Principal Name</label>
                            <input type="hidden" id="user_principal" name="user_principal">
                            <select name="principal_id" id="principal_id" class="form-control">
                                <?php $__currentLoopData = $principal_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="submit" class="btn btn-success btn-sm" id="save-principal-btn"><i class="fas fa-save"></i> <span>Save</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
            </form>
        </div>
    </div>
</div> 

<div class="modal fade" tabindex="-1" role="dialog" id="menu-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Menu Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-menu" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="user_id_menu" name="user_id_menu">
                            <div class="table-responsive">
                                <table id="menu_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                    <thead class="text-center">
                                        <tr>   
                                            <th>Menu Name</th>
                                            <th>Access</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>Print</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" class="btn btn-success btn-sm" id="save-menu-btn"><i class="fas fa-plus"></i> <span>Save</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
                </div>
            </form>
        </div>
    </div>
</div>  

<div class="modal fade" tabindex="-1" role="dialog" id="branch-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Branch Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="user_id_branch">
                        <div class="table-responsive">
                            <table id="branch_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>   
                                        <th>branch Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-primary btn-sm" id="add-branch-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>  

<div class="modal fade" tabindex="-1" role="dialog" id="add-branch-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-branch" name="form-branch" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Add branch Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo csrf_field(); ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="hidden" id="user_branch" name="user_branch">
                            <select name="branch_id" id="branch_id" class="form-control">
                                <?php $__currentLoopData = $branch_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->branch_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="submit" class="btn btn-success btn-sm" id="save-branch-btn"><i class="fas fa-save"></i> <span>Save</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
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
                url : "<?php echo e(route('user.index')); ?>",
                type : "GET"
            },
            columns : [
                { data:'name', name:'name' },
                { data:'username', name:'username' },
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
            $('#action-delete').val('user');
            $('#modal-konfirmasi').modal('show');
        });

        $('body').on('click', '.edit-data', function () {
            var data_id = $(this).data('id');
            $.get('user/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#role_id').val(data.role_id);
                $('#username').val(data.username);
                $('#email').val(data.email);
                $('#password').val(data.password);
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
                        url: "<?php echo e(route('user.store')); ?>", 
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
            var action = $('#action-delete').val();
            var requestUrl = "";

            if (action == 'user') {
                requestUrl = "user/" + dataId; 
            } else if (action == 'site') {
                var user_id = $('#user_id_site').val();
                requestUrl = "user-site/" + user_id + "/" + dataId; 
            } else if (action == 'principal') {
                var user_id = $('#user_id_principal').val();
                requestUrl = "user-principal/" + user_id + "/" + dataId; 
            } else if (action == 'menu') {
                var user_id = $('#user_id_menu').val();
                requestUrl = "user-menu/" + user_id + "/" + dataId; 
            } else if (action == 'branch') {
                var user_id = $('#user_id_branch').val();
                requestUrl = "user-branch/" + user_id + "/" + dataId; 
            } 
            
            $.ajax({
                url: requestUrl,
                type: 'delete',
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>"
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#modal-konfirmasi').modal('hide');

                        var oTable = "";
                        if (action == 'user') {
                            oTable = $('#table_list').dataTable();
                        } else if (action == 'site') {
                            oTable = $('#site_table').dataTable();
                        } else if (action == 'principal') {
                            oTable = $('#principal_table').dataTable();
                        } else if (action == 'menu') {
                            oTable = $('#menu_table').dataTable();
                        }

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

        $(document).on('click', '.site', function () {
            dataId = $(this).attr('id');
            $('#user_id_site').val(dataId);
            $('#user_site').val(dataId);
            $('#site_table').DataTable().destroy();
            $('#site_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "<?php echo e(route('user-site.index')); ?>",
                    type : "GET",
                    data : { 
                        user_id: dataId
                    } 
                },
                columns : [
                    { data:'site_name', name:'site_name'},
                    { data:'action', name:'action'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        
            $('#site-modal').modal(
                {
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }
            );
        });

        $(document).on('click', '.delete-site', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('site');
            $('#modal-konfirmasi').modal('show');
        });

        $('#add-site-btn').click(function () {   
            $('#add-site-modal').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
            
        if ($("#form-site").length > 0) {
            $("#form-site").validate({
                submitHandler: function (form) {
                    var actionType = $('#save-site-btn').val();
                    $('#save-site-btn').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-site').serialize(), 
                        url: "<?php echo e(route('user-site.store')); ?>",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-site').trigger("reset"); 
                                $('#add-site-modal').modal('hide'); 
                                $('#save-site-btn').html('Simpan');
                                var oTable = $('#site_table').dataTable(); 
                                oTable.fnDraw(false); 
                                
                                swal({
                                    icon: "success",
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
                                $('#save-site-btn').html('Save');
                            }
                        },       
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#save-site-btn').html('Simpan');
                        }
                    });
                }
            })
        }

        $(document).on('click', '.principal', function () {
            dataId = $(this).attr('id');
            $('#user_id_principal').val(dataId);
            $('#user_principal').val(dataId);
            $('#principal_table').DataTable().destroy();
            $('#principal_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "<?php echo e(route('user-principal.index')); ?>",
                    type : "GET",
                    data : { 
                        user_id: dataId
                    } 
                },
                columns : [
                    { data:'principal_name', name:'principal_name'},
                    { data:'action', name:'action'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        
            $('#principal-modal').modal(
                {
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }
            );
        });

        $(document).on('click', '.delete-principal', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('principal');
            $('#modal-konfirmasi').modal('show');
        });

        $('#add-principal-btn').click(function () {   
            $('#add-principal-modal').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
            
        if ($("#form-principal").length > 0) {
            $("#form-principal").validate({
                submitHandler: function (form) {
                    var actionType = $('#save-principal-btn').val();
                    $('#save-principal-btn').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-principal').serialize(), 
                        url: "<?php echo e(route('user-principal.store')); ?>",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-principal').trigger("reset"); 
                                $('#add-principal-modal').modal('hide'); 
                                $('#save-principal-btn').html('Simpan');
                                var oTable = $('#principal_table').dataTable(); 
                                oTable.fnDraw(false); 
                                
                                swal({
                                    icon: "success",
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
                                $('#save-principal-btn').html('Save');
                            }
                        },       
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#save-principal-btn').html('Simpan');
                        }
                    });
                }
            })
        }

        $(document).on('click', '.branch', function () {
            dataId = $(this).attr('id');
            $('#user_id_branch').val(dataId);
            $('#user_branch').val(dataId);
            $('#branch_table').DataTable().destroy();
            $('#branch_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "<?php echo e(route('user-branch.index')); ?>",
                    type : "GET",
                    data : { 
                        user_id: dataId
                    } 
                },
                columns : [
                    { data:'branch_name', name:'branch_name'},
                    { data:'action', name:'action'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        
            $('#branch-modal').modal(
                {
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }
            );
        });

        $(document).on('click', '.delete-branch', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('branch');
            $('#modal-konfirmasi').modal('show');
        });

        $('#add-branch-btn').click(function () {   
            $('#add-branch-modal').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
            
        if ($("#form-branch").length > 0) {
            $("#form-branch").validate({
                submitHandler: function (form) {
                    var actionType = $('#save-branch-btn').val();
                    $('#save-branch-btn').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-branch').serialize(), 
                        url: "<?php echo e(route('user-branch.store')); ?>",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-branch').trigger("reset"); 
                                $('#add-branch-modal').modal('hide'); 
                                $('#save-branch-btn').html('Simpan');
                                var oTable = $('#branch_table').dataTable(); 
                                oTable.fnDraw(false); 
                                
                                swal({
                                    icon: "success",
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
                                $('#save-branch-btn').html('Save');
                            }
                        },       
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#save-branch-btn').html('Simpan');
                        }
                    });
                }
            })
        }

        $(document).on('click', '.menu', function () {
            dataId = $(this).attr('id');
            $('#user_id_menu').val(dataId);            
            $('#menu_table').DataTable().destroy();
            $('#menu_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ordering: false,
                ajax : {
                    url : "<?php echo e(route('user-menu.index')); ?>",
                    type : "GET",
                    data : { 
                        user_id: dataId
                    } 
                },
                columns : [
                    { data:'name', name:'name'},
                    { data:'akses', name:'akses'},
                    { data:'tambah', name:'tambah'},
                    { data:'edit', name:'edit'},
                    { data:'hapus', name:'hapus'},
                    { data:'cetak', name:'cetak'},
                ]
            });
        
            $('#menu-modal').modal(
                {
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }
            );
        });

        $(document).on('click', '.delete-menu', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('menu');
            $('#modal-konfirmasi').modal('show');
        });
            
        if ($("#form-menu").length > 0) {
            $("#form-menu").validate({
                submitHandler: function (form) {
                    var actionType = $('#save-menu-btn').val();
                    $('#save-menu-btn').html('Sending..');
                    console.log($('#form-menu').serialize());
                    $.ajax({
                        data: $('#form-menu').serialize(), 
                        url: "<?php echo e(route('user-menu.store')); ?>",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-menu').trigger("reset"); 
                                $('#save-menu-btn').html('Simpan');
                                var oTable = $('#menu_table').dataTable(); 
                                oTable.fnDraw(false); 
                                
                                swal({
                                    icon: "success",
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
                                $('#save-menu-btn').html('Save');
                            }
                        },       
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#save-menu-btn').html('Simpan');
                        }
                    });
                }
            })
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/admin/user.blade.php ENDPATH**/ ?>