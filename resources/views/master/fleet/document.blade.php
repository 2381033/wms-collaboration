@extends('layouts.main')

@section('title')
    Fleet Master
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Fleet Master</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Fleet Master</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="master-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="branch-link" data-toggle="tab" href="#branch-tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-box"></i> Branch</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="document-link" data-toggle="tab" href="#document-tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-box"></i> Document</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="group-link" data-toggle="tab" href="#group-tab" role="tab" aria-controls="vehicle" aria-selected="false">
                            <i class="fas fa-box"></i> Inspection Group</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="item-link" data-toggle="tab" href="#item-tab" role="tab" aria-controls="vehicle" aria-selected="false">
                            <i class="fas fa-box"></i> Inspection Item</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="driver-link" data-toggle="tab" href="#driver-tab" role="tab" aria-controls="vehicle" aria-selected="false">
                            <i class="fas fa-box"></i> Driver</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="type-link" data-toggle="tab" href="#type-tab" role="tab" aria-controls="vehicle" aria-selected="false">
                            <i class="fas fa-box"></i> Vehicle Type</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="vehicle-link" data-toggle="tab" href="#vehicle-tab" role="tab" aria-controls="vehicle" aria-selected="false">
                            <i class="fas fa-box"></i> Vehicle</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="masterTab">                 
                        <div class="tab-pane fade show active" id="branch-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">            
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('branch');" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>    
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-branch" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Branch Name</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="document-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">            
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('document');" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>    
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-document" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Document Name</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="group-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">            
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('group');" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>    
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-group" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Group Name</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="item-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-6">      
                                    <div class="form-group">
                                        <label for="group_type">Group Name</label>
                                        <select name="group_type" id="group_type" class="form-control">
                                            @foreach ($group_list as $item)
                                                <option value="{{$item->id}}">{{$item->group_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" id="btn-add-item"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-item" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Item Type</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="driver-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-6">      
                                    <div class="form-group">
                                        <label for="branch_filter_driver">Branch Name</label>
                                        <select name="branch_filter_driver" id="branch_filter_driver" class="form-control">
                                            @foreach ($branch_list as $item)
                                                <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" id="btn-add-driver"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-driver" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Driver Name</th>
                                                    <th>SIM No</th>
                                                    <th>SIM Expired</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="type-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">                                
                                <div class="col-md-6">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" id="btn-add-type"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-type" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Vehicle Type</th>
                                                    <th>Description</th>
                                                    <th>CBM</th>
                                                    <th>Weight</th>
                                                    <th>Status</th>     
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="vehicle-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-6">      
                                    <div class="form-group">
                                        <label for="branch_filter_vehicle">Branch Name</label>
                                        <select name="branch_filter_vehicle" id="branch_filter_vehicle" class="form-control">
                                            @foreach ($branch_list as $item)
                                                <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>                       
                                <div class="col-md-6">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" id="btn-add-vehicle"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-vehicle" class="table table-striped table-bordered table-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Vehicle No</th>
                                                    <th>Vehicle Type</th>
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
                <input type="hidden" id="job_type" name="job_type">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" autocomplete="off" id="description" name="description" class="form-control">
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal-item">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-item" name="form-item" method="post">
                @csrf
                <input type="hidden" id="item_id" name="item_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">                              
                            <div class="form-group">
                                <label for="group_id">Group Name</label>
                                <select name="group_id" id="group_id" class="form-control">
                                    @foreach ($group_list as $item)
                                        <option value="{{$item->id}}">{{$item->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" autocomplete="off" id="item_name" name="item_name" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Item Type</label>
                                <select class="custom-select" id="item_type" name="item_type">
                                    <option value="Expired">Expired</option>
                                    <option value="Action">Action</option>
                                    <option value="Remarks">Remarks</option>
                                </select>
                            </div>
                        </div>
                    </div>     
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Active</label>
                                <select class="custom-select" id="active_item" name="active_item">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>        
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-item"><i class="fas fa-save"></i> <span>Save</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-driver">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-driver" name="form-driver" method="post">
                @csrf
                <input type="hidden" id="driver_id" name="driver_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">                              
                            <div class="form-group">
                                <label for="branch_driver">Group Name</label>
                                <select name="branch_driver" id="branch_driver" class="form-control">
                                    @foreach ($branch_list as $item)
                                        <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Driver Name</label>
                                <input type="text" autocomplete="off" id="driver_name" name="driver_name" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" autocomplete="off" id="phone" name="phone" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Join Date</label>
                                <input type="text" autocomplete="off" id="join_date" name="join_date" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>SIM No</label>
                                <input type="text" autocomplete="off" id="sim_no" name="sim_no" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>SIM Date</label>
                                <input type="text" autocomplete="off" id="sim_date" name="sim_date" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Active</label>
                                <select class="custom-select" id="active_driver" name="active_driver">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>        
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-driver"><i class="fas fa-save"></i> <span>Save</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-type">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-type" name="form-type" method="post">
                @csrf
                <input type="hidden" id="type_id" name="type_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Code</label>
                                <input type="text" autocomplete="off" id="vehicle_type" name="vehicle_type" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" autocomplete="off" id="type_name" name="type_name" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>CBM</label>
                                <input type="text" autocomplete="off" id="cbm" name="cbm" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Weight</label>
                                <input type="text" autocomplete="off" id="weight" name="weight" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Pallet Count</label>
                                <input type="text" autocomplete="off" id="pallet_count" name="pallet_count" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Active</label>
                                <select class="custom-select" id="active_type" name="active_type">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>        
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-driver"><i class="fas fa-save"></i> <span>Save</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-vehicle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-vehicle" name="form-vehicle" method="post">
                @csrf
                <input type="hidden" id="vehicle_id" name="vehicle_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Branch Name</label>
                                <select name="branch_vehicle" id="branch_vehicle" class="custom-select">
                                    @foreach ($branch_list as $item)
                                        <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">    
                            <div class="form-group">
                                <label>Vehicle Type</label>
                                <select name="vehicle_type_id" id="vehicle_type_id" class="custom-select">
                                    @foreach ($type_list as $item)
                                        <option value="{{$item->id}}">{{$item->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Code</label>
                                <input type="text" autocomplete="off" id="vehicle_code" name="vehicle_code" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Vehicle No</label>
                                <input type="text" autocomplete="off" id="vehicle_no" name="vehicle_no" class="form-control">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Active</label>
                                <select class="custom-select" id="active_vehicle" name="active_vehicle">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>        
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-vehicle"><i class="fas fa-save"></i> <span>Save</span></button>
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
        
        $('#join_date').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        });

        $('#sim_date').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        });

        load_data();

        function load_data() {
            link_id = $('.nav-tabs .active').attr('id');

            if (link_id == 'branch-link') {
                load_branch();
            } else if (link_id == 'document-link') {
                load_document();
            } else if (link_id == 'group-link') {
                load_group();
            } else if (link_id == 'item-link') {
                load_item();
            } else if (link_id == 'driver-link') {
                load_driver();
            } else if (link_id == 'type-link') {
                load_type();
            } else if (link_id == 'vehicle-link') {
                load_vehicle();
            } 
        }

        $('#branch-link').on('click', function() {
            load_branch();
        });

        $('#document-link').on('click', function() {
            load_document();
        });

        $('#group-link').on('click', function() {
            load_group();
        });

        $('#item-link').on('click', function() {
            load_item();
        });

        $('#driver-link').on('click', function() {
            load_driver();
        });

        $('#type-link').on('click', function() {
            load_type();
        });

        $('#vehicle-link').on('click', function() {
            load_vehicle();
        });

        function load_branch() {
            $('#table-branch').DataTable().destroy();
            $('#table-branch').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('master-branch.index') }}",
                    type : "GET"
                },
                columns : [
                    { data:'branch_name', name:'branch_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_document() {
            $('#table-document').DataTable().destroy();
            $('#table-document').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-document.index') }}",
                    type : "GET"
                },
                columns : [
                    { data:'document_name', name:'document_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_group() {
            $('#table-group').DataTable().destroy();
            $('#table-group').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-group.index') }}",
                    type : "GET"
                },
                columns : [
                    { data:'group_name', name:'group_name' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_driver() {
            $('#table-driver').DataTable().destroy();
            $('#table-driver').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-driver.index') }}",
                    type : "GET",
                    data: {
                        branch_id: $("#branch_filter_driver").val()
                    }
                },
                columns : [
                    { data:'driver_name', name:'driver_name' },
                    { data:'sim_no', name:'sim_no' },
                    { data:'sim_date', name:'sim_date' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_type() {
            $('#table-type').DataTable().destroy();
            $('#table-type').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-vehicle-type.index') }}",
                    type : "GET"
                },
                columns : [
                    { data:'vehicle_type', name:'vehicle_type' },
                    { data:'description', name:'description' },
                    { data:'cbm', name:'cbm' },
                    { data:'weight', name:'weight' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_vehicle() {
            $('#table-vehicle').DataTable().destroy();
            $('#table-vehicle').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-vehicle.index') }}",
                    type : "GET",
                    data: {
                        branch_id: $("#branch_filter_vehicle").val()
                    }
                },
                columns : [
                    { data:'vehicle_code', name:'vehicle_code' },
                    { data:'vehicle_no', name:'vehicle_no' },
                    { data:'description', name:'description' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        function load_item() {
            $('#table-item').DataTable().destroy();
            $('#table-item').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-item.index') }}",
                    type : "GET",
                    data : {
                        group_id: $("#group_type").val()
                    }
                },
                columns : [
                    { data:'item_name', name:'item_name' },
                    { data:'item_type', name:'item_type' },
                    { data:'active', name:'active' },
                    { data: 'action', name: 'action' }
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }

        $('#group_type').on('change', function() {
            load_item();
        });

        $('#branch_filter_driver').on('change', function() {
            load_driver();
        });

        $('#branch_filter_vehicle').on('change', function() {
            load_vehicle();
        });
        
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    let job_type = $("#job_type").val();
                    let urlRequest = "";
                    let tableName = "";

                    if ( job_type == "branch" ) {
                        urlRequest = "{{ route('master-branch.store') }}";
                        tableName = '#table-branch';
                    } else if (job_type == "document") {
                        urlRequest = "{{ route('fleet-document.store') }}";
                        tableName = '#table-document';
                    } else if (job_type == "group") {
                        urlRequest = "{{ route('fleet-group.store') }}";
                        tableName = '#table-group';
                    }
                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: urlRequest, 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-entry').trigger("reset"); 
                                $('#modal-entry').modal('hide'); 
                                var oTable = $(tableName).dataTable(); 
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

        $('#btn-add-item').click(function () {
            $('#item_id').val(''); 
            $('#form-item').trigger("reset"); 
            $('#modal-title').html("Add New");
            $('#modal-item').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
        
        if ($("#form-item").length > 0) {
            $("#form-item").validate({
                submitHandler: function (form) {
                    let urlRequest = "{{ route('fleet-item.store') }}";
                    let tableName = '#table-item';
                    
                    $.ajax({
                        data: $('#form-item').serialize(),
                        url: urlRequest, 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-item').trigger("reset"); 
                                $('#modal-item').modal('hide'); 
                                var oTable = $(tableName).dataTable(); 
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

        $('body').on('click', '.edit-branch', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('branch-master/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {      
                    $('#modal-title').html("Edit");
                    $('#btn-save').val("Edit");
                    $('#modal-entry').modal('show');

                    $('#job_type').val("branch");
                    $('#id').val(data.id);
                    $('#description').val(data.branch_name);
                    $('#active').val(data.active);
                }
            });
        });

        $('body').on('click', '.edit-document', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-document/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {      
                    $('#modal-title').html("Edit");
                    $('#btn-save').val("Edit");
                    $('#modal-entry').modal('show');

                    $('#job_type').val("document");
                    $('#id').val(data.id);
                    $('#description').val(data.document_name);
                    $('#active').val(data.active);
                }
            });
        });

        $('body').on('click', '.edit-group', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-group/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {      
                    $('#modal-title').html("Edit");
                    $('#btn-save').val("Edit");
                    $('#modal-entry').modal('show');

                    $('#job_type').val("group");
                    $('#id').val(data.id);
                    $('#description').val(data.group_name);
                    $('#active').val(data.active);
                }
            });
        });

        $('body').on('click', '.edit-item', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-group/item/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {      
                    $('#modal-title').html("Edit");
                    $('#btn-save').val("Edit");
                    $('#modal-item').modal('show');

                    $('#item_id').val(data.id);
                    $('#group_id').val(data.group_id);
                    $('#item_name').val(data.item_name);
                    $('#item_type').val(data.item_type);
                    $('#active_item').val(data.active);
                }
            });
        });

        $('body').on('click', '.edit-driver', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-driver/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {   
                    console.log(data);
                    var join_date = "";
                    var sim_date = "";
                    
                    if (data.join_date !== null ) {
                        join_date = getFormatDate(data.join_date);
                    }

                    if (data.sim_date !== null ) {
                        sim_date = getFormatDate(data.sim_date);
                    }
                    
                    $('#modal-title').html("Edit");
                    $('#modal-driver').modal('show');

                    $('#driver_id').val(data.id);
                    $('#branch_driver').val(data.branch_id);
                    $('#driver_name').val(data.driver_name);
                    $('#phone').val(data.phone);
                    $('#join_date').val(join_date);
                    $('#sim_no').val(data.sim_no);
                    $('#sim_date').val(sim_date);
                    $('#active_driver').val(data.active);
                }
            });
        });

        $('#btn-add-driver').click(function () {
            $('#driver_id').val(''); 
            $('#form-driver').trigger("reset"); 
            $('#modal-title').html("Add New");
            $('#modal-driver').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
        
        if ($("#form-driver").length > 0) {
            $("#form-driver").validate({
                submitHandler: function (form) {
                    let urlRequest = "{{ route('fleet-driver.store') }}";
                    let tableName = '#table-driver';
                    
                    $.ajax({
                        data: $('#form-driver').serialize(),
                        url: urlRequest, 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-driver').trigger("reset"); 
                                $('#modal-driver').modal('hide'); 
                                var oTable = $(tableName).dataTable(); 
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

        $('body').on('click', '.edit-type', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-vehicle-type/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {   
                    $('#modal-title').html("Edit");
                    $('#modal-type').modal('show');

                    $('#type_id').val(data.id);
                    $('#vehicle_type').val(data.vehicle_type);
                    $('#type_name').val(data.description);
                    $('#cbm').val(data.cbm);
                    $('#weight').val(data.weight);
                    $('#pallet_count').val(data.pallet_count);
                    $('#active_type').val(data.active);
                }
            });
        });

        $('#btn-add-type').click(function () {
            $('#type_id').val(''); 
            $('#form-type').trigger("reset"); 
            $('#modal-title').html("Add New");
            $('#modal-type').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
        
        if ($("#form-type").length > 0) {
            $("#form-type").validate({
                submitHandler: function (form) {
                    let urlRequest = "{{ route('fleet-vehicle-type.store') }}";
                    let tableName = '#table-type';
                    
                    $.ajax({
                        data: $('#form-type').serialize(),
                        url: urlRequest, 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-type').trigger("reset"); 
                                $('#modal-type').modal('hide'); 
                                var oTable = $(tableName).dataTable(); 
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

        $('body').on('click', '.edit-vehicle', function () {
            var data_id = $(this).data('id');
            let urlRequest = "{{ url('fleet-vehicle/edit') }}/" + data_id;

            $.ajax({
                url: urlRequest, 
                type: "GET", 
                dataType: 'json',
                success: function (data) {   
                    $('#modal-title').html("Edit");
                    $('#modal-vehicle').modal('show');

                    $('#vehicle_id').val(data.id);
                    $('#branch_vehicle').val(data.branch_id);
                    $('#vehicle_type_id').val(data.type_id);
                    $('#vehicle_code').val(data.vehicle_code);
                    $('#vehicle_no').val(data.vehicle_no);
                    $('#active_vehicle').val(data.active);
                }
            });
        });

        $('#btn-add-vehicle').click(function () {
            $('#vehicle_id').val(''); 
            $('#form-vehicle').trigger("reset"); 
            $('#modal-title').html("Add New");
            $('#modal-vehicle').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });
        
        if ($("#form-vehicle").length > 0) {
            $("#form-vehicle").validate({
                submitHandler: function (form) {
                    let urlRequest = "{{ route('fleet-vehicle.store') }}";
                    let tableName = '#table-vehicle';
                    
                    $.ajax({
                        data: $('#form-vehicle').serialize(),
                        url: urlRequest, 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){
                                $('#form-vehicle').trigger("reset"); 
                                $('#modal-vehicle').modal('hide'); 
                                var oTable = $(tableName).dataTable(); 
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

    function addMaster (job_type) {
        $('#job_type').val(job_type); 
        $('#id').val(''); 
        $('#form-entry').trigger("reset"); 
        $('#modal-title').html("Add New");
        $('#modal-entry').modal({
            backdrop: 'static', 
            keyboard: false,
            show: true
        }); 
    }
</script>
@endpush