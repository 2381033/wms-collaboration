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
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-link" data-toggle="tab" href="#info-tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-box"></i> Principal Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="branch-link" data-toggle="tab" href="#branch-tab" role="tab" aria-controls="branch" aria-selected="false">
                            <i class="fas fa-box"></i> Branch Autorization</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="site-link" data-toggle="tab" href="#site-tab" role="tab" aria-controls="site" aria-selected="false">
                            <i class="fas fa-box"></i> Site Autorization</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="storage-link" data-toggle="tab" href="#storage-tab" role="tab" aria-controls="packing" aria-selected="false">
                            <i class="fas fa-box"></i> Storage Master</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="handling-link" data-toggle="tab" href="#handling-tab" role="tab" aria-controls="grn" aria-selected="false">
                            <i class="fas fa-box"></i> Handling Master</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="inboundTab">
                        <div class="tab-pane fade show active" id="info-tab" role="tabpanel" aria-labelledby="home-tab5">                          
                            <form id="form-entry" name="form-entry" method="post">
                                @csrf
                                <input type="hidden" id="id" name="id" @isset($view->id) value="{{$view->id}}" @endisset>
                                <div class="row mt-3">
                                    <div class="col-md-4">    
                                        <div class="form-group">
                                            <label>Short Name</label>
                                            <input type="text" autocomplete="off" id="short_name" name="short_name" @isset($view->short_name) value="{{$view->short_name}}" @endisset class="form-control">
                                            @if ($errors->has('short_name'))
                                                <span class="help-block">{{ $errors->first('short_name') }}</span>
                                            @endif
                                        </div>
                                    </div> 
                                    <div class="col-md-8">    
                                        <div class="form-group">
                                            <label>Principal Name</label>
                                            <input type="text" autocomplete="off" id="principal_name" name="principal_name" @isset($view->principal_name) value="{{$view->principal_name}}" @endisset class="form-control">
                                            @if ($errors->has('principal_name'))
                                                <span class="help-block">{{ $errors->first('principal_name') }}</span>
                                            @endif
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" autocomplete="off" id="address1" name="address1" @isset($view->address1) value="{{$view->address1}}" @endisset class="form-control mb-1">                            
                                        </div>
                                    </div> 
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input type="text" autocomplete="off" id="address2" name="address2" @isset($view->address2) value="{{$view->address2}}" @endisset class="form-control mb-1">
                                        </div>
                                    </div> 
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <input type="text" autocomplete="off" id="address3" name="address3" @isset($view->address3) value="{{$view->address3}}" @endisset class="form-control mb-1">                            
                                        </div>
                                    </div> 
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <input type="text" autocomplete="off" id="address4" name="address4" @isset($view->address4) value="{{$view->address4}}" @endisset class="form-control mb-1">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Interface Mode</label>
                                            <select class="custom-select" id="interface_mode" name="interface_mode">
                                                <option value="FMCG">FMCG - Standard Product Pack-keys</option>
                                                <option value="GML">GML - Project Logistics (Non-standard Shipments)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" autocomplete="off" id="email" name="email" @isset($view->email) value="{{$view->email}}" @endisset class="form-control">
                                        </div>
                                    </div> 
                                </div>     
                                <div class="row">
                                    <div class="col-md-3">    
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" autocomplete="off" id="phone" name="phone" @isset($view->phone) value="{{$view->phone}}" @endisset class="form-control">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">    
                                        <div class="form-group">
                                            <label>PIC Name</label>
                                            <input type="text" autocomplete="off" id="pic_name" name="pic_name" @isset($view->pic_name) value="{{$view->pic_name}}" @endisset class="form-control">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">    
                                        <div class="form-group">
                                            <label>PIC Phone</label>
                                            <input type="text" autocomplete="off" id="pic_phone" name="pic_phone" @isset($view->pic_phone) value="{{$view->pic_phone}}" @endisset class="form-control">
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
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-save"><i class="fas fa-save"></i> <span>Save</span></button>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="branch-tab" role="tabpanel" aria-labelledby="vehicle-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-branch-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="branch_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th>Branch Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="site-tab" role="tabpanel" aria-labelledby="vehicle-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-site-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
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
                        <div class="tab-pane fade show" id="storage-tab" role="tabpanel" aria-labelledby="vehicle-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-storage-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="storage_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th>Action</th>
                                                    <th>FOC</th>
                                                    <th>Currency Name</th>
                                                    <th>CPU</th>
                                                    <th>Quota</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="handling-tab" role="tabpanel" aria-labelledby="vehicle-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-handling-btn"><i class="fas fa-plus"></i> <span>Add</span></button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="handling_table" class="table table-striped table-bordered table-sm" style="width:100%;" cellspacing="0" width="100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th>Action</th>
                                                    <th>Type</th>
                                                    <th>FOC</th>
                                                    <th>CPU</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> 
