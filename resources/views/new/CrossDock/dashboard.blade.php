@extends('layouts.new.base')
@section('title', 'MKT - Spot Order Dashboard')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        @if (in_array('scan_cross_dock', $permissions))
                            <div class="col-sm-12">
                                <div class="card card-custom card-stretch gutter-b bg-light-primary"
                                    style="border-radius: 15px;" onclick="window.open(`{{ url('crossDock/scanCargo') }}`)">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-dark font-size-h5 mb-2 text-hover-danger">Scan
                                                Cargo</span>
                                            <span class="font-weight-bold text-dark font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/scan.png') }}" alt=""
                                            class="align-self-end h-120px">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('inbound_cross_dock', $permissions))
                            <div class="col-sm-6">
                                <div class="card card-custom card-stretch gutter-b bg-light-warning inbound"
                                    style="border-radius: 15px;" onclick="menu('inbound')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-dark font-size-h5 mb-2 text-hover-primary">Inbound</span>
                                            <span class="font-weight-bold text-muted  font-size-lg">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/inbound.png') }}" alt=""
                                            class="align-self-end h-120px">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('outbound_cross_dock', $permissions))
                            <div class="col-sm-6">
                                <div class="card card-custom card-stretch gutter-b bg-light-info outbound"
                                    style="border-radius: 15px;" onclick="menu('outbound')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-dark font-size-h5 mb-2 text-hover-primary">Outbound</span>
                                            <span class="font-weight-bold text-muted  font-size-lg">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/outbound.png') }}" alt=""
                                            class="align-self-end h-120px">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('stock_report_cross_dock', $permissions))
                            <div class="col-sm-4">
                                <div class="card card-custom card-stretch gutter-b stock-ledger"
                                    style="border-radius: 15px; background-color: #663259;"
                                    onclick="menuReport('stock-ledger')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-primary">Stock
                                                Report</span>
                                            <span class="font-weight-bold text-white  font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/stock.png') }}" alt=""
                                            class="align-self-end h-120px">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('transaction_cross_dock', $permissions))
                            <div class="col-sm-4">
                                <div class="card card-custom card-stretch gutter-b transactionReport"
                                    style="border-radius: 15px; background-color: #1B283F;"
                                    onclick="menuReport('transaction-report')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-danger">Transcation
                                                Report</span>
                                            <span class="font-weight-bold text-white font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/stocks.png') }}" alt=""
                                            class="align-self-end h-120px">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('daily_report_cross_dock', $permissions))
                            <div class="col-sm-4">
                                <div class="card card-custom card-stretch gutter-b inout-report"
                                    style="border-radius: 15px; background-color: #9c5ee9;"
                                    onclick="menuReport('inout-report')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-danger">Daily
                                                Inbound & Outbound Report</span>
                                            <span class="font-weight-bold text-white font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/inout.png') }}" alt=""
                                            class="align-self-end h-100px mb-4">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('storage_report_cross_dock', $permissions))
                            <div class="col-sm-6">
                                <div class="card card-custom card-stretch gutter-b"
                                    style="border-radius: 15px; background-color: #53d8f9;"
                                    onclick="menuReport('storage-report')">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-danger">Storage
                                                Report</span>
                                            <span class="font-weight-bold text-white font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/cbm.png') }}" alt=""
                                            class="align-self-end h-100px mb-4">
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array('master_data_cross_dock', $permissions))
                            <div class="col-sm-6">
                                <div class="card card-custom card-stretch gutter-b"
                                    style="border-radius: 15px; background-color: #0aac4d;"
                                    onclick="location.href='{{ url('crossDock/masterData') }}'">
                                    <div class="card-body d-flex align-items-center py-0 mt-8">
                                        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                            <span
                                                class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-danger">Master
                                                Data</span>
                                            <span class="font-weight-bold text-white font-size-xl">Spot Order</span>
                                        </div>
                                        <img src="{{ asset('images/reporting.png') }}" alt=""
                                            class="align-self-end h-100px mb-4">
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- <div class="col-sm-12">
                            <div class="card card-custom card-stretch gutter-b"
                                style="border-radius: 15px; background-color: #8cbcfc;"
                                onclick="location.href='{{ url('crossDock/tools') }}'">
                                <div class="card-body d-flex align-items-center py-0 mt-8">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span
                                            class="card-title font-weight-bolder text-white font-size-h5 mb-2 text-hover-danger">Tools</span>
                                        <span class="font-weight-bold text-white font-size-xl">Spot Order</span>
                                    </div>
                                    <img src="{{ asset('images/tools.png') }}" alt=""
                                        class="align-self-end h-100px mb-4">
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-sm-12">
                            <div class="card konten hide" style="outline: solid; black; border-radius: 15px;"
                                id="konten">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required
                                                value="{{ date('Y-m-01') }}" id="startDate">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required
                                                value="{{ date('Y-m-t') }}" id="endDate">
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <select id="statusJob" class="form-control">
                                                    <option value="open" selected>Open</option>
                                                    <option value="confirmed">Confirmed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <a href="#" onclick="searchData()" class="btn btn-block btn-dark"><i
                                                    class="fas fa-search"></i>
                                            </a>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="float-right">
                                                <a href="#" onclick="addNewJob()"
                                                    class="btn btn-lg btn-light-info"><i class="fas fa-add"></i> Add
                                                    New
                                                    Job</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tableList">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Job No</th>
                                                            <th>Warehouse</th>
                                                            <th>Customer</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

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
            </div>
        </div>
    </div>

    <div id="stock-ledger" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="my-modal-title">STOCK REPORT</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('crossDock/report') }}" method="post" id="form-stock-ledger">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="type" id="typeValue-stock-ledger">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-select">Customer</label>
                                            <select class="form-control" name="id_customer" required>
                                                <option value="all" selected>ALL</option>
                                                @foreach ($customer as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" style="zoom: 120%">
                                            <label>Report Type</label>
                                            <div class="radio-inline">
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="detail" />
                                                    <span></span>
                                                    Detail
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="summary" checked />
                                                    <span></span>
                                                    Summary
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <label for="my-select">Warehouse</label>
                                            <select class="form-control" name="id_warehouse" required>
                                                @foreach ($warehouse as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-2">
                                        <div class="form-group row ">
                                            <div class="col-sm-9 col-form-label" style="zoom: 120%">
                                                <label for="">File Type</label>
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="print" checked="checked" />
                                                        <span></span>
                                                        PDF
                                                    </label>
                                                    <label class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="excel" />
                                                        <span></span>
                                                        Excel
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="float-right">
                                            <button type="submit" onclick="searchStockReport()"
                                                class="btn btn-md btn-primary" id="submitStockLedger">
                                                <i class="flaticon-download"></i>
                                                Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="inout-report" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="my-modal-title">INBOUND & OUTBOUND DAILY REPORT</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" action="{{ url('crossDock/report/daily') }}" method="post"
                                id="inoutDaily">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="my-select">Warehouse</label>
                                            <select class="form-control" name="id_warehouse" required>
                                                @foreach ($warehouse as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" style="zoom: 120%">
                                            <label>Report Type</label>
                                            <div class="radio-inline">
                                                <label class="radio">
                                                    <input type="radio" name="type" value="inbound" checked />
                                                    <span></span>
                                                    Inbound
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" name="type" value="outbound" />
                                                    <span></span>
                                                    Outbound
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period Start</label>
                                            <input id="start" class="form-control" type="text" required
                                                name="start">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period End</label>
                                            <input id="end" class="form-control" type="text" required
                                                name="end">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" style="zoom: 120%">
                                            <label>File Type</label>
                                            <div class="radio-inline">
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="pdf" checked />
                                                    <span></span>
                                                    PDF
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="excel" />
                                                    <span></span>
                                                    Excel
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info" id="submitInOutDaily">
                                        <i class="fas fa-search"></i>
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transaction-report" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="my-modal-title">TRANSACTION REPORT</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" action="{{ url('crossDock/report/transaction') }}" method="post"
                                id="formTransactionReport">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Warehouse</label>
                                            <select class="form-control" name="id_warehouse" required>
                                                @foreach ($warehouse as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period Start</label>
                                            <input id="transaction_start" class="form-control" type="text" required
                                                name="start">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period End</label>
                                            <input id="transaction_end" class="form-control" type="text" required
                                                name="end">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" style="zoom: 120%">
                                            <label>File Type</label>
                                            <div class="radio-inline">
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="pdf" checked />
                                                    <span></span>
                                                    PDF
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="excel" />
                                                    <span></span>
                                                    Excel
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info" id="submitTransactionReport">
                                        <i class="fas fa-search"></i>
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="storage-report" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="my-modal-title">STORAGE REPORT</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" action="{{ url('crossDock/report/storage') }}" method="post"
                                id="storageReport">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Warehouse</label>
                                            <select class="form-control" name="id_warehouse" required>
                                                @foreach ($warehouse as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period Start</label>
                                            <input id="pariodeStart" class="form-control" type="text" required
                                                name="start">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="my-input">Period End</label>
                                            <input id="pariodeEnd" class="form-control" type="text" required
                                                name="end">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" style="zoom: 120%">
                                            <label>File Type</label>
                                            <div class="radio-inline">
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="pdf" checked />
                                                    <span></span>
                                                    PDF
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" name="report_type" value="excel" />
                                                    <span></span>
                                                    Excel
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info">
                                        <i class="fas fa-search"></i>
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('input[type="checkbox"]').on('change', function() {
            $('input[type="checkbox"]').not(this).prop('checked', false);
        })

        $('#start').datepicker();
        $('#end').datepicker();
        $('#pariodeStart').datepicker();
        $('#pariodeEnd').datepicker();
        $('#transaction_start').datepicker();
        $('#transaction_end').datepicker();

        function menu(menu) {
            sessionStorage.setItem('menu', menu);
            $('.card-custom').toggle('fast')
            $('.' + menu).toggle('fast');
            $('.konten').toggle('fast')
            $('#konten').removeClass('hide')
            $('#tableList').DataTable().clear().destroy()
        }

        function menuReport(menu) {
            $('#' + menu).modal('show');
            $('#typeValue-' + menu).val(menu);
        }

        function addNewJob() {
            var menu = sessionStorage.getItem('menu');
            location.href = "{{ url('crossDock') }}/" + menu
        }

        function searchData() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var jobType = sessionStorage.getItem("menu");
            var statusJob = $('#statusJob').val();

            $('#tableList').DataTable().clear().destroy()
            $('#tableList').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ordering: false,
                paging: false,
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                ajax: {
                    url: "{{ url('crossDock/getListJob') }}/" + startDate + "/" + endDate + "/" + jobType + "/" +
                        statusJob,
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a href="{{ url('crossDock/${jobType}/showJobFrontend/${data.id}') }}">${data.job_no}</a>`;
                        },
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.confirmed_flag == 'open') {
                                var status =
                                    '<span class = "badge badge-primary">Open</span>'
                            } else {
                                var status =
                                    '<span class="badge badge-success">Confirmed</span>'
                            }
                            return status;
                        },
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        $('#inoutDaily').on('submit', function() {
            $('#submitInOutDaily').hide();
        });

        $('#form-stock-ledger').on('submit', function() {
            $('#submitStockLedger').hide();
        });

        $('#formTransactionReport').on('submit', function() {
            $('#submitTransactionReport').hide();
        });
    </script>
@endpush
