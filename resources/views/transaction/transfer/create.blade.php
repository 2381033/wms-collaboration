@extends('layouts.main')

@section('title')
    Stock Transfer
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Transfer</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Stock Transfer</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="job_no">Job Number</label>
                        <input type="text" id="job_no" name="job_no"
                            @isset($job_view->job_no) value="{{ $job_view->job_no }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="job_date">Job Date</label>
                        <input type="text" id="job_date" name="job_date"
                            @isset($job_view->job_date) value="{{ \Carbon\Carbon::parse($job_view->job_date)->format('d-m-Y') }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">
                    <div class="btn-group">
                        <a href="{{ url('/inventory/stock-transfer/create/0') }}" class="btn btn-primary btn-sm"><i
                                class="fas fa-plus"></i> <span>Add New Job</span></a>
                        &nbsp;&nbsp;
                        <a href="#" class="btn btn-info btn-sm"><i class="fas fa-folder-open"></i> <span>Open
                                Job</span></a>
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab"
                                aria-controls="home" aria-selected="true">
                                <i class="fas fa-box"></i> Job Information</a>
                        </li>
                        @if (isset($job_view->id) && !empty($job_view->id))
                            <li class="nav-item">
                                <a class="nav-link" id="detail-link" data-toggle="tab" href="#detail-tab" role="tab"
                                    aria-controls="detail" aria-selected="false">
                                    <i class="fas fa-box"></i> Entry Detail</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="process-link" data-toggle="tab" href="#process-tab" role="tab"
                                    aria-controls="process" aria-selected="false">
                                    <i class="fas fa-box"></i> Pick & Put Away</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cancel-link" data-toggle="tab" href="#cancel-tab" role="tab"
                                    aria-controls="cancel" aria-selected="false">
                                    <i class="fas fa-box"></i> Cancel</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="confirm-link" data-toggle="tab" href="#confirm-tab" role="tab"
                                    aria-controls="confirm" aria-selected="false">
                                    <i class="fas fa-box"></i> Confirmation</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content" id="transferTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel" aria-labelledby="home-tab5">
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="transfer_id" name="transfer_id"
                                    @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Branch Name</label>
                                                <select class="custom-select" id="branch_id" name="branch_id"
                                                    @isset($job_view->id) disabled @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (isset($job_view->branch_id) && !empty($job_view->branch_id)) @if ($item->id == $job_view->branch_id) selected @endif
                                                            @endif>{{ $item->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Principal Name</label>
                                                <select class="custom-select" id="principal_id" name="principal_id"
                                                    @isset($job_view->id) disabled @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->principal as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (isset($job_view->principal_id) && !empty($job_view->principal_id)) @if ($item->id == $job_view->principal_id) selected @endif
                                                            @endif>{{ $item->principal_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description"
                                                    name="description" class="form-control"
                                                    @isset($job_view->description) value="{{ $job_view->description }}" @endisset
                                                    @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">
                                                @if (!isset($job_view->id) && empty($job_view->id))
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
                        <div class="tab-pane fade show" id="detail-tab" role="tabpanel" aria-labelledby="detail-tab5">
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                                @if ($job_view->confirmed_flag == 'No')
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="btn-add-detail"><i class="fas fa-plus"></i>
                                                        <span>Add Entry</span>
                                                    </button>
                                                    <a href="#modal-excel" data-toggle="modal" class="btn btn-success btn-sm"
                                                        id="btn-upload-excel"><i class="fas fa-file-excel"></i>
                                                            <span>Upload Excel</span>
                                                    </a>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    id="btn-add-detail"><i class="fas fa-plus"></i> <span>Add
                                                        Entry</span></button>
                                            @endif
                                            <a id="entry-print"
                                                @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                class="btn btn-info btn-sm"><i class="fas fa-print"></i>
                                                <span>Report</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="detail_table" class="table table-striped table-bordered table-sm"
                                                style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th rowspan="2">Action</th>
                                                        <th rowspan="2">Pallet ID</th>
                                                        <th rowspan="2">Product Name</th>
                                                        <th rowspan="2">Batch No</th>
                                                        <th rowspan="2">Site Name</th>
                                                        <th rowspan="2">Area Name</th>
                                                        <th rowspan="2">Location</th>
                                                        <th colspan="6">Quantity</th>
                                                        <th colspan="3">Destination</th>
                                                    </tr>
                                                    <tr>
                                                        <th>1st</th>
                                                        <th>Unit</th>
                                                        <th>2nd</th>
                                                        <th>Unit</th>
                                                        <th>3rd</th>
                                                        <th>Unit</th>
                                                        <th>Site Name</th>
                                                        <th>Area Name</th>
                                                        <th>Location</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="process-tab" role="tabpanel" aria-labelledby="process-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processProcess()" id="btn-process"><i
                                                class="fas fa-play"></i> <span>Proccess</span></a>
                                        <a id="pick-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                            class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Pick
                                                Report</span></a>
                                        <a id="put-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                            class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Put-away
                                                Report</span></a>
                                        <a id="pickput-print"
                                            @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                            class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Combined Pick
                                                and Put-away Report</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <form id="form-process" name="form-process" method="post">
                                        @csrf
                                    </form>
                                    <div class="table-responsive">
                                        <table id="process_table" class="table table-striped table-bordered table-sm"
                                            style="width:100%">
                                            <thead class="text-center">
                                                <tr>
                                                    <th rowspan="2">
                                                        <input type='checkbox' required="required"
                                                            class="process-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Quantity</th>
                                                    <th colspan="3">Destination</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>
                                                    <th>Site Name</th>
                                                    <th>Area Name</th>
                                                    <th>Location</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="cancel-tab" role="tabpanel" aria-labelledby="cancel-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processCancel()"
                                            id="btn-process-cancel"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <form id="form-cancel" name="form-cancel" method="post">
                                        @csrf
                                    </form>
                                    <div class="table-responsive">
                                        <table id="cancel_table" class="table table-striped table-bordered table-sm"
                                            style="width:100%">
                                            <thead class="text-center">
                                                <tr>
                                                    <th rowspan="2">
                                                        <input type='checkbox' required="required"
                                                            class="cancel-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Quantity</th>
                                                    <th colspan="3">Destination</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>
                                                    <th>Site Name</th>
                                                    <th>Area Name</th>
                                                    <th>Location</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="confirm-tab" role="tabpanel" aria-labelledby="confirm-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processConfirm()"
                                            id="btn-process-confirm"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <form id="form-confirm" name="form-confirm" method="post">
                                        @csrf
                                        <input type="hidden" name="product_status" value=""
                                            class="productStatusValue">
                                    </form>
                                    <div class="table-responsive">
                                        <table id="confirm_table" class="table table-striped table-bordered table-sm"
                                            style="width:100%">
                                            <thead class="text-center">
                                                <tr>
                                                    <th rowspan="2">
                                                        <input type='checkbox' required="required"
                                                            class="confirm-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Quantity</th>
                                                    <th colspan="3">Destination</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>
                                                    <th>Site Name</th>
                                                    <th>Area Name</th>
                                                    <th>Location</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-filter">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Search</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-filter" name="form-filter" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" id="f_product_code" name="f_product_code" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="hidden" id="f_product_id" name="f_product_id">
                                    <input type="text" id="f_product_name" name="f_product_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Site Name</label>
                                    <input type="hidden" id="f_site_id" name="f_site_id">
                                    <input type="text" id="f_site_name" name="f_site_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Area Name</label>
                                    <input type="hidden" id="f_area_id" name="f_area_id">
                                    <input type="text" id="f_area_name" name="f_area_name" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Location From</label>
                                    <input type="text" id="f_location_from" name="f_location_from"
                                        class="form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Location To</label>
                                    <input type="text" id="f_location_to" name="f_location_to" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stock Status</label>
                                    <select name="f_status" id="f_status" class="form-control">
                                        <option value="N" selected>Not Full</option>
                                        <option value="F">Full</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <a href="#" class="btn btn-success btn-sm" onclick="load_stock()"><i
                                class="fas fa-save"></i> <span>Search</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-excel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Excel</h5>
                    @if (isset($job_view->id))
                        <a href="{{url('inventory/stock-transfer/downloadTemplate/'. $job_view->id)}}" class="btn btn-dark btn-md"><i class="fas fa-download"></i> Download Template</a>
                    @endif
                </div>
                <form id="form-upload" action="{{url('inventory/stock-transfer/upload')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                @if (isset($job_view->id))
                                    <input type="hidden" name="job_id" value="{{$job_view->id}}">
                                @endif
                                <div class="form-group">
                                    <label>File Excel</label>
                                    <input type="file" id="" name="excel" required class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span>
                        </button>
                        <button type="submit" class="btn btn-success btn-sm" id="btnImport">
                            <i class="fas fa-save"></i> <span>Upload</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-stock">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="stock_table" class="table table-striped table-bordered table-sm"
                                    width="100%">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="2">Action</th>
                                            <th rowspan="2">Pallet ID</th>
                                            <th rowspan="2">SKU Code</th>
                                            <th rowspan="2">SKU Name</th>
                                            <th rowspan="2">Batch No</th>
                                            <th rowspan="2">Site Name</th>
                                            <th rowspan="2">Area Name</th>
                                            <th rowspan="2">Location</th>
                                            <th colspan="6">Quantity Stock</th>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-detail" name="form-detail" method="post">
                    <input type="hidden" id="transfer_detail" name="transfer_detail"
                        @if (isset($job_view->id) && !empty($job_view->id)) value="{{ $job_view->id }}" @endif>
                    <input type="hidden" id="serial_id" name="serial_id">
                    <input type="hidden" id="detail_id" name="detail_id">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Principal Name</label>
                                    <input type="text" id="principal_name" name="principal_name" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" id="product_name" name="product_name" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Batch No</label>
                                    <input type="text" id="lot_no" name="lot_no" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mfg Date</label>
                                    <input type="text" id="mfg_date" name="mfg_date" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Exp Date</label>
                                    <input type="text" id="exp_date" name="exp_date" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Site Name</label>
                                    <input type="text" id="site_name" name="site_name" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Area Name</label>
                                    <input type="text" id="area_name" name="area_name" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" id="location_code" name="location_code" class="form-control"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>1st Qty</label>
                                    <input type="hidden" id="uppp" name="uppp">
                                    <input type="hidden" id="muppp" name="muppp">
                                    <input type="text" autocomplete="off" id="pqty" name="pqty"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="puom" name="puom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>2nd Qty</label>
                                    <input type="text" autocomplete="off" id="mqty" name="mqty"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="muom" name="muom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>3rd Qty</label>
                                    <input type="text" autocomplete="off" id="bqty" name="bqty"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="buom" name="buom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>1st Qty</label>
                                    <input type="text" autocomplete="off" id="actual_pqty" name="actual_pqty"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="actual_puom" name="actual_puom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>2nd Qty</label>
                                    <input type="text" autocomplete="off" id="actual_mqty" name="actual_mqty"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="actual_muom" name="actual_muom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>3rd Qty</label>
                                    <input type="text" autocomplete="off" id="actual_bqty" name="actual_bqty"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Unit</label>
                                    <input type="text" autocomplete="off" id="actual_buom" name="actual_buom"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Destination Site</label>
                                    <select name="dest_site_id" id="dest_site_id" class="custom-select">
                                        <option value="">.:Select:.</option>
                                        @foreach (Auth::user()->site as $item)
                                            <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Destination Area</label>
                                    <select name="dest_area_id" id="dest_area_id" class="custom-select">
                                        <option value="">.:Select:.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Destination Location</label>
                                    <input type="hidden" id="dest_location_id" name="dest_location_id">
                                    <input type="text" id="dest_location_code" name="dest_location_code"
                                        class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Product Status</label>
                                    <select name="product_status" required onchange="setupStatus(this.value)"
                                        id="product_status" class="custom-select">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-success btn-sm" id="btn-save-detail"><i
                                class="fas fa-save"></i> <span>Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setupStatus(value) {
            sessionStorage.setItem('status', value);
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $("meta[name='csrf-token']").attr("content");

            $("#detail-link").click(function(e) {
                e.preventDefault();
                load_detail();
            });

            $("#process-link").click(function(e) {
                e.preventDefault();
                load_process();
            });

            $("#cancel-link").click(function(e) {
                e.preventDefault();
                load_cancel();
            });

            $("#confirm-link").click(function(e) {
                e.preventDefault();
                load_confirm();
            });

            load_data();

            function load_data() {
                link_id = $(".nav-tabs .active").attr("id");
                if (link_id == "detail-link") {
                    load_detail();
                } else if (link_id == "process-link") {
                    load_process();
                } else if (link_id == "cancel-link") {
                    load_cancel();
                } else if (link_id == "confirm-link") {
                    load_confirm();
                }
            }

            function load_detail() {
                $("#detail_table").DataTable().destroy();
                $("#detail_table").DataTable({
                    "dom": "<'toolbar'>frtip",
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transfer-detail.index') }}",
                        type: "GET",
                        data: {
                            transfer_id: $("#transfer_id").val()
                        }
                    },
                    columns: [{
                            data: "action",
                            name: "action"
                        },
                        {
                            data: "serial_no",
                            name: "serial_no"
                        },
                        {
                            data: "product_name",
                            name: "product_name"
                        },
                        {
                            data: "lot_no",
                            name: "lot_no"
                        },
                        {
                            data: "site_name",
                            name: "site_name"
                        },
                        {
                            data: "area_name",
                            name: "area_name"
                        },
                        {
                            data: "location_code",
                            name: "location_code"
                        },
                        {
                            data: "actual_pqty",
                            name: "actual_pqty"
                        },
                        {
                            data: "puom",
                            name: "puom"
                        },
                        {
                            data: "actual_mqty",
                            name: "actual_mqty"
                        },
                        {
                            data: "muom",
                            name: "muom"
                        },
                        {
                            data: "actual_bqty",
                            name: "actual_bqty"
                        },
                        {
                            data: "buom",
                            name: "buom"
                        },
                        {
                            data: "dest_site_name",
                            name: "dest_site_name"
                        },
                        {
                            data: "dest_area_name",
                            name: "dest_area_name"
                        },
                        {
                            data: "dest_location_code",
                            name: "dest_location_code"
                        },
                    ],
                    order: [
                        [0, "asc"]
                    ]
                });
            }

            function load_process() {
                $("#process_table").DataTable().destroy();
                $("#process_table").DataTable({
                    "dom": "<'toolbar'>frtip",
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transfer-process.index') }}",
                        type: "GET",
                        data: {
                            transfer_id: $("#transfer_id").val()
                        }
                    },
                    columns: [{
                            data: "check",
                            name: "check",
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: "product_name",
                            name: "product_name"
                        },
                        {
                            data: "lot_no",
                            name: "lot_no"
                        },
                        {
                            data: "site_name",
                            name: "site_name"
                        },
                        {
                            data: "area_name",
                            name: "area_name"
                        },
                        {
                            data: "location_code",
                            name: "location_code"
                        },
                        {
                            data: "actual_pqty",
                            name: "actual_pqty"
                        },
                        {
                            data: "puom",
                            name: "puom"
                        },
                        {
                            data: "actual_mqty",
                            name: "actual_mqty"
                        },
                        {
                            data: "muom",
                            name: "muom"
                        },
                        {
                            data: "actual_bqty",
                            name: "actual_bqty"
                        },
                        {
                            data: "buom",
                            name: "buom"
                        },
                        {
                            data: "dest_site_name",
                            name: "dest_site_name"
                        },
                        {
                            data: "dest_area_name",
                            name: "dest_area_name"
                        },
                        {
                            data: "dest_location_code",
                            name: "dest_location_code"
                        },
                    ],
                    order: [
                        [0, "asc"]
                    ]
                });
            }

            function load_cancel() {
                $("#cancel_table").DataTable().destroy();
                $("#cancel_table").DataTable({
                    "dom": "<'toolbar'>frtip",
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transfer-cancel.index') }}",
                        type: "GET",
                        data: {
                            transfer_id: $("#transfer_id").val()
                        }
                    },
                    columns: [{
                            data: "check",
                            name: "check",
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: "product_name",
                            name: "product_name"
                        },
                        {
                            data: "lot_no",
                            name: "lot_no"
                        },
                        {
                            data: "site_name",
                            name: "site_name"
                        },
                        {
                            data: "area_name",
                            name: "area_name"
                        },
                        {
                            data: "location_code",
                            name: "location_code"
                        },
                        {
                            data: "actual_pqty",
                            name: "actual_pqty"
                        },
                        {
                            data: "puom",
                            name: "puom"
                        },
                        {
                            data: "actual_mqty",
                            name: "actual_mqty"
                        },
                        {
                            data: "muom",
                            name: "muom"
                        },
                        {
                            data: "actual_bqty",
                            name: "actual_bqty"
                        },
                        {
                            data: "buom",
                            name: "buom"
                        },
                        {
                            data: "dest_site_name",
                            name: "dest_site_name"
                        },
                        {
                            data: "dest_area_name",
                            name: "dest_area_name"
                        },
                        {
                            data: "dest_location_code",
                            name: "dest_location_code"
                        },
                    ],
                    order: [
                        [0, "asc"]
                    ]
                });
            }

            function load_confirm() {
                $("#confirm_table").DataTable().destroy();
                $("#confirm_table").DataTable({
                    "dom": "<'toolbar'>frtip",
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transfer-confirm.index') }}",
                        type: "GET",
                        data: {
                            transfer_id: $("#transfer_id").val()
                        }
                    },
                    columns: [{
                            data: "check",
                            name: "check",
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: "product_name",
                            name: "product_name"
                        },
                        {
                            data: "lot_no",
                            name: "lot_no"
                        },
                        {
                            data: "site_name",
                            name: "site_name"
                        },
                        {
                            data: "area_name",
                            name: "area_name"
                        },
                        {
                            data: "location_code",
                            name: "location_code"
                        },
                        {
                            data: "actual_pqty",
                            name: "actual_pqty"
                        },
                        {
                            data: "puom",
                            name: "puom"
                        },
                        {
                            data: "actual_mqty",
                            name: "actual_mqty"
                        },
                        {
                            data: "muom",
                            name: "muom"
                        },
                        {
                            data: "actual_bqty",
                            name: "actual_bqty"
                        },
                        {
                            data: "buom",
                            name: "buom"
                        },
                        {
                            data: "dest_site_name",
                            name: "dest_site_name"
                        },
                        {
                            data: "dest_area_name",
                            name: "dest_area_name"
                        },
                        {
                            data: "dest_location_code",
                            name: "dest_location_code"
                        },
                    ],
                    order: [
                        [0, "asc"]
                    ]
                });
            }

            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        var actionType = $("#btn-save-job").val();
                        $("#btn-save-job").html("Sending..");

                        $.ajax({
                            data: $("#form-job").serialize(),
                            url: "{{ route('transfer-job.store') }}",
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });

                                    window.open(data.success, "_top");
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + "</br>";
                                    }
                                    pesan += "</div>";

                                    const wrapper = document.createElement("div");
                                    wrapper.innerHTML = pesan;
                                    swal({
                                        icon: "error",
                                        content: wrapper
                                    });
                                    $("#btn-save-job").html("Save");
                                }
                            },
                            error: function(data) {
                                console.log("Error:", data);
                                $("#btn-save-job").html("Save");
                            }
                        });
                    }
                })
            }

            $("#btn-add-detail").click(function() {
                $("#f_product_id").val("");
                $("#f_product_code").val("");
                $("#f_product_name").val("");
                $("#f_site_id").val("");
                $("#f_site_name").val("");
                $("#f_area_id").val("");
                $("#f_area_name").val("");
                $("#f_location_from").val("");
                $("#f_location_to").val("");
                $("#f_status").val("N");

                $("#form-detail").trigger("reset");
                $("#modal-filter").modal({
                    backdrop: "static",
                    keyboard: false,
                    show: true
                });
            });

            $("#f_product_name").autocomplete({
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
                                principal_id: $("#principal_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#f_product_name").val(ui.item.product_name);
                        $("#f_product_code").val(ui.item.product_code);
                        $("#f_product_id").val(ui.item.product_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.product_code + "</b><br>" + item.product_name +
                            "<br>Stock 1st : " + item.pqty + " " + item.puom + ", 2nd : " + item.mqty + " " + item
                            .muom + ", 3rd : " + item.bqty + " " + item.buom + "</div>")
                        .appendTo(ul);
                };

            $("#f_site_name").autocomplete({
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
                                principal_id: $("#principal_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#f_site_id").val(ui.item.site_id);
                        $("#f_site_name").val(ui.item.site_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.site_name + "</b></div>")
                        .appendTo(ul);
                };

            $("#f_area_name").autocomplete({
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
                                site_id: $("#f_site_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#f_site_id").val(ui.item.site_id);
                        $("#f_site_name").val(ui.item.site_name);
                        $("#f_area_id").val(ui.item.area_id);
                        $("#f_area_name").val(ui.item.area_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div><b>" + item.site_name + "</b><br>" + item.area_name + "</div>")
                        .appendTo(ul);
                };

            $("#f_location_from").autocomplete({
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
                                principal_id: $("#principal_id").val(),
                                product_id: $("#f_product_id").val(),
                                site_id: $("#f_site_id").val(),
                                area_id: $("#f_area_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#sf_ite_id").val(ui.item.site_id);
                        $("#f_site_name").val(ui.item.site_name);
                        $("#f_area_id").val(ui.item.area_id);
                        $("#f_area_name").val(ui.item.area_name);
                        $("#f_location_from").val(ui.item.location_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Site : " + item.site_name + "<br>Area : " + item.area_name + "<br>" + item
                            .location_code + "</div>")
                        .appendTo(ul);
                };

            $("#f_location_to").autocomplete({
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
                                principal_id: $("#principal_id").val(),
                                product_id: $("#f_product_id").val(),
                                site_id: $("#f_site_id").val(),
                                area_id: $("#f_area_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#f_site_id").val(ui.item.site_id);
                        $("#f_site_name").val(ui.item.site_name);
                        $("#f_area_id").val(ui.item.area_id);
                        $("#f_area_name").val(ui.item.area_name);
                        $("#f_location_to").val(ui.item.location_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Site : " + item.site_name + "<br>Area : " + item.area_name + "<br>" + item
                            .location_code + "</div>")
                        .appendTo(ul);
                };

            $("body").on("click", ".edit-stock", function() {
                var data_id = $(this).data("id");

                $.ajax({
                    url: "{{ route('transfer-detail.edit') }}",
                    dataType: "json",
                    method: "post",
                    data: {
                        _token: CSRF_TOKEN,
                        id: data_id
                    },
                    success: function(data) {
                        $("#modal-stock").modal("hide");

                        $("#modal-detail").modal({
                            backdrop: "static",
                            keyboard: false,
                            show: true
                        });

                        var mfg_date = "";
                        var exp_date = "";

                        if (data.mfg_date !== null) {
                            mfg_date = getFormatDate(data.mfg_date);
                        }

                        if (data.exp_date !== null) {
                            exp_date = getFormatDate(data.exp_date);
                        }
                        $('#product_status').html('')
                        $('#product_status').append(`
                                        <option value="" disabled selected>SILAHKAN PILIH</option>
                                        <option value="G">GOOD</option>
                                        <option value="B">BAD</option>
                                        `)

                        if (data.unit_level == 1) {
                            document.getElementById("actual_mqty").className =
                                'disabled form-control';
                            document.getElementById("actual_bqty").className =
                                'disabled form-control';
                        } else if (data.unit_level == 2) {
                            document.getElementById("actual_mqty").className =
                                'disabled form-control';
                            document.getElementById("actual_bqty").className = 'form-control';
                        } else {
                            document.getElementById("actual_mqty").className = 'form-control';
                            document.getElementById("actual_bqty").className = 'form-control';
                        }

                        $("#serial_id").val(data.id);
                        $("#detail_id").val("");
                        $("#principal_name").val(data.principal_name);
                        $("#product_name").val(data.product_name);
                        $("#lot_no").val(data.lot_no);
                        $("#mfg_date").val(mfg_date);
                        $("#exp_date").val(exp_date);
                        $("#pqty").val(data.pqty);
                        $("#mqty").val(data.mqty);
                        $("#bqty").val(data.bqty);
                        $("#uppp").val(data.uppp);
                        $("#muppp").val(data.muppp);
                        $("#puom").val(data.puom);
                        $("#muom").val(data.muom);
                        $("#buom").val(data.buom);
                        $("#actual_pqty").val(0);
                        $("#actual_mqty").val(0);
                        $("#actual_bqty").val(0);
                        $("#site_name").val(data.site_name);
                        $("#area_name").val(data.area_name);
                        $("#location_code").val(data.location_code);
                        $("#actual_puom").val(data.puom);
                        $("#actual_muom").val(data.muom);
                        $("#actual_buom").val(data.buom);
                    }
                });
            });

            $("#dest_site_id").on("change", function() {
                var site_id = this.value;
                $("#dest_area_id").html("");
                $.ajax({
                    url: "{{ route('site.getAreaList') }}",
                    type: "GET",
                    data: {
                        site_id: site_id,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(result) {
                        $("#dest_area_id").html("<option value=''>.:Select:.</option>");
                        $.each(result.area_list, function(key, value) {
                            $("#dest_area_id").append("<option value='" + value.id +
                                "'>" + value.area_name + "</option>");
                        });
                    }
                });
            });

            $("#dest_location_code").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('site.getLocationAuto') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                site_id: $("#dest_site_id").val(),
                                area_id: $("#dest_area_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $("#dest_location_id").val(ui.item.location_id);
                        $("#dest_location_code").val(ui.item.location_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.location_code + "</div>")
                        .appendTo(ul);
                };

            if ($("#form-detail").length > 0) {
                $("#form-detail").validate({
                    submitHandler: function(form) {
                        var actionType = $("#btn-save-detail").val();
                        $("#btn-save-detail").html("Sending..");

                        $.ajax({
                            data: $("#form-detail").serialize(),
                            url: "{{ route('transfer-detail.store') }}",
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $("#form-detail").trigger("reset");
                                    $("#modal-detail").modal("hide");
                                    $("#btn-save-detail").html("Save");

                                    var oTable = $("#detail_table").dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + "</br>";
                                    }
                                    pesan += "</div>";

                                    const wrapper = document.createElement("div");
                                    wrapper.innerHTML = pesan;
                                    swal({
                                        icon: "error",
                                        content: wrapper
                                    });
                                    $("#btn-save-detail").html("Save");
                                }
                            },
                            error: function(data) {
                                console.log(data)

                                $("#btn-save-detail").html("Save");
                            }
                        });
                    }
                })
            }

            $(document).on("click", ".delete-detail", function() {
                dataId = $(this).attr("id");
                $("#modal-konfirmasi").modal("show");
            });

            $("#btn-delete").click(function() {
                $.ajax({
                    url: "{{ route('transfer-detail.destroy') }}",
                    type: "delete",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId
                    },
                    beforeSend: function() {
                        $("#btn-delete").text("Sending..");
                    },
                    success: function(data) {
                        $("#btn-delete").text("Delete");
                        setTimeout(function() {
                            $("#modal-konfirmasi").modal("hide");
                            var oTable = $("#detail_table").dataTable();
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
                        $("#btn-delete").text("Delete");
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                })
            });

            $("#process_table").on("click", ".process-check", function() {
                if (this.checked == true) {
                    $(".process-check-all").prop("checked", true);
                } else {
                    $(".process-check-all").prop("checked", false);
                }
            });

            $("#process_table").on("click", ".process-check-all", function() {
                $(".process-check").prop("checked", this.checked);
            });

            $("#cancel_table").on("click", ".cancel-check", function() {
                if (this.checked == true) {
                    $(".cancel-check-all").prop("checked", true);
                } else {
                    $(".cancel-check-all").prop("checked", false);
                }
            });

            $("#cancel_table").on("click", ".cancel-check-all", function() {
                $(".cancel-check").prop("checked", this.checked);
            });

            $("#confirm_table").on("click", ".confirm-check", function() {
                if (this.checked == true) {
                    $(".confirm-check-all").prop("checked", true);
                } else {
                    $(".confirm-check-all").prop("checked", false);
                }
            });

            $("#confirm_table").on("click", ".confirm-check-all", function() {
                $(".confirm-check").prop("checked", this.checked);
            });

            $("body").on("click", "#entry-print", function() {
                var data_id = $("#transfer_id").val();

                window.open("{{ url('/inventory/stock-transfer/report/entry/') }}" + "/" + data_id,
                    "TransferReport", "width=800,height=600")
            });

            $("body").on("click", "#pickput-print", function() {
                var data_id = $("#transfer_id").val();

                window.open("{{ url('/inventory/stock-transfer/report/pickputs/') }}" + "/" + data_id,
                    "TransferReport", "width=800,height=600")
            });

            $("body").on("click", "#pick-print", function() {
                var data_id = $("#transfer_id").val();

                window.open("{{ url('/inventory/stock-transfer/report/pick/') }}" + "/" + data_id,
                    "TransferReport", "width=800,height=600")
            });

            $("body").on("click", "#put-print", function() {
                var data_id = $("#transfer_id").val();

                window.open("{{ url('/inventory/stock-transfer/report/put/') }}" + "/" + data_id,
                    "TransferReport", "width=800,height=600")
            });
        });

        function load_stock() {
            var principal_id = $("#principal_id").val();
            var branch_id = $("#branch_id").val();

            if (principal_id == "") {
                swal({
                    icon: "error",
                    text: "Principal name must be entry!"
                });
                return;
            }

            $("#modal-filter").modal("hide");

            $("#stock_table").DataTable().destroy();
            $("#stock_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                info: false,
                ajax: {
                    url: "{{ route('transfer-detail.stockList') }}",
                    type: "GET",
                    data: {
                        branch_id: branch_id,
                        principal_id: principal_id,
                        product_id: $("#f_product_id").val(),
                        product_code: $("#f_product_code").val(),
                        site_id: $("#f_site_id").val(),
                        area_id: $("#f_area_id").val(),
                        location_from: $("#f_location_from").val(),
                        location_to: $("#f_location_to").val(),
                        stock_status: $("#f_status").val()
                    }
                },
                columns: [{
                        data: "action",
                        name: "action",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "serial_no",
                        name: "serial_no"
                    },
                    {
                        data: "product_code",
                        name: "product_code"
                    },
                    {
                        data: "product_name",
                        name: "product_name"
                    },
                    {
                        data: "lot_no",
                        name: "lot_no"
                    },
                    {
                        data: "site_name",
                        name: "site_name"
                    },
                    {
                        data: "area_name",
                        name: "area_name"
                    },
                    {
                        data: "location_code",
                        name: "location_code"
                    },
                    {
                        data: "pqty",
                        name: "pqty"
                    },
                    {
                        data: "puom",
                        name: "puom"
                    },
                    {
                        data: "mqty",
                        name: "mqty"
                    },
                    {
                        data: "muom",
                        name: "muom"
                    },
                    {
                        data: "bqty",
                        name: "bqty"
                    },
                    {
                        data: "buom",
                        name: "buom"
                    }
                ],
                order: [
                    [0, "asc"]
                ]
            });

            $("#modal-stock").modal({
                backdrop: "static",
                keyboard: false,
                show: true
            });
        }

        function processProcess() {
            var oTable = $("#process_table").dataTable();
            $("#form-process").trigger("reset");

            $(".hidden-process").remove();
            oTable.$("input[type='checkbox']").each(function() {
                if (this.checked) {
                    $("#form-process").append(
                        $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-process")
                        .val(this.value)
                    );
                }
            });

            $("#btn-process").html("Sending..");

            $.ajax({
                data: $("#form-process").serialize(),
                url: "{{ route('transfer-process.submit') }}",
                type: "POST",
                dataType: "json",
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#form-process").trigger("reset");
                        $("#btn-process").html("Process");
                        var oTable = $("#process_table").dataTable();
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
                    $("#btn-process").html("Process");
                }
            });
        }

        function processCancel() {
            var oTable = $("#cancel_table").dataTable();
            $("#form-cancel").trigger("reset");

            $(".hidden-cancel").remove();
            oTable.$("input[type='checkbox']").each(function() {
                if (this.checked) {
                    $("#form-cancel").append(
                        $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-cancel")
                        .val(this.value)
                    );
                }
            });

            $("#btn-process-cancel").html("Sending..");

            $.ajax({
                data: $("#form-cancel").serialize(),
                url: "{{ route('transfer-cancel.submit') }}",
                type: "POST",
                dataType: "json",
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#form-cancel").trigger("reset");
                        $("#btn-process-cancel").html("Process");
                        var oTable = $("#cancel_table").dataTable();
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
                    $("#btn-process-cancel").html("Process");
                }
            });
        }

        function processConfirm() {

            var btn = $("#btn-process-confirm");
            btn.addClass('disabled')
            .css({
                'pointer-events': 'none',
                'opacity': '0.5',
                'cursor': 'not-allowed'
            })

            var status = sessionStorage.getItem('status');
            $('.productStatusValue').val(status);

            var oTable = $("#confirm_table").dataTable();
            $("#form-confirm").trigger("reset");

            $(".hidden-confirm").remove();
            oTable.$("input[type='checkbox']").each(function() {
                if (this.checked) {
                    $("#form-confirm").append(
                        $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-confirm")
                        .val(this.value)
                    );
                }
            });

            $("#form-confirm").append(
                $("<input>")
                .attr("type", "hidden")
                .attr("name", "transfer_id")
                .attr("class", "hidden-confirm")
                .val($("#transfer_id").val())
            );

            $("#btn-process-confirm").html("Sending..");

            $.ajax({
                data: $("#form-confirm").serialize(),
                url: "{{ route('transfer-confirm.submit') }}",
                type: "POST",
                dataType: "json",
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#form-confirm").trigger("reset");
                        $("#btn-process-confirm").html("Process");
                        var oTable = $("#confirm_table").dataTable();
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
                    $("#btn-process-confirm").html("Process");
                },
                complete: function() {
                btn.removeClass('disabled').css({
                    'pointer-events': '',
                    'opacity': '',
                    'cursor': ''
                }).html('<i class="fas fa-play"></i> Process');
            }
            });
        }

        $('#form-upload').on('submit', function() {
            $('#btnImport').attr('disabled', true);
        });
    </script>
@endpush