@endsection

@section('modal')
<div class="modal fade" tabindex="-1" role="dialog" id="add-branch-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-branch" name="form-branch" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Add Branch Authorization</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @csrf
                    <div class="col-md-12">                        
                        <div class="form-group">
                            <input type="hidden" id="principal_branch" name="principal_branch">
                            <label>Branch Name</label>
                            <select name="branch_id" id="branch_id" class="form-control">
                                @foreach ($branch_list as $item)
                                    <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                @endforeach
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

<div class="modal fade" tabindex="-1" role="dialog" id="add-storage-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-storage" name="form-storage" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Storage</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @csrf
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <input type="hidden" id="storage_id" name="storage_id">
                            <input type="hidden" id="principal_storage" name="principal_storage">
                            <label>Form Of Charge</label>
                            <select name="foc" id="foc" class="form-control">
                                @foreach ($unit_list as $item)
                                    <option value="{{$item->code}}">{{$item->uom_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Currency</label>
                            <select name="currency_id" id="currency_id" class="form-control">
                                <option value=""></option>
                                @foreach ($currency_list as $item)
                                    <option value="{{$item->id}}">{{$item->currency_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Charge Per Unit</label>
                            <input type="text" name="cpu" id="cpu" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Quota</label>
                            <input type="text" name="quota" id="quota" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>CPU Exceeding Quota</label>
                            <input type="text" name="cpu_ext" id="cpu_ext" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Flat Rate</label>
                            <input type="text" name="flat_rate" id="flat_rate" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Active</label>
                            <select class="custom-select" id="active_storage" name="active_storage">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="submit" class="btn btn-success btn-sm" id="save-storage-btn"><i class="fas fa-save"></i> <span>Save</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="add-handling-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-handling" name="form-handling" method="post">
            <div class="modal-header">
                <h5 class="modal-title">Handling</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @csrf
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <input type="hidden" id="handling_id" name="handling_id">
                            <input type="hidden" id="principal_handling" name="principal_handling">
                            <label>Type</label>
                            <select name="job_type" id="job_type" class="form-control">
                                <option value=""></option>
                                <option value="IMP">Inbound</option>
                                <option value="EXP">Outbound</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Form Of Charge</label>
                            <select name="foc_hand" id="foc_hand" class="form-control">
                                <option value=""></option>
                                @foreach ($unit_list as $item)
                                    <option value="{{$item->code}}">{{$item->uom_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Charge Per Unit</label>
                            <input type="text" name="cpu_hand" id="cpu_hand" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>CPU Middle</label>
                            <input type="text" name="cpu_middle" id="cpu_middle" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>CPU Lowest</label>
                            <input type="text" name="cpu_lowest" id="cpu_lowest" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Quota</label>
                            <input type="text" name="quota_hand" id="quota_hand" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>CPU Exceeding Quota</label>
                            <input type="text" name="cpu_ext_hand" id="cpu_ext_hand" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>FOC Return</label>
                            <select name="foc_return" id="foc_return" class="form-control">
                                <option value=""></option>
                                @foreach ($unit_list as $item)
                                    <option value="{{$item->code}}">{{$item->uom_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>CPU Return</label>
                            <input type="text" name="cpu_return" id="cpu_return" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-4">                        
                        <div class="form-group">
                            <label>Quota Return</label>
                            <input type="text" name="quota_return" id="quota_return" value="0" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control">                                
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Active</label>
                            <select class="custom-select" id="active_handling" name="active_handling">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="submit" class="btn btn-success btn-sm" id="save-handling-btn"><i class="fas fa-save"></i> <span>Save</span></button>
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
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('principal.store') }}", 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-entry').trigger("reset"); 
                                $('#modal-entry').modal('hide'); 
                                
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });

                                window.location = data.success;
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
                        }
                    });
                }
            })
        }
        
        load_data();

        function load_data() {
            link_id = $('.nav-tabs .active').attr('id');

            if (link_id == 'branch-link') {
                load_branch();
            } else if (link_id == 'site-link') {
                load_site();
            } else if (link_id == 'storage-link') {
                load_storage();
            } else if (link_id == 'handling-link') {
                load_handling();
            } 
        }

        $('#branch-link').on('click', function() {
            load_branch();
        });

        $('#site-link').on('click', function() {
            load_site();
        });

        $('#storage-link').on('click', function() {
            load_storage();
        });

        $('#handling-link').on('click', function() {
            load_handling();
        });

        function load_branch() {           
            var principal = $("#id").val();
            
            $('#branch_table').DataTable().destroy();
            $('#branch_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "{{ route('principal-branch.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: principal
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
        };

        function load_site() {           
            var principal = $("#id").val();
            
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
                        principal_id: principal
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
        };

        function load_storage() {           
            var principal = $("#id").val();
            
            $('#storage_table').DataTable().destroy();
            $('#storage_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "{{ route('storage.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: principal
                    } 
                },
                columns : [
                    { data:'action', name:'action'},
                    { data:'uom_name', name:'uom_name'},
                    { data:'currency_name', name:'currency_name'},
                    { data:'cpu', name:'cpu'},
                    { data:'quota', name:'quota'},
                    { data:'remarks', name:'remarks'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        };

        function load_handling() {           
            var principal = $("#id").val();
            
            $('#handling_table').DataTable().destroy();
            $('#handling_table').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                destroy : true,
                paging : false,
                info : false,
                ajax : {
                    url : "{{ route('handling.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: principal
                    } 
                },
                columns : [
                    { data:'action', name:'action'},
                    { data:'job_type', name:'job_type'},
                    { data:'uom_name', name:'uom_name'},
                    { data:'cpu', name:'cpu'},
                    { data:'remarks', name:'remarks'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        };

        $(document).on('click', '.delete-branch', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('branch');
            $('#modal-konfirmasi').modal('show');
        });

        $(document).on('click', '.delete-site', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('site');
            $('#modal-konfirmasi').modal('show');
        });

        $(document).on('click', '.delete-storage', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('storage');
            $('#modal-konfirmasi').modal('show');
        });

        $('#btn-delete').click(function () {
            var action = $('#action-delete').val();
            var requestUrl = "";

            if (action == 'branch') {
                var principal_id = $('#id').val();
                requestUrl = "{{ URL('product-master/principal-branch/') }}/" + principal_id + "/" + dataId; 
            } else if (action == 'site') {
                var principal_id = $('#id').val();
                requestUrl = "{{ URL('product-master/principal-site/') }}/" + principal_id + "/" + dataId; 
            } else if (action == 'storage') {
                requestUrl = "{{ URL('product-master/storage/') }}/" + dataId; 
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
                    if (action == 'site') {
                        oTable = $('#site_table').dataTable();
                    } else if (action == 'storage') {
                        oTable = $('#storage_table').dataTable();
                    } 
                        
                    oTable.fnDraw(false);
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

        $('#add-branch-btn').click(function () {   
            var principal = $("#id").val();

            if ( principal > 0 ) {
                $("#principal_branch").val(principal); 
                $('#add-branch-modal').modal({
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }); 
            } else {
                swal({
                    icon: "error",
                    text: "Principal not found."   
                });
            }
        });

        $('#add-site-btn').click(function () {   
            var principal = $("#id").val();

            if ( principal > 0 ) {
                $("#principal_site").val(principal); 
                $('#add-site-modal').modal({
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }); 
            } else {
                swal({
                    icon: "error",
                    text: "Principal not found."   
                });
            }
        });

        $('#add-storage-btn').click(function () {   
            $('#form-storage').trigger("reset");
            var principal = $("#id").val();

            if ( principal > 0 ) {
                $("#principal_storage").val(principal); 
                $('#add-storage-modal').modal({
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }); 
            } else {
                swal({
                    icon: "error",
                    text: "Principal not found."   
                });
            }
        });

        $('body').on('click', '.edit-storage', function () {
            var data_id = $(this).data('id');
            
            $.ajax({
                data: {                    
                    "id": data_id,
                    _token: CSRF_TOKEN
                },
                url: "{{ route('storage.edit') }}",
                type: 'POST',
                dataType: 'json',
                success: function (data) { 
                    $('#add-storage-modal').modal('show');    
       
                    $('#storage_id').val(data.id);
                    $('#principal_storage').val(data.principal_id);
                    $('#currency_id').val(data.currency_id);
                    $('#foc').val(data.foc);
                    $('#cpu').val(data.cpu);                      
                    $('#quota').val(data.quota);                
                    $('#cpu_ext').val(data.cpu_ext);         
                    $('#flat_rate').val(data.flat_rate);    
                    $('#remarks').val(data.remarks);    
                    $('#active').val(data.active);    
                },
                error: function(data) {
                    console.log(data);
                }
            });            
        });
            
        if ($("#form-branch").length > 0) {
            $("#form-branch").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-branch').serialize(), 
                        url: "{{ route('principal-branch.store') }}",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-branch').trigger("reset"); 
                                $('#add-branch-modal').modal('hide');
                                
                                var oTable = $('#branch_table').dataTable(); 
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
                        }
                    });
                }
            })
        }
            
        if ($("#form-site").length > 0) {
            $("#form-site").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-site').serialize(), 
                        url: "{{ route('principal-site.store') }}",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-site').trigger("reset"); 
                                $('#add-site-modal').modal('hide');
                                
                                var oTable = $('#site_table').dataTable(); 
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
                        }
                    });
                }
            })
        }
        
        if ($("#form-storage").length > 0) {
            $("#form-storage").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-storage').serialize(), 
                        url: "{{ route('storage.store') }}",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-storage').trigger("reset"); 
                                $('#add-storage-modal').modal('hide');
                                
                                var oTable = $('#storage_table').dataTable(); 
                                oTable.fnDraw(false); 
                                                
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                     
                                });
                            } else {
                                var pesan = data
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
                        }
                    });
                }
            })
        }

        $('#add-handling-btn').click(function () {   
            $('#form-handling').trigger("reset");
            var principal = $("#id").val();

            if ( principal > 0 ) {
                $("#principal_handling").val(principal); 
                $('#add-handling-modal').modal({
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                }); 
            } else {
                swal({
                    icon: "error",
                    text: "Principal not found."   
                });
            }
        });

        $('body').on('click', '.edit-handling', function () {
            var data_id = $(this).data('id');
            
            $.ajax({
                data: {                    
                    "id": data_id,
                    _token: CSRF_TOKEN
                },
                url: "{{ route('handling.edit') }}",
                type: 'POST',
                dataType: 'json',
                success: function (data) { 
                    $('#add-handling-modal').modal('show');    
       
                    $('#handling_id').val(data.id);
                    $('#principal_handling').val(data.principal_id);
                    $('#job_type').val(data.job_type);
                    $('#foc_hand').val(data.foc);
                    $('#cpu_hand').val(data.cpu);           
                    $('#cpu_middle').val(data.cpu_middle);                
                    $('#cpu_lowest').val(data.cpu_lowest);                
                    $('#quota_hand').val(data.quota);                
                    $('#cpu_ext_hand').val(data.cpu_ext);         
                    $('#foc_return').val(data.foc_return);    
                    $('#cpu_return').val(data.cpu_return);    
                    $('#quota_return').val(data.quota_return);    
                    $('#remarks').val(data.remarks);    
                    $('#active').val(data.active);    
                },
                error: function(data) {
                    console.log(data);
                }
            });            
        });
            
        if ($("#form-handling").length > 0) {
            $("#form-handling").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $('#form-handling').serialize(), 
                        url: "{{ route('handling.store') }}",
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-handling').trigger("reset"); 
                                $('#add-handling-modal').modal('hide');
                                
                                var oTable = $('#handling_table').dataTable(); 
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
                        }
                    });
                }
            })
        }
    });
</script>
@endpush