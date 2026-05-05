@extends('layouts.main')

@section('title')
    Location
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Location</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Location</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Site Name</label>
                        <select class="custom-select" id="site_filter" name="site_filter">
                            @foreach ($site_list as $item)
                                <option value="{{$item->id}}">{{$item->site_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Area Name</label>
                        <select class="custom-select" id="area_filter" name="area_filter">
                            <option value="0">All</option>
                            @foreach ($area_list as $item)
                                <option value="{{$item->id}}">{{$item->area_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-12">                  
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        &nbsp;
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> Add New</button>
                        &nbsp;
                        <button type="button" class="btn btn-success btn-round" onclick="downloadExcel();" target="_blank"><i class="fas fa-download"></i> Download</button>
                        &nbsp;
                        <button type="button" class="btn btn-success btn-round" data-toggle="modal" data-target="#importExcel"><i class="fas fa-upload"></i> Upload</button>
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
                                    <th>Area Name</th>
                                    <th>Location Code</th>
                                    <th>Location Name</th>
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
    <div class="modal-dialog" role="document">
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
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Site Name</label>
                                <select class="custom-select" id="site_id" name="site_id">
                                    <option value="">.:Select:.</option>
                                    @foreach ($site_list as $item)
                                        <option value="{{$item->id}}">{{$item->site_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Area Name</label>
                                <select class="custom-select" id="area_id" name="area_id">
                                    <option value="">.:Select:.</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Location Code</label>
                                <input type="text" autocomplete="off" id="location_code" name="location_code" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-8">    
                            <div class="form-group">
                                <label>Location Name</label>
                                <input type="text" autocomplete="off" id="location_name" name="location_name" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Location Type</label>
                                <select class="custom-select" id="type_id" name="type_id">
                                    @foreach ($location_type_list as $item)
                                        <option value="{{$item->id}}">{{$item->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Location Status</label>
                                <select class="custom-select" id="status_code" name="status_code">
                                    @foreach ($location_status_list as $item)
                                        <option value="{{$item->status_code}}">{{$item->status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Aisle</label>
                                <input type="text" autocomplete="off" id="location_aisle" name="location_aisle" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Column</label>
                                <input type="text" autocomplete="off" id="location_column" name="location_column" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Level</label>
                                <input type="text" autocomplete="off" id="location_level" name="location_level" class="form-control">
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

<div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="{{url('site-master/location/import')}}" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="col-md-12">    
                        <div class="form-group">
                            <label>Site Name</label>
                            <select class="custom-select" id="site_upload" name="site_upload">
                                <option value="">.:Select:.</option>
                                @foreach ($site_list as $item)
                                    <option value="{{$item->id}}">{{$item->site_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Area Name</label>
                            <select class="custom-select" id="area_upload" name="area_upload">
                                <option value="">.:Select:.</option>
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
                    url : "{{ route('location.index') }}",                    
                    type : "GET",                    
                    data : { 
                        site_id: $('#site_filter').val(),
                        area_id: $('#area_filter').val() 
                    } 
                },
                columns : [
                    { data:'area_name', name:'area_name' },
                    { data:'location_code', name:'location_code' },
                    { data:'location_name', name:'location_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }  

        $('#refresh').click(function () {
            var site_id = $('#site_filter').val();
            
            if (site_id != '' && site_id > 0) {
                $('#table_list').DataTable().destroy();
                load_data();
            } else {
                swal({
                    icon: "error",
                    text: "Site name cannot be empty."                     
                });
            }                
        });
        
        $('#site_filter').on('change', function() {
            var site_id = this.value;
            $("#area_filter").html('');
            $.ajax({
                url:"{{route('location.area')}}",
                type: "GET",
                data: {
                    site_id: site_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#area_filter').html('<option value="0">All</option>'); 
                    $.each(result.area_list,function(key,value){
                        $("#area_filter").append('<option value="'+value.id+'">'+value.area_name+'</option>');
                    });
                }
            });
        });    
        
        $('#site_id').on('change', function() {
            var site_id = this.value;
            $("#area_id").html('');
            $.ajax({
                url:"{{route('location.area')}}",
                type: "GET",
                data: {
                    site_id: site_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#area_id').html('<option value="">.:Select:.</option>'); 
                    $.each(result.area_list,function(key,value){
                        $("#area_id").append('<option value="'+value.id+'">'+value.area_name+'</option>');
                    });
                }
            });
        });    
        
        $('#site_upload').on('change', function() {
            var site_id = this.value;
            $("#area_upload").html('');
            $.ajax({
                url:"{{route('location.area')}}",
                type: "GET",
                data: {
                    site_id: site_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#area_upload').html('<option value="">.:Select:.</option>'); 
                    $.each(result.area_list,function(key,value){
                        $("#area_upload").append('<option value="'+value.id+'">'+value.area_name+'</option>');
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
            $.get('location/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#area_id').html('<option value="">.:Select:.</option>'); 
                $.each(data.area_list,function(key,value){
                    $("#area_id").append('<option value="'+value.id+'">'+value.area_name+'</option>');
                });
                
                $('#id').val(data.edit_view.id);
                $('#site_id').val(data.edit_view.site_id);
                $('#area_id').val(data.edit_view.area_id);
                $('#location_code').val(data.edit_view.location_code);  
                $('#location_name').val(data.edit_view.location_name);                
                $('#status_code').val(data.edit_view.status_code);                
                $('#type_id').val(data.edit_view.type_id);                
                $('#location_aisle').val(data.edit_view.location_aisle);                
                $('#location_column').val(data.edit_view.location_column);                
                $('#location_level').val(data.edit_view.location_level);
                $('#active').val(data.edit_view.active);
            })
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('location.store') }}", 
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
                url: "location/" + dataId,
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
        var url = "{{URL('site-master/location/export')}}/" + $('#site_filter').val()

        window.location = url;
    }
</script>
@endpush