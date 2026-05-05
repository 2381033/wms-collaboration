@extends('layouts.main')

@section('title')
    Outbound
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Outbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Outbound</li>
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
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="job_no">Outbound Number</label>
                        <input type="hidden" id="multi_level" name="multi_level"
                            @isset($job_view->multi_level) value="{{ $job_view->multi_level }}" @endisset>
                        <input type="text" id="job_no" name="job_no"
                            @isset($job_view->job_no) value="{{ $job_view->job_no }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="job_date">Outbound Date</label>
                        <input type="text" id="job_date" name="job_date"
                            @isset($job_view->job_date) value="{{ \Carbon\Carbon::parse($job_view->job_date)->format('d-m-Y') }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">
                    <div class="btn-group">
                        @can('gate-access', 'warehouse/outbound')
                            <a href="{{ url('/warehouse/outbound/create/0') }}" class="btn btn-primary btn-sm"><i
                                    class="fas fa-plus"></i> <span>Add New Job</span></a>
                        @endcan
                        {{-- <a href="#" class="btn btn-info btn-sm"><i class="fas fa-folder-open"></i> <span>Open Job</span></a> --}}
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="outbound-tabs" role="tablist" style="zoom: 90%;">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab"
                                aria-controls="home" aria-selected="true">
                                <i class="fas fa-info-circle"></i> Job Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="order-link" data-toggle="tab" href="#order-tab" role="tab"
                                aria-controls="order" aria-selected="false">
                                <i class="fas fa-box"></i> Order Header</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="detail-link" data-toggle="tab" href="#detail-tab" role="tab"
                                aria-controls="detail" aria-selected="false">
                                <i class="fas fa-list"></i> Order Detail</a>
                        </li>
                        @can('gate-access', 'adminDC')
                            <li class="nav-item">
                                <a class="nav-link" id="picking-link" data-toggle="tab" href="#picking-tab" role="tab"
                                    aria-controls="picking" aria-selected="false">
                                    <i class="fas fa-list-ol"></i> Picking</a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link" id="picking-checker" onclick="generateTablePick()" data-toggle="tab"
                                href="#picking-checker-tab" role="tab" aria-controls="picking" aria-selected="false">
                                <i class="fas fa-user"></i> Pick By Checker</a>
                        </li>
                        @if (isset($job_view->id))
                            @if ($job_view->principal_id == 3)
                                <li class="nav-item">
                                    <a class="nav-link" id="scan-ean" onclick="tabEan()" data-toggle="tab"
                                        href="#scan-ean-checker" role="tab" aria-controls="scan-ean"
                                        aria-selected="false">
                                        <i class="fas fa-barcode"></i> Scan Carton ID</a>
                                </li>
                            @endif
                        @endif
                        @can('gate-access', 'AdminDC')
                            <li class="nav-item">
                                <a class="nav-link" id="cancel-link" data-toggle="tab" href="#cancel-tab" role="tab"
                                    aria-controls="cancel" aria-selected="false">
                                    <i class="fas fa-reply"></i> Cancel</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link print-loading-list" id="loading-list-link" data-toggle="tab"
                                    href="#loading-list-tab" role="tab" aria-controls="loading-list"
                                    aria-selected="false">
                                    <i class="fas fa-truck-moving"></i> Loading List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="confirm-link" data-toggle="tab" href="#confirm-tab" role="tab"
                                    aria-controls="confirm" aria-selected="false">
                                    <i class="fas fa-check-circle"></i> Confirm Job</a>
                            </li>
                        @endcan
                        @if (isset($job_view->id))
                            @if ($job_view->confirmed_flag == 'Yes')
                                <li class="nav-item">
                                    <a class="nav-link" id="despatch-link" data-toggle="tab" href="#despatch-tab"
                                        role="tab" aria-controls="cancel" aria-selected="false">
                                        <i class="fas fa-print"></i> Despatch</a>
                                </li>
                            @endif
                        @endif
                    </ul>
                    <div class="tab-content" id="outboundTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel"
                            aria-labelledby="home-tab5">
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="outbound_id" name="outbound_id"
                                    @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
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
                                        <div class="col-md-4">
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
                                        <div class="col-md-2">
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
                                        <div class="col-md-2">
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
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description"
                                                    name="description" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>ETD</label>
                                                <input type="text" autocomplete="off" id="etd" name="etd"
                                                    class="form-control">
                                                @if (isset($job_view->allocated_flag) && !empty($job_view->allocated_flag))
                                                    <a href="javascript:void(0)" onclick="updateETD()"
                                                        class="btn btn-sm btn-dark mt-3 btnETD">Update ETD</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Entry Date</label>
                                                <input type="text" autocomplete="off" id="entry_date"
                                                    name="entry_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="entryBy"></label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Allocated Date</label>
                                                <input type="text" autocomplete="off" id="allocated_date"
                                                    name="allocated_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="allocatedBy"></label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Confirmed Date</label>
                                                <input type="text" autocomplete="off" id="confirmed_date"
                                                    name="confirmed_date" class="form-control" readonly>
                                                <span class="text-muted">By: <label for=""
                                                        class="confirmatedBy"></label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">
                                                @can('gate-access', 'warehouse/outbound')
                                                    @if (isset($job_view->allocated_flag) && !empty($job_view->allocated_flag))
                                                        @if ($job_view->allocated_flag == 'No')
                                                            <button type="submit" id="btn-save-job"
                                                                class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                                <span>Save</span></button>
                                                        @endif
                                                    @else
                                                        <button type="submit" id="btn-save-job"
                                                            class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                            <span>Save</span></button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="order-tab" role="tabpanel" aria-labelledby="order-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/outbound')
                                                @if (isset($job_view->allocated_flag) && !empty($job_view->allocated_flag))
                                                    @if ($job_view->allocated_flag == 'No')
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            id="btn-add-order"><i class="fas fa-plus"></i> <span>Add
                                                                Order</span></button>
                                                    @endif
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="btn-add-order"><i class="fas fa-plus"></i> <span>Add
                                                            Order</span></button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="order_table" class="table table-striped table-bordered table-sm"
                                                style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Customer Code</th>
                                                        <th>Customer Name</th>
                                                        <th>PO Number</th>
                                                        <th>Order Number</th>
                                                        <th>Order Date</th>
                                                        <th>Due Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="detail-tab" role="tabpanel" aria-labelledby="detail-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/outbound')
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-detail"><i
                                                        class="fas fa-plus"></i> <span>Add Order Detail</span></button>
                                            @endcan
                                            <button class="btn btn-success btn-sm" id="btn-import"><i
                                                    class="fas fa-upload"></i> Upload</button>
                                            @if ($job_view)
                                                <button type="button"
                                                    onclick="downloadExcel('detail/export/{{ $job_view->id }}');"
                                                    class="btn btn-success btn-sm"><i class="fas fa-download"></i>
                                                    <span>Template</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="detail_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">Order Number</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
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
                                                <table id="detail_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">Order Number</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th colspan="2">Expected Quantity</th>
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
                        <div class="tab-pane fade show" id="picking-tab" role="tabpanel" aria-labelledby="picking-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/outbound')
                                                <button type="button" class="btn btn-danger btn-sm" id="btn-process-picking"
                                                    onclick="processPicking();"><i class="fas fa-gear"></i>
                                                    <span>Process</span></button>
                                            @endcan
                                            {{-- <a id="picking-print"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Picking List</span>
                                            </a> --}}
                                            <a id="picking-report-print"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-success text-white btn-sm"><i class="fas fa-print"></i>
                                                <span>Picking Report</span>
                                            </a>
                                            <a id="pallet-picking-report-print"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-dark text-white btn-sm"><i class="fas fa-print"></i>
                                                <span>Pallet Picking Report</span>
                                            </a>
                                            <a id="pallet-picking-report-print-excel"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-success text-white btn-sm"><i class="fas fa-print"></i>
                                                <span>Pallet Picking Report Excel</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <form id="form-picking" name="form-picking" method="post">
                                        @csrf
                                    </form>
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="picking_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="picking-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th rowspan="2">Location From</th>
                                                            <th rowspan="2">Location To</th>
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
                                                <table id="picking_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <input type="checkbox" required="required"
                                                                    class="picking-check-all">
                                                            </th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th rowspan="2">Location From</th>
                                                            <th rowspan="2">Location To</th>
                                                            <th colspan="2">Expected Quantity</th>
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
                        <div class="tab-pane fade show" id="picking-checker-tab" role="tabpanel"
                            aria-labelledby="picking-tab5">
                            <div class="container mt-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        @can('bypass-scan-outbound', 'bypass-pick-bychecker')
                                            <div class="float-right mb-2 mt-2">
                                                <a href="javascript:void(0)" onclick="byPass('{{ $job_view->id ?? 0 }}')"
                                                    class="btn btn-outline-primary btn-lg">
                                                    <i class="fas fa-user-cog"></i>
                                                    Bypass Scan</a>
                                            </div>
                                        @endcan
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>SKU NO</th>
                                                        <th>SKU NAME</th>
                                                        <th>BATCH</th>
                                                        <th>LOCATION CODE</th>
                                                        <th>QTY</th>
                                                        <th>TOOLS</th>
                                                        <th>SOA</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tablePickByChecker">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="scan-ean-checker" role="tabpanel"
                            aria-labelledby="scan-ean-checker">
                            <div class="container mt-3">
                                <div class="row">
                                    <div class="col-sm-8">
                                        @can('gate-access', 'CheckerDC')
                                            @if (isset($job_view))
                                                @if ($job_view->principal_id == 3)
                                                    <div class="row">
                                                        <div class="col-sm-6 mt-4">
                                                            <div class="form-group">
                                                                <input type="text" name="" id="scanEAN"
                                                                    class="form-control" placeholder="Scan Barcode EAN Here.."
                                                                    aria-describedby="helpId" autofocus autocomplete="off"
                                                                    style="background-color: yellow;">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 mt-4">
                                                            <a href="#modal-scanning-list" data-toggle="modal"
                                                                class="btn btn-dark btn-md"> <i class="fas fa-eye"></i> Show
                                                                Scanning List</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endcan
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="table-ean">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>SKU NO</th>
                                                        {{-- <th>SKU NAME</th> --}}
                                                        {{-- <th>BATCH</th> --}}
                                                        <th>QTY SCAN</th>
                                                        <th>QTY EXPECTED</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="cancel-tab" role="tabpanel" aria-labelledby="cancel-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/outbound')
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
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="6">Quantity</th>
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
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th rowspan="2">Location</th>
                                                            <th colspan="2">Quantity</th>
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
                        {{-- <div class="tab-pane fade show" id="loading-list-tab" role="tabpanel"
                            aria-labelledby="loading-list-tab">
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)"
                                            class="btn btn-md btn-dark print-loading-list mt-4"><i
                                                class="fas fa-print"></i> Print Loading List</a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="tab-pane fade show" id="confirm-tab" role="tabpanel" aria-labelledby="confirm-tab5">
                            <div class="container mt-3">
                                <form id="form-confirm" name="form-confirm" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ata">Shipment Arrival Date</label>
                                                <input type="text" id="ata" name="ata"
                                                    class="form-control floating-label"
                                                    @isset($job_view->ata) value="{{ \Carbon\Carbon::parse($job_view->ata)->format('d/m/Y H:i') }}" @endisset
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ata">Loading Start</label>
                                                <input type="text" id="loading_start" name="loading_start"
                                                    class="form-control floating-label"
                                                    @isset($job_view->loading_start) value="{{ \Carbon\Carbon::parse($job_view->loading_start)->format('d/m/Y H:i') }}" @endisset
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ata">Loading Finish</label>
                                                <input type="text" id="loading_finish" name="loading_finish"
                                                    class="form-control floating-label"
                                                    @isset($job_view->loading_finish) value="{{ \Carbon\Carbon::parse($job_view->loading_finish)->format('d/m/Y H:i') }}" @endisset
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @can('gate-access', 'warehouse/outbound')
                                                @if ($confirm_checker)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        id="btn-process-confirm" onclick="processConfirm();"><i
                                                            class="fas fa-gear"></i>
                                                        <span>Process</span>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-dark btn-sm" id=""
                                                        onclick="alert('Menunggu Checker Mengkonfirmasi Lokasi');"><i
                                                            class="fas fa-gear"></i>
                                                        <span>Menunggu Checker</span>
                                                    </button>
                                                @endif
                                            @endcan
                                            @if ($confirm_checker)
                                                <a id="confirm-print"
                                                    @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                    class="btn btn-info btn-sm"><i class="fas fa-print"></i>
                                                    <span>Confirmation Report</span></a>
                                                <button type="button" onclick="downloadExcel('confirm/export');"
                                                    class="btn btn-success btn-sm"><i class="fas fa-download"></i>
                                                    <span>Download</span></button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
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
                                                            <th rowspan="2">Location From</th>
                                                            <th rowspan="2">Location Confirm Checker</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th colspan="6">Quantity</th>
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
                                                            <th rowspan="2">Location From</th>
                                                            <th rowspan="2">Location Confirm Checker</th>
                                                            <th rowspan="2">SKU No.</th>
                                                            <th rowspan="2">SKU Name</th>
                                                            <th rowspan="2">Batch No</th>
                                                            <th rowspan="2">Expired Date</th>
                                                            <th rowspan="2">Site Name</th>
                                                            <th rowspan="2">Area Name</th>
                                                            <th colspan="2">Quantity</th>
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
                        <div class="tab-pane fade show" id="despatch-tab" role="tabpanel"
                            aria-labelledby="confirm-tab5">
                            <div class="container mt-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            @if ($multi_level == 'Yes')
                                                <table id="despatch_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">Customer Name</th>
                                                            <th rowspan="2">Delivery Type</th>
                                                            <th rowspan="2">ETD</th>
                                                            <th rowspan="2">Mode</th>
                                                            <th rowspan="2">Carrier Name</th>
                                                            <th rowspan="2">Vessel Name</th>
                                                            <th rowspan="2">Driver Name</th>
                                                            <th rowspan="2">AWB No</th>
                                                            <th colspan="3">Quantity</th>
                                                            {{-- <th>Expected Qty</th> --}}
                                                        </tr>
                                                        <tr>
                                                            <th>1st</th>
                                                            <th>2nd</th>
                                                            <th>3rd</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            @else
                                                <table id="despatch_table"
                                                    class="table table-striped table-bordered table-sm"
                                                    style="width:100%;">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th rowspan="2">Action</th>
                                                            <th rowspan="2">Customer Name</th>
                                                            <th rowspan="2">Delivery Type</th>
                                                            <th rowspan="2">ETD</th>
                                                            <th rowspan="2">Mode</th>
                                                            <th rowspan="2">Carrier Name</th>
                                                            <th rowspan="2">Vessel Name</th>
                                                            <th rowspan="2">Driver Name</th>
                                                            <th rowspan="2">AWB No</th>
                                                            <th colspan="2">Quantity</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Actual Qty</th>
                                                            <th>Expected Qty</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-order">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-order" name="form-order" method="post">
                    @csrf
                    <input type="hidden" id="order_id" name="order_id">
                    <input type="hidden" id="outbound_order" name="outbound_order">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="hidden" id="customer_id" name="customer_id" class="form-control">
                                    <input type="text" autocomplete="off" id="customer_name" name="customer_name"
                                        class="form-control">
                                    {{-- <select class="custom-select" id="customer_id" name="customer_id">
                                    <option value="">.:Select:.</option>
                                </select> --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PO Number</label>
                                    <input type="text" autocomplete="off" id="po_number" name="po_number"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Order Number</label>
                                    <input type="text" autocomplete="off" id="order_no" name="order_no"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="text" autocomplete="off" id="order_date" name="order_date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="text" autocomplete="off" id="due_date" name="due_date"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-order"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
        <div class="modal-dialog  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-detail"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-detail" name="form-detail" method="post">
                    @csrf
                    <input type="hidden" id="detail_id" name="detail_id">
                    <input type="hidden" id="outbound_detail" name="outbound_detail">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Order Number</label>
                                    <input type="hidden" id="order_id_detail" name="order_id_detail"
                                        class="form-control">
                                    <input type="text" id="order_no_detail" name="order_no_detail"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="hidden" id="customer_id_detail" name="customer_id_detail">
                                    <input type="text" id="customer_name_detail" name="customer_name_detail"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>SKU No. / SKU Name</label>
                                    <input type="hidden" id="product_id" name="product_id">
                                    <input type="hidden" id="product_code" name="product_code">
                                    <input type="hidden" id="uppp" name="uppp">
                                    <input type="hidden" id="muppp" name="muppp">
                                    <input type="text" autocomplete="off" id="product_name" name="product_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>1st Qty</label>
                                    <input type="text" autocomplete="off" id="pqty" name="pqty"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>1st Unit</label>
                                    <input type="text" autocomplete="off" id="puom" name="puom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>2nd Qty</label>
                                    <input type="text" autocomplete="off" id="mqty" name="mqty"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>2nd Unit</label>
                                    <input type="text" autocomplete="off" id="muom" name="muom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>3rd Qty</label>
                                    <input type="text" autocomplete="off" id="bqty" name="bqty"
                                        value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>3rd Unit</label>
                                    <input type="text" autocomplete="off" id="buom" name="buom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <legend>Filter Stock</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Site Name</label>
                                        <input type="hidden" id="site_id" name="site_id">
                                        <input type="text" id="site_name" name="site_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Area Name</label>
                                        <input type="hidden" id="area_id" name="area_id">
                                        <input type="text" id="area_name" name="area_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Location From</label>
                                        <input type="hidden" id="location_from_id" name="location_from_id">
                                        <input type="text" id="location_from" name="location_from"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Location To</label>
                                        <input type="hidden" id="location_to_id" name="location_to_id">
                                        <input type="text" id="location_to" name="location_to" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Batch Number</label>
                                        <input type="text" id="lot_no" name="lot_no" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-detail"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-scanning-list">
        <div class="modal-dialog  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-detail"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>SKU</th>
                                        <th>CARTON ID</th>
                                        <th>QTY SCAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list_sku->whereNotNull('ean_code') as $item)
                                        <tr class="text-center align-top">
                                            <td>{{ $item->product_code }}</td>
                                            <td class="text-start">
                                                <ol class="mb-0 ps-3">
                                                    @foreach (explode(',', $item->ean_code) as $ean)
                                                        <li> {{ $ean }}</li>
                                                    @endforeach
                                                </ol>
                                            </td>
                                            <td>{{ count(explode(',', $item->ean_code)) }}</td>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-despatch">
        <div class="modal-dialog  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-despatch">Edit Despatch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-despatch" name="form-despatch" method="post">
                    @csrf
                    <input type="hidden" id="despatch_id" name="despatch_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>DO Number</label>
                                    <input type="text" id='do_no' name="do_no" class="form-control"
                                        autocomplete="off" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="text" id="customer_name_despatch" name="customer_name_despatch"
                                        class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Delivery Type</label>
                                    <select name="delivery_type" id="delivery_type" class="custom-select">
                                        <option value="Reguler">Reguler</option>
                                        <option value="Ritase">Ritase</option>
                                        <option value="Lumpsum">Lumpsum</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Mode Of Transport</label>
                                    <select name="mode_id_despatch" id="mode_id_despatch" class="custom-select" disabled>
                                        @foreach ($mode_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->mode_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Reference Number</label>
                                    <input type="text" id='ref_no' name="ref_no" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Carrier Name</label>
                                    <input type="text" id='carrier_name' name="carrier_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vessel Name</label>
                                    <input type="text" id='vessel_name' name="vessel_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehicle No</label>
                                    <select name="vehicle_no" id="vehicle_no" required class="form-control">
                                        <option value="" disabled selected>.:Select:.</option>
                                        @foreach ($vehicle as $item)
                                            <option value="{{ $item->vehicle_number }}">{{ $item->vehicle_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Driver Name</label>
                                    <input type="text" id='driver_name' name="driver_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Driver Phone</label>
                                    <input type="text" id='phone' name="phone" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Seal No</label>
                                    <input type="text" id='seal_no' name="seal_no" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type Size</label>
                                    <select class="custom-select" id="size_id" name="size_id" required>
                                        <option value="">.:Select:.</option>
                                        @foreach ($container_size as $item)
                                            <option value="{{ $item->id }}">{{ $item->size_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Container No</label>
                                    <input type="text" id='container_no' name="container_no" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>ETD</label>
                                    <input type="text" id='etd_despatch' name="etd_despatch" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>AWB No</label>
                                    <input type="text" id='awb_no' name="awb_no" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>AWB Date</label>
                                    <input type="text" id='awb_date' name="awb_date" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Send Date Doc.</label>
                                    <input type="text" id='send_date_doc' name="send_date_doc" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>1st Qty</label>
                                    <input type="text" autocomplete="off" id="pqty_despatch" name="pqty_despatch"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>2nd Qty</label>
                                    <input type="text" autocomplete="off" id="mqty_despatch" name="mqty_despatch"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>3rd Qty</label>
                                    <input type="text" autocomplete="off" id="bqty_despatch" name="bqty_despatch"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Store Name</label>
                                    <input type="hidden" id="store_id" name="store_id">
                                    <input type="text" autocomplete="off" id="store_name" name="store_name"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" autocomplete="off" name="price" class="form-control"
                                        value="0" id="price" min="0" required>
                                    <span class="text-muted">Abaikan jika tidak ada</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <textarea name="address" id="address" cols="30" rows="3" class="form-control" disabled>

                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save-despatch"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <form method="post" id="form-import" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="job_id" name="job_id">
                        <div class="col-md-12">
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

    <div class="modal fade" role="dialog" id="modal-submit-scan-lokasi">
        <div class="modal-dialog  modal-lg" role="document">
            <form action="{{ route('outbound-scan_lokasi') }}" method="post" id="form-scan-lokasi">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">MAPPING LOKASI SKU <b class="skuText"></b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" id="id_list" name="id">
                    <input type="hidden" id="skuValue" name="product_code">
                    <input type="hidden" id="locationCodeValue" name="location_code">
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm btn-scan-lokasi"> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-scan-lokasi">
        <div class="modal-dialog modal-dialog-centered  modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">MAPPING LOKASI SKU <b class="skuText"></b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="inputLocationCode" autofocus="on"
                                    autocomplete="off" placeholder="Scan Location Here..">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-submit-scan-pallet">
        <div class="modal-dialog  modal-lg" role="document">
            <form action="" method="post" id="form-scan-pallet">
                @csrf
                <div class="modal-content">
                    <input type="hidden" id="qrcodeValue" name="qrcode">
                    <input type="hidden" id="skuValue" name="product_code">
                    <input type="hidden" class="inbound_id" name="inbound_id">
                    <input type="hidden" class="id_per_pallet" name="id_per_pallet">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class="cameraScanPallet">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm btn-scan-pallet hide"> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-scan-pallet">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="inputPalletTag" placeholder="Scan Here.."
                                autocomplete="off" autofocus>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-qty-outbound">
        <div class="modal-dialog modal-dialog-centered  modal-md" role="document">
            <form action="{{ route('outbound.validasi_qty_batch') }}" method="post" id="form-qty-outbound">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            SKU : <b class="skuText"></b>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">QTY</span>
                                    </div>
                                    <input type="text" class="form-control qtyBatch" placeholder="Silahkan isi"
                                        aria-label="Username" aria-describedby="basic-addon1" name="qty" required>
                                </div>
                                <input class="form-control idBatch" hidden type="text" name="id_batch">
                                @if (isset($job_view->id) && !empty($job_view->id))
                                    <input class="form-control" value="{{ $job_view->id }}" hidden type="text"
                                        name="outbound_id">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm btn-qty-outbound"> Simpan</button>
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

        function tabEan() {
            loadDataEan();
        }

        $("#scanEAN").keyup(function(event) {
            var job_id = "{{ $job_view->id ?? 0 }}";
            var value = $('#scanEAN').val();
            if (event.keyCode === 13) {
                $('#scanEAN').val("");
                $.ajax({
                    url: "{{ url('warehouse/outbound/detail/doScanEan') }}/" + value + '/' +
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
                        } else if (data.message == 'qty') {
                            error.play();
                            swal({
                                icon: "warning",
                                text: "The scan process exceeds the limit!"
                            }).then(function() {
                                $("#scanEAN").focus();
                            })
                        } else {
                            success.play();
                            swal({
                                icon: "success",
                                text: `Good Job! ${data.sku}`
                            }).then(function() {
                                $("#scanEAN").focus();
                            })
                            loadDataEan();
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

        function loadDataEan() {
            $('#table-ean').DataTable().destroy();
            $('#table-ean').DataTable({
                "dom": '<"wrapper"flipt>',
                processing: true,
                serverSide: true,
                paging: false,
                destroy: true,
                searching: false,
                ajax: {
                    url: "{{ url('warehouse/outbound/getListEAN/') }}/" + '{{ $job_view->id ?? 0 }}',
                    type: "GET",
                },
                columns: [{
                        data: 'product_code',
                        name: 'product_code'
                    },
                    {
                        data: 'ean_code_count',
                        name: 'ean_code_count',
                        render: function(data, type, row) {
                            return data + ' ' + row.puom; // Menggabungkan ean_code_count dan puom
                        }
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        render: function(data, type, row) {
                            return data + ' ' + row.puom; // Menggabungkan ean_code_count dan puom
                        }
                    },
                ],
                order: [
                    [0, 'asc']
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data.ean_code_count != data.qty) {
                        $(row).css('background-color', 'yellow'); // Beri warna kuning
                    }
                }
            });
        }



        $('#locationCode').select2({
            'placeholder': 'Select a location'
        });

        $('#modal-scan-pallet').on('shown.bs.modal', function() {
            $('#inputPalletTag').focus();
        });

        $('#modal-scan-lokasi').on('shown.bs.modal', function() {
            $('#inputLocationCode').focus();
        });

        function byPass(outbound_id) {
            swal({
                title: "Konfirmasi Bypass Scan?",
                text: "Pastikan data yang di picking sudah sesuai!",
                icon: "warning",
                buttons: [
                    'Nanti Dulu,',
                    'Ya, Saya Yakin!'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    location.href = "{{ url('warehouse/outbound/bypass') }}/" + outbound_id;
                } else {
                    return false;
                }
            })
        }
        generateListPickChecker();

        function generateTablePick() {
            generateListPickChecker();
        }

        function generateListPickChecker() {
            $.ajax({
                url: "{{ url('warehouse/outbound/getListPickByChecker') }}/" + '{{ $job_view->id ?? 0 }}',
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    $("#tablePickByChecker").html("");
                    $.each(response, function(key, value) {
                        var batch = ""
                        var tools = ""
                        var soa = ""
                        if (value.lot_no == null) {
                            var batch = "-"
                        } else {
                            var batch = value.lot_no
                        }
                        if (value.scan_pallet_tag == 'N' && value.scan_location == 'N') {
                            @can('gate-access', 'CheckerDC')
                                var tools = ` <a href="javascript:void(0)" class="btn btn-sm btn-dark"
                                onclick="scanPalletTag('${value.id}', '${value.product_code}')">
                                <i class="fas fa-camera"></i> Scan Pallet Tag</a>`
                            @endcan
                        } else if (value.scan_pallet_tag == 'Y' && value.scan_location == 'Y') {
                            var tools = `<span class="badge badge-success"> <i class="fas fa-check mr-1"></i> Done!
                            </span>`
                        } else {
                            @can('gate-access', 'CheckerDC')
                                var tools = `<a href="javascript:void(0)" class="btn btn-sm btn-info"
                                onclick="scanLocation('${value.id}', '${value.product_code}')"><i
                                    class="fas fa-camera"></i> Scan Location </a>`
                            @endcan
                        }
                        if (value.soa > 0) {
                            var soa = value.soa + " " + value.puom;
                        } else {
                            var soa = '-';
                        }
                        $("#tablePickByChecker").append(`
                            <tr class="text-center">
                                <td>${value.product_code}</td>
                                <td>${value.product_name}</td>
                                <td>${batch}</td>
                                <td>${value.location_code}</td>
                                <td>${value.qty} ${value.puom}</td>
                                <td>${tools}</td>
                                <td>${value.soa} ${value.puom}</td>
                            </tr>
                        `);
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function manualLocation(id, product_code, qty) {
            $('#modal-scan-lokasi').modal('show');
            $('.skuText').text(product_code);
            $('#qtyValueScan').val(qty);
            $('#id_list').val(id);
            $('#productCodeValue').val(product_code);
            $('.manual').removeClass('hide')
            $('.scan').addClass('hide')
        }

        function scanPalletTag(id, product_code) {
            $('#inputPalletTag').focus();
            sessionStorage.setItem('product_code', product_code);
            sessionStorage.setItem('id_batch', id);
            $('#modal-scan-pallet').modal('show')
        }
        $('#inputPalletTag').on('keydown', function(event) {
            var value = $('#inputPalletTag').val();
            var product_code = sessionStorage.getItem('product_code');
            var id_batch = sessionStorage.getItem('id_batch');
            if (event.keyCode === 13) {
                event.preventDefault();
                doScanPallet();
            }
        });

        function doScanPallet() {
            var id_batch = sessionStorage.getItem('id_batch')
            var product_code = sessionStorage.getItem('product_code')
            $.ajax({
                url: "{{ url('warehouse/outbound/scanPalletTag') }}/" + id_batch + '/' + product_code,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status == null) {
                        error.play();
                        swal({
                            icon: "warning",
                            text: "qrcode tidak di kenali.."
                        });
                    } else if (response.status == 'not_same') {
                        error.play();
                        swal({
                            icon: "warning",
                            text: "SKU Dengan Pallet Tag Tidak Sesuai.."
                        });
                    } else {
                        $('#modal-qty-outbound').modal('show');
                        $('.qtyBatch').val(response.data.qty);
                        $('.idBatch').val(response.data.id);
                        $('.skuText').text(response.data.product_code);
                        $('#modal-scan-pallet').modal('hide')
                    }
                },
                error: function(response) {
                    error.play();
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }

        $('#inputLocationCode').on('keydown', function(event) {
            var value = $('#inputLocationCode').val();
            $('#locationCodeValue').val(value);
            // var sessionLoc = sessionStorage.getItem('parsingLocationCode');
            if (event.keyCode === 13) {
                event.preventDefault();
                doScanLocation(value);
            }
        });

        function scanLocation(id, product_code) {
            $('#modal-scan-lokasi').modal('show');
            $('.skuText').text(product_code);
            $('#id_list').val(id);
            // sessionStorage.setItem('product_code', product_code);

        }

        function doScanLocation(params) {
            $.ajax({
                url: "{{ url('warehouse/outbound/scanLokasi') }}/" + params,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data == null) {
                        error.play();
                        $('#inputLocationCode').val("");
                        $('#inputLocationCode').focus();
                        swal({
                            icon: "error",
                            text: "Location Not Found!"
                        });
                    } else {
                        $('#form-scan-lokasi').submit();
                    }
                },
                error: function(response) {
                    error.play();
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }

        $('#form-qty-outbound').on('submit', function(e) {
            e.preventDefault();
            $('.btn-qty-outbound').hide();
            $.ajax({
                url: "{{ route('outbound.validasi_qty_batch') }}",
                data: $('#form-qty-outbound').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'lebih_besar') {
                        error.play();
                        $('#modal-qty-outbound').modal('hide');
                        $('#qtyBatch').val("");
                        $('#inputPalletTag').val("");
                        swal({
                            icon: "error",
                            text: 'Quantity yang diinputkan melebihi batas yang ditentukan.'
                        });
                        $('.btn-qty-outbound').show();
                    } else {
                        swal({
                            icon: "success",
                            text: "Good Job!"
                        });
                        success.play();
                        generateListPickChecker();
                        $('#modal-qty-outbound').modal('hide');
                        $('#inputPalletTag').val("");
                        $('#inputLocationCode').val("");
                        $('.btn-qty-outbound').show();
                    }
                },
                error: function(error) {
                    $('.btn-qty-outbound').show();
                    console.log(error);
                }
            });
        });

        $('#form-scan-lokasi').on('submit', function(e) {
            e.preventDefault();
            $('.btn-scan-lokasi').hide();
            $.ajax({
                url: "{{ route('outbound-scan_lokasi') }}",
                data: $('#form-scan-lokasi').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'ok') {
                        $('#modal-scan-lokasi').modal('hide');
                        success.play();
                        generateListPickChecker();
                        $('#inputPalletTag').val("");
                        $('#inputLocationCode').val("");
                    } else {
                        $('#inputLocationCode').val("");
                        error.play();
                        swal({
                            icon: "error",
                            text: "Lokasi tidak sesuai dengan picking list.."
                        });
                    }
                },
                error: function(error) {
                    error.play();
                    console.log(error);
                }
            });
        });

        function updateETD() {
            var konfirmasi = confirm("Perubahan ETD akan merubah due date, apakah anda yakin?");
            if (konfirmasi == false) // here you need to use == instead of = and no semi-colon
            {
                return false;
            } else {
                var etd = $('#etd').val();
                var val = etd.replace(/[/]/g, '-');
                var outbound_id = $('#outbound_id').val();
                $('.btnETD').attr('disabled', true);
                $.ajax({
                    url: "{{ url('/warehouse/outbound/updateEtd') }}/" + val + "/" + outbound_id,
                    type: "GET",
                    dataType: 'json',
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(reponse) {
                        $("#loader").hide();
                        location.reload();
                    },
                    error: function(error) {
                        $("#loader").hide();
                        $('.btnETD').attr('disabled', true);
                    }
                });
            }
        }
        $(function() {
            var d = new Date();
            d.setDate(d.getDate());
            $('#etd').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);

            $('#order_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);

            $('#due_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);

            $('#awb_date').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);

            $('#send_date_doc').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);


            $('#ata').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });

            $('#loading_start').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });

            $('#loading_finish').bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY HH:mm'
            });
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('metd[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('metd[name="csrf-token"]').attr('content');

            load_data();

            function load_data() {
                link_id = $('.nav-tabs .active').attr('id');

                if (link_id == 'job-link') {
                    load_job();
                } else if (link_id == 'order-link') {
                    load_order();
                } else if (link_id == 'detail-link') {
                    load_detail();
                } else if (link_id == 'picking-link') {
                    load_picking();
                } else if (link_id == 'despatch-link') {
                    load_despatch();
                } else if (link_id == 'cancel-link') {
                    load_cancel();
                } else if (link_id == 'confirm-link') {
                    load_confirm();
                }
            }

            $('#job-link').on('click', function() {
                load_job();
            });

            $('#order-link').click(function(e) {
                e.preventDefault();
                load_order();
            });

            $('#detail-link').click(function(e) {
                e.preventDefault();
                load_detail();
            });

            $('#picking-link').click(function(e) {
                e.preventDefault();
                load_picking();
            });

            $('#cancel-link').click(function(e) {
                e.preventDefault();
                load_cancel();
            });

            $('#despatch-link').click(function(e) {
                e.preventDefault();
                load_despatch();
            });

            $('#confirm-link').click(function(e) {
                e.preventDefault();
                load_confirm();
            });

            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('outbound-job.store') }}",
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
                var data_id = $('#outbound_id').val();

                if (data_id == '') {
                    $('#form-job').trigger("reset");
                    return;
                }

                $.ajax({
                    url: "{{ route('outbound-job.edit') }}",
                    data: {
                        outbound_id: data_id
                    },
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        var job_date = "";
                        var etd = "";
                        var entry_date = "";
                        var allocated_date = "";
                        var confirmed_date = "";
                        var entryBy = "";
                        var allocatedBy = "";
                        var confirmatedBy = "";

                        if (data.job_date !== null) {
                            job_date = getFormatDate(data.job_date);
                        }

                        if (data.etd !== null) {
                            etd = getFormatDate(data.etd);
                        }

                        if (data.entry_date !== null) {
                            entry_date = getFormatDateTime(data.entry_date);
                        }

                        if (data.allocated_date !== null) {
                            allocated_date = getFormatDateTime(data.allocated_date);
                        }

                        if (data.confirmed_date !== null) {
                            confirmed_date = getFormatDateTime(data.confirmed_date);
                        }

                        if (data.user_id !== null) {
                            entryBy = data.user_id;
                        }

                        if (data.allocated_by !== null) {
                            allocatedBy = data.allocated_by;
                        }

                        if (data.confirmed_by !== null) {
                            confirmatedBy = data.confirmed_by;
                        }

                        $('#job_no').val(data.job_no);
                        $('#job_date').val(job_date);
                        $('#principal_id').val(data.principal_id);
                        $('#class_id').val(data.class_id);
                        $('#mode_id').val(data.mode_id);
                        $('#description').val(data.description);
                        // $('#reference_no').val(data.reference_no);
                        // $('#reference_other').val(data.reference_other);
                        $('#etd').val(etd);
                        $('#entry_date').val(entry_date);
                        $('#allocated_date').val(allocated_date);
                        $('#confirmed_date').val(confirmed_date);
                        $('.entryBy').text(entryBy);
                        $('.allocatedBy').text(allocatedBy);
                        $('.confirmatedBy').text(confirmatedBy);
                    }
                });
            }

            function load_order() {
                var dataId = $('#outbound_id').val();

                $('#order_table').DataTable().destroy();
                $('#order_table').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    destroy: true,
                    ajax: {
                        url: "{{ route('outbound-order.index') }}",
                        type: "GET",
                        data: {
                            outbound_id: dataId
                        }
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'customer_code',
                            name: 'customer_code'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'po_number',
                            name: 'po_number'
                        },
                        {
                            data: 'order_no',
                            name: 'order_no'
                        },
                        {
                            data: 'order_date',
                            name: 'order_date'
                        },
                        {
                            data: 'due_date',
                            name: 'due_date'
                        },
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            function load_detail() {
                var dataId = $('#outbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#detail_table').DataTable().destroy();
                    $('#detail_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-detail.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'order_no',
                                name: 'order_no'
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
                    $('#detail_table').DataTable().destroy();
                    $('#detail_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-detail.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'order_no',
                                name: 'order_no'
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

            $('#btn-add-order').click(function() {
                $('#order_id').val('');
                $('#outbound_order').val($('#outbound_id').val());
                $('#form-order').trigger("reset");
                $('#modal-title').html("Add New Order");
                getCustomer();
                $('#modal-order').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            if ($("#form-order").length > 0) {
                $("#form-order").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-order').serialize(),
                            url: "{{ route('outbound-order.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-order').trigger("reset");
                                    $('#modal-order').modal('hide');

                                    var oTable = $('#order_table').dataTable();
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

            $(document).on('click', '.delete-order', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('order')
                $('#modal-konfirmasi').modal('show');
            });

            function getCustomer() {
                var principal_id = $('#principal_id').val();

                $("#customer_id").html('');

                $.ajax({
                    url: "{{ route('customer.getCustomer') }}",
                    type: "GET",
                    data: {
                        principal_id: principal_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#customer_id').html('<option value="">.:Select:.</option>');
                        $.each(result.customer_list, function(key, value) {
                            $("#customer_id").append('<option value="' + value.id +
                                '">' + value
                                .customer_name + '</option>');
                        });
                    }
                });
            }

            $('body').on('click', '.edit-order', function() {
                var data_id = $(this).data('id');

                $.ajax({
                    data: {
                        "_token": CSRF_TOKEN,
                        "id": data_id
                    },
                    url: "{{ route('outbound-order.edit') }}",
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html("Edit");
                        $('#btn-save-order').val("Edit");
                        $('#modal-order').modal('show');

                        var order_date = "";
                        var due_date = "";

                        if (data.order_date !== null) {
                            order_date = getFormatDate(data.order_date);
                        }

                        if (data.due_date !== null) {
                            due_date = getFormatDate(data.due_date);
                        }

                        $('#order_id').val(data.id);
                        $('#outbound_order').val(data.outbound_id);
                        $('#customer_id').val(data.customer_id);
                        $('#customer_name').val(data.customer_name);
                        $('#po_number').val(data.po_number);
                        $('#order_no').val(data.order_no);
                        $('#order_date').val(order_date);
                        $('#due_date').val(due_date);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            $('#btn-add-detail').click(function() {
                $('#detail_id').val('');
                $('#outbound_detail').val($('#outbound_id').val());
                $('#form-detail').trigger("reset");
                $('#modal-title-detail').html("Add New Detail");
                $('#modal-detail').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#btn-import').click(function() {
                $('#job_id').val($('#outbound_id').val());
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
                            url: "{{ route('outbound-detail.import') }}",
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
                                    var oTable = $('#detail_table').dataTable();
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
                            url: "{{ route('stock.getStockProduct') }}",
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
                            document.getElementById("mqty").className = 'disabled form-control';
                            document.getElementById("bqty").className = 'disabled form-control';
                        } else if (ui.item.unit_level == 2) {
                            document.getElementById("mqty").className = 'disabled form-control';
                            document.getElementById("bqty").className = 'form-control';
                        } else {
                            document.getElementById("mqty").className = 'form-control';
                            document.getElementById("bqty").className = 'form-control';
                        }

                        $('#uppp').val(ui.item.uppp);
                        $('#muppp').val(ui.item.muppp);
                        $('#puom').val(ui.item.puom);
                        $('#muom').val(ui.item.muom);
                        $('#buom').val(ui.item.buom);
                        $('#product_name').val(ui.item.product_code + " - " + ui.item.product_name);
                        $('#product_code').val(ui.item.product_code);
                        $('#product_id').val(ui.item.product_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>Code : " + item.product_code + "<br> Name : " + item.product_name +
                            "</b><br>Stock Available 1st : " + item.pqty + ' ' + item.puom + ', 2nd : ' +
                            item
                            .mqty + ' ' + item.muom + ', 3rd : ' + item.bqty + ' ' + item.buom + "</div>")
                        .appendTo(ul);
                };

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
                        $('#customer_name').val(ui.item.customer_name);
                        $('#customer_id').val(ui.item.id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.customer_name + "</div>")
                        .appendTo(ul);
                };

            $("#store_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('customer.getStore') }}",
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
                        $('#store_id').val(ui.item.id);
                        $('#store_name').val(ui.item.store_name);
                        $('#address').val(ui.item.address);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.store_name + " (" + item.store_code + ")<br>" + item
                            .city_name +
                            "</div>")
                        .appendTo(ul);
                };

            $("#order_no_detail").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('outbound.getOrder') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                outbound_id: $('#outbound_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#order_id_detail').val(ui.item.order_id);
                        $('#customer_id_detail').val(ui.item.customer_id);
                        $('#customer_name_detail').val(ui.item.customer_name);
                        $('#order_no_detail').val(ui.item.order_no);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.customer_name + "<br> Order No : " + item.order_no +
                            "</b><br>Order Date : " + getFormatDate(item.order_date) + ", Due Date : " +
                            getFormatDate(item.due_date) + "</div>")
                        .appendTo(ul);
                };

            $("#site_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock.getStockSite') }}",
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
                        $('#site_id').val(ui.item.site_id);
                        $('#site_name').val(ui.item.site_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.site_name + "</b></div>")
                        .appendTo(ul);
                };

            $("#area_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('site.getAreaAuto') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                site_id: $('#site_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#site_id').val(ui.item.site_id);
                        $('#site_name').val(ui.item.site_name);
                        $('#area_id').val(ui.item.area_id);
                        $('#area_name').val(ui.item.area_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.site_name + "</b><br>" + item.area_name + "</div>")
                        .appendTo(ul);
                };

            $("#location_from").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock.getStockLocation') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                product_id: $('#product_id').val(),
                                site_id: $('#site_id').val(),
                                area_id: $('#area_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#site_id').val(ui.item.site_id);
                        $('#site_name').val(ui.item.site_name);
                        $('#area_id').val(ui.item.area_id);
                        $('#area_name').val(ui.item.area_name);
                        $('#location_from').val(ui.item.location_code);
                        $('#location_from_id').val(ui.item.location_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>Site : " + item.site_name + ", Area : " + item.area_name +
                            "</b><br>" +
                            item.location_code + "</div>")
                        .appendTo(ul);
                };

            $("#location_to").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock.getStockLocation') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                product_id: $('#product_id').val(),
                                site_id: $('#site_id').val(),
                                area_id: $('#area_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#site_id').val(ui.item.site_id);
                        $('#site_name').val(ui.item.site_name);
                        $('#area_id').val(ui.item.area_id);
                        $('#area_name').val(ui.item.area_name);
                        $('#location_to').val(ui.item.location_code);
                        $('#location_to_id').val(ui.item.location_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>Site : " + item.site_name + ", Area : " + item.area_name +
                            "</b><br>" +
                            item.location_code + "</div>")
                        .appendTo(ul);
                };

            $("#lot_no").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock.getStockBatch') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                product_id: $('#product_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#lot_no').val(ui.item.lot_no);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>Batch Number : " + item.lot_no + "</b></div>")
                        .appendTo(ul);
                };

            $('body').on('click', '.edit-detail', function() {
                var data_id = $(this).data('id');

                $('#form-detail').trigger("reset");

                $.ajax({
                    data: {
                        "_token": CSRF_TOKEN,
                        "id": data_id
                    },
                    url: "{{ route('outbound-detail.edit') }}",
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title-detail').html("Edit");
                        $('#btn-save-detail').val("Edit");
                        $('#modal-detail').modal('show');

                        if (data.unit_level == 1) {
                            document.getElementById("mqty").className =
                                'disabled form-control';
                            document.getElementById("bqty").className =
                                'disabled form-control';
                        } else if (data.unit_level == 2) {
                            document.getElementById("mqty").className =
                                'disabled form-control';
                            document.getElementById("bqty").className = 'form-control';
                        } else {
                            document.getElementById("mqty").className = 'form-control';
                            document.getElementById("bqty").className = 'form-control';
                        }

                        $('#detail_id').val(data.id);
                        $('#outbound_detail').val(data.outbound_id);
                        $('#customer_id_detail').val(data.customer_id);
                        $('#customer_name_detail').val(data.customer_name);
                        $('#order_id_detail').val(data.order_id);
                        $('#order_no_detail').val(data.order_no);
                        $('#product_id').val(data.product_id);
                        $('#product_code').val(data.product_code);
                        $('#product_name').val(data.product_code + " - " + data
                            .product_name);
                        $('#pqty').val(data.pqty);
                        $('#mqty').val(data.mqty);
                        $('#bqty').val(data.bqty);
                        $('#puom').val(data.puom);
                        $('#muom').val(data.muom);
                        $('#buom').val(data.buom);
                        $('#uppp').val(data.uppp);
                        $('#muppp').val(data.muppp);
                        $('#site_id').val(data.site_id);
                        $('#site_name').val(data.site_name);
                        $('#area_id').val(data.area_id);
                        $('#area_name').val(data.area_name);
                        $('#location_from').val(data.location_from);
                        $('#location_from_id').val(data.location_from_id);
                        $('#location_to').val(data.location_to);
                        $('#location_to_id').val(data.location_to_id);
                        $('#lot_no').val(data.lot_no);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            if ($("#form-detail").length > 0) {
                $("#form-detail").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-detail').serialize(),
                            url: "{{ route('outbound-detail.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-detail').trigger("reset");
                                    $('#modal-detail').modal('hide');

                                    var oTable = $('#detail_table').dataTable();
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

            $(document).on('click', '.delete-detail', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('detail')
                $('#modal-konfirmasi').modal('show');
            });

            function load_picking() {
                var dataId = $('#outbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#picking_table').DataTable().destroy();
                    $('#picking_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-picking.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
                            },
                            {
                                data: 'location_from',
                                name: 'location_from'
                            },
                            {
                                data: 'location_to',
                                name: 'location_to'
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
                    $('#picking_table').DataTable().destroy();
                    $('#picking_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-picking.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
                            },
                            {
                                data: 'location_from',
                                name: 'location_from'
                            },
                            {
                                data: 'location_to',
                                name: 'location_to'
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

            function load_cancel() {
                var dataId = $('#outbound_id').val();
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
                            url: "{{ route('outbound-cancel.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
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
                            url: "{{ route('outbound-cancel.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
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
                        ]
                    });
                }
            }

            function load_despatch() {
                var dataId = $('#outbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#despatch_table').DataTable().destroy();
                    $('#despatch_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-despatch.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'customer_name',
                                name: 'customer_name'
                            },
                            {
                                data: 'delivery_type',
                                name: 'delivery_type'
                            },
                            {
                                data: 'etd',
                                name: 'etd'
                            },
                            {
                                data: 'mode_name',
                                name: 'mode_name'
                            },
                            {
                                data: 'carrier_name',
                                name: 'carrier_name'
                            },
                            {
                                data: 'vessel_name',
                                name: 'vessel_name'
                            },
                            {
                                data: 'driver_name',
                                name: 'driver_name'
                            },
                            {
                                data: 'awb_no',
                                name: 'awb_no'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'mqty',
                                name: 'mqty'
                            },
                            {
                                data: 'bqty',
                                name: 'bqty'
                            },
                        ]
                    });
                } else {
                    $('#despatch_table').DataTable().destroy();
                    $('#despatch_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-despatch.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'customer_name',
                                name: 'customer_name'
                            },
                            {
                                data: 'delivery_type',
                                name: 'delivery_type'
                            },
                            {
                                data: 'etd',
                                name: 'etd'
                            },
                            {
                                data: 'mode_name',
                                name: 'mode_name'
                            },
                            {
                                data: 'carrier_name',
                                name: 'carrier_name'
                            },
                            {
                                data: 'vessel_name',
                                name: 'vessel_name'
                            },
                            {
                                data: 'driver_name',
                                name: 'driver_name'
                            },
                            {
                                data: 'awb_no',
                                name: 'awb_no'
                            },
                            {
                                data: 'pqty',
                                name: 'pqty'
                            },
                            {
                                data: 'expected_qty',
                                name: 'expected_qty'
                            },
                        ]
                    });
                }
            }

            const inputPrice = document.getElementById('price');

            inputPrice.addEventListener('keypress', e => {
                if (e.key === '.') e.preventDefault();
            });

            inputPrice.addEventListener('paste', e => {
                const pasted = (e.clipboardData || window.clipboardData).getData('text');
                if (pasted.includes('.')) e.preventDefault();
            });

            $('body').on('click', '.edit-despatch', function() {
                var data_id = $(this).data('id');

                $('#form-despatch').trigger("reset");

                $.ajax({
                    data: {
                        "_token": CSRF_TOKEN,
                        "id": data_id
                    },
                    url: "{{ route('outbound-despatch.edit') }}",
                    type: 'get',
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title-despatch').html("Edit");
                        $('#btn-save-despatch').val("Edit");
                        $('#modal-despatch').modal('show');

                        var etd = "";
                        var awb_date = "";
                        var send_date_doc = "";

                        if (data.etd !== null) {
                            etd = getFormatDate(data.etd);
                        }

                        if (data.awb_date !== null) {
                            awb_date = getFormatDate(data.awb_date);
                        }

                        if (data.send_date_doc !== null) {
                            send_date_doc = getFormatDate(data.send_date_doc);
                        }

                        var address = "";

                        if (data.address1 !== null) {
                            address += data.address1;
                        }

                        if (data.address2 !== null) {
                            address += ' ' + data.address2;
                        }

                        if (data.address3 !== null) {
                            address += ' ' + data.address3;
                        }

                        if (data.address4 !== null) {
                            address += ' ' + data.address4;
                        }

                        $('#despatch_id').val(data.id);
                        $('#do_no').val(data.do_no);
                        $('#ref_no').val(data.reference_no);
                        $('#vehicle_no').val(data.vehicle_no);
                        $('#seal_no').val(data.seal_no);
                        $('#size_id').val(data.size_id);
                        $('#customer_id_despatch').val(data.customer_id);
                        $('#customer_name_despatch').val(data.customer_name);
                        $('#mode_id_despatch').val(data.mode_id);
                        $('#vessel_name').val(data.vessel_name);
                        $('#carrier_name').val(data.carrier_name);
                        $('#driver_name').val(data.driver_name);
                        $('#phone').val(data.phone);
                        $('#price').val(data.price);
                        $('#container_no').val(data.container_no);
                        $('#delivery_type').val(data.delivery_type);
                        $('#awb_no').val(data.awb_no);
                        $('#etd_despatch').val(etd);
                        $('#awb_date').val(awb_date);
                        $('#send_date_doc').val(send_date_doc);
                        $('#pqty_despatch').val(data.pqty);
                        $('#mqty_despatch').val(data.mqty);
                        $('#bqty_despatch').val(data.bqty);
                        $('#store_id').val(data.store_id);
                        $('#store_name').val(data.store_name);
                        $('#address').val(address);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });

            $('body').on('click', '.print-despatch', function() {
                var data_id = $(this).data('id');

                window.open("{{ url('/warehouse/outbound/report/despatch/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });

            $('body').on('click', '.print-loading-list', function() {
                var data_id = $('#outbound_id').val();

                window.open("{{ url('/warehouse/outbound/report/loading_list/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });

            $('body').on('click', '#pallet-picking-report-print', function() {
                var data_id = $('#outbound_id').val();
                window.open("{{ url('/warehouse/outbound/palletPickingReport/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });
            $('body').on('click', '#pallet-picking-report-print-excel', function() {
                var data_id = $('#outbound_id').val();
                location.href = "{{ url('/warehouse/outbound/palletPickingReportExcel/') }}" + "/" +
                    data_id
            });

            if ($("#form-despatch").length > 0) {
                $("#form-despatch").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-despatch').serialize(),
                            url: "{{ route('outbound-despatch.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-despatch').trigger("reset");
                                    $('#modal-despatch').modal('hide');

                                    var oTable = $('#despatch_table').dataTable();
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

            function load_confirm() {
                var dataId = $('#outbound_id').val();
                var multi_level = $('#multi_level').val();

                if (multi_level == "Yes") {
                    $('#confirm_table').DataTable().destroy();
                    $('#confirm_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-confirm.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'location_code',
                                name: 'location_code'
                            },
                            {
                                data: 'null',
                                name: 'null',
                                searchable: false,
                                orderable: false,
                                render: function(data, type, row, meta) {
                                    return `<label>${row.location_code}</label>
                                        <input type="text" name="location_code[]" value="${row.location_code}>`
                                },
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
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
                    $('#confirm_table').DataTable().destroy();
                    $('#confirm_table').DataTable({
                        "dom": '<"wrapper"flipt>',
                        processing: true,
                        serverSide: true,
                        paging: false,
                        destroy: true,
                        ajax: {
                            url: "{{ route('outbound-confirm.index') }}",
                            type: "GET",
                            data: {
                                outbound_id: dataId
                            }
                        },
                        columns: [{
                                data: 'check',
                                name: 'check',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'location_code',
                                name: 'location_code'
                            },
                            {
                                data: 'null',
                                name: 'null',
                                searchable: false,
                                orderable: false,
                                render: function(data, type, row, meta) {
                                    return `<label>${row.location_code}</label>
                                        <input type="text" name="location_code[]" value="${row.location_code}>`
                                },
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
                                data: 'site_name',
                                name: 'site_name'
                            },
                            {
                                data: 'area_name',
                                name: 'area_name'
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

            $('#btn-delete').click(function() {
                var action = $('#action-delete').val();
                var requestUrl = "";
                var requestData = {};

                if (action == 'order') {
                    requestUrl = "{{ route('outbound-order.destroy') }}";
                    requestData = {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId
                    };
                } else if (action == 'detail') {
                    requestUrl = "{{ route('outbound-detail.destroy') }}";
                    requestData = {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId
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
                            if (action == 'order') {
                                oTable = $('#order_table').dataTable();
                            } else if (action == 'detail') {
                                oTable = $('#detail_table').dataTable();
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

            $('#picking_table').on('click', '.picking-check', function() {
                if (this.checked == true) {
                    var table = $('#picking_table').DataTable();
                    data = table.row(this.closest('tr')).data()['id'];

                    $('.picking-check-all').prop('checked', true);
                } else {
                    $('.picking-check-all').prop('checked', false);
                }
            });

            $('#picking_table').on('click', '.picking-check-all', function() {
                $('.picking-check').prop('checked', this.checked);
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

            $('body').on('click', '#picking-print', function() {
                var data_id = $('#outbound_id').val();

                window.open("{{ url('/warehouse/outbound/report/picking/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });

            $('body').on('click', '#picking-report-print', function() {
                var data_id = $('#outbound_id').val();

                window.open("{{ url('/warehouse/outbound/report/picking_report/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });

            $('body').on('click', '#confirm-print', function() {
                var data_id = $('#outbound_id').val();

                window.open("{{ url('/warehouse/outbound/report/confirm/') }}" + "/" + data_id,
                    'OutboundReport', 'width=800,height=600')
            });
        });

        function processPicking() {
            var oTable = $('#picking_table').dataTable();
            $('#form-picking').trigger("reset");

            $('.hidden-picking').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-picking').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-picking')
                        .val(this.value)
                    );
                }
            });

            $.ajax({
                data: $('#form-picking').serialize(),
                url: "{{ route('outbound-picking.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    if ($.isEmptyObject(data.error)) {
                        $('#form-picking').trigger("reset");
                        var oTable = $('#picking_table').dataTable();
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
                url: "{{ route('outbound-cancel.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    if ($.isEmptyObject(data.error)) {
                        $('#form-cancel').trigger("reset");
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
            var ata = $("#ata").val();
            var loading_start = $("#loading_start").val();
            var loading_finish = $("#loading_finish").val();

            if (ata == "" || loading_start == "" || loading_finish == "") {
                swal({
                    icon: "error",
                    text: "Loading date is required."
                });
                return;
            }

            var oTable = $('#confirm_table').dataTable();
            // $('#form-confirm').trigger("reset");

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
                url: "{{ route('outbound-confirm.submit') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    if ($.isEmptyObject(data.error)) {
                        $('#form-confirm').trigger("reset");
                        var oTable = $('#confirm_table').dataTable();
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

        function downloadExcel(link) {
            var query = {
                outbound_id: $('#outbound_id').val(),
            }

            var url = "{{ URL::to('warehouse/outbound/') }}/" + link + "?" + $.param(query)

            window.location = url;
        }
    </script>
@endpush
