@extends('layouts.main')

@section('title')
    Principal
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Principal</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Principal</li>
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
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{session('success')}}
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Short Name</th>
                                    <th>Principal Name</th>
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
@endsection

@section('modal')
<div class="modal fade" tabindex="-1" role="dialog" id="modal-entry">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-entry" name="form-entry" method="post">
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Short Name</label>
                                <input type="text" autocomplete="off" id="short_name" name="short_name" class="form-control">
                                @if ($errors->has('short_name'))
                                    <span class="help-block">{{ $errors->first('short_name') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Principal Name</label>
                                <input type="text" autocomplete="off" id="principal_name" name="principal_name" class="form-control">
                                @if ($errors->has('principal_name'))
                                    <span class="help-block">{{ $errors->first('principal_name') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Interface Mode</label>
                                <select class="custom-select" id="interface_mode" name="interface_mode">
                                    <option value="FMCG">FMCG - Standard Product Pack-keys</option>
                                    <option value="GML">GML - Project Logistics (Non-standard Shipments)</option>
                                </select>
                            </div>
                        </div>
                    </div>     
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" autocomplete="off" id="address1" name="address1" class="form-control mb-1">
                                <input type="text" autocomplete="off" id="address2" name="address2" class="form-control mb-1">
                                <input type="text" autocomplete="off" id="address3" name="address3" class="form-control mb-1">
                                <input type="text" autocomplete="off" id="address4" name="address4" class="form-control mb-1">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" autocomplete="off" id="phone" name="phone" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-8">    
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" autocomplete="off" id="email" name="email" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>PIC Name</label>
                                <input type="text" autocomplete="off" id="pic_name" name="pic_name" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>PIC Phone</label>
                                <input type="text" autocomplete="off" id="pic_phone" name="pic_phone" class="form-control">
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
                        <input type="hidden" id="principal_id_site">
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
                    @csrf
                    <div class="col-md-12">                        
                        <div class="form-group">
                            <input type="hidden" id="principal_site" name="principal_site">
                            <label>Site Name</label>
                            <select name="site_id" id="site_id" class="form-control">
                                @foreach ($site_list as $item)
                                    <option value="{{$item->id}}">{{$item->site_name}}</option>
                                @endforeach
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
@endsection

@push('scripts')
<script>        
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
        $('#table_list').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            ajax : {
                url : "{{ route('principal.index') }}",
                type : "GET"
            },
            columns : [
                { data:'short_name', name:'short_name' },
                { data:'principal_name', name:'principal_name' },
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
            $('#action-delete').val('principal');
            $('#modal-konfirmasi').modal('show');
        });

        $('body').on('click', '.edit-data', function () {
            var data_id = $(this).data('id');
            $.get('principal/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#id').val(data.id);
                $('#short_name').val(data.short_name);
                $('#principal_name').val(data.principal_name);
                $('#interface_mode').val(data.interface_mode);           
                $('#address1').val(data.address1);                
                $('#address2').val(data.address2);                
                $('#address3').val(data.address3);                
                $('#address4').val(data.address4);                  
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#pic_name').val(data.pic_name);
                $('#pic_phone').val(data.pic_phone);
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
                        url: "{{ route('principal.store') }}", 
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

            if (action == 'principal') {
                requestUrl = "principal/" + dataId; 
            } else if (action == 'site') {
                var principal_id = $('#principal_id_site').val();
                requestUrl = "principal-site/" + principal_id + "/" + dataId; 
            } 

            $.ajax({
                url: requestUrl,
                type: 'delete',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
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

                    $('#modal-konfirmasi').modal('hide');

                    var oTable = "";
                    if (action == 'principal') {
                        oTable = $('#table_list').dataTable();
                    } else if (action == 'site') {
                        oTable = $('#site_table').dataTable();
                    } 
                        
                    oTable.fnDraw(false);
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

        $(document).on('click', '.site', function () {
            dataId = $(this).attr('id');
            $('#principal_id_site').val(dataId);
            $('#principal_site').val(dataId);
            $('#site_table').DataTable().destroy();
            $('#site_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "{{ route('principal-site.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: dataId
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
                        url: "{{ route('principal-site.store') }}",
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
                            $('#save-site-btn').html('Save');
                        }
                    });
                }
            })
        }
    });
</script>
@endpush