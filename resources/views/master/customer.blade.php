@extends('layouts.main')

@section('title')
    Customer
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Customer</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Customer</li>
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
                            @foreach (Auth::user()->principal as $item)
                                <option value="{{$item->id}}">{{$item->principal_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-round">Refresh</button>
                        &nbsp;
                        <button type="button" class="btn btn-primary btn-round" id="btn-add"><i class="fas fa-plus"></i> Add New</button>
                        &nbsp;
                        <button class="btn btn-success btn-round" onclick="downloadExcel();" target="_blank"><i class="fas fa-download"></i> Download</button>
                        &nbsp;
                        <button class="btn btn-success btn-round" data-toggle="modal" data-target="#importExcel"><i class="fas fa-upload"></i> Upload</button>
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
                                    <th>Customer Code</th>
                                    <th>Customer Name</th>
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
                                <label>Principal Name</label>
                                <select class="custom-select" id="principal_id" name="principal_id">
                                    <option value="">.:Select:.</option>
                                    @foreach (Auth::user()->principal as $item)
                                        <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Customer Code</label>
                                <input type="text" autocomplete="off" id="customer_code" name="customer_code" class="form-control">
                                @if ($errors->has('customer_code'))
                                    <span class="help-block">{{ $errors->first('customer_code') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" autocomplete="off" id="customer_name" name="customer_name" class="form-control">
                                @if ($errors->has('customer_name'))
                                    <span class="help-block">{{ $errors->first('customer_name') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Customer Group</label>
                                <select class="custom-select" id="group_id" name="group_id">
                                    <option value="">.:Select:.</option>
                                </select>
                                @if ($errors->has('group_id'))
                                    <span class="help-block">{{ $errors->first('group_id') }}</span>
                                @endif
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Customer Type</label>
                                <select class="custom-select" id="type_id" name="type_id">
                                    <option value="">.:Select:.</option>
                                </select>
                                @if ($errors->has('type_id'))
                                    <span class="help-block">{{ $errors->first('type_id') }}</span>
                                @endif
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
                        <div class="col-md-9">    
                            <div class="form-group">
                                <label>Store Name</label>
                                <input type="hidden" id="store_id" name="store_id">
                                <input type="text" autocomplete="off" id="store_name" name="store_name" class="form-control">
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
   
<div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="{{url('customer-master/customer/import')}}" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="col-md-12">    
                        <div class="form-group">
                            <label>Principal Name</label>
                            <select class="custom-select" id="principal_upload" name="principal_upload">
                                @foreach (Auth::user()->principal as $item)
                                    <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-12">    
                        <label>Pilih file excel</label>
                        <div class="form-group">
                            <input type="file" name="file" required="required">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>        
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
        load_data();

        function load_data(principal = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('customer.index') }}",                    
                    type : "GET",                    
                    data : { principal_id: $('#principal_filter').val() } 
                },
                columns : [
                    { data:'customer_code', name:'customer_code' },
                    { data:'customer_name', name:'customer_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }  

        $('#refresh').click(function () {
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
            $("#group_id").html('');
            $("#type_id").html('');
            $.ajax({
                url:"{{route('customer.reference')}}",
                type: "GET",
                data: {
                    principal_id: principal_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#group_id').html('<option value="">.:Select:.</option>'); 
                    $.each(result.group_list,function(key,value){
                        $("#group_id").append('<option value="'+value.id+'">'+value.group_name+'</option>');
                    });
                    $('#type_id').html('<option value="">.:Select:.</option>'); 
                    $.each(result.type_list,function(key,value){
                        $("#type_id").append('<option value="'+value.id+'">'+value.type_name+'</option>');
                    });
                }
            });
        });  
        
        $( "#store_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('customer.getStore')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {      
                $('#store_name').val(ui.item.store_name);
                $('#store_id').val(ui.item.id);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.store_name + " (" + item.store_code + ")<br>"+ item.city_name+"</div>" )
                .appendTo( ul );
        };   

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
            $.get('customer/' + data_id + '/edit', function (data) {

                console.log(data);
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#group_id').html('<option value="">.:Select:.</option>'); 
                $.each(data.group_list,function(key,value){
                    $("#group_id").append('<option value="'+value.id+'">'+value.group_name+'</option>');
                });
                $('#type_id').html('<option value="">.:Select:.</option>'); 
                $.each(data.type_list,function(key,value){
                    $("#type_id").append('<option value="'+value.id+'">'+value.type_name+'</option>');
                });
                
                $('#id').val(data.edit_view.id);
                $('#principal_id').val(data.edit_view.principal_id);
                $('#customer_code').val(data.edit_view.customer_code);  
                $('#customer_name').val(data.edit_view.customer_name);                
                $('#group_id').val(data.edit_view.group_id);                
                $('#type_id').val(data.edit_view.type_id);                
                $('#address1').val(data.edit_view.address1);                
                $('#address2').val(data.edit_view.address2);                
                $('#address3').val(data.edit_view.address3);                
                $('#address4').val(data.edit_view.address4);                  
                $('#phone').val(data.edit_view.phone);
                $('#email').val(data.edit_view.email);
                $('#pic_name').val(data.edit_view.pic_name);
                $('#pic_phone').val(data.edit_view.pic_phone);
                $('#store_id').val(data.edit_view.store_id);
                $('#store_name').val(data.edit_view.store_name);  
                $('#active').val(data.edit_view.active);
            })
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('customer.store') }}", 
                        type: "POST", 
                        dataType: 'json',       
                        beforeSend: function () {
                            $("#loader").show();
                        },
                        success: function (data) {                          
                            $("#loader").hide();
                            if($.isEmptyObject(data.error)){
                                $('#form-entry').trigger("reset"); 
                                $('#modal-entry').modal('hide'); 
                                
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
                            } 
                        },
                        error: function (data) { 
                            console.log('Error:', data);
                            $("#loader").hide();
                        }
                    });
                }
            })
        }

        $('#btn-delete').click(function () {
            $.ajax({
                url: "customer/" + dataId,
                type: 'delete',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (data) {
                    $("#loader").hide();
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
                    $("#loader").hide();
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            })
        });
    });

    function downloadExcel() {
        var principal_filter = $("#principal_filter").val();

        var url = "{{URL('customer-master/customer/export')}}/" + $('#principal_filter').val()

        window.location = url;
    }
</script>
@endpush