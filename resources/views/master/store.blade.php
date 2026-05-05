@extends('layouts.main')

@section('title')
    Store 
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Store</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Store</li>
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
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        &nbsp;
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></a>
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
                                    <th>Store Name</th>
                                    <th>Address</th>
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
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Store Code</label>
                                <input type="text" autocomplete="off" id="store_code" name="store_code" class="form-control">
                                @if ($errors->has('store_code'))
                                    <span class="help-block">{{ $errors->first('store_code') }}</span>
                                @endif
                            </div>
                        </div> 
                        <div class="col-md-8">    
                            <div class="form-group">
                                <label>Store Name</label>
                                <input type="text" autocomplete="off" id="store_name" name="store_name" class="form-control">
                                @if ($errors->has('store_name'))
                                    <span class="help-block">{{ $errors->first('store_name') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Country Name</label>
                                <select class="custom-select" id="country_code" name="country_code">
                                    <option value="">.:Select:.</option>
                                    @foreach ($country_list as $item)
                                        <option value="{{$item->country_code}}">{{$item->country_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Region Name</label>
                                <select class="custom-select" id="region_code" name="region_code">
                                    <option value="">.:Select:.</option>
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>City Name</label>
                                <select class="custom-select" id="city_code" name="city_code">
                                    <option value="">.:Select:.</option>
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
                                <input type="text" autocomplete="off" id="telephone" name="telephone" class="form-control">
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
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>PIC Name</label>
                                <input type="text" autocomplete="off" id="pic_name" name="pic_name" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>PIC Phone</label>
                                <input type="text" autocomplete="off" id="pic_phone" name="pic_phone" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-4">
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
        <form method="post" action="{{url('customer-master/store/import')}}" enctype="multipart/form-data">
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
                    url : "{{ route('customer-store.index') }}",                    
                    type : "GET",                    
                    data : { principal_id: $('#principal_filter').val() } 
                },
                columns : [
                    { data:'store_name', name:'store_name' },
                    { data:'address1', name:'address1' },
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
            $("#customer_id").html('');
            $.ajax({
                url:"{{route('customer-store.customer')}}",
                type: "GET",
                data: {
                    principal_id: principal_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#customer_id').html('<option value="">.:Select:.</option>'); 
                    $.each(result.customer_list,function(key,value){
                        $("#customer_id").append('<option value="'+value.id+'">'+value.customer_name+'</option>');
                    });
                }
            });
        });    
        
        $('#country_code').on('change', function() {
            var country_code = this.value;
            
            $("#region_code").html('');
            $.ajax({
                url:"{{route('region.list')}}",
                type: "GET",
                data: {
                    country_code: country_code,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#region_code').html('<option value="">.:Select:.</option>'); 
                    $.each(result.region_list,function(key,value){
                        $("#region_code").append('<option value="'+value.region_code+'">'+value.region_name+'</option>');
                    });
                }
            });
        });   
        
        $('#region_code').on('change', function() {
            var country_code = $("#country_code").val();
            var region_code = this.value;
            
            $("#city_code").html('');
            $.ajax({
                url:"{{route('city.list')}}",
                type: "GET",
                data: {
                    country_code: country_code,
                    region_code: region_code,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#city_code').html('<option value="">.:Select:.</option>'); 
                    $.each(result.city_list,function(key,value){
                        $("#city_code").append('<option value="'+value.city_code+'">'+value.city_name+'</option>');
                    });
                }
            });
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
            $.get('store/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#region_code').html('<option value="">.:Select:.</option>'); 
                $.each(data.region_list,function(key,value){
                    $("#region_code").append('<option value="'+value.region_code+'">'+value.region_name+'</option>');
                });
                $('#city_code').html('<option value="">.:Select:.</option>'); 
                $.each(data.city_list,function(key,value){
                    $("#city_code").append('<option value="'+value.city_code+'">'+value.city_name+'</option>');
                });
       
                $('#id').val(data.edit_view.id);
                $('#principal_id').val(data.edit_view.principal_id);
                $('#store_code').val(data.edit_view.store_code);
                $('#store_name').val(data.edit_view.store_name);        
                $('#country_code').val(data.edit_view.country_code);                
                $('#region_code').val(data.edit_view.region_code);                
                $('#city_code').val(data.edit_view.city_code);            
                $('#address1').val(data.edit_view.address1);                
                $('#address2').val(data.edit_view.address2);                
                $('#address3').val(data.edit_view.address3);                
                $('#address4').val(data.edit_view.address4);                   
                $('#telephone').val(data.edit_view.telephone);
                $('#email').val(data.edit_view.email);
                $('#pic_name').val(data.edit_view.pic_name);
                $('#pic_phone').val(data.edit_view.pic_phone);
                $('#active').val(data.edit_view.active);
            })
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    var actionType = $('#btn-save').val();
                    $('#btn-save').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('customer-store.store') }}", 
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
                url: "customer-store/" + dataId,
                type: 'delete',
                data: {
                    "_token": "{{ csrf_token() }}"
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

    function downloadExcel() {
        var principal_filter = $("#principal_filter").val();

        var url = "{{URL('customer-master/store/export')}}/" + $('#principal_filter').val()

        window.location = url;
    }
</script>
@endpush