@extends('layouts.main')

@section('title')
    City
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>City</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>City</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Country Name</label>
                        <select class="custom-select" id="country_filter" name="country_filter">
                            @foreach ($country_list as $item)
                                <option value="{{$item->country_code}}">{{$item->country_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
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
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead>
                                <tr>
                                    <th>City Name</th>
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
                        <div class="col-md-12">    
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
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Region Name</label>
                                <select class="custom-select" id="region_code" name="region_code">
                                    <option value="">.:Select:.</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>City Code</label>
                                <input type="text" autocomplete="off" id="city_code" name="city_code" class="form-control">
                                @if ($errors->has('city_code'))
                                    <span class="help-block">{{ $errors->first('city_code') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>City Name</label>
                                <input type="text" autocomplete="off" id="city_name" name="city_name" class="form-control">
                                @if ($errors->has('city_name'))
                                    <span class="help-block">{{ $errors->first('city_name') }}</span>
                                @endif
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
                    url : "{{ route('city.index') }}",                    
                    type : "GET",                    
                    data : { country_code: $('#country_filter').val() } 
                },
                columns : [
                    { data:'city_name', name:'city_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }  

        $('#refresh').click(function () {
            var country_code = $('#country_filter').val();
            
            if (country_code != '') {
                $('#table_list').DataTable().destroy();
                load_data();
            } else {
                swal({
                    icon: "error",
                    text: "Country name cannot be empty."                     
                });
            }                
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
            $.get('city/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');
       
                $('#region_code').html('<option value="">.:Select:.</option>'); 
                $.each(data.region_list,function(key,value){
                    $("#region_code").append('<option value="'+value.region_code+'">'+value.region_name+'</option>');
                });
       
                $('#id').val(data.edit_view.id);
                $('#country_code').val(data.edit_view.country_code);
                $('#region_code').val(data.edit_view.region_code);
                $('#city_code').val(data.edit_view.city_code);
                $('#city_name').val(data.edit_view.city_name);
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
                        url: "{{ route('city.store') }}", 
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
                url: "city/" + dataId,
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
</script>
@endpush