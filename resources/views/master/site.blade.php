@extends('layouts.main')

@section('title')
    Site
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Site</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Site</li>
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
                                    <th>Site ID</th>
                                    <th>Site Name</th>
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
                                <label>Site Name</label>
                                <input type="text" autocomplete="off" id="site_name" name="site_name" class="form-control">
                                @if ($errors->has('site_name'))
                                    <span class="help-block">{{ $errors->first('site_name') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Type Name</label>
                                <select class="custom-select" id="type_id" name="type_id">
                                    <option value="">.:Select:.</option>
                                    @foreach ($site_type_list as $item)
                                        <option value="{{$item->id}}">{{$item->type_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Indicator Name</label>
                                <select class="custom-select" id="indicator_id" name="indicator_id">
                                    <option value="">.:Select:.</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Location Type</label>
                                <select class="custom-select" id="location_id" name="location_id">
                                    <option value="">.:Select:.</option>
                                    @foreach ($location_type_list as $item)
                                        <option value="{{$item->id}}">{{$item->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" id="address" rows="3" class="form-control"></textarea>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" autocomplete="off" id="phone" name="phone" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Fax</label>
                                <input type="text" autocomplete="off" id="fax" name="fax" class="form-control">
                            </div>
                        </div> 
                    </div>      
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Zip Code</label>
                                <input type="text" autocomplete="off" id="zip_code" name="zip_code" class="form-control">
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
                url : "{{ route('site.index') }}",
                type : "GET"
            },
            columns : [
                { data:'id', name:'id' },
                { data:'site_name', name:'site_name' },
                { data:'address', name:'address' },
                { data:'active', name:'active' },
                { data: 'action', name: 'action' }
            ],
            order : [
                [0, 'asc']
            ]
        });
        
        $('#type_id').on('change', function() {
            var type_id = this.value;
            $("#indicator_id").html('');
            $.ajax({
                url:"{{route('site.indicator')}}",
                type: "GET",
                data: {
                    type_id: type_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#indicator_id').html('<option value="">.:Select:.</option>'); 
                    $.each(result.indicator_list,function(key,value){
                        $("#indicator_id").append('<option value="'+value.id+'">'+value.indicator_name+'</option>');
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
            $.get('site/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');

                $('#indicator_id').html('<option value="">.:Select:.</option>'); 
                $.each(data.indicator_list,function(key,value){
                    $("#indicator_id").append('<option value="'+value.id+'">'+value.indicator_name+'</option>');
                });
       
                $('#id').val(data.edit_view.id);
                $('#site_name').val(data.edit_view.site_name);
                $('#type_id').val(data.edit_view.type_id);
                $('#indicator_id').val(data.edit_view.indicator_id);
                $('#location_id').val(data.edit_view.location_id);
                $('#address').val(data.edit_view.address);
                $('#zip_code').val(data.edit_view.zip_code);
                $('#phone').val(data.edit_view.phone);
                $('#fax').val(data.edit_view.fax);
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
                        url: "{{ route('site.store') }}", 
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
                url: "site/" + dataId,
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