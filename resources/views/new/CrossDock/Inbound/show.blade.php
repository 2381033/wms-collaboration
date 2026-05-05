@extends('layouts.new.base')
@section('title', 'MKT - Inbound')
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
                        <div class="col-sm-12">
                            <div class="float-right mb-4">
                                <a href="{{ url('crossDock') }}" class="btn btn-md btn-dark" style="border-radius: 15px;"><i
                                        class="flaticon2-arrow-2"></i>
                                    Dashboard</a>
                                <a href="{{ url('crossDock/inbound') }}" class="btn btn-md btn-info"
                                    style="border-radius: 15px;"><i class="flaticon2-add"></i> Add New Job</a>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-line mb-5">
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_header ? 'active' : '' }}" data-toggle="tab"
                                        href="#JobHeader">
                                        <span class="nav-icon"><i class="flaticon-information"></i></span>
                                        <span class="nav-text">Job Header</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_cargo ? 'active' : '' }}" data-toggle="tab"
                                        href="#CargoDetail">
                                        <span class="nav-icon"><i class="flaticon2-open-box"></i></span>
                                        <span class="nav-text">Cargo Detail</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_mapping ? 'active' : '' }}" data-toggle="tab"
                                        href="#MappingPallet">
                                        <span class="nav-icon"><i class="flaticon-clipboard"></i></span>
                                        <span class="nav-text">Good Receipt</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_putaway ? 'active' : '' }}" data-toggle="tab"
                                        href="#Putaway">
                                        <span class="nav-icon"><i class="flaticon2-layers-2"></i></span>
                                        <span class="nav-text">Putaway</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_confirm ? 'active' : '' }}" data-toggle="tab"
                                        href="#Confirmation">
                                        <span class="nav-icon"><i class="flaticon2-checkmark"></i></span>
                                        <span class="nav-text">Confirmation</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade  {{ $menu_header ? ' show active' : '' }}" id="JobHeader"
                                    role="tabpanel" aria-labelledby="JobHeader">
                                    <form action="{{ route('storeHeader') }}" method="post" id="PostForm">
                                        @csrf
                                        <div class="card-body p-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Job No:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="job_no" readonly
                                                            value="{{ $header->job_no }}" />
                                                        <span class="form-text text-muted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Date In:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" readonly
                                                            value="{{ formatTanggalIndonesia2($header->created_at) }}" />
                                                        <span class="form-text text-muted"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="my-select">Branch</label>
                                                        <select class="form-control" name="id_branch" required disabled>
                                                            <option value="" selected>
                                                                {{ $branch->where('id', $header->id_branch)->first()->branch_name }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="my-select">Customer</label>
                                                        <select class="form-control" name="id_customer" required disabled>
                                                            <option value="" selected>
                                                                {{ $customer->where('id', $header->id_customer)->first()->name }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="my-select">Remarks</label>
                                                        <textarea class="form-control mt-2" name="description" rows="2" name="description" placeholder="Description"
                                                            disabled autocomplete="off">{{ $header->remarks }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="my-select">Warehouse</label>
                                                        <select class="form-control" name="id_warehouse" required
                                                            disabled>
                                                            <option value="" selected>
                                                                {{ $warehouse->where('id', $header->id_warehouse)->first()->name }}
                                                            </option>
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>Vehicle Number</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="vehicle_number"
                                                                    required autocomplete="off"
                                                                    value="{{ $header->vehicle_number }}" disabled />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-select">Vehicle Type</label>
                                                                <select class="form-control" name="vehicle" required
                                                                    disabled>
                                                                    <option value="{{ $header->vehicle }}" selected
                                                                        disabled> {{ $header->vehicle }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Driver Name</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="driver_name" required
                                                                    autocomplete="off" disabled
                                                                    value="{{ $header->driver_name }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>Container Number</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="container_number"
                                                                    autocomplete="off" required disabled
                                                                    value="{{ $header->container_number }}" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-select">Size</label>
                                                                <select class="form-control" name="size" required
                                                                    disabled>
                                                                    <option value="{{ $header->size }}" selected>
                                                                        {{ $header->size }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Transporter Name</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="transporter_name"
                                                                    autocomplete="off" required disabled
                                                                    value="{{ $header->container_number }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="float-right">
                                                <button type="submit" class="btn btn-info btn-lg mr-2"
                                                    id="submitHeader"><i class="flaticon2-checkmark"></i> Save</button>
                                            </div> --}}
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade {{ $menu_cargo ? ' show active' : '' }}" id="CargoDetail"
                                    role="tabpanel" aria-labelledby="CargoDetail">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <div class="float-end">
                                                    @if ($header->confirmed_flag == 'open')
                                                        <a class="btn btn-md btn-info mt-2 mb-2" href="#upload"
                                                            data-toggle="modal"><i class="las la-file-excel"></i>
                                                            Upload Excel</a>
                                                    @endif
                                                </div>
                                                <form action="{{ url('inbound/create/storeDetail') }}" method="post"
                                                    id="form-update">
                                                    @csrf
                                                    <table class="table table-bordered table-scroll mt-3"
                                                        id="productTable">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th rowspan="2">NO</th>
                                                                <th rowspan="2">CARGO ID</th>
                                                                <th rowspan="2">DESCRIPTION</th>
                                                                <th colspan="3">DIMENSION(cm)</th>
                                                                <th rowspan="2">QTY</th>
                                                                <th rowspan="2">CBM/UNIT</th>
                                                                <th rowspan="2">TOTAL CBM</th>
                                                                <th rowspan="2">TOTAL WEIGHT(Kg)</th>
                                                                @if ($header->confirmed_flag == 'open')
                                                                    <th rowspan="2">
                                                                        <a class="btn btn-warning" id="addProduct"
                                                                            data-toggle="modal" href="#addModal"><i
                                                                                class="las la-plus-circle"></i>Add</a>
                                                                    </th>
                                                                @endif
                                                            </tr>
                                                            <tr class="text-center">
                                                                <th>P</th>
                                                                <th>L</th>
                                                                <th>T</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($cargo as $item)
                                                                <tr class="text-center">
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->id_cargo }}</td>
                                                                    <td>{{ $item->description }}</td>
                                                                    <td>{{ $item->p }}</td>
                                                                    <td>{{ $item->l }}</td>
                                                                    <td>{{ $item->t }}</td>
                                                                    <td>{{ $item->qty . ' ' . $item->unit }}
                                                                    </td>
                                                                    <td>{{ number_format($item->cbm_per_unit, 2, '.', '') }}
                                                                    </td>
                                                                    <td>{{ number_format($item->cbm_total, 2, '.', '') }}
                                                                    </td>
                                                                    <td>{{ $item->w }}</td>
                                                                    @if ($header->status == 2)
                                                                        <td>
                                                                            <a href="#"
                                                                                onclick="editCargo('{{ $item->id }}')"
                                                                                class="btn btn-sm btn-dark"><i
                                                                                    class="fas fa-edit"></i></a>
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="deleteCargo('{{ $item->id }}')"><i
                                                                                    class="fas fa-trash-alt"></i></a>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            @if ($cargo->count() > 0 and $header->confirmed_flag == 'open')
                                                <div class="float-right">
                                                    <a class="btn btn-lg btn-primary" href="#"
                                                        onclick="confirmation('cargo','{{ $header->id }}')"><i
                                                            class="fas fa-check"></i>
                                                        Konfirm</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ $menu_mapping ? ' show active' : '' }}" id="MappingPallet"
                                    role="tabpanel" aria-labelledby="MappingPallet">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-left">
                                                <a href="#" class="btn btn-sm bg-light-primary"
                                                    style="border-radius: 15px;"
                                                    onclick="report('mapping-detail', '{{ $header->id }}')">
                                                    <i class="flaticon-clipboard"></i>
                                                    Good Receipt Detail
                                                </a>
                                                <a href="#" class="btn btn-sm bg-light-info"
                                                    style="border-radius: 15px;"
                                                    onclick="report('mapping-summary', '{{ $header->id }}')">
                                                    <i class="flaticon-clipboard"></i>
                                                    Good Receipt Summary
                                                </a>
                                                <a href="#" class="btn btn-sm bg-light-warning"
                                                    style="border-radius: 15px;"
                                                    onclick="report('pallet-tag', '{{ $header->id }}')">
                                                    <i class="flaticon2-print"></i>
                                                    Pallet Tag Report
                                                </a>
                                                <hr>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No.</th>
                                                        <th>#</th>
                                                        <th>Cargo ID</th>
                                                        <th>QTY</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cargo->where('status', 3) as $item)
                                                        <tr class="text-center">
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <a href="#"
                                                                    onclick="mappingPalllet('{{ $item->id }}')"
                                                                    class="btn btn-sm btn-info"><i
                                                                        class="flaticon2-notepad"></i>
                                                                    Add/Update Pallet
                                                                </a>
                                                            </td>
                                                            <td>{{ $item->id_cargo }}</td>
                                                            <td>{{ $item->qty . ' ' . $item->unit }}</td>
                                                            <td
                                                                class="{{ $batch->where('id_detail', $item->id)->count() > 0 ? 'bg-success' : 'bg-warning' }}">
                                                                {{ $batch->where('id_detail', $item->id)->count() . ' Pallet' ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="float-right">
                                                @if ($cargo->count() == $batch->groupBy('id_detail')->count() and $header->status == 3)
                                                    <a href="#"
                                                        onclick="confirmation('mapping','{{ $header->id }}')"
                                                        class="btn btn-md btn-info" style="border-radius: 15px;"><i
                                                            class="flaticon2-checkmark"></i> Konfirm</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ $menu_putaway ? ' show active' : '' }}" id="Putaway"
                                    role="tabpanel" aria-labelledby="Putaway">
                                    <form action="{{ route('postPutaway') }}" method="post" id="postPutaway">
                                        @csrf
                                        <input type="hidden" name="id_header" value="{{ $header->id }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="float-left">
                                                    <a href="#" class="btn btn-sm bg-light-success"
                                                        style="border-radius: 15px;"
                                                        onclick="report('putaway', '{{ $header->id }}')">
                                                        <i class="flaticon2-print"></i>
                                                        Putaway Report
                                                    </a>
                                                    <hr>
                                                </div>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No.</th>
                                                            <th>Cargo ID</th>
                                                            <th>Description</th>
                                                            <th>QTY</th>
                                                            <th>Location</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($batch->where('status', 4) as $item)
                                                            <tr class="text-center">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    {{ $item->detail->id_cargo ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ $item->detail->description ?? '-' }}
                                                                </td>
                                                                <td>{{ $item->qty_pallet . ' ' . $item->detail->unit }}
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="location_code[]"
                                                                        placeholder="Location.." class="form-control"
                                                                        required autocomplete="off" value="Floor">

                                                                    <input type="hidden" name="id[]"
                                                                        value="{{ $item->id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="float-right">
                                                    @if ($cargo->count() > 0 and $header->status == 4)
                                                        <button type="submit" class="btn btn-lg btn-info"
                                                            style="border-radius: 10px;"><i
                                                                class="flaticon2-checkmark"></i>
                                                            Konfirm</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade {{ $menu_confirm ? ' show active' : '' }}" id="Confirmation"
                                    role="tabpanel" aria-labelledby="Confirmation">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            @if ($header->status == 0)
                                                <div class="float-left">
                                                    <a href="#" class="btn btn-sm bg-light-info"
                                                        style="border-radius: 15px;"
                                                        onclick="report('icr-detail', '{{ $header->id }}')">
                                                        <i class="flaticon2-print"></i>
                                                        Inbound Confirmation Report Detail
                                                    </a>
                                                    <a href="#" class="btn btn-sm bg-light-warning"
                                                        style="border-radius: 15px;"
                                                        onclick="report('icr-summary', '{{ $header->id }}')">
                                                        <i class="flaticon2-print"></i>
                                                        Inbound Confirmation Report Summary
                                                    </a>
                                                    <hr>
                                                </div>
                                            @endif
                                            @if ($header->unloading_start == null)
                                                <form action="{{ route('submitUnloading') }}" method="post"
                                                    id="unloadForm" autocomplete="off">
                                                    @csrf
                                                    <input type="hidden" name="id_header"
                                                        value="{{ $header->id }}" />
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>Shipment Arrival Date:</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi"
                                                                    name="shipment_arrival_date" required
                                                                    id="shipmentArrival" />
                                                                <span class="form-text text-muted"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>Unloading Start</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="unloading_start"
                                                                    required id="unloadingStart" />
                                                                <span class="form-text text-muted"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>Unloading Finish</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="unloading_finish"
                                                                    required id="unloadingFinish" />
                                                                <span class="form-text text-muted"></span>
                                                            </div>
                                                        </div>
                                                        <div class="float-right">
                                                            <br>
                                                            <button type="submit" class="btn btn-md btn-info mt-2">
                                                                <i class="fas fa-save"></i> Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No.</th>
                                                        <th>Cargo ID</th>
                                                        <th>DESCRIPTION</th>
                                                        <th>QTY</th>
                                                        <th>Location</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($batch as $item)
                                                        <tr class="text-center">
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                {{ $item->detail->id_cargo ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $item->detail->description ?? '-' }}
                                                            </td>
                                                            <td>{{ $item->qty_pallet . ' ' . $item->detail->unit }}</td>
                                                            <td>{{ $item->location_code }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="float-right">
                                                @if ($header->unloading_start != null and $header->status == 5)
                                                    <a href="#"
                                                        onclick="confirmation('finish','{{ $header->id }}')"
                                                        class="btn btn-lg btn-info" style="border-radius: 15px;"><i
                                                            class="flaticon2-checkmark"></i>
                                                        Konfirm</a>
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
        </div>
    </div>

    <div id="upload" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('importCargo') }}" method="post" id="formUpload"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id_header" value="{{ $header->id }}">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <a href="{{ url('assets/excel/template-inbound.xlsx') }}"
                                            class="btn btn-md btn-dark"><i class="las la-download"></i> Download
                                            Template</a>
                                        <br>
                                        <div class="input-group mt-4">
                                            <input class="form-control" type="file" name="excel" placeholder=""
                                                aria-label="Recipient's text" aria-describedby="my-addon" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">File</span>
                                            </div>
                                        </div>
                                        <div class="float-right mt-4">
                                            <button type="submit" class="btn btn-md btn-success btn-upload"><i
                                                    class="las la-upload"></i> Upload
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

    <div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('addCargo') }}" method="post" id="addCargo">
                                @csrf
                                <input type="hidden" name="id_header" value="{{ $header->id }}">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Cargo ID</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off"
                                                name="cargo_id" placeholder="Silahkan di isi.." required
                                                aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Description</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off"
                                                name="description" placeholder="Silahkan di isi.." required
                                                aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">P</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="p"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">L</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="l"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">T</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="t"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">Weight</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="w"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Qty</span>
                                            </div>
                                            <input class="form-control" type="number" autocomplete="off" name="qty"
                                                placeholder="Silahkan di isi.." required aria-label="Recipient's text"
                                                aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-4">
                                        <div class="form-group">
                                            <select class="form-control" name="uom" required>
                                                <option value="" selected disabled>UOM</option>
                                                @foreach ($uom as $item)
                                                    <option value="{{ $item->code }}">{{ $item->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="float-right ">
                                        <button type="submit" class="btn btn-lg btn-info mt-4" id="btnUpload"><i
                                                class="fas fa-save"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editCargo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('updateCargo') }}" method="post" id="updateCargo">
                                @csrf
                                <input type="hidden" name="id_header" id="idHeaderCargo" />
                                <input type="hidden" name="id_detail" id="idDetailCargo" />
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Cargo ID</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off"
                                                name="id_cargo" id="cargoIdValue" placeholder="Silahkan di isi.."
                                                required aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Description</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off"
                                                name="description" id="descValue" placeholder="Silahkan di isi.."
                                                required aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">P</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="p"
                                                required id="pValue" placeholder="Silahkan Di isi.."
                                                aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">L</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="l"
                                                required id="lValue" placeholder="Silahkan Di isi.."
                                                aria-label="Recipient's text" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">T</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="t"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                id="tValue" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 mt-3">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">W</span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" name="w"
                                                required placeholder="Silahkan Di isi.." aria-label="Recipient's text"
                                                id="wValue" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="my-addon">Qty</span>
                                            </div>
                                            <input class="form-control" type="number" autocomplete="off" name="qty"
                                                placeholder="Silahkan di isi.." required aria-label="Recipient's text"
                                                id="qtyValue" aria-describedby="my-addon">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-4">
                                        <div class="form-group">
                                            <select class="form-control" name="uom" required>
                                                <option value="" selected disabled>UOM</option>
                                                @foreach ($uom as $item)
                                                    <option value="{{ $item->code }}">{{ $item->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="float-right ">
                                        <button type="submit" class="btn btn-lg btn-info mt-4" id="btnUpdate"><i
                                                class="fas fa-save"></i>
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mappingPallet" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-backdrop="static">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Cargo ID : <b class="cargoText"></b> || SKU <b
                            class="skuText"></b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('postMappingPallet') }}" method="post" id="postMappingPallet">
                                @csrf
                                <input type="hidden" id="idMappingValue" name="id_detail">
                                <input type="hidden" id="idHeaderValue" name="id_header">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="my-input">Qty</label>
                                            <input type="text" disabled id="qtyValueMapping" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="my-select">Jumlah Pallet</label>
                                            <select class="form-control" name="jumlahPallet" id="selectPallet"
                                                onchange="selectJumlahPallet(this.value)" required>
                                                <option value="" selected disabled>Choose</option>
                                                @for ($i = 1; $i <= 150; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div id="kontenMapping">

                                        </div>
                                        <div class="float-right mt-4">
                                            <button type="submit" class="btn btn-lg btn-info"><i
                                                    class="fas fa-check"></i> Submit</button>
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
@endsection

@push('scripts')
    <script type="text/javascript">
        function report(type, id) {
            window.open("{{ url('crossDock/inbound/report') }}/" + type + "/" + id);
        }
        $('#shipmentArrival').datetimepicker();
        $('#unloadingStart').datetimepicker();
        $('#unloadingFinish').datetimepicker();

        function selectJumlahPallet(jumlah) {
            $('#kontenMapping').html('')
            for (i = 1; i <= jumlah; i++) {
                $('#kontenMapping').append(`<div class="input-group mt-4">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">QTY Pallet ${i}</span>
                                            </div>
                                            <input class="form-control" type="number" name="qty[]" required
                                                placeholder="Silahkan isi" aria-label="Recipient's text"
                                                aria-describedby="my-addon" autocomplete="off">
                                        </div>`)
            }
        }

        function mappingPalllet(id) {
            $('#kontenMapping').html('')
            $('#idMappingValue').val(id);
            $('#selectPallet').val("");
            $.ajax({
                url: "{{ url('crossDock/inbound/getMappingPallet') }}/" + id,
                method: 'GET',
                success: function(data) {
                    $('#mappingPallet').modal('show');
                    $('.cargoText').text(data.id_cargo);
                    $('.skuText').text(data.sku);
                    $('#idHeaderValue').val(data.id_header);
                    $('#qtyValueMapping').val(data.qty);
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        }

        function editCargo(id) {
            $.ajax({
                url: "{{ url('crossDock/inbound/editCargo') }}/" + id,
                method: 'GET',
                success: function(data) {
                    $('#cargoIdValue').val(data.id_cargo);
                    $('#pValue').val(data.p);
                    $('#lValue').val(data.l);
                    $('#tValue').val(data.t);
                    $('#wValue').val(data.w);
                    $('#qtyValue').val(data.qty);
                    $('#idHeaderCargo').val(data.id_header);
                    $('#idDetailCargo').val(data.id);
                    $('#descValue').val(data.description);
                    $('#editCargo').modal('show');
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        }

        function deleteCargo(id) {
            Swal.fire({
                title: 'Do you want to delete this cargo?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, delete this cargo',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    location.href = "{{ url('crossDock/inbound/deleteCargo') }}/" + id;
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        function confirmation(type, id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Konfirm',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('crossDock/inbound/confirm') }}/" + type + '/' + id,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            Swal.fire({
                                icon: 'success',
                                title: data.pesan,
                            });
                            location.reload();
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'error',
                                title: data,
                            })
                        }
                    });
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        $('#PostForm').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {

                },
                error: function(error) {
                    $('#submitHeader').attr('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        });

        $('#formUpload').on('submit', function() {
            $('.btn-upload').hide();
        });

        $('#addCargo').on('submit', function(e) {
            e.preventDefault();
            var data = $('#addCargo').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'duplicate') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate ID Cargo',
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        location.reload();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        $('#unloadForm').on('submit', function(e) {
            e.preventDefault();
            var data = $('#unloadForm').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.error,
                        })
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        $('#updateCargo').on('submit', function(e) {
            e.preventDefault();
            var data = $('#updateCargo').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'duplicate') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate ID Cargo',
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        location.reload();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        $('#postMappingPallet').on('submit', function(e) {
            e.preventDefault();
            var data = $('#postMappingPallet').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.limit) {
                        Swal.fire({
                            icon: 'error',
                            title: data.limit,
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        location.reload();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        $('#postPutaway').on('submit', function(e) {
            e.preventDefault();
            var data = $('#postPutaway').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data was processed successfully.',
                    });
                    location.reload();
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });
    </script>
@endpush
