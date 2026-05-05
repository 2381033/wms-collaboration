@extends('layouts.main')

@section('title')
    Setting Email Principal
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Setting Email Principal</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Setting Email Principal</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">        
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Principal Name</label>
                                <select class="custom-select" id="principal_filter" name="principal_filter">
                                    @foreach (Auth::user()->principal as $item)
                                        <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                 
                        <div class="col-md-4">    
                            <div class="form-group">
                                <label>Branch Name</label>
                                <select class="custom-select" id="branch_filter" name="branch_filter">
                                    @foreach (Auth::user()->principal()->first()->branch as $item)
                                        <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                                 
                        <div class="col-md-4">
                            <div class="btn-group">
                                <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></a>
                            </div>    
                        </div>
                    </div>      
                    <div class="row">      
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Email To</th>
                                            <th>Status</th>     
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
                                <label>Branch Name</label>
                                <select class="custom-select" id="branch_id" name="branch_id">
                                    @foreach (Auth::user()->principal()->first()->branch as $item)
                                        <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" autocomplete="off" id="description" name="description" class="form-control">
                                @if ($errors->has('description'))
                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" autocomplete="off" id="subject" name="subject" class="form-control">
                                @if ($errors->has('subject'))
                                    <span class="help-block">{{ $errors->first('subject') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>To</label>
                                <textarea name="email_to" id="email_to" rows="3" class="form-control"></textarea>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>CC</label>
                                <textarea name="email_cc" id="email_cc" rows="5" class="form-control"></textarea>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>BCC</label>
                                <textarea name="email_bcc" id="email_bcc" rows="2" class="form-control"></textarea>
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

        load_data();

        function load_data(principal = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy: true,
                scrollx: true,
                ajax : {
                    url : "{{ route('settings-email-principal.index') }}",
                    type : "GET",
                    data : { 
                        branch_id: $('#branch_filter').val(),
                        principal_id: $('#principal_filter').val()
                    } 
                },
                columns : [
                    { data:'description', name:'description' },
                    { data:'email_to', name:'email_to' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

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
        
        $('#principal_filter').on('change', function() {
            var principal_id = this.value;
            $("#branch_filter").html('');
            $.ajax({
                url:"{{route('principal.getBranchList')}}",
                type: "GET",
                data: {
                    principal_id: principal_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $.each(result.list,function(key,value){
                        $("#branch_filter").append('<option value="'+value.id+'">'+value.branch_name+'</option>');
                    });
                }
            });
        });  
        
        $('#principal_id').on('change', function() {
            var principal_id = this.value;
            $("#branch_id").html('');
            $.ajax({
                url:"{{route('principal.getBranchList')}}",
                type: "GET",
                data: {
                    principal_id: principal_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $.each(result.list,function(key,value){
                        $("#branch_id").append('<option value="'+value.id+'">'+value.branch_name+'</option>');
                    });
                }
            });
        });  

        $('body').on('click', '.edit-data', function () {
            var data_id = $(this).data('id');
            var url = "{{ url('settings/email-principal') }}";
            
            $.get(url + '/' + data_id + '/edit', function (data) {
                $('#modal-title').html("Edit");
                $('#btn-save').val("Edit");
                $('#modal-entry').modal('show');

                $("#branch_id").html('');
                $.each(data.list,function(key,value){
                    $("#branch_id").append('<option value="'+value.id+'">'+value.branch_name+'</option>');
                });
       
                $('#id').val(data.item.id);
                $('#branch_id').val(data.item.branch_id);
                $('#principal_id').val(data.item.principal_id);
                $('#description').val(data.item.description);
                $('#subject').val(data.item.subject);
                $('#email_to').val(data.item.email_to);
                $('#email_cc').val(data.item.email_cc);
                $('#email_bcc').val(data.item.email_bcc);
                $('#active').val(data.item.active);
            })
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    var actionType = $('#btn-save').val();
                    $('#btn-save').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('settings-email-principal.store') }}", 
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
                url: "settings/email-principal/" + dataId,
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