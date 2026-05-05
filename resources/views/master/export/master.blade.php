@extends('layouts.main')

@section('title')
    Export Master
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export Master</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Export Master</li>
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
                            <a class="nav-link active" id="forwarder-link" data-toggle="tab" href="#forwarder-tab"
                                role="tab" aria-controls="home" aria-selected="true">
                                <i class="fas fa-box"></i> Forwarder</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="consignee-link" data-toggle="tab" href="#consignee-tab" role="tab"
                                aria-controls="home" aria-selected="true">
                                <i class="fas fa-box"></i> Consignee</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="shipper-link" data-toggle="tab" href="#shipper-tab" role="tab"
                                aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-box"></i> Shipper</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" id="checklist-link" data-toggle="tab" href="#checklist-tab" role="tab"
                                aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-box"></i> Checklist</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="invoicetype-link" data-toggle="tab" href="#invoicetype-tab"
                                role="tab" aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-box"></i> Invoice Type</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link" id="location-link" data-toggle="tab" href="#location-tab" role="tab"
                                aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-box"></i> Location</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="checker-link" data-toggle="tab" href="#checker-tab" role="tab"
                                aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-users"></i> Checker</a>
                        </li>
                        {{-- <li class="nav-item"><a class="nav-link" id="checker-link" data-toggle="tab" href="#checker-tab" role="tab"aria-controls="vehicle" aria-selected="false"><i class="fas fa-users"></i> Checker</a></li> --}}
                    </ul>
                    <div class="tab-content" id="masterTab">
                        <div class="tab-pane fade show active" id="forwarder-tab" role="tabpanel"
                            aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button class="btn btn-primary btn-sm" id="btn-add-forwarder"><i
                                                class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-forwarder" class="table table-striped table-bordered table-sm"
                                            style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th>Company Name</th>
                                                    <th>Storage Cost</th>
                                                    <th>Admin Cost</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="consignee-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('consignee');" class="btn btn-primary btn-sm"
                                            id="btn-add-consignee"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-consignee" class="table table-striped table-bordered table-sm"
                                            style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="shipper-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('shipper');" class="btn btn-primary btn-sm"
                                            id="btn-add-shipper"><i class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-shipper" class="table table-striped table-bordered table-sm"
                                            style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="checklist-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button onclick="addMaster('checklist');" class="btn btn-primary btn-sm"
                                            id="btn-add-checklist"><i class="fas fa-plus"></i> <span>Add
                                                New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-checklist" class="table table-striped table-bordered table-sm"
                                            style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="invoicetype-tab" role="tabpanel"
                            aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button class="btn btn-primary btn-sm" id="btn-add-invoicetype"><i
                                                class="fas fa-plus"></i> <span>Add New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table id="table-invoicetype" class="table table-striped table-bordered table-sm"
                                            style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>Invoice Flag</th>
                                                    <th>Free Flag</th>
                                                    <th>Free Days</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="checker-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button class="btn btn-primary btn-sm" onclick="addChecker()"><i
                                                class="fas fa-plus"></i>
                                            <span>Add New</span></button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm" style="width:100%;"
                                            id="tableChecker">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>NO</th>
                                                    <th>Branch</th>
                                                    <th>Nama</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($checker as $item)
                                                    <tr class="text-center">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->branch_name }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->status ? 'Active' : 'Inactive' }}</td>
                                                        <td>
                                                            {{-- @if ($item->status == 1) <a href="{{ url('actionChecker/disable/' . $item->id) }}" class="btn btn-dark btn-sm"><i class="fas fa-power-off"></i> Disable</a> --}}
                                                            {{-- @else <a href="{{ url('actionChecker/enable/' . $item->id) }}" class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i> Enable</a> @endif --}}
                                                            <a href="javascript:void(0)" data-toggle="tooltip"
                                                                data-id="{{ $item->id }}" data-original-title="Edit"
                                                                class="btn btn-info btn-sm edit-group"
                                                                onclick="editChecker({{ $item->id }})"><i
                                                                    class="far fa-edit"></i> Edit</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="location-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group mb-3">
                                        <button class="btn btn-primary btn-sm" onclick="addLocation()"><i
                                                class="fas fa-plus"></i>
                                            <span>Add New</span>
                                        </button>
                                        <button class="btn btn-sm text-white" style="background-color: green" onclick="uploadLocation()"><i
                                                class="fas fa-file-excel"></i>
                                            <span>Upload Excel</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm" style="width:100%;"
                                            id="tableLocation">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>No.</th>
                                                    <th>Branch</th>
                                                    <th>Location Code</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($location as $item)
                                                    <tr class="text-center">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->branch_name }}</td>
                                                        <td>{{ $item->location_code }}</td>
                                                        <td>{{ $item->active == 'Yes' ? 'Active' : 'Inactive' }}</td>
                                                        <td>
                                                            @if ($item->active == 'Yes')
                                                            <a href="javascript:void(0)" data-toggle="tooltip"
                                                                data-id="{{ $item->id }}" data-original-title="Edit"
                                                                class="btn btn-info btn-sm edit-group"
                                                                onclick="editLocation({{ $item->id }})">
                                                                <i class="far fa-edit"></i> Edit
                                                            </a>
                                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm"
                                                                onclick="toggleLocation({{ $item->id }}, 'disable')">
                                                                <i class="fas fa-power-off"></i> Disable
                                                            </a>
                                                            @else    
                                                            <a href="javascript:void(0)" class="btn btn-success btn-sm"
                                                                onclick="toggleLocation({{ $item->id }}, 'enable')">
                                                                <i class="far fa-check-circle"></i> Enable
                                                            </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
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
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="my-select">Branch</label>
                                    <select id="my-select" class="form-control" name="branch_id" required>
                                        <option value="" selected disabled>Silahkan Pilih</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" autocomplete="off" id="description" name="description"
                                        class="form-control">
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
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save"><i class="fas fa-save"></i>
                            <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-forwarder">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New Fordwarder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-forwarder" name="form-forwarder" method="post">
                    @csrf
                    <input type="hidden" id="forwarder_id" name="forwarder_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="my-select">Branch</label>
                                    <select id="my-select" class="form-control" name="branch_id" required>
                                        <option value="" selected disabled>Silahkan Pilih</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" autocomplete="off" id="forwarder_name" name="forwarder_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Storage Cost</label>
                                    <input type="text" autocomplete="off" id="storage_amount" name="storage_amount"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Admin Cost</label>
                                    <input type="text" autocomplete="off" id="adm_amount" name="adm_amount"
                                        value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="custom-select" id="forwarder_active" name="forwarder_active">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-forwarder"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-invoicetype">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-invoicetype" name="form-invoicetype" method="post">
                    @csrf
                    <input type="hidden" id="type_id" name="type_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" autocomplete="off" id="type_name" name="type_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Invoice Flag</label>
                                    <select class="custom-select" id="invoice_flag" name="invoice_flag">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Free Flag</label>
                                    <select class="custom-select" id="free_flag" name="free_flag">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Free Days</label>
                                    <input type="text" autocomplete="off" id="free_storage" name="free_storage"
                                        value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="custom-select" id="type_active" name="type_active">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-invoice"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="service-modal">
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
                            <input type="hidden" id="forwarder_id_service_list">
                            <div class="table-responsive">
                                <table id="table-service" class="table table-striped table-bordered table-sm"
                                    style="width:100%;" cellspacing="0" width="100%">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary btn-sm" id="add-service-btn"><i
                            class="fas fa-plus"></i> <span>Add</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="add-service-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-service" name="form-service" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Service Name</label>
                                    <input type="hidden" id="forwarder_id_service" name="forwarder_id_service">
                                    <select name="service_id" id="service_id" class="form-control">
                                        @foreach ($service_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->service_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-success btn-sm" id="save-service-btn"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="size-modal">
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
                            <input type="hidden" id="forwarder_id_size_list">
                            <div class="table-responsive">
                                <table id="table-size" class="table table-striped table-bordered table-sm"
                                    style="width:100%;" cellspacing="0" width="100%">
                                    <thead class="text-center">
                                        <tr>
                                            <th>size Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary btn-sm" id="add-size-btn"><i class="fas fa-plus"></i>
                        <span>Add</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="add-size-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-size" name="form-size" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Container Size</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Container Size</label>
                                    <input type="hidden" id="forwarder_id_size" name="forwarder_id_size">
                                    <select name="size_id" id="size_id" class="form-control">
                                        @foreach ($size_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->size_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Rate Amount</label>
                                    <input type="text" id="rate_amount" name="rate_amount" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-success btn-sm" id="save-site-btn"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-checker">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-checker" name="form-checker" method="post">
                    @csrf
                    <input type="hidden" id="idChecker" name="idChecker">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" autocomplete="off" id="checkername" name="checkername"
                                        class="form-control" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Branch Name</label>
                                    <select class="custom-select" id="checkerBranch_id" name="checkerBranch_id">
                                        @foreach (Auth::user()->branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Active</label>
                                    <select class="custom-select" id="checkerStatus" name="checkerStatus">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-checker"><i
                                class="fas fa-save"></i>
                            <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-location">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-location"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-location" name="form-location" method="post">
                    @csrf
                    <input type="hidden" id="idLocation" name="idLocation">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Branch Name</label>
                                    <select class="custom-select" id="locationBranch" name="branch">
                                        @foreach (Auth::user()->branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Location Code</label>
                                    <input type="text" autocomplete="off" id="locationCode" name="location_code"
                                        class="form-control" placeholder="Silahkan isi" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Location Aisle</label>
                                    <input type="text" autocomplete="off" id="locationAisle" name="location_aisle"
                                        class="form-control" placeholder="Silahkan isi" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Location Column</label>
                                    <input type="text" autocomplete="off" id="locationColumn" name="location_column"
                                        class="form-control" placeholder="Silahkan isi" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Location Level</label>
                                    <input type="number" autocomplete="off" id="locationLevel" name="location_level"
                                        class="form-control" placeholder="Silahkan isi">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-location">
                            <i class="fas fa-save"></i>
                            <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-upload-location">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-upload-location"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-upload-location" action="{{ route('export-master.uploadLocation')}}" name="form-upload-location" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                           <div class="col-sm-12">
                            <div class="form-group">
                                <label for="my-select">Branch</label>
                                <select id="my-select" class="form-control" name="branch_id" required>
                                    <option value="" selected disabled>Silahkan Pilih</option>
                                    @foreach ($branch as $item)
                                        <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                              <label for="">File Excel</label>
                              <input type="file" class="form-control-file" required name="excel" id="" placeholder="" aria-describedby="fileHelpId">
                            </div>
                           </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-info btn-sm" id="btn-upload-location">
                            <i class="fas fa-upload"></i>
                            <span>Upload</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#tableChecker').DataTable();
        $('#tableLocation').DataTable();

        function addChecker() {
            $('#idChecker').val('');
            $('#form-checker').trigger("reset");
            $('#modal-title').html("Add New Checker");
            $('#modal-checker').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function addLocation() {
            $('#form-location').trigger("reset");
            $('.modal-title-location').html("Add New Location");
            $('#modal-location').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function uploadLocation() {
            $('#form-upload-location').trigger("reset");
            $('.modal-title-upload-location').html("Upload Location");
            $('#modal-upload-location').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function editChecker(id) {
            $('#idChecker').val(id);
            let urlRequest = "{{ url('export-master/edit') }}/" + id;

            $.ajax({
                url: urlRequest,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('#modal-title').html("Edit Checker");
                    $('#btn-save-checker').val("Edit");
                    $('#modal-checker').modal('show');
                    $('#idChecker').val(data.id);
                    $('#checkerBranch_id').val(data.branch_id);
                    let status = "No";
                    if (data.status == 0) {
                        status = "No";
                    } else {
                        status = "Yes";
                    }
                    $('#checkerStatus').val(status);
                    $('#checkername').val(data.name);
                }
            });
            // $('#form-checker').trigger("reset");
            $('#modal-title').html("Add New Checker");
            $('#modal-checker').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function editLocation(id) {
            let urlRequest = "{{ url('export-master/editLocation') }}/" + id;

            $.ajax({
                url: urlRequest,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('#modal-location').modal('show');
                    $('#modal-title-location').html("Add New Location");
                    $('#idLocation').val(data.id);
                    $('#locationBranch').val(data.branch_id);
                    $('#locationCode').val(data.location_code);
                    $('#locationName').val(data.location_name);
                    $('#locationAisle').val(data.location_aisle);
                    $('#locationColumn').val(data.location_column);
                    $('#locationLevel').val(data.location_level);
                }
            });
        }

        function toggleLocation(id, type) {
            let urlRequest = "{{ url('export-master/toggleLocation') }}/" + id + '/' + type;
            if (confirm("Are You sure?") == true) {
                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        }

        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            load_data();

            function load_data() {
                link_id = $('.nav-tabs .active').attr('id');

                if (link_id == 'forwarder-link') {
                    load_forwarder();
                } else if (link_id == 'consignee-link') {
                    load_consignee();
                } else if (link_id == 'shipper-link') {
                    load_shipper();
                } else if (link_id == 'checklist-link') {
                    load_checklist();
                } else if (link_id == 'invoicetype-link') {
                    load_invoicetype();
                }
            }

            $('#forwarder-link').on('click', function() {
                load_forwarder();
            });

            $('#consignee-link').on('click', function() {
                load_consignee();
            });

            $('#shipper-link').on('click', function() {
                load_shipper();
            });

            $('#checklist-link').on('click', function() {
                load_checklist();
            });

            $('#invoicetype-link').on('click', function() {
                load_invoicetype();
            });

            $(document).on('click', '.edit-service', function() {
                dataId = $(this).attr('id');
                $('#forwarder_id_service_list').val(dataId);
                $('#forwarder_id_service').val(dataId);
                $('#table-service').DataTable().destroy();
                $('#table-service').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    paging: false,
                    info: false,
                    ajax: {
                        url: "{{ route('export-forwarder-service.index') }}",
                        type: "GET",
                        data: {
                            forwarder_id: dataId
                        }
                    },
                    columns: [{
                            data: 'service_name',
                            name: 'service_name'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });

                $('#service-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#add-service-btn').click(function() {
                $('#add-service-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-service").length > 0) {
                $("#form-service").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-service').serialize(),
                            url: "{{ route('export-forwarder-service.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-service').trigger("reset");
                                    $('#add-service-modal').modal('hide');
                                    var oTable = $('#table-service').dataTable();
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
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            $(document).on('click', '.delete-service', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('service');
                $('#modal-konfirmasi').modal('show');
            });

            $(document).on('click', '.edit-size', function() {
                dataId = $(this).attr('id');
                $('#forwarder_id_size_list').val(dataId);
                $('#forwarder_id_size').val(dataId);
                $('#table-size').DataTable().destroy();
                $('#table-size').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    paging: false,
                    info: false,
                    ajax: {
                        url: "{{ route('export-forwarder-container-size.index') }}",
                        type: "GET",
                        data: {
                            forwarder_id: dataId
                        }
                    },
                    columns: [{
                            data: 'size_name',
                            name: 'size_name'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });

                $('#size-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#add-size-btn').click(function() {
                $('#add-size-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-size").length > 0) {
                $("#form-size").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-size').serialize(),
                            url: "{{ route('export-forwarder-container-size.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-size').trigger("reset");
                                    $('#add-size-modal').modal('hide');
                                    var oTable = $('#table-size').dataTable();
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
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            $(document).on('click', '.delete-size', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('size');
                $('#modal-konfirmasi').modal('show');
            });

            function load_forwarder() {
                $('#table-forwarder').DataTable().destroy();
                $('#table-forwarder').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('export-forwarder.index') }}",
                        type: "GET"
                    },
                    columns: [{
                            data: 'branch_name',
                            name: 'branch_name'
                        },
                        {
                            data: 'forwarder_name',
                            name: 'forwarder_name'
                        },
                        {
                            data: 'storage_amount',
                            name: 'storage_amount'
                        },
                        {
                            data: 'adm_amount',
                            name: 'adm_amount'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            function load_consignee() {
                $('#table-consignee').DataTable().destroy();
                $('#table-consignee').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('export-consignee.index') }}",
                        type: "GET"
                    },
                    columns: [{
                            data: 'branch_name',
                            name: 'branch_name'
                        },
                        {
                            data: 'consignee_name',
                            name: 'consignee_name'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            function load_shipper() {
                $('#table-shipper').DataTable().destroy();
                $('#table-shipper').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('export-shipper.index') }}",
                        type: "GET"
                    },
                    columns: [{
                            data: 'branch_name',
                            name: 'branch_name'
                        },
                        {
                            data: 'shipper_name',
                            name: 'shipper_name'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            function load_checklist() {
                $('#table-checklist').DataTable().destroy();
                $('#table-checklist').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('cy-checklist.index') }}",
                        type: "GET"
                    },
                    columns: [{
                            data: 'check_name',
                            name: 'check_name'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            function load_invoicetype() {
                $('#table-invoicetype').DataTable().destroy();
                $('#table-invoicetype').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('cy-invoice-type.index') }}",
                        type: "GET"
                    },
                    columns: [{
                            data: 'type_name',
                            name: 'type_name'
                        },
                        {
                            data: 'invoice_flag',
                            name: 'invoice_flag'
                        },
                        {
                            data: 'free_flag',
                            name: 'free_flag'
                        },
                        {
                            data: 'free_storage',
                            name: 'free_storage'
                        },
                        {
                            data: 'active',
                            name: 'active'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            $('body').on('click', '.edit-consignee', function() {
                var data_id = $(this).data('id');
                let urlRequest = "{{ url('export-consignee/edit') }}/" + data_id;

                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html("Edit");
                        $('#btn-save').val("Edit");
                        $('#modal-entry').modal('show');

                        $('#job_type').val("consignee");
                        $('#id').val(data.id);
                        $('#description').val(data.consignee_name);
                        $('#active').val(data.active);
                    }
                });
            });

            $('body').on('click', '.edit-shipper', function() {
                var data_id = $(this).data('id');
                let urlRequest = "{{ url('export-shipper/edit') }}/" + data_id;

                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html("Edit");
                        $('#btn-save').val("Edit");
                        $('#modal-entry').modal('show');

                        $('#job_type').val("shipper");
                        $('#id').val(data.id);
                        $('#description').val(data.shipper_name);
                        $('#active').val(data.active);
                    }
                });
            });

            $('body').on('click', '.edit-checklist', function() {
                var data_id = $(this).data('id');
                let urlRequest = "{{ url('cy-checklist/edit') }}/" + data_id;

                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html("Edit");
                        $('#btn-save').val("Edit");
                        $('#modal-entry').modal('show');

                        $('#job_type').val("checklist");
                        $('#id').val(data.id);
                        $('#description').val(data.check_name);
                        $('#active').val(data.active);
                    }
                });
            });

            if ($("#form-entry").length > 0) {
                $("#form-entry").validate({
                    submitHandler: function(form) {
                        let job_type = $("#job_type").val();
                        let urlRequest = "";
                        let tableName = "";

                        if (job_type == "consignee") {
                            urlRequest = "{{ route('export-consignee.store') }}";
                            tableName = '#table-consignee';
                        } else if (job_type == "shipper") {
                            urlRequest = "{{ route('export-shipper.store') }}";
                            tableName = '#table-shipper';
                        } else if (job_type == "checklist") {
                            urlRequest = "{{ route('cy-checklist.store') }}";
                            tableName = '#table-checklist';
                        }

                        $.ajax({
                            data: $('#form-entry').serialize(),
                            url: urlRequest,
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-entry').trigger("reset");
                                    $('#modal-entry').modal('hide');
                                    var oTable = $(tableName).dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            $('#btn-add-forwarder').click(function() {
                $('#forwarder_id').val('');
                $('#form-forwarder').trigger("reset");
                $('#modal-title').html("Add New Forwarder");
                $('#modal-forwarder').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-forwarder").length > 0) {
                $("#form-forwarder").validate({
                    submitHandler: function(form) {
                        let urlRequest = "{{ route('export-forwarder.store') }}";
                        let tableName = '#table-forwarder';
                        $.ajax({
                            data: $('#form-forwarder').serialize(),
                            url: urlRequest,
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-forwarder').trigger("reset");
                                    $('#modal-forwarder').modal('hide');
                                    var oTable = $(tableName).dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            $('body').on('click', '.edit-forwarder', function() {
                var data_id = $(this).data('id');
                let urlRequest = "{{ url('export-forwarder/edit') }}/" + data_id;

                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-forwarder').modal('show');

                        $('#forwarder_id').val(data.id);
                        $('#forwarder_name').val(data.forwarder_name);
                        $('#storage_amount').val(data.storage_amount);
                        $('#adm_amount').val(data.adm_amount);
                        $('#forwarder_active').val(data.active);
                    }
                });
            });

            $('#btn-add-invoicetype').click(function() {
                $('#type_id').val('');
                $('#form-invoicetype').trigger("reset");
                $('#modal-title').html("Add New");
                $('#modal-invoicetype').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-invoicetype").length > 0) {
                $("#form-invoicetype").validate({
                    submitHandler: function(form) {
                        let urlRequest = "{{ route('cy-invoice-type.store') }}";
                        let tableName = '#table-invoicetype';
                        $.ajax({
                            data: $('#form-invoicetype').serialize(),
                            url: urlRequest,
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-invoicetype').trigger("reset");
                                    $('#modal-invoicetype').modal('hide');
                                    var oTable = $(tableName).dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            $('body').on('click', '.edit-invoicetype', function() {
                var data_id = $(this).data('id');
                let urlRequest = "{{ url('cy-invoice-type/edit') }}/" + data_id;

                $.ajax({
                    url: urlRequest,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-invoicetype').modal('show');

                        $('#type_id').val(data.id);
                        $('#type_name').val(data.type_name);
                        $('#invoice_flag').val(data.invoice_flag);
                        $('#free_flag').val(data.free_flag);
                        $('#free_storage').val(data.free_storage);
                        $('#type_active').val(data.active);
                    }
                });
            });

            $('#btn-delete').click(function() {
                var action = $('#action-delete').val();
                var requestUrl = "";

                if (action == 'service') {
                    var data_id = $('#forwarder_id_service_list').val();
                    requestUrl = "export-forwarder/service/delete/" + data_id + "/" + dataId;
                } else if (action == 'size') {
                    var data_id = $('#forwarder_id_size_list').val();
                    requestUrl = "export-forwarder/container-size/delete/" + data_id + "/" + dataId;
                }

                $.ajax({
                    url: requestUrl,
                    type: 'delete',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        setTimeout(function() {
                            $('#modal-konfirmasi').modal('hide');

                            var oTable = "";
                            if (action == 'service') {
                                oTable = $('#table-service').dataTable();
                            } else if (action == 'size') {
                                oTable = $('#table-size').dataTable();
                            }

                            oTable.fnDraw(false);
                        });

                        if ($.isEmptyObject(data.error)) {
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
                    error: function(data) {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                })
            });

            if ($("#form-checker").length > 0) {
                $("#form-checker").validate({
                    submitHandler: function(form) {
                        let urlRequest = "{{ route('export-master.store') }}";

                        $.ajax({
                            data: $('#form-checker').serialize(),
                            url: urlRequest,
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-checker').trigger("reset");
                                    $('#modal-entry').modal('hide');
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Updated."
                                    }).then(function() {
                                        location.reload();
                                        return false;
                                    });
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }

            if ($("#form-location").length > 0) {
                $("#form-location").validate({
                    submitHandler: function(form) {
                        let urlRequest = "{{ route('export-master.storeLocation') }}";
                        $('#btn-save-location').attr('disabled', true)
                        $.ajax({
                            data: $('#form-location').serialize(),
                            url: urlRequest,
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#btn-save-location').attr('disabled', false)
                                    $('#form-location').trigger("reset");
                                    $('#modal-location').modal('hide');
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully.."
                                    }).then(function() {
                                        location.reload();
                                        return false;
                                    });
                                } else {
                                    $('#btn-save-location').attr('disabled', true)
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                $('#btn-save-location').attr('disabled', false)
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }
        });

        function addMaster(job_type) {
            $('#job_type').val(job_type);
            $('#id').val('');
            $('#form-entry').trigger("reset");
            $('#modal-title').html("Add New " + job_type);
            $('#modal-entry').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
    </script>
@endpush
