@extends('layouts.main')

@section('title')
    Inbound
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Inbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Inbound</li>
                </ol>
            </div>
        </div>
    </section>

    @php
        if (isset($job_view->multi_level)) {
            $multi_level = $job_view->multi_level;
        } else {
            $multi_level = 'Yes';
        }
    @endphp
    <section id="contact" class="contact">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="job_no">Inbound Number</label>
                        <input type="hidden" id="multi_level" name="multi_level"
                            @isset($job_view->multi_level) value="{{ $job_view->multi_level }}" @endisset>
                        <input type="text" id="job_no" name="job_no"
                            @isset($job_view->job_no) value="{{ $job_view->job_no }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="job_date">Inbound Date</label>
                        <input type="text" id="job_date" name="job_date"
                            @isset($job_view->job_date) value="{{ \Carbon\Carbon::parse($job_view->job_date)->format('d-m-Y') }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-sm-12">
                    <div class="btn-group">
                        @can('gate-access', 'warehouse/inbound')
                            <a href="{{ url('/warehouse/inbound/create/0') }}" class="btn btn-primary btn-sm"><i
                                    class="fas fa-plus"></i> <span>Add New Job</span></a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-sm 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab"
                                aria-controls="home" aria-selected="true">
                                <i class="fas fa-info-circle"></i> Job Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="vehicle-link" data-toggle="tab" href="#vehicle-tab" role="tab"
                                aria-controls="vehicle" aria-selected="false">
                                <i class="fas fa-truck"></i> Vehicle Detail</a>
                        </li>
                        {{-- @can('gate-access', 'AdminDC') --}}
                        <li class="nav-item">
                            <a class="nav-link" id="packing-link" data-toggle="tab" href="#packing-tab" role="tab"
                                aria-controls="packing" aria-selected="false">
                                <i class="fas fa-list"></i> Packing Detail</a>
                        </li>
                        {{-- @endcan --}}
                        <li class="nav-item">
                            <a class="nav-link" id="grn-link" data-toggle="tab" href="#grn-tab" role="tab"
                                aria-controls="grn" aria-selected="false">
                                <i class="fas fa-pallet"></i> Goods Receipt</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="putaway-link" data-toggle="tab" href="#putaway-tab" role="tab"
                                aria-controls="putaway" aria-selected="false">
                                <i class="fas fa-location-arrow"></i> Put Away</a>
                        </li>
                        @can('gate-access', 'AdminDC')
                            <li class="nav-item">
                                <a class="nav-link" id="cancel-link" data-toggle="tab" href="#cancel-tab" role="tab"
                                    aria-controls="cancel" aria-selected="false">
                                    <i class="fas fa-reply"></i> Cancel</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="confirm-link" data-toggle="tab" href="#confirm-tab" role="tab"
                                    aria-controls="confirm" aria-selected="false">
                                    <i class="fas fa-check-circle"></i> Confirmation</a>
                            </li>
                        @endcan
                        {{-- @if (isset($job_view->job_class->class_name))
                            @if ($job_view->job_class->class_name == 'Cross Dock')
                                <li class="nav-item">
                                    <a class="nav-link" id="cross-link" data-toggle="tab" href="#cross-tab"
                                        role="tab" aria-controls="contact" aria-selected="false">
                                        <i class="fas fa-shipping-fast"></i> Crosk Dock</a>
                                </li>
                            @endif
                        @endif --}}
                    </ul>
                    <div class="tab-content" id="inboundTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel"
                            aria-labelledby="home-tab5">
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="inbound_id" name="inbound_id"
                                    @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Branch Name</label>
                                                <select class="custom-select" id="branch_id" name="branch_id">
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (isset($job_view->branch_id) && !empty($job_view->branch_id)) @if ($item->id == $job_view->branch_id) selected @endif
                                                            @endif>{{ $item->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Principal Name</label>
                                                <select class="custom-select" id="principal_id" name="principal_id">
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->principal as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (isset($job_view->principal_id) && !empty($job_view->principal_id)) @if ($item->id == $job_view->principal_id) selected @endif
                                                            @endif>{{ $item->principal_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Job Classification</label>
                                                <select class="custom-select" id="class_id" name="class_id">
                                                    <option value="">.:Select:.</option>
                                                    @foreach ($class_list as $item)
                                                        <option value="{{ $item->id }}">{{ $item->class_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Mode Of Transport</label>
                                                <select class="custom-select" id="mode_id" name="mode_id">
                                                    <option value="">.:Select:.</option>
                                                    @foreach ($mode_list as $item)
                                                        <option value="{{ $item->id }}">{{ $item->mode_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description"
                                                    name="description" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>ETA</label>
                                                <input type="text" autocomplete="off" id="eta" name="eta"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Entry Date</label>
                                                <input type="text" autocomplete="off" id="entry_date"
                                                    name="entry_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="entryBy"></label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Received Date</label>
                                                <input type="text" autocomplete="off" id="received_date"
                                                    name="received_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="receivedBy"></label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Allocated Date</label>
                                                <input type="text" autocomplete="off" id="allocated_date"
                                                    name="allocated_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="allocatedBy"></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Confirmed Date</label>
                                                <input type="text" autocomplete="off" id="confirmed_date"
                                                    name="confirmed_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="confirmBy"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="btn-group">
                                                @if (isset($job_view->received_flag) && !empty($job_view->received_flag))
                                                    @if ($job_view->received_flag == 'No')
                                                        <button type="submit" id="btn-save-job"
                                                            class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                            <span>Save</span></button>
                                                    @endif
                                                @else
                                                    <button type="submit" id="btn-save-job"
                                                        class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                        <span>Save</span></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="vehicle-tab" role="tabpanel" aria-labelledby="vehicle-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/inbound')
                                                @if (isset($job_view->received_flag) && !empty($job_view->received_flag))
                                                    @if ($job_view->received_flag == 'No')
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            id="btn-add-vehicle"><i class="fas fa-plus"></i> <span>Add
                                                                Vehicle</span></a>
                                                    @endif
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="btn-add-vehicle"><i class="fas fa-plus"></i> <span>Add
                                                            Vehicle</span></a>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="vehicle_table" class="table table-striped table-bordered table-sm"
                                                style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Vehicle No</th>
                                                        <th>Transporter Name</th>
                                                        <th>Driver Name</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="packing-tab" role="tabpanel" aria-labelledby="packing-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/inbound')
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-packing"><i
                                                        class="fas fa-plus"></i> <span>Add Packing</span></button>
                                                <button class="btn btn-success btn-sm" id="btn-import"><i
                                                        class="fas fa-upload"></i> Upload</button>
                                                <button type="button" onclick="downloadExcel('detail/export');"
                                                    class="btn btn-success btn-sm"><i class="fas fa-download"></i>
                                                    <span>Template</span></button>
                                            @endcan
                                        </div>
                                    </div>
                                    @can('gate-access', 'CheckerDC')
                                        @if (isset($job_view))
                                            @if ($job_view->principal_id == 3)
                                                <div class="col-sm-6 mt-4">
                                                    <div class="form-group">
                                                        <input type="text" name="" id="scanEAN"
                                                            class="form-control" placeholder="Scan Barcode EAN Here.."
                                                            aria-describedby="helpId" autofocus autocomplete="off"
                                                            style="background-color: yellow;">
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endcan
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="packing_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th colspan="6">Expected Quantity</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>2nd</th>
                                                            <th>Unit</th>
                                                            <th>3rd</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @else
                                                <table id="packing_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th colspan="2">Actual Quantity</th>
                                                            <th colspan="2">Expected Quantity</th>
                                                            {{-- <th rowspan="2">Pallet ID</th> --}}
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="grn-tab" role="tabpanel" aria-labelledby="grn-tab5">
                            <div class="container mt-3">
                                <form id="form-grn" name="form-grn" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="ata">Shipment Arrival Date</label>
                                                <input type="text" id="ata" name="ata"
                                                    class="form-control floating-label"
                                                    value="{{ isset($ata) ? \Carbon\Carbon::parse($ata)->format('d/m/Y H:i') : '' }}"
                                                    placeholder="Input Here..">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="ata">Unloading Start</label>
                                                <input type="text" id="unloading_start" name="unloading_start"
                                                    class="form-control floating-label"
                                                    @isset($job_view->unloading_start) @if ($job_view->unloading_start != null) value="{{ \Carbon\Carbon::parse($job_view->unloading_start)->format('d/m/Y H:i') }}" @endif @endisset
                                                    {{ isset($job_view->unloading_start) ? 'readonly' : '' }}
                                                    placeholder="Input Here..">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="ata">Unloading Finish</label>
                                                <input type="text" id="unloading_finish" name="unloading_finish"
                                                    class="form-control floating-label"
                                                    @isset($job_view->unloading_finish) @if ($job_view->unloading_finish != null) value="{{ \Carbon\Carbon::parse($job_view->unloading_finish)->format('d/m/Y H:i') }}" @endif @endisset
                                                    {{ isset($job_view->unloading_finish) ? 'readonly' : '' }}
                                                    placeholder="Input Here..">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3" data-aos="fade-up">
                                        <div class="col-sm-12">
                                            <div class="btn-group">
                                                @can('gate-access', 'warehouse/inbound')
                                                    <button type="button" class="btn btn-danger btn-sm" id="btn-process-grn"
                                                        onclick="processGRN();"><i class="fas fa-gear"></i>
                                                        <span>Process</span></button>
                                                @endcan
                                                <a id="grn-print"
                                                    @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                    class="btn btn-info btn-sm text-white"><i class="fas fa-print"></i>
                                                    <span>
                                                        Goods Receipt Report Detail</span>
                                                </a>
                                                <a id="grn-print-summary"
                                                    @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                    class="btn btn-success btn-sm text-white"><i class="fas fa-print"></i>
                                                    <span>
                                                        Goods Receipt Report Summary</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                @if ($multi_level == 'Yes')
                                                    <table id="grn_table"
                                                        class="table table-striped table-bordered table-sm"
                                                        style="width:100%;">
                                                        <thead class="text-center">
                                                            <tr>
                                                                <th rowspan="2">Action</th>
                                                                {{-- <th rowspan="2">
                                                                    <input type="checkbox" required="required" class="grn-check-all">
                                                                </th> --}}
                                                                <th rowspan="2">SKU No.</th>
                                                                <th rowspan="2">SKU Name</th>
                                                                <th rowspan="2">Batch No</th>
                                                                <th rowspan="2">Expired Date</th>
                                                                <th colspan="6">Actual Quantity</th>
                                                                <th colspan="6">Expected Quantity</th>
                                                            </tr>
                                                            <tr>
                                                                <th>1st</th>
                                                                <th>Unit</th>
                                                                <th>2nd</th>
                                                                <th>Unit</th>
                                                                <th>3rd</th>
                                                                <th>Unit</th>
                                                                <th>1st</th>
                                                                <th>Unit</th>
                                                                <th>2nd</th>
                                                                <th>Unit</th>
                                                                <th>3rd</th>
                                                                <th>Unit</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                @else
                                                    <table id="grn_table"
                                                        class="table table-striped table-bordered table-sm"
                                                        style="width:100%;">
                                                        <thead class="text-center">
                                                            <tr>
                                                                <th rowspan="2">Action</th>
                                                                {{-- <th rowspan="2">
                                                                    <input type="checkbox" required="required"
                                                                        class="grn-check-all">
                                                                </th> --}}
                                                                <th rowspan="2">SKU No.</th>
                                                                <th rowspan="2">SKU Name</th>
                                                                <th rowspan="2">Batch No</th>
                                                                <th rowspan="2">Expired Date</th>
                                                                <th colspan="2">Actual Quantity</th>
                                                                <th colspan="2">Expected Quantity</th>
                                                            </tr>
                                                            <tr>
                                                                <th>1st</th>
                                                                <th>Unit</th>
                                                                <th>1st</th>
                                                                <th>Unit</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                @endif
                                            </div>
                                        </div>
                                        @if (count($perpallet) > 0)
                                            @foreach ($perpallet->groupBy('product_code') as $key => $value)
                                                <input type="text" name="product_code[]" hidden
                                                    value="{{ $key }}">
                                                <input type="text" name="inbound_id" hidden
                                                    value="{{ $job_view->id ?? '0' }}">
                                                {{-- <div class="col-sm-6">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th colspan="2"><b>SKU : {{$key}}</b></th>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <th>NO.</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($perpallet->where('product_code', $key) as $item)
                                                        <tr class="text-center">
                                                            <td>{{$loop->iteration}}</td>
                                                            <td>
                                                                {{$item->qty_per_pallet}}
                                                                <input type="text" name="qty_per_pallet[]" hidden value="{{$item->qty_per_pallet}}">
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div> --}}
                                            @endforeach
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="putaway-tab" role="tabpanel" aria-labelledby="putaway-tab5">
                            <div class="container mt-3">
                                <form id="form-putaway" name="form-putaway" method="post">
                                    @csrf
                                    @foreach ($perpallet->whereNull('putaway_date') as $item)
                                        <input type="hidden" class="form-control" value="{{ $item->product_code }}"
                                            name="product_code[]">
                                        <input type="hidden" class="form-control" value="{{ $item->inbound_id }}"
                                            name="inbound_id">
                                        <input type="hidden" class="form-control" value="{{ $item->location_code }}"
                                            name="location_code[]">
                                        <input type="hidden" class="form-control" value="{{ $item->location_id }}"
                                            name="location_id[]">
                                        <input type="hidden" class="form-control" value="{{ $item->qty_per_pallet }}"
                                            name="qty[]">
                                        <input type="hidden" class="form-control" value="{{ $item->picking_id }}"
                                            name="packing_id[]">
                                    @endforeach
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Site Name</label>
                                                <select class="custom-select" id="site_putaway" required
                                                    name="site_putaway">
                                                    <option value=""></option>
                                                    @foreach (Auth::user()->site as $item)
                                                        <option value="{{ $item->id }}">{{ $item->site_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Area Name</label>
                                                <select class="custom-select" id="area_putaway" required
                                                    name="area_putaway">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" data-aos="fade-up">
                                        <div class="col-sm-6">
                                            <div class="btn-group mb-4">
                                                @can('gate-access', 'AdminDC')
                                                    @if ($button_gr)
                                                        @if ($button_putaway)
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                id="btn-process-putaway" onclick="processPutaway();"><i
                                                                    class="fas fa-gear"></i>
                                                                <span>Process</span></button>
                                                        @endif
                                                    @endif
                                                @endcan
                                                &nbsp;
                                                <a href="javascript:void(0)" class="btn btn-sm text-white"
                                                    style="background-color: coral;"
                                                    onclick="draftPutaway('{{ $job_view->id ?? 0 }}', 0)"><i
                                                        class="fas fa-print"></i> Draft Put away List</a>
                                                <a id="putaway-report"
                                                    @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                    class="btn btn-success btn-sm text-white"><i class="fas fa-print"></i>
                                                    <span>Put Away Report</span>
                                                </a>
                                                @can('gate-access', 'AdminDC')
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-dark"
                                                        onclick="printPalletTagAfter('{{ $job_view->id ?? 0 }}', 0)"><i
                                                            class="fas fa-print"></i> All Pallet Tag</a>
                                                @endcan
                                            </div>
                                            <div class="float-right">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-right">
                                                @can('bypass-scan-inbound', 'bypass-scan-putaway')
                                                    <a href="javascript:void(0)" class="btn btn-lg btn-outline-info mb-3"
                                                        onclick="bypassScan('{{ $job_view->id ?? 0 }}', 0)"><i
                                                            class="fas fa-user-cog"></i> Bypass Scan Pallet Tag</a>
                                                @endcan
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>SKU No.</th>
                                                            <th>SKU Name</th>
                                                            <th>Batch No</th>
                                                            <th>Expired Date</th>
                                                            <th>Quantity</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($list_data as $list)
                                                            <tr class="text-center">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $list->product_code }}</td>
                                                                <td>{{ $list->product_name }}</td>
                                                                <td>{{ $list->lot_no }}</td>
                                                                <td>{{ \Carbon\carbon::parse($list->exp_date)->format('d-M-Y') }}
                                                                </td>
                                                                <td>{{ $list->pqty }}</td>
                                                                <td>
                                                                    @can('gate-access', 'AdminDC')
                                                                        <a href="javascript:void(0)"
                                                                            class="btn btn-sm btn-dark"
                                                                            onclick="printPalletTag('{{ $list->inbound_id }}', '{{ $list->product_code }}', '{{ $list->id }}')"><i
                                                                                class="fas fa-print"></i> Pallet Tag</a>
                                                                    @endcan
                                                                    @can('gate-access', 'CheckerDC')
                                                                        @if ($list->wherenotnull != $list->counting)
                                                                            <a href="javascript:void(0)"
                                                                                class="btn btn-sm btn-success"
                                                                                onclick="startPutaway('{{ $list->inbound_id }}', '{{ $list->id_product }}', '{{ $list->id }}')"><i
                                                                                    class="fas fa-camera"></i> Start
                                                                                Putaway</a>
                                                                        @else
                                                                            <a href="javascript:void(0)"
                                                                                onclick="startPutaway('{{ $list->inbound_id }}', '{{ $list->id_product }}', '{{ $list->id }}')"
                                                                                class="btn btn-sm btn-info"><i
                                                                                    class="fas fa-edit"></i> Edit
                                                                                Putaway</a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="appendStartPutaway">

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="cancel-tab" role="tabpanel" aria-labelledby="cancel-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/inbound')
                                                <button type="button" class="btn btn-danger btn-sm" id="btn-process-cancel"
                                                    onclick="processCancel();"><i class="fas fa-gear"></i>
                                                    <span>Process</span></button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <form id="form-cancel" name="form-cancel" method="post">
                                        @csrf
                                    </form>
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="cancel_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="cancel-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th colspan="6">Expected Quantity</th>
                                                            <th colspan="6">Actual Quantity</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>2nd</th>
                                                            <th>Unit</th>
                                                            <th>3rd</th>
                                                            <th>Unit</th>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>2nd</th>
                                                            <th>Unit</th>
                                                            <th>3rd</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @else
                                                <table id="cancel_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="cancel-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th colspan="2">Expected Quantity</th>
                                                            <th colspan="2">Actual Quantity</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="confirm-tab" role="tabpanel" aria-labelledby="confirm-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/inbound')
                                                <button type="button" class="btn btn-danger btn-sm" id="btn-process-confirm"
                                                    onclick="processConfirm();"><i class="fas fa-gear"></i>
                                                    <span>Process</span></button>
                                            @endcan
                                            <a id="confirm-print"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Inbound
                                                    Confirmation Report</span>
                                            </a>
                                            @if (isset($job_view))
                                                @if ($job_view->multi_level == 'Yes')
                                                    <a id="confirm-quantum" class="btn btn-dark btn-sm text-white"><i
                                                            class="fas fa-print"></i>
                                                        <span>Inbound
                                                            Confirmation Quantum Report</span>
                                                    </a>
                                                @endif
                                            @endif
                                            <button type="button" onclick="downloadExcel('confirm/export');"
                                                class="btn btn-success btn-sm"><i class="fas fa-download"></i>
                                                <span>Download</span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <form id="form-confirm" name="form-confirm" method="post">
                                        @if (isset($job_view->id))
                                            <input type="hidden" name="inbound_id" value="{{ $job_view->id }}">
                                        @endif
                                        @foreach ($list_confirm as $item)
                                            <input type="hidden" name="location_code[]"
                                                value="{{ $item->location_code }}">
                                        @endforeach
                                        @foreach ($perpallet as $item)
                                            <input type="hidden" name="location_status[]"
                                                value="{{ $item->location_status }}">
                                        @endforeach
                                        @csrf
                                    </form>
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="confirm_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="confirm-check-all">
                                                            </th>
                                                            <th rowspan="2" class="buttonEdit">
                                                                <i class="fas fa-tools text-dark"></i>
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="6">Quantity</th>
                                                            <th rowspan="2">Serial No</th>
                                                            <th rowspan="2" class="bg-warning">Remarks</th>
                                                            <th rowspan="2">Dimension</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>2nd</th>
                                                            <th>Unit</th>
                                                            <th>3rd</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @else
                                                <table id="confirm_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="confirm-check-all">
                                                            </th>
                                                            <th rowspan="2">
                                                                <i class="fas fa-tools text-dark"></i>
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="2">Quantity</th>
                                                            <th rowspan="2">Serial No</th>
                                                            <th rowspan="2">Dimension</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="cross-tab" role="tabpanel" aria-labelledby="cross-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/inbound')
                                                <button type="button" class="btn btn-danger btn-sm" id="btn-process-cross"
                                                    onclick="selectJob();"><i class="fas fa-gear"></i>
                                                    <span>Process</span></button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <form id="form-cross" name="form-cross" method="post">
                                        @csrf
                                    </form>
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="cross_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="cross-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="6">Quantity</th>
                                                            <th rowspan="2">Serial No</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                            <th>2nd</th>
                                                            <th>Unit</th>
                                                            <th>3rd</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @else
                                                <table id="cross_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="cross-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="2">Quantity</th>
                                                            <th rowspan="2">Serial No</th>
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>Unit</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @endif
                                        </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-vehicle">
        <div class="modal-dialog modal-lg" role="document">
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
                    <input type="hidden" id="inbound_vehicle" name="inbound_vehicle">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Vehicle No <small class="text-danger">*</small></label>
                                    <select name="vehicle_no" id="vehicle_no" required class="form-control"
                                        onchange="selectVehicle(this.value)">
                                        <option value="" disabled selected>.:Select:.</option>
                                        @foreach ($vehicle as $item)
                                            <option value="{{ $item->vehicle_number }}">{{ $item->vehicle_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Vehicle Size <small class="text-danger">*</small></label>
                                    <input type="text" autocomplete="off" id="size_id" name="size_id"
                                        class="form-control" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Transporter Name <small class="text-danger">*</small></label>
                                    <input type="text" autocomplete="off" id="transporter_name"
                                        name="transporter_name" class="form-control" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Driver Name <small class="text-danger">*</small></label>
                                    <input type="text" autocomplete="off" id="driver_name" name="driver_name"
                                        class="form-control" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Container No <small class="text-muted">(Optional)</small></label>
                                    <input type="text" autocomplete="off" id="container_no" name="container_no"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Seal No <small class="text-muted">(Optional)</small></label>
                                    <input type="text" autocomplete="off" id="seal_no" name="seal_no"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>AWB No <small class="text-muted">(Optional)</small></label>
                                    <input type="text" autocomplete="off" id="awb_no" name="awb_no"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-vehicle"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-packing">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-packing" id="modal-title-packing"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-packing" name="form-packing" method="post">
                    @csrf
                    <input type="hidden" id="packing_id" name="packing_id">
                    <input type="hidden" id="inbound_packing" name="inbound_packing">
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="product_code" name="product_code">
                    <input type="hidden" id="uppp" name="uppp">
                    <input type="hidden" id="muppp" name="muppp">
                    <input type="hidden" id="packing_flag" name="packing_flag">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Vehicle No</label>
                                    <input type="text" id="vehicle_manual" name="vehicle_manual" autocomplete="off"
                                        class="form-control">
                                    <select class="custom-select" id="vehicle_packing" name="vehicle_packing">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>SKU No. / SKU Name</label>
                                    <input type="text" autocomplete="off" id="product_name" name="product_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>PO / DO No.</label>
                                    <input type="text" autocomplete="off" id="po_number" name="po_number"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Batch Number</label>
                                    <input type="text" autocomplete="off" id="lot_no" name="lot_no"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Document Reference</label>
                                    <input type="text" autocomplete="off" id="document_ref" name="document_ref"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Manufactur Name</label>
                                    <select class="custom-select" id="manufactur_id" name="manufactur_id">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Mfg Date</label>
                                    <input type="text" autocomplete="off" id="mfg_date" name="mfg_date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Exp Date</label>
                                    <input type="text" autocomplete="off" id="exp_date" name="exp_date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" autocomplete="off" id="pallet_id" name="pallet_id" value="0"
                                    class="form-control">
                            </div>
                        </div>
                        <fieldset>
                            <legend>Expected Quantity</legend>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>1st Qty</label>
                                        <input type="text" autocomplete="off" id="pqty" name="pqty"
                                            value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>1st Unit</label>
                                        <input type="text" autocomplete="off" id="puom" name="puom"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>2nd Qty</label>
                                        <input type="text" autocomplete="off" id="mqty" name="mqty"
                                            value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>2nd Unit</label>
                                        <input type="text" autocomplete="off" id="muom" name="muom"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>3rd Qty</label>
                                        <input type="text" autocomplete="off" id="bqty" name="bqty"
                                            value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>3rd Unit</label>
                                        <input type="text" autocomplete="off" id="buom" name="buom"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        {{-- INI ADALAH GOOGLE --}}
                        @if (isset($job_view->id))
                            @if ($job_view->quality_flag == 'Yes')
                                <fieldset>
                                    <hr>
                                    <legend>Quality</legend>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="my-select">Product Quality </label>
                                                    <select id="my-select" class="form-control" name="product_quality"
                                                        required>
                                                        <option value="" selected disabled>CHOOSE</option>
                                                        <option value="1st">1st</option>
                                                        <option value="2nd">2nd</option>
                                                        <option value="3rd">3rd</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            @endif
                        @endif
                        <div id="actual-quantity">
                            <fieldset>
                                <legend>Actual Quantity ( Goods )</legend>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>1st Qty</label>
                                            <input type="text" autocomplete="off" id="actual_pqty" name="actual_pqty"
                                                value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>1st Unit</label>
                                            <input type="text" autocomplete="off" id="actual_puom" name="actual_puom"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>2nd Qty</label>
                                            <input type="text" autocomplete="off" id="actual_mqty" name="actual_mqty"
                                                value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>2nd Unit</label>
                                            <input type="text" autocomplete="off" id="actual_muom" name="actual_muom"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>3rd Qty</label>
                                            <input type="text" autocomplete="off" id="actual_bqty" name="actual_bqty"
                                                value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>3rd Unit</label>
                                            <input type="text" autocomplete="off" id="actual_buom" name="actual_buom"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Actual Quantity ( Not Goods )</legend>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>1st Qty</label>
                                            <input type="text" autocomplete="off" id="discrepancy_pqty"
                                                name="discrepancy_pqty" value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>1st Unit</label>
                                            <input type="text" autocomplete="off" id="discrepancy_puom"
                                                name="discrepancy_puom" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>2nd Qty</label>
                                            <input type="text" autocomplete="off" id="discrepancy_mqty"
                                                name="discrepancy_mqty" value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>2nd Unit</label>
                                            <input type="text" autocomplete="off" id="discrepancy_muom"
                                                name="discrepancy_muom" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>3rd Qty</label>
                                            <input type="text" autocomplete="off" id="discrepancy_bqty"
                                                name="discrepancy_bqty" value="0" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>3rd Unit</label>
                                            <input type="text" autocomplete="off" id="discrepancy_buom"
                                                name="discrepancy_buom" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <input type="text" autocomplete="off" id="remarks" name="remarks"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div id="manual-site">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Site Name</label>
                                        <select class="custom-select" id="site_id" name="site_id">
                                            <option value="">.:Select:.</option>
                                            @foreach (Auth::user()->site as $item)
                                                <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Area Name</label>
                                        <select class="custom-select" id="area_id" name="area_id">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select class="custom-select" id="location_id" name="location_id">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-packing"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-location">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-location" id="modal-title-location"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-location" name="form-location" method="post">
                    @csrf
                    <input type="hidden" id="batch_id" name="batch_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>SKU Name</label>
                                    <input type="text" autocomplete="off" id="product_name_confirm"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Batch Number</label>
                                    <input type="text" autocomplete="off" id="lot_no_confirm"
                                        class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>1st Qty</label>
                                    <input type="text" autocomplete="off" id="pqty_confirm" value="0"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>1st Unit</label>
                                    <input type="text" autocomplete="off" id="puom_confirm" class="form-control"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>2nd Qty</label>
                                    <input type="text" autocomplete="off" id="mqty_confirm" value="0"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>2nd Unit</label>
                                    <input type="text" autocomplete="off" id="muom_confirm" class="form-control"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>3rd Qty</label>
                                    <input type="text" autocomplete="off" id="bqty_confirm" value="0"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>3rd Unit</label>
                                    <input type="text" autocomplete="off" id="buom_confirm" class="form-control"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Exp Date</label>
                                    <input type="text" autocomplete="off" id="exp_date_confirm"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Site Name</label>
                                    <input type="hidden" id="site_id_confirm" name="site_id_confirm">
                                    <input type="text" id="site_name_confirm" name="site_name_confirm"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Area Name</label>
                                    <input type="hidden" id="area_id_confirm" name="area_id_confirm">
                                    <input type="text" id="area_name_confirm" name="area_name_confirm"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="hidden" id="location_id_confirm" name="location_id_confirm">
                                    <input type="text" id="location_code_confirm" name="location_code_confirm"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-location"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="edit_lokasi_batch">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5> Edit Lokasi SKU <b class="sku_edit"></b> | Batch : <b class="batch_edit"></b> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inbound.edit_lokasi_batch') }}" method="post" id="form-edit-lokasi-batch">
                    @csrf
                    <input type="hidden" id="batch_id-edit_lokasi_batch" name="batch_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">Lokasi Awal</span>
                                    </div>
                                    <input class="form-control" type="text" id="lokasiAwalBatch" name=""
                                        disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select id="locationCode" class="form-control" name="location_id"
                                        style="width: 100%;" required>
                                        <option selected disabled>Pilih Lokasi Baru</option>
                                        @foreach ($location as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->location_code }} - {{ $item->site_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-location"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-pallet">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pallet Capacity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-pallet" name="form-pallet" method="post">
                    @csrf
                    <input type="hidden" id="product_id_pallet" name="product_id_pallet">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" autocomplete="off" id="product_code_pallet"
                                        class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>SKU Name</label>
                                    <input type="text" autocomplete="off" id="product_name_pallet"
                                        class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="puom_pallet" class="form-control"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table id="pallet_table" class="table table-striped table-bordered table-sm"
                                        style="width:100%;" cellspacing="0" width="100%">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Location Type</th>
                                                <th>Pallet Quantity</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-save-pallet"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="form-import" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="job_id" name="job_id">
                        <div class="col-sm-12">
                            <label>Pilih file excel</label>
                            <div class="form-group">
                                <input type="file" name="file" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-upload">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-print-pallet-tag" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="post" action="{{ url('warehouse/inbound/report/allPallet') }}" id="form-print-pallet-tag"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Choose Product</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" id="job_id_print_pallet_tag" name="job_id">
                                <table class="table table-bordered table-sm" style="width:100%;">
                                    <thead class="text-center">
                                        <tr class="text-center">
                                            <th>
                                                <input type="checkbox" class="print-pallet-tag-check-all">
                                            </th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Batch No</th>
                                        </tr>
                                    <tbody>
                                        @foreach ($list_data as $item)
                                            <tr class="text-center">
                                                <td>
                                                    <input type="checkbox" name="list_sku[]"
                                                        class="print-pallet-tag-checked"
                                                        value="{{ $item->product_code }}" />
                                                </td>
                                                <td>{{ $item->product_code }}</td>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->lot_no == null ? '-' : $item->lot_no }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-print-pallet-tag">Print</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-cross">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Cross Dock Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="outbound_id" name="outbound_id">
                <input type="hidden" id="order_id" name="order_id">
                <input type="hidden" id="customer_id" name="customer_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="custom-select" id="job_status" name="job_status">
                                    <option value="">.:Select:.</option>
                                    <option value="N">New Job</option>
                                    <option value="E">Existing Job</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Job No</label>
                                <input type="text" id="job_number" name="job_number" class="form-control"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" autocomplete="off" id="customer_name" name="customer_name"
                                    class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Customer Code</label>
                                <input type="text" autocomplete="off" id="customer_code" name="customer_code"
                                    class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Order No</label>
                                <input type="text" autocomplete="off" id="order_no" name="order_no"
                                    class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Order Date</label>
                                <input type="text" autocomplete="off" id="order_date" name="order_date"
                                    class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Due Date</label>
                                <input type="text" autocomplete="off" id="due_date" name="due_date"
                                    class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-primary btn-sm"
                        onclick="processCrossDock();"><span>Process</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-add-pallet">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('inbound.add_per_pallet') }}" method="post" id="form-add-pallet">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">MODAL ADD/UPDATE PALLET SKU <b class="skuText"></b>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" id="inbound_id_per_pallet" name="inbound_id">
                    <input type="hidden" id="picking_id" name="picking_id">
                    <input type="hidden" id="skuValue" name="product_code">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="my-input">QTY</label>
                                    <input id="qtyValue" class="form-control" type="text" readonly
                                        name="qty">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="jumlah_pallet" required
                                        onchange="jumlahPallet(this.value)" id="selectPallet">
                                        <option value="" disabled selected>JUMLAH PALLET</option>
                                        @for ($i = 1; $i <= 100; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="appendTable">

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="resultTable">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm btn-save-add-pallet hide"> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var success = new Audio("{{ url('assets/audio/success.mp3') }}");
        var error = new Audio("{{ url('assets/audio/error.mp3') }}");
        let confirmTable = null;

        $(document).ready(function() {
            if (window.location.hash === '#packing-tab') {
                // Fokuskan input jika fragmen sesuai
                setTimeout(function() {
                    $("#scanEAN").focus();
                }, 100); // Menunda fokus agar elemen ter-render terlebih dahulu
            }
            $("#scanEAN").keyup(function(event) {
                var job_id = "{{ $job_view->id ?? 0 }}";
                var value = $('#scanEAN').val();
                if (event.keyCode === 13) {
                    $('#scanEAN').val("");
                    $.ajax({
                        url: "{{ url('warehouse/inbound/detail/doScanEan') }}/" + value + '/' +
                            job_id,
                        dataType: "json",
                        success: function(data) {
                            if (data.message == 'invalid') {
                                error.play();
                                swal({
                                    icon: "error",
                                    text: "Product not found!"
                                }).then(function() {
                                    $("#scanEAN").focus();
                                });
                            } else if (data.message == 'duplicate') {
                                error.play();
                                swal({
                                    icon: "warning",
                                    text: "CARTON ID has already been!"
                                }).then(function() {
                                    $("#scanEAN").focus();
                                })
                            } else {
                                success.play();
                                swal({
                                    icon: "success",
                                    text: `Good Job! ${data.sku}, ${data.counting}x`
                                }).then(function() {
                                    $("#scanEAN").focus();
                                })
                                load_packing();
                            }
                        },
                        error: function(error) {
                            console.log('====================================');
                            console.log(error);
                            console.log('====================================');
                        }
                    });
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            load_data();

            function load_data() {
                link_id = $('.nav-tabs .active').attr('id');

                if (link_id == 'job-link') {
                    load_job();
                } else if (link_id == 'vehicle-link') {
                    load_vehicle();
                } else if (link_id == 'packing-link') {
                    load_packing();
                } else if (link_id == 'grn-link') {
                    load_grn();
                } else if (link_id == 'putaway-link') {
                    // load_putaway();
                } else if (link_id == 'cancel-link') {
                    load_cancel();
                } else if (link_id == 'confirm-link') {
                    load_confirm();
                } else if (link_id == 'cross-link') {
                    load_cross();
                }
            }

            $('#job_status').on('change', function() {
                var job_status = this.value;

                if (job_status == "N") {
                    document.getElementById("job_number").setAttribute("readonly", true);
                    document.getElementById("customer_name").removeAttribute("readonly");
                    document.getElementById("order_no").removeAttribute("readonly");
                    document.getElementById("order_date").removeAttribute("readonly");
                    document.getElementById("due_date").removeAttribute("readonly");
                } else if (job_status == "E") {
                    document.getElementById("job_number").removeAttribute("readonly");
                    document.getElementById("customer_name").setAttribute("readonly", true);
                    document.getElementById("order_no").setAttribute("readonly", true);
                    document.getElementById("order_date").setAttribute("readonly", true);
                    document.getElementById("due_date").setAttribute("readonly", true);
                }
                $("#outbound_id").val("");
                $("#order_id").val("");
                $("#customer_id").val("");
                $("#job_number").val("");
                $("#customer_name").val("");
                $("#customer_code").val("");
                $("#order_no").val("");
                $("#order_date").val("");
                $("#due_date").val("");
            });


            $("#customer_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('customer.getCustomerAuto') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#customer_id').val(ui.item.id);
                        $('#customer_name').val(ui.item.customer_name);
                        $('#customer_code').val(ui.item.customer_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.customer_name + "</div>")
                        .appendTo(ul);
                };

            $("#job_number").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('outbound.getOrderCrossDock') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#outbound_id').val(ui.item.outbound_id);
                        $('#order_id').val(ui.item.order_id);
                        $('#job_number').val(ui.item.job_no);
                        $('#customer_id').val(ui.item.customer_id);
                        $('#customer_name').val(ui.item.customer_name);
                        $('#customer_code').val(ui.item.customer_code);
                        $('#order_no').val(ui.item.order_no);
                        $('#order_date').val(ui.item.order_date);
                        $('#due_date').val(ui.item.due_date);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.customer_name + "<br>Job Number: " + item.job_no +
                            "<br>Order Date: " + item.order_date + "<br>Due Date: " + item.due_date + "</div>")
                        .appendTo(ul);
                };

            $('#job-link').on('click', function() {
                load_job();
            });

            $('#vehicle-link').click(function(e) {
                e.preventDefault();
                load_vehicle();
            });

            $('#packing-link').click(function(e) {
                e.preventDefault();
                load_packing();
            });

            $('#grn-link').click(function(e) {
                e.preventDefault();
                load_grn();
            });

            $('#putaway-link').click(function(e) {
                e.preventDefault();
                // load_putaway();
            });

            $('#cancel-link').click(function(e) {
                e.preventDefault();
                load_cancel();
            });

            $('#confirm-link').click(function(e) {
                e.preventDefault();
                load_confirm();
            });

            $('#cross-link').click(function(e) {
                e.preventDefault();
                load_cross();
            });

            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('inbound-job.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });

                                    window.open(data.success, '_top');
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
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            function load_job() {
                var data_id = $('#inbound_id').val();

                if (data_id == '') {
                    $('#form-job').trigger("reset");
                    return;
                }

                $.ajax({
                    url: "{{ route('inbound-job.edit') }}",
                    data: {
                        inbound_id: data_id
                    },
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        var job_date = "";
                        var eta = "";
                        var entry_date = "";
                        var received_date = "";
                        var allocated_date = "";
                        var confirmed_date = "";
                        var received_by = "";
                        var allocated_by = "";
                        var confirm_by = "";

                        if (data.job_date !== null) {
                            job_date = getFormatDate(data.job_date);
                        }

                        if (data.eta !== null) {
                            eta = getFormatDate(data.eta);
                        }

                        if (data.entry_date !== null) {
                            entry_date = getFormatDateTime(data.entry_date);
                        }

                        if (data.received_by !== null) {
                            received_by = data.received_by;
                        }
                        if (data.allocated_by !== null) {
                            allocated_by = data.allocated_by;
                        }
                        if (data.confirmed_by !== null) {
                            confirmed_by = data.confirmed_by;
                        }

                        if (data.received_date !== null) {
                            received_date = getFormatDateTime(data.received_date);
                        }

                        if (data.allocated_date !== null) {
                            allocated_date = getFormatDateTime(data.allocated_date);
                        }

                        if (data.confirmed_date !== null) {
                            confirmed_date = getFormatDateTime(data.confirmed_date);
                        }

                        $('#job_no').val(data.job_no);
                        $('#job_date').val(job_date);
                        $('#principal_id').val(data.principal_id);
                        $('#class_id').val(data.class_id);
                        $('#mode_id').val(data.mode_id);
                        $('#description').val(data.description);
                        // $('#reference_no').val(data.reference_no);
                        // $('#reference_other').val(data.reference_other);
                        $('#eta').val(eta);
                        $('#entry_date').val(entry_date);
                        $('#received_date').val(received_date);
                        $('#allocated_date').val(allocated_date);
                        $('#confirmed_date').val(confirmed_date);
                        $('.entryBy').text(received_by);
                        $('.receivedBy').text(received_by);
                        $('.allocatedBy').text(allocated_by);
                        $('.confirmBy').text(confirmed_by);
                    }
                });
            }

            function load_vehicle() {
                var dataId = $('#inbound_id').val();

                $('#vehicle_table').DataTable().destroy();
                $('#vehicle_table').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    destroy: true,
                    ajax: {
                        url: "{{ route('inbound-vehicle.index') }}",
                        type: "GET",
                        data: {
                            inbound_id: dataId
                        }
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'vehicle_no',
                            name: 'vehicle_no'
                        },
                        {
                            data: 'transporter_name',
                            name: 'transporter_name'
                        },
                        {
                            data: 'driver_name',
                            name: 'driver_name'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            $('#btn-add-vehicle').click(function() {
                $('#vehicle_id').val('');
                $('#inbound_vehicle').val($('#inbound_id').val());
                $('#form-vehicle').trigger("reset");
                $('#modal-title').html("Add New Vehicle");
                $('#modal-vehicle').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('body').on('click', '.edit-vehicle', function() {
                var data_id = $(this).data('id');

                $.ajax({
                    data: {
                        "id": data_id
                    },
                    url: "{{ route('inbound-vehicle.edit') }}",
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html("Edit");
                        $('#btn-save-vehicle').val("Edit");
                        $('#modal-vehicle').modal('show');

                        $('#vehicle_id').val(data.id);
                        $('#inbound_vehicle').val(data.inbound_id);
                        $('#type_id').val(data.type_id);
                        $('#size_id').val(data.vehicle_type);
                        $('#vehicle_no').val(data.vehicle_no);
                        $('#driver_name').val(data.driver_name);
                        $('#transporter_name').val(data.transporter_name);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            if ($("#form-vehicle").length > 0) {
                $("#form-vehicle").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-vehicle').serialize(),
                            url: "{{ route('inbound-vehicle.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-vehicle').trigger("reset");
                                    $('#modal-vehicle').modal('hide');
                                    var oTable = $('#vehicle_table').dataTable();
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
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            $(document).on('click', '.delete-vehicle', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('vehicle')
                $('#modal-konfirmasi').modal('show');
            });

            function load_packing() {
                var dataId = $('#inbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#packing_table').DataTable().destroy();
                    $('#packing_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-detail.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            },
                        ]
                    });
                } else {
                    $('#packing_table').DataTable().destroy();
                    $('#packing_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-detail.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'ean_code_count',
                                name: 'ean_code_count'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                        ]
                    });
                }
            }

            function getVehicle() {
                var inbound_id = $('#inbound_id').val();

                $("#vehicle_packing").html('');
                $("#status_id").html('');
                $("#manufactur_id").html('');

                $.ajax({
                    url: "{{ route('inbound.getInboundVehicle') }}",
                    type: "GET",
                    data: {
                        inbound_id: inbound_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#vehicle_packing').html('<option value="">.:Select:.</option>');
                        $.each(result.vehicle_list, function(key, value) {
                            $("#vehicle_packing").append('<option value="' + value.vehicle_no +
                                '">' + value.vehicle_no + '</option>');
                        });

                        $('#status_id').html('<option value="">.:Select:.</option>');
                        $.each(result.stock_status_list, function(key, value) {
                            $("#status_id").append('<option value="' + value.id + '">' + value
                                .status_name + '</option>');
                        });

                        $('#manufactur_id').html('<option value="">.:Select:.</option>');
                        $.each(result.manufactur_list, function(key, value) {
                            $("#manufactur_id").append('<option value="' + value.id + '">' +
                                value.manufactur_name + '</option>');
                        });
                    }
                });
            }

            $('#btn-add-packing').click(function() {
                $('#actual-quantity').hide();
                $('#manual-site').hide();
                $('#vehicle_manual').hide();
                $('#vehicle_packing').show();
                $('#packing_flag').val('packing');
                $('#packing_id').val('');
                $('#inbound_packing').val($('#inbound_id').val());
                $('#form-packing').trigger("reset");
                $('#pallet_id').val('0');
                $('#modal-title-packing').html("Add New Detail");
                getVehicle();
                $('#modal-packing').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#btn-import').click(function() {
                $('#job_id').val($('#inbound_id').val());
                $('#form-import').trigger("reset");
                $('#modal-import').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-import").length > 0) {
                $("#form-import").validate({
                    submitHandler: function(form) {
                        let myForm = document.getElementById("form-import");
                        let formData = new FormData(myForm);
                        formData.append("file", $('#file')[0]);

                        $.ajax({
                            data: formData,
                            url: "{{ route('inbound-detail.import') }}",
                            type: "POST",
                            dataType: 'json',
                            enctype: 'multipart/form-data',
                            processData: false,
                            contentType: false,
                            cache: false,
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-import').trigger("reset");
                                    $('#modal-import').modal('hide');
                                    var oTable = $('#packing_table').dataTable();
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
                                $("#loader").hide();
                                // location.reload();
                            }
                        });
                    }
                })
            }

            $('body').on('click', '.edit-packing', function() {
                var data_id = $(this).data('id');
                $('#actual-quantity').hide();
                $('#manual-site').hide();
                $('#vehicle_manual').hide();
                $('#vehicle_packing').show();
                $('#packing_flag').val('packing');

                getVehicle();

                $.ajax({
                    data: {
                        "_token": CSRF_TOKEN,
                        "id": data_id
                    },
                    url: "{{ route('inbound-detail.edit') }}",
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        var mfg_date = "";
                        var exp_date = "";

                        if (data.mfg_date !== null) {
                            mfg_date = getFormatDate(data.mfg_date);
                        }

                        if (data.exp_date !== null) {
                            exp_date = getFormatDate(data.exp_date);
                        }

                        if (data.unit_level == 1) {
                            document.getElementById("mqty").readOnly = true;
                            document.getElementById("bqty").readOnly = true;
                        } else if (data.unit_level == 2) {
                            document.getElementById("mqty").readOnly = true;
                            document.getElementById("bqty").readOnly = false;
                        } else {
                            document.getElementById("mqty").readOnly = false;
                            document.getElementById("bqty").readOnly = false;
                        }

                        $('#modal-title-packing').html("Edit");
                        $('#btn-save-packing').val("Edit");
                        $('#modal-packing').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });

                        $('#packing_id').val(data.id);
                        $('#inbound_packing').val(data.inbound_id);
                        $('#vehicle_packing').val(data.vehicle_no).trigger('change');
                        $('#product_id').val(data.product_id);
                        $('#product_code').val(data.product_code);
                        $('#product_name').val(data.product_code + " - " + data.product_name);
                        $('#po_number').val(data.po_number);
                        $('#lot_no').val(data.lot_no);
                        $('#document_ref').val(data.document_ref);
                        $('#manufactur_id').val(data.manufactur_id).trigger('change');
                        $('#status_id').val(data.status_id).trigger('change');
                        $('#mfg_date').val(mfg_date);
                        $('#exp_date').val(exp_date);
                        $('#pqty').val(data.pqty);
                        $('#puom').val(data.puom);
                        $('#mqty').val(data.mqty);
                        $('#muom').val(data.muom);
                        $('#bqty').val(data.bqty);
                        $('#buom').val(data.buom);
                        $('#uppp').val(data.uppp);
                        $('#muppp').val(data.muppp);
                        $('#pallet_id').val(data.pallet_id);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            if ($("#form-packing").length > 0) {
                $("#form-packing").validate({
                    submitHandler: function(form) {
                        var packing_flag = $('#packing_flag').val();
                        var requestUrl = "";

                        if (packing_flag == 'packing') {
                            requestUrl = "{{ route('inbound-detail.store') }}";
                        } else if (packing_flag == 'grn') {
                            requestUrl = "{{ route('inbound-grn.store') }}";
                        } else if (packing_flag == 'manual') {
                            requestUrl = "{{ route('inbound-manual.store') }}";
                        }

                        $.ajax({
                            data: $('#form-packing').serialize(),
                            url: requestUrl,
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-packing').trigger("reset");
                                    $('#modal-packing').modal('hide');

                                    var oTable = "";
                                    if (packing_flag == 'packing') {
                                        oTable = $('#packing_table').dataTable();
                                    } else if (packing_flag == 'grn') {
                                        oTable = $('#grn_table').dataTable();
                                    } else if (packing_flag == 'manual') {
                                        oTable = $('#manual_table').dataTable();
                                    }

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
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            $('body').on('click', '.edit-location', function() {
                var data_id = $(this).data('id');

                $.ajax({
                    data: {
                        "id": data_id
                    },
                    url: "{{ route('inbound-confirm.edit') }}",
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title-location').html("Edit Location");
                        $('#modal-location').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });

                        var exp_date = "";

                        if (data.exp_date !== null) {
                            exp_date = getFormatDate(data.exp_date);
                        }

                        $('#batch_id').val(data.id);
                        $('#product_name_confirm').val(data.product_code + " - " + data
                            .product_name);
                        $('#lot_no_confirm').val(data.lot_no);
                        $('#exp_date_confirm').val(exp_date);
                        $('#pqty_confirm').val(data.pqty);
                        $('#puom_confirm').val(data.puom);
                        $('#mqty_confirm').val(data.mqty);
                        $('#muom_confirm').val(data.muom);
                        $('#bqty_confirm').val(data.bqty);
                        $('#buom_confirm').val(data.buom);

                        $('#site_id_confirm').val(data.site_id);
                        $('#site_name_confirm').val(data.site_name);
                        $('#area_id_confirm').val(data.area_id);
                        $('#area_name_confirm').val(data.area_name);
                        $('#location_id_confirm').val(data.location_id);
                        $('#location_code_confirm').val(data.location_code);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            if ($("#form-location").length > 0) {
                $("#form-location").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-location').serialize(),
                            url: "{{ route('inbound-confirm.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-location').trigger("reset");
                                    $('#modal-location').modal('hide');

                                    var oTable = $('#confirm_table').dataTable();
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
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            $("#product_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('product.getProduct') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        if (ui.item.unit_level == 1) {
                            document.getElementById("mqty").readOnly = true;
                            document.getElementById("bqty").readOnly = true;
                        } else if (ui.item.unit_level == 2) {
                            document.getElementById("mqty").readOnly = false;
                            document.getElementById("bqty").readOnly = true;
                        } else {
                            document.getElementById("mqty").readOnly = false;
                            document.getElementById("bqty").readOnly = true;
                        }

                        $('#pqty').val(0);
                        $('#mqty').val(0);
                        $('#bqty').val(0);

                        $('#uppp').val(ui.item.uppp);
                        $('#muppp').val(ui.item.muppp);
                        $('#puom').val(ui.item.puom);
                        $('#muom').val(ui.item.muom);
                        $('#buom').val(ui.item.buom);
                        $('#product_name').val(ui.item.product_code + " - " + ui.item.product_name);
                        $('#product_code').val(ui.item.product_code);
                        $('#product_id').val(ui.item.id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.product_code + " - " + item.product_name + "</div>")
                        .appendTo(ul);
                };

            $("#location_code_confirm").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('site.getLocationPrincipalAuto') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $("#principal_id").val(),
                                site_id: '%',
                                area_id: '%',
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#site_id_confirm').val(ui.item.site_id);
                        $('#site_name_confirm').val(ui.item.site_name);
                        $('#area_id_confirm').val(ui.item.area_id);
                        $('#area_name_confirm').val(ui.item.area_name);
                        $('#location_id_confirm').val(ui.item.location_id);
                        $('#location_code_confirm').val(ui.item.location_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Site : " + item.site_name + "<br>Area : " + item.area_name + "<br>Area : " +
                            item.location_code + "</div>")
                        .appendTo(ul);
                };

            $(document).on('click', '.delete-packing', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('packing')
                $('#modal-konfirmasi').modal('show');
            });

            function load_grn() {
                var dataId = $('#inbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#grn_table').DataTable().destroy();
                    $('#grn_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-grn.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action'
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            }
                        ]
                    });
                } else {
                    $('#grn_table').DataTable().destroy();
                    $('#grn_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-grn.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action'
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'ean_code_count',
                                name: 'ean_code_count'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                        ]
                    });
                }
            }

            $('#btn-add-manual').click(function() {
                $('#actual-quantity').hide();
                $('#manual-site').show();
                $('#vehicle_manual').show();
                $('#vehicle_packing').hide();
                $('#packing_flag').val('manual');
                $('#packing_id').val('');
                $('#inbound_packing').val($('#inbound_id').val());
                $('#form-packing').trigger("reset");
                $('#modal-title-packing').html("Add New Detail");
                getVehicle();
                $('#modal-packing').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#site_id').on('change', function() {
                var site_id = this.value;
                $("#area_id").html('');
                $.ajax({
                    url: "{{ route('site.getAreaList') }}",
                    type: "GET",
                    data: {
                        site_id: site_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#area_id').html('<option value="">.:Select:.</option>');
                        $.each(result.area_list, function(key, value) {
                            $("#area_id").append('<option value="' + value.id + '">' +
                                value.area_name + '</option>');
                        });
                    }
                });
            });

            $('#site_putaway').on('change', function() {
                var site_id = this.value;
                $("#area_putaway").html('');
                $.ajax({
                    url: "{{ route('site.getAreaList') }}",
                    type: "GET",
                    data: {
                        site_id: site_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#area_putaway').html('<option value="">.:Select:.</option>');
                        $.each(result.area_list, function(key, value) {
                            $("#area_putaway").append('<option value="' + value.id +
                                '">' + value.area_name + '</option>');
                        });
                    },
                    error: function(result) {
                        $('#area_putaway').html('<option value="">.:Select:.</option>');
                    }
                });
            });

            $('#area_id').on('change', function() {
                var site_id = $('#site_id').val();
                var area_id = this.value;
                $("#location_id").html('');
                $.ajax({
                    url: "{{ route('site.getLocationList') }}",
                    type: "GET",
                    data: {
                        site_id: site_id,
                        area_id: area_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#location_id').html('<option value="">.:Select:.</option>');
                        $.each(result.location_list, function(key, value) {
                            $("#location_id").append('<option value="' + value.id +
                                '">' + value.location_code + '</option>');
                        });
                    }
                });
            });

            $('body').on('click', '.edit-grn', function() {
                var data_id = $(this).data('id');
                $('#actual-quantity').show();
                $('#manual-site').hide();
                $('#vehicle_manual').hide();
                $('#vehicle_packing').show();
                $('#packing_flag').val('grn');

                getVehicle();

                $.ajax({
                    data: {
                        "_token": CSRF_TOKEN,
                        "id": data_id
                    },
                    url: "{{ route('inbound-detail.edit') }}",
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        var mfg_date = "";
                        var exp_date = "";

                        if (data.mfg_date !== null) {
                            mfg_date = getFormatDate(data.mfg_date);
                        }

                        if (data.exp_date !== null) {
                            exp_date = getFormatDate(data.exp_date);
                        }

                        if (data.unit_level == 1) {
                            document.getElementById("actual_mqty").readOnly = true;
                            document.getElementById("actual_bqty").readOnly = true;
                            document.getElementById("discrepancy_mqty").readOnly = true;
                            document.getElementById("discrepancy_bqty").readOnly = true;
                        } else if (data.unit_level == 2) {
                            document.getElementById("actual_mqty").readOnly = true;
                            document.getElementById("actual_bqty").readOnly = false;
                            document.getElementById("discrepancy_mqty").readOnly = true;
                            document.getElementById("discrepancy_bqty").readOnly = false;
                        } else {
                            document.getElementById("actual_mqty").readOnly = false;
                            document.getElementById("actual_bqty").readOnly = false;
                            document.getElementById("discrepancy_mqty").readOnly = false;
                            document.getElementById("discrepancy_bqty").readOnly = false;
                        }

                        document.getElementById("vehicle_packing").readOnly = true;
                        document.getElementById("product_name").readOnly = true;
                        document.getElementById("po_number").readOnly = true;
                        document.getElementById("lot_no").readOnly = true;
                        document.getElementById("document_ref").readOnly = true;
                        document.getElementById("mfg_date").readOnly = true;
                        document.getElementById("exp_date").readOnly = true;
                        document.getElementById("manufactur_id").readOnly = true;
                        document.getElementById("status_id").readOnly = true;
                        document.getElementById("pqty").readOnly = true;
                        document.getElementById("mqty").readOnly = true;
                        document.getElementById("bqty").readOnly = true;
                        document.getElementById("pallet_id").readOnly = true;

                        $('#modal-title-packing').html("Edit Actual Quantity");
                        $('#btn-save-packing').val("Edit");
                        $('#modal-packing').modal('show');

                        $('#packing_id').val(data.id);
                        $('#inbound_packing').val(data.inbound_id);
                        $('#vehicle_packing').val(data.vehicle_no).trigger('change');
                        $('#product_id').val(data.product_id);
                        $('#product_code').val(data.product_code);
                        $('#product_name').val(data.product_code + " - " + data.product_name);
                        $('#po_number').val(data.po_number);
                        $('#lot_no').val(data.lot_no);
                        $('#document_ref').val(data.document_ref);
                        $('#manufactur_id').val(data.manufactur_id).trigger('change');
                        $('#status_id').val(data.status_id).trigger('change');
                        $('#mfg_date').val(mfg_date);
                        $('#exp_date').val(exp_date);
                        $('#pqty').val(data.pqty);
                        $('#puom').val(data.puom);
                        $('#mqty').val(data.mqty);
                        $('#muom').val(data.muom);
                        $('#bqty').val(data.bqty);
                        $('#buom').val(data.buom);
                        $('#actual_pqty').val(data.actual_pqty);
                        $('#actual_puom').val(data.puom);
                        $('#actual_mqty').val(data.actual_mqty);
                        $('#actual_muom').val(data.muom);
                        $('#actual_bqty').val(data.actual_bqty);
                        $('#actual_buom').val(data.buom);
                        $('#uppp').val(data.uppp);
                        $('#muppp').val(data.muppp);
                        $('#discrepancy_pqty').val(data.discrepancy_pqty);
                        $('#discrepancy_puom').val(data.puom);
                        $('#discrepancy_mqty').val(data.discrepancy_mqty);
                        $('#discrepancy_muom').val(data.muom);
                        $('#discrepancy_bqty').val(data.discrepancy_bqty);
                        $('#discrepancy_buom').val(data.buom);
                        $('#pallet_id').val(data.pallet_id);
                        $('#remarks').val(data.remarks);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            if ($("#form-pallet").length > 0) {
                $("#form-pallet").validate({
                    submitHandler: function(form) {
                        var requestUrl = "{{ route('inbound-pallet.store') }}";

                        $.ajax({
                            data: $('#form-pallet').serialize(),
                            url: requestUrl,
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-pallet').trigger("reset");
                                    $('#modal-pallet').modal('hide');

                                    // var oTable = $('#putaway_table').dataTable();
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
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }

            $('#btn-delete').click(function() {
                var action = $('#action-delete').val();
                var requestUrl = "";
                var requestData = {};

                if (action == 'vehicle') {
                    requestUrl = "{{ route('inbound-vehicle.destroy') }}";
                    requestData = {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId
                    };
                } else if (action == 'packing') {
                    requestUrl = "{{ route('inbound-detail.destroy') }}";
                    requestData = {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId,
                        "inbound_id": "{{ $job_view->id ?? 0 }}",
                    };
                }

                $.ajax({
                    url: requestUrl,
                    type: 'delete',
                    data: requestData,
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(data) {
                        $("#loader").hide();
                        setTimeout(function() {
                            $('#modal-konfirmasi').modal('hide');

                            var oTable = "";
                            if (action == 'vehicle') {
                                oTable = $('#vehicle_table').dataTable();
                            } else if (action == 'packing') {
                                oTable = $('#packing_table').dataTable();
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
                        $("#loader").hide();
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                })
            });

            function load_cancel() {
                var dataId = $('#inbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#cancel_table').DataTable().destroy();
                    $('#cancel_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-cancel.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            },
                            {
                                data: 'actual_pqty',
                                name: 'actual_pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'actual_mqty',
                                name: 'actual_mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'actual_bqty',
                                name: 'actual_bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            }
                        ]
                    });
                } else {
                    $('#cancel_table').DataTable().destroy();
                    $('#cancel_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-cancel.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'actual_pqty',
                                name: 'actual_pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                        ]
                    });
                }
            }

            function load_confirm() {
                var dataId = $('#inbound_id').val();
                var multi_level = $('#multi_level').val();
                if ($.fn.DataTable.isDataTable('#confirm_table')) {
                    $('#confirm_table').DataTable().destroy();
                }

                let columnsConfig = [];

                if (multi_level == "Yes") {
                    columnsConfig = [{
                            data: 'check',
                            name: 'check',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: 'exp_date',
                            name: 'exp_date'
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: 'pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'mqty',
                            name: 'mqty'
                        },
                        {
                            data: 'muom',
                            name: 'muom'
                        },
                        {
                            data: 'bqty',
                            name: 'bqty'
                        },
                        {
                            data: 'buom',
                            name: 'buom'
                        },
                        {
                            data: 'serial_no',
                            name: 'serial_no'
                        },
                        {
                            data: 'remarks',
                            name: 'remarks'
                        },

                        {
                            data: null,
                            name: 'dimension',
                            orderable: false,
                            searchable: false,
                            render: function(data) {

                                if (parseFloat(data.volume) === 0) {
                                    return `
                            <div style="display:flex; gap:4px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="length" placeholder="L" style="width:60px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="width" placeholder="W" style="width:60px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="height" placeholder="H" style="width:60px;">
                            </div>
                        `;
                                } else {
                                    return `
                            <span class="badge badge-success">
                                    ${parseFloat(data.length).toFixed(2)} x 
                                    ${parseFloat(data.width).toFixed(2)} x 
                                    ${parseFloat(data.height).toFixed(2)}
                            </span>
                        `;
                                }
                            }
                        }
                    ];
                } else {
                    columnsConfig = [{
                            data: 'check',
                            name: 'check',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: 'exp_date',
                            name: 'exp_date'
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: 'pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'serial_no',
                            name: 'serial_no'
                        },
                        {
                            data: null,
                            name: 'dimension',
                            orderable: false,
                            searchable: false,
                            render: function(data) {

                                if (parseFloat(data.volume) === 0) {
                                    return `
                            <div style="display:flex; gap:4px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="length" placeholder="L" style="width:60px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="width" placeholder="W" style="width:60px;">
                                <input type="number" step="0.01" 
                                    class="dim-input form-control form-control-sm" 
                                    data-id="${data.product_id}" 
                                    data-type="height" placeholder="H" style="width:60px;">
                            </div>
                        `;
                                } else {
                                    return `
                            <span class="badge badge-success">
                                    ${parseFloat(data.length).toFixed(2)} x 
                                    ${parseFloat(data.width).toFixed(2)} x 
                                    ${parseFloat(data.height).toFixed(2)}
                            </span>
                        `;
                                }
                            }
                        }
                    ];
                }

                confirmTable = $('#confirm_table').DataTable({
                    dom: '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    destroy: true,
                    ajax: {
                        url: "{{ route('inbound-confirm.index') }}",
                        type: "GET",
                        data: {
                            inbound_id: dataId
                        }
                    },
                    columns: columnsConfig,

                    rowCallback: function(row, data) {
                        if (parseFloat(data.volume) === 0) {
                            $(row).addClass('table-danger');
                        }
                    },

                    drawCallback: function(settings) {
                        let json = settings.json;

                        if (!json) return;

                        if (json.has_zero_volume) {
                            $('#btn-process-confirm').hide();
                            swal({
                                icon: 'warning',
                                title: 'Dimensi belum lengkap',
                                text: 'Silakan isi panjang, lebar, dan tinggi terlebih dahulu.',
                            });
                        } else {
                            $('#btn-process-confirm').show();
                            window.volumeWarningShown = false;
                        }
                    }
                });
            }

            let tempDim = {};

            // trigger saat user selesai input (blur)
            $(document).on('blur', '.dim-input', function() {

                let productId = $(this).data('id');
                let type = $(this).data('type');
                let value = $(this).val();

                if (!value || value <= 0) return;

                if (!tempDim[productId]) {
                    tempDim[productId] = {};
                }

                tempDim[productId][type] = value;

                let dim = tempDim[productId];
                if (dim.length && dim.width && dim.height) {

                    $(`input[data-id="${productId}"]`).prop('disabled', true);
                    $.ajax({
                        url: "{{ url('product-master/update-dimension') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            product_id: productId,
                            length: dim.length,
                            width: dim.width,
                            height: dim.height
                        },
                        success: function(res) {
                            delete tempDim[productId];
                            $('#confirm_table').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            $(`input[data-id="${productId}"]`).prop('disabled', false);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menyimpan dimensi'
                            });
                        }
                    });
                }
            });




            function load_cross() {
                var dataId = $('#inbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#cross_table').DataTable().destroy();
                    $('#cross_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-crossdock.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'location_code',
                                name: 'location_code'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'muom',
                                name: 'muom'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                            {
                                data: 'buom',
                                name: 'buom'
                            },
                            {
                                data: 'serial_no',
                                name: 'serial_no'
                            }
                        ]
                    });
                } else {
                    $('#cross_table').DataTable().destroy();
                    $('#cross_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('inbound-crossdock.index') }}",
                            type: "GET",
                            data: {
                                inbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'product_code',
                                name: 'product_code'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'lot_no',
                                name: 'lot_no'
                            },
                            {
                                data: 'exp_date',
                                name: 'exp_date'
                            },
                            {
                                data: 'location_code',
                                name: 'location_code'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'puom',
                                name: 'puom'
                            },
                            {
                                data: 'serial_no',
                                name: 'serial_no'
                            }
                        ]
                    });
                }
            }

            $('.print-pallet-tag-check-all').change(function() {
                $('.print-pallet-tag-checked').prop('checked', this.checked);
            });

            $('#grn_table').on('click', '.grn-check', function() {
                if (this.checked == true) {
                    var table = $('#grn_table').DataTable();
                    data = table.row(this.closest('tr')).data()['id'];

                    $('.grn-check-all').prop('checked', true);
                } else {
                    $('.grn-check-all').prop('checked', false);
                }
            });

            $('#grn_table').on('click', '.grn-check-all', function() {
                $('.grn-check').prop('checked', this.checked);
            });

            $('#cancel_table').on('click', '.cancel-check', function() {
                if (this.checked == true) {
                    var table = $('#cancel_table').DataTable();
                    data = table.row(this.closest('tr')).data()['id'];

                    $('.cancel-check-all').prop('checked', true);
                } else {
                    $('.cancel-check-all').prop('checked', false);
                }
            });

            $('#cancel_table').on('click', '.cancel-check-all', function() {
                $('.cancel-check').prop('checked', this.checked);
            });

            $('#confirm_table').on('click', '.confirm-check', function() {
                if (this.checked == true) {
                    var table = $('#confirm_table').DataTable();
                    data = table.row(this.closest('tr')).data()['id'];

                    $('.confirm-check-all').prop('checked', true);
                } else {
                    $('.confirm-check-all').prop('checked', false);
                }
            });

            $('#confirm_table').on('click', '.confirm-check-all', function() {
                $('.confirm-check').prop('checked', this.checked);
            });

            $('body').on('click', '#icr-print', function() {
                var data_id = $('#inbound_id').val();

                window.open("{{ url('/warehouse/inbound/report/icr/') }}" + "/" + data_id,
                    'InboundReport',
                    'width=800,height=600')
            });

            $('body').on('click', '#grn-print', function() {
                var data_id = $('#inbound_id').val();

                window.open("{{ url('/warehouse/inbound/report/grn/') }}" + "/" + data_id,
                    'InboundReport',
                    'width=800,height=600')
            });

            $('body').on('click', '#grn-print-summary', function() {
                var data_id = $('#inbound_id').val();

                window.open("{{ url('/warehouse/inbound/report/grn-summary/') }}" + "/" + data_id,
                    'InboundReport',
                    'width=800,height=600')
            });

            $('body').on('click', '#putaway-print', function() {
                var data_id = $('#inbound_id').val();

                window.open("{{ url('/warehouse/inbound/report/putaway/') }}" + "/" + data_id,
                    'InboundReport', 'width=800,height=600')
            });

            $('body').on('click', '#putaway-report', function() {
                var data_id = $('#inbound_id').val();

                window.open("{{ url('/warehouse/inbound/report/putaway_report/') }}" + "/" + data_id,
                    'InboundReport', 'width=800,height=600')
            });

            // $('body').on('click', '#pallet-print', function() {
            //     var data_id = $('#inbound_id').val();
            //     var pallet_type = 0; //$('#pallet_type').val();

            //     if (pallet_type == 0) {
            //         window.open("{{ url('/warehouse/inbound/report/pallet/') }}" + "/" + data_id,
            //             'InboundReport', 'width=800,height=600')
            //     } else if (pallet_type == 4) {
            //         window.open("{{ url('/warehouse/inbound/report/pallet_4/') }}" + "/" + data_id,
            //             'InboundReport', 'width=800,height=600')
            //     } else if (pallet_type == 8) {
            //         window.open("{{ url('/warehouse/inbound/report/pallet_8/') }}" + "/" + data_id,
            //             'InboundReport', 'width=800,height=600')
            //     }
            // });

            $('body').on('click', '#confirm-print', function() {
                var data_id = $('#inbound_id').val();
                window.open("{{ url('/warehouse/inbound/report/confirm/') }}" + "/" + data_id,
                    'InboundReport', 'width=800,height=600')
            });
            $('body').on('click', '#confirm-quantum', function() {
                var data_id = $('#inbound_id').val();
                window.open("{{ url('/warehouse/inbound/report/confirm-quantum/') }}" + "/" + data_id,
                    'InboundReport', 'width=800,height=600')
            });
        });

        $('#selectSKU').select2({
            'placeholder': {
                id: '', // the value of the option
                text: 'Choose SKU..'
            }
        });

        function bypassScan(inbound_id) {
            swal({
                title: "Konfirmasi Bypass Scan?",
                text: "Pastikan data yang di packing sudah sesuai!",
                icon: "warning",
                buttons: [
                    'Nanti Dulu,',
                    'Ya, Saya Yakin!'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    location.href = "{{ url('warehouse/inbound/bypass') }}/" + inbound_id;
                } else {
                    return false;
                }
            })
        }

        $('#locationCode').select2({
            'placeholder': 'Select a location'
        });

        function getEditLokasiBatch(id_batch) {
            $.ajax({
                url: "{{ url('warehouse/inbound/getEditLokasiBatch') }}/" + id_batch,
                dataType: "json",
                success: function(data) {
                    $('#edit_lokasi_batch').modal('show')
                    $('#batch_id-edit_lokasi_batch').val(id_batch);
                    $('#lokasiAwalBatch').val(data.location_code);
                    $('.sku_edit').text(data.product_code)
                    $('.batch_edit').text(data.lot_no)
                },
                error: function(error) {
                    console.log('====================================');
                    console.log(error);
                    console.log('====================================');
                }
            });
        }

        function startPutaway(inbound_id, product_id, picking_id) {
            window.open("{{ url('/warehouse/inbound/startPutaway/') }}/" + inbound_id + '/' + product_id +
                '/' +
                picking_id, '_blank');
        }

        function printPalletTag(id, product_code, picking_id) {
            var prod_slash = product_code.search('/');
            var prod_hastag = product_code.search('#');
            if (prod_slash > 0) {
                var product = product_code.replace('/', '|');
            } else {
                var product = product_code;
            }
            if (prod_hastag > 0) {
                var product = product_code.replace('#', '-|');
            } else {
                var product = product_code;
            }
            window.open("{{ url('/warehouse/inbound/report/pallet/') }}" + "/" + id + '/' + product + '/' +
                picking_id, 'InboundReport',
                'width=800,height=600')
        }

        function printPalletTagAfter(id) {
            $('#modal-print-pallet-tag').modal('show')
            $('#job_id_print_pallet_tag').val(id)
        }

        function draftPutaway(id) {
            window.open("{{ url('/warehouse/inbound/report/draftPutaway/') }}" + "/" + id, 'InboundReport',
                'width=800,height=600')
        }

        $(function() {
            var d = new Date();
            d.setDate(d.getDate());
            $('#eta').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);

            $('#mfg_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            });

            $('#exp_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            });

            $('#ata').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });

            $('#unloading_start').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });

            $('#unloading_finish').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });

            $('#order_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            });

            $('#due_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            });
        });

        function processGRN() {
            var ata = $("#ata").val();
            var unloading_start = $("#unloading_start").val();
            var unloading_finish = $("#unloading_finish").val();

            if (ata == "" || unloading_start == "" || unloading_finish == "") {
                swal({
                    icon: "error",
                    text: "Shipment arrival & unloading date is required."
                });
                return;
            }

            var oTable = $('#grn_table').dataTable();
            // $('#form-grn').trigger("reset");

            $('.hidden-grn').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-grn').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-grn')
                        .val(this.value)
                    );
                }
            });

            $.ajax({
                data: $('#form-grn').serialize(),
                url: "{{ route('inbound-grn.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-grn').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        var oTable = $('#grn_table').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });

                        window.location.reload();
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    $("#loader").hide();
                }
            });
        }

        function processPutaway() {

            var btn = $("#btn-process-putaway");
            btn.prop('disabled', true).addClass('disabled');

            var site_id = $("#site_putaway").val();
            var area_putaway = $("#area_putaway").val();
            if (area_putaway == "" || site_id == "") {
                swal({
                    icon: "error",
                    text: "Site or Area cannot be empty."
                });
                return false;
            } else {
                var oTable = $('#putaway_table').dataTable();

                $('.hidden-putaway').remove();
                oTable.$('input[type="checkbox"]').each(function() {
                    if (this.checked) {
                        $('#form-putaway').append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', this.name)
                            .attr('class', 'hidden-putaway')
                            .val(this.value)
                        );
                    }
                });

                $.ajax({
                    data: $('#form-putaway').serialize(),
                    url: "{{ route('inbound-putaway.submit') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(data) {
                        $("#loader").hide();
                        if ($.isEmptyObject(data.error)) {
                            $('#form-putaway').trigger("reset");
                            var oTable = $('#putaway_table').dataTable();
                            oTable.fnDraw(false);

                            swal({
                                icon: "success",
                                text: "Data was processed successfully."
                            });
                            location.reload();
                        } else {
                            if ($.isEmptyObject(data.code)) {
                                swal({
                                    icon: "error",
                                    text: data.error
                                });
                            } else {
                                $("#product_id_pallet").val(data.product.id);
                                $("#product_code_pallet").val(data.product.product_code);
                                $("#product_name_pallet").val(data.product.product_name);
                                $("#puom_pallet").val(data.product.puom);

                                palletCapacity(data.product.id);
                            }
                        }
                    },
                    error: function(data) {
                        $("#loader").hide();
                    },
                    complete: function() {
                        btn.prop('disabled', false).removeClass('disabled');
                    }
                });
            }
        }

        function palletCapacity(product_id) {
            $('#pallet_table').DataTable().destroy();
            $('#pallet_table').DataTable({
                "dom": '<"wrapper"flipt>',
                processing: true,
                serverSide: true,
                destroy: true,
                paging: false,
                info: false,
                search: false,
                ajax: {
                    url: "{{ route('inbound-pallet.index') }}",
                    type: "GET",
                    data: {
                        product_id: product_id
                    }
                },
                columns: [{
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'pallet_qty',
                        name: 'pallet_qty'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });

            $('#modal-pallet').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function processCancel() {
            var oTable = $('#cancel_table').dataTable();
            $('#form-cancel').trigger("reset");

            $('.hidden-cancel').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-cancel').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-cancel')
                        .val(this.value)
                    );
                }
            });

            $.ajax({
                data: $('#form-cancel').serialize(),
                url: "{{ route('inbound-cancel.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-cancel').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        var oTable = $('#cancel_table').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    $("#loader").hide();
                }
            });
        }

        function processConfirm() {
            var oTable = $('#confirm_table').dataTable();
            $('#form-confirm').trigger("reset");

            $('.hidden-confirm').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-confirm').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-confirm')
                        .val(this.value)
                    );
                }
            });

            $.ajax({
                data: $('#form-confirm').serialize(),
                url: "{{ route('inbound-confirm.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    console.log('====================================');
                    console.log(data);
                    console.log('====================================');
                    $("#loader").hide();
                    $('#form-confirm').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        var oTable = $('#confirm_table').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    console.log(data);
                    $("#loader").hide();
                }
            });
        }

        function downloadExcel(link) {
            var query = {
                inbound_id: $('#inbound_id').val(),
            }

            var url = "{{ URL::to('warehouse/inbound/') }}/" + link + "?" + $.param(query)

            window.location = url;
        }

        function selectJob() {
            $("#outbound_id").val("");
            $("#order_id").val("");
            $("#customer_id").val("");
            $("#job_status").val("");
            $("#job_number").val("");
            $("#customer_name").val("");
            $("#customer_code").val("");
            $("#order_no").val("");
            $("#order_date").val("");
            $("#due_date").val("");

            document.getElementById("job_number").setAttribute("readonly", true);
            document.getElementById("customer_name").setAttribute("readonly", true);
            document.getElementById("order_no").setAttribute("readonly", true);
            document.getElementById("order_date").setAttribute("readonly", true);
            document.getElementById("due_date").setAttribute("readonly", true);

            $('#modal-cross').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }

        function processCrossDock() {
            var oTable = $('#cross_table').dataTable();
            $('#form-cross').trigger("reset");

            // $('.hidden-cross').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-cross').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-cross')
                        .val(this.value)
                    );
                }
            });

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'job_status')
                .attr('class', 'hidden-cross')
                .val($("#job_status").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'inbound_id')
                .attr('class', 'hidden-cross')
                .val($("#inbound_id").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'outbound_id')
                .attr('class', 'hidden-cross')
                .val($("#outbound_id").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'order_id')
                .attr('class', 'hidden-cross')
                .val($("#order_id").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'customer_id')
                .attr('class', 'hidden-cross')
                .val($("#customer_id").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'order_no')
                .attr('class', 'hidden-cross')
                .val($("#order_no").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'order_date')
                .attr('class', 'hidden-cross')
                .val($("#order_date").val())
            );

            $('#form-cross').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'due_date')
                .attr('class', 'hidden-cross')
                .val($("#due_date").val())
            );

            $.ajax({
                data: $('#form-cross').serialize(),
                url: "{{ route('inbound-crossdock.store') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-cross').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        var oTable = $('#cross_table').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    console.log(data);
                    $("#loader").hide();
                }
            });
        }

        function addPallet(picking_id, id, product_code, qty) {
            $('#modal-add-pallet').modal('show')
            $('.skuText').text(product_code);
            $('#skuValue').val(product_code);
            $('#qtyValue').val(qty);
            $('#inbound_id_per_pallet').val(id);
            $('#picking_id').val(picking_id);
            $.ajax({
                url: "{{ url('warehouse/inbound/detailPallet') }}/" + picking_id + "/" + id + "/" +
                    product_code,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('.resultTable').html('');
                    if (response.data.length > 0) {
                        $('.resultTable').append(`
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody-id">
                                                    </tbody>
                                                </table>`);
                        $.each(response.data, function(key, value) {
                            $('#tbody-id').append(`
                                                <tr>
                                                    <th>${key+1}</th>
                                                    <th>
                                                        ${value.qty_per_pallet}
                                                        <input class="form-control" type="text" hidden name="qty_per_pallet[]" placeholder="Silahkan Isi..">
                                                    </th>
                                                </tr>`)
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

        function jumlahPallet(jumlah) {
            $('.btn-save-add-pallet').removeClass('hide');
            $('.appendTable').html('');
            $('.appendTable').append(`
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>QTY</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-id">
                                            </tbody>
                                        </table>`);
            for (i = 1; i <= jumlah; i++) {
                $('#tbody-id').append(`
                                    <tr>
                                        <th>${i}</th>
                                        <th>
                                            <input class="form-control qtyPerPallet" type="number" required name="qtyPerPallet[]" placeholder="Silahkan Isi..">
                                        </th>
                                    </tr>`)
            }
        }

        $('#form-add-pallet').on('submit', function(e) {
            e.preventDefault();
            $('.btn-save-add-pallet').hide();
            $.ajax({
                url: "{{ route('inbound.add_per_pallet') }}",
                data: $('#form-add-pallet').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'lebih_besar') {
                        alert('QTY yang di masukan terlalu besar..');
                        $('.btn-save-add-pallet').show();
                    } else if (response.status == 'lebih_kecil') {
                        alert('QTY yang di masukan terlalu kecil..');
                        $('.btn-save-add-pallet').show();
                    } else {
                        swal({
                            icon: "success",
                            text: "Good Job!"
                        }).then(function() {
                            $('#modal-add-pallet').modal('hide');
                            $('.resultTable').html('');
                            $('.appendTable').html('');
                            $('#selectPallet').val('');
                            $('.btn-save-add-pallet').show();
                            load_grn_new();
                        });
                    }
                },
                error: function(error) {
                    alert('Internal Server Error, Please refresh the page and try again..');
                    $('.btn-save-add-pallet').show();
                }
            })
        });

        function load_grn_new() {
            var dataId = $('#inbound_id').val();
            var multi_level = $('#multi_level').val();

            if (multi_level == "Yes") {
                $('#grn_table').DataTable().destroy();
                $('#grn_table').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    destroy: true,
                    ajax: {
                        url: "{{ route('inbound-grn.index') }}",
                        type: "GET",
                        data: {
                            inbound_id: dataId
                        }
                    },
                    columns: [{
                            data: 'action',
                            name: 'action'
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: 'exp_date',
                            name: 'exp_date'
                        },
                        {
                            data: 'pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'mqty',
                            name: 'mqty'
                        },
                        {
                            data: 'muom',
                            name: 'muom'
                        },
                        {
                            data: 'bqty',
                            name: 'bqty'
                        },
                        {
                            data: 'buom',
                            name: 'buom'
                        },
                        {
                            data: 'pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'mqty',
                            name: 'mqty'
                        },
                        {
                            data: 'muom',
                            name: 'muom'
                        },
                        {
                            data: 'bqty',
                            name: 'bqty'
                        },
                        {
                            data: 'buom',
                            name: 'buom'
                        }
                    ]
                });
            } else {
                $('#grn_table').DataTable().destroy();
                $('#grn_table').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    destroy: true,
                    ajax: {
                        url: "{{ route('inbound-grn.index') }}",
                        type: "GET",
                        data: {
                            inbound_id: dataId
                        }
                    },
                    columns: [{
                            data: 'action',
                            name: 'action'
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: 'exp_date',
                            name: 'exp_date'
                        },
                        {
                            data: 'ean_code_count',
                            name: 'ean_code_count'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                    ]
                });
            }
        }

        function selectVehicle(value) {
            $.ajax({
                url: "{{ url('/warehouse/inbound/getDetailVehicle/') }}/" + value,
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    $('#driver_name').val(response.driver_name);
                    $('#transporter_name').val(response.transporter_name);
                    $('#size_id').val(response.vehicle_type);
                },
                error: function(error) {
                    alert('Internal Server Error, Please refresh the page and try again..');
                    $('.btn-save-add-pallet').show();
                }
            })
        }
    </script>
@endpush
