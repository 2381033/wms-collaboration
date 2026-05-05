@extends('layouts.new.base')
@section('title', 'MKT - Outbound')
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
                                <a href="{{ url('crossDock/outbound') }}" class="btn btn-md btn-info"
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
                                    <a class="nav-link {{ $order_detail ? 'active' : '' }}" data-toggle="tab"
                                        href="#orderDetail">
                                        <span class="nav-icon"><i class="flaticon-clipboard"></i></span>
                                        <span class="nav-text">Order Detail</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_picking ? 'active' : '' }}" data-toggle="tab"
                                        href="#picking">
                                        <span class="nav-icon"><i class="flaticon2-layers-2"></i></span>
                                        <span class="nav-text">Picking</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_scan ? 'active' : '' }}" data-toggle="tab" href="#scan">
                                        <span class="nav-icon"><i class="fas fa-barcode"></i></span>
                                        <span class="nav-text">Scan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#cancel">
                                        <span class="nav-icon"><i class="flaticon-circle"></i></span>
                                        <span class="nav-text">Cancel</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#despatch">
                                        <span class="nav-icon"><i class="flaticon-truck"></i></span>
                                        <span class="nav-text">Despatch</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $menu_confirm ? 'active' : '' }}" data-toggle="tab"
                                        href="#confirm">
                                        <span class="nav-icon"><i class="flaticon2-checkmark"></i></span>
                                        <span class="nav-text"> Confirmation</span>
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
                                                            placeholder="Enter full name" name="job_no" disabled
                                                            value="{{ $header->job_no }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Date Out:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" disabled
                                                            value="{{ formatTanggalIndonesia2($header->created_at) }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Branch</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" disabled
                                                            value="{{ $branch->where('id', $header->id_branch)->first()->branch_name }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Warehouse</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" disabled
                                                            value="{{ $warehouse->where('id', $header->id_warehouse)->first()->name }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Customer</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" disabled
                                                            value="{{ $customer->where('id', $header->id_customer)->first()->name }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>PO Number:</label>
                                                        <input type="text" class="form-control form-control" required
                                                            placeholder="Silahkan isi.." disabled name="po_no"
                                                            value="{{ $header->po_no }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>DO Number:</label>
                                                        <input type="text" class="form-control form-control" required
                                                            placeholder="Silahkan isi.." disabled name="do_no"
                                                            value="{{ $header->do_no }}" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <textarea class="form-control mt-2" name="description" id="" rows="2" name="description"
                                                            placeholder="Description" disabled autocomplete="off">{{ $header->description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade  {{ $order_detail ? ' show active' : '' }}" id="orderDetail"
                                    role="tabpanel" aria-labelledby="orderDetail">
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="float-left">
                                                    @if ($header->confirmed_flag == 'open')
                                                        <a href="#addDetail" data-toggle="modal"
                                                            class="btn btn-lg btn-primary mb-3"><i
                                                                class="flaticon-add-circular-button"></i> Add</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <form action="{{ route('updateCargoOutbound') }}" method="post"
                                                    id="updateCargoOutbound">
                                                    @csrf
                                                    <table class="table table-bordered">
                                                        <tr class="text-center" style="background-color: bisque">
                                                            <th colspan="6">TABLE EDIT ORDER DETAIL</th>
                                                        </tr>
                                                        <tr class="center">
                                                            <th>NO</th>
                                                            <th>ID CARGO</th>
                                                            <th>DESCRIPTION</th>
                                                            <th>LOCATION</th>
                                                            <th>QTY</th>
                                                            <th>#</th>
                                                        </tr>
                                                        <tbody>
                                                            @foreach ($cargo->where('status', 2)->where('picking_flag', 'No') as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->stock->id_cargo }}</td>
                                                                    <td>{{ $item->stock->description }}</td>
                                                                    <td>{{ $item->stock->location_code }}</td>
                                                                    <td>
                                                                        <input type="number"
                                                                            class="form-control qtyValue" required
                                                                            name="qty[]" placeholder="Silahkan isi"
                                                                            value="{{ $item->qty }}"
                                                                            autocomplete="off" readonly>
                                                                        <input type="hidden"name="id[]"
                                                                            value="{{ $item->id }}">
                                                                        <input type="hidden"name="id_stock[]"
                                                                            value="{{ $item->stock->id }}">
                                                                    </td>
                                                                    <td>
                                                                        <a href="#"
                                                                            onclick="deleteCargo('{{ $item->id }}')"
                                                                            class="btn btn-sm btn-danger btnEdit hide"><i
                                                                                class="fas fa-trash-alt"></i> </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <div class="float-left modeEdit">
                                                        @if ($cargo->count() > 0)
                                                            <a href="#" class="btn btn-dark btn-lg mt-4"
                                                                onclick="modeEdit()" id=""><i
                                                                    class="flaticon-edit-1"></i>
                                                                Mode Edit
                                                            </a>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-sm-12 mt-4">
                                                <h2 class="text-center">
                                                    DRAFT PICKING
                                                </h2>
                                                <hr width="100%" size="50" align="center" color="black">
                                                <table class="table table-hover mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>ID CARGO</th>
                                                            <th>DESCRIPTION</th>
                                                            <th>LOCATION</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cargo->where('status', 2)->where('picking_flag', 'No') as $item)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $item->stock->id_cargo }}</td>
                                                                <td>{{ $item->stock->description }}</td>
                                                                <td>{{ $item->stock->location_code }}</td>
                                                                <td>{{ $item->qty . ' ' . $item->stock->unit }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @if ($cargo->count() > 0)
                                                    <div class="float-right btnKonfirm">
                                                        <a href="#"
                                                            onclick="confirmation('order-detail', '{{ $header->id }}')"
                                                            class="btn btn-info btn-lg mt-4"><i
                                                                class="flaticon2-checkmark"></i>
                                                            Konfirm
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade  {{ $menu_picking ? ' show active' : '' }}" id="picking"
                                    role="tabpanel" aria-labelledby="orderDetail">
                                    <div class="card-body p-0">
                                        <form action="{{ route('postPicking') }}" method="post" id="postPicking">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="float-left">
                                                        <a href="#" class="btn btn-lg bg-light-info mb-4"
                                                            style="border-radius: 15px;"
                                                            onclick="report('picking', '{{ $header->id }}')">
                                                            <i class="flaticon2-print"></i>
                                                            Picking Report
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <table class="table table-bordered">
                                                        <tr class="text-center" style="background-color: coral">
                                                            <th colspan="6" class="text-white">PICKING DETAIL</th>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <th>NO</th>
                                                            <th>
                                                                <div class="form-check">
                                                                    <input id="my-input"
                                                                        class="form-check-input selectAll" type="checkbox"
                                                                        name="" value="true">
                                                                </div>
                                                            </th>
                                                            <th>ID CARGO</th>
                                                            <th>SKU</th>
                                                            <th>LOCATION</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                        <tbody>
                                                            @foreach ($cargo->where('status', 3)->where('picking_flag', 'No') as $item)
                                                                <tr class="text-center">
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <input id="my-input" class="form-check-input"
                                                                            type="checkbox" name="id[]"
                                                                            value="{{ $item->id . '-' . $item->id_stock }}">
                                                                    </td>
                                                                    <td>{{ $item->stock->id_cargo }}</td>
                                                                    <td>{{ $item->stock->sku }}</td>
                                                                    <td>{{ $item->stock->location_code }}</td>
                                                                    <td>
                                                                        <input type="number"
                                                                            class="form-control qtyValue" required
                                                                            name="qty[]" placeholder="Silahkan isi"
                                                                            value="{{ $item->qty }}"
                                                                            autocomplete="off" readonly />
                                                                        <input type="hidden" name="id_header"
                                                                            value="{{ $header->id }}">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <div class="float-right">
                                                        <button type="submit" class="btn btn-info btn-lg mt-4"><i
                                                                class="flaticon2-checkmark"></i>
                                                            Process
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ $menu_scan ? ' show active' : '' }}" id="scan"
                                    role="tabpanel" aria-labelledby="scan">
                                    <div class="card-body p-0">
                                        <form action="{{ route('scanByPass') }}" method="post" id="scanByPass">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="float-left">
                                                        <a href="#" class="btn btn-lg bg-light-warning mb-4"
                                                            style="border-radius: 15px;"
                                                            onclick="report('scan', '{{ $header->id }}')">
                                                            <i class="fas fa-barcode"></i>
                                                            Scan Report
                                                        </a>
                                                    </div>
                                                    <table class="table table-hover">
                                                        <tr class="text-center" style="background-color: darksalmon">
                                                            <th colspan="6" class="text-white">LIST SCAN</th>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <th>STATUS</th>
                                                            <th>
                                                                <div class="form-check">
                                                                    <input id="my-input"
                                                                        class="form-check-input selectAll" type="checkbox"
                                                                        name="" value="true">
                                                                </div>
                                                            </th>
                                                            <th>ID CARGO</th>
                                                            <th>SKU</th>
                                                            <th>LOCATION</th>
                                                            <th>QTY</th>
                                                        </tr>
                                                        <tbody>
                                                            @foreach ($cargo->where('picking_flag', 'Yes')->where('scan_flag', 'No') as $item)
                                                                <tr class="text-center">
                                                                    <td>
                                                                        @if ($item->scan_flag == 'No')
                                                                            <span class="badge badge-pill badge-danger">
                                                                                <i
                                                                                    class="flaticon2-hourglass-1 text-white">
                                                                                </i>
                                                                                Waiting..
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-pill badge-info">
                                                                                <i class="fas fa-check-circle-o"></i>
                                                                                Done
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <input id="my-input" class="form-check-input"
                                                                            type="checkbox" name="id[]"
                                                                            value="{{ $item->id }}">
                                                                    </td>
                                                                    <td>{{ $item->stock->id_cargo }}</td>
                                                                    <td>{{ $item->stock->sku }}</td>
                                                                    <td>{{ $item->stock->location_code }}</td>
                                                                    <td>{{ $item->qty }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <div class="float-right">
                                                        <button type="submit" class="btn btn-lg btn-info"><i
                                                                class="fas fa-users-cog"></i> Bypass</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="cancel" role="tabpanel" aria-labelledby="cancel">
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-bordered">
                                                    <tr class="text-center" style="background-color: rgb(207, 173, 110)">
                                                        <th colspan="6" class="text-white">CANCEL CARGO</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>NO</th>
                                                        <th>
                                                            <i class="fas fa-tools text-dark"></i>
                                                        </th>
                                                        <th>ID CARGO</th>
                                                        <th>SKU</th>
                                                        <th>LOCATION</th>
                                                        <th>QTY</th>
                                                    </tr>
                                                    <tbody>
                                                        @foreach ($cargo->whereNull('confirmed_at')->whereNull('scan_at') as $item)
                                                            <tr class="text-center">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <a href="#"
                                                                        onclick="cancelCargo('{{ $item->id }}')"
                                                                        class="btn btn-sm btn-danger"><i
                                                                            class="fas fa-window-close"></i> </a>
                                                                </td>
                                                                <td>{{ $item->stock->id_cargo }}</td>
                                                                <td>{{ $item->stock->sku }}</td>
                                                                <td>{{ $item->stock->location_code }}</td>
                                                                <td>{{ $item->qty }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="despatch" role="tabpanel"
                                    aria-labelledby="despatch">
                                    <div class="card-body p-0">
                                        @if ($despatch->count() > 0)
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Store Name</th>
                                                                <th scope="col">Carrier Name</th>
                                                                <th scope="col">Vehicle No</th>
                                                                <th scope="col">Container No</th>
                                                                <th scope="col">ETD</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($despatch as $item)
                                                                <tr>
                                                                    <th scope="row">
                                                                        <a class="btn btn-sm btn-dark" href="#"
                                                                            onclick="editDespatch('{{ $item->id_header }}')"><i
                                                                                class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="#"
                                                                            onclick="report('despatch','{{ $item->id_header }}')"
                                                                            class="btn btn-sm btn-info"><i
                                                                                class="fas fa-print"></i></a>
                                                                    </th>
                                                                    <td>{{ $item->store_name }}</td>
                                                                    <td>{{ $item->carrier_name }}</td>
                                                                    <td>{{ $item->vehicle_no }}</td>
                                                                    <td>{{ $item->container_no }}</td>
                                                                    <td>{{ formatTanggalIndonesia2($item->etd) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="appendEditDespatch">

                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="">
                                                <form action="{{ route('postDespatch') }}" method="post"
                                                    autocomplete="off" id="postDespatch">
                                                    @csrf
                                                    <input type="hidden" name="id_header"
                                                        value="{{ $header->id }}" />
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">Customer Name</label>
                                                                <input id="my-input" class="form-control" disabled
                                                                    type="text" name="id_customer"
                                                                    value="{{ $customer->where('id', $header->id_customer)->first()->name }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">DO Number</label>
                                                                <input id="my-input" class="form-control" disabled
                                                                    type="text" name="reference_number" required
                                                                    value="{{ $header->do_no }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">PO Number</label>
                                                                <input id="my-input" class="form-control" disabled
                                                                    type="text" name="reference_number" required
                                                                    value="{{ $header->po_no }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="my-input">Store Name</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="store_name" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="my-input">Reference Number.</label>
                                                                <input id="my-input" class="form-control" type="number"
                                                                    name="ref_number" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="my-textarea">Store Address</label>
                                                                <textarea id="my-textarea" class="form-control" name="store_address" required placeholder="Silahkan isi.."
                                                                    rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">Carrier Name</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="carrier_name" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-input">ETD</label>
                                                                <input id="my-input" class="form-control" type="date"
                                                                    name="etd" value="{{ date('Y-m-d') }}" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">Vehicle No</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="vehicle_no" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-input">AWB No</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="awb_no" required placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="my-input">Driver Name</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="driver_name" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-input">AWB Date</label>
                                                                <input id="my-input" class="form-control" type="date"
                                                                    name="awb_date" value="{{ date('Y-m-d') }}" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="my-select">Vehicle Type</label>
                                                                <select class="form-control" name="vehicle"
                                                                    id="my-select" required>
                                                                    <option value="" selected disabled>Silahkan
                                                                        Pilih
                                                                    </option>
                                                                    @foreach ($vehicle as $item)
                                                                        <option value="{{ $item->name }}">
                                                                            {{ $item->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-input">Container No</label>
                                                                <input id="my-input" class="form-control" type="text"
                                                                    name="container_no" value="0" required
                                                                    placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="my-select">Size</label>
                                                                <select class="form-control" name="size"
                                                                    id="my-select" required>
                                                                    <option value="" selected disabled>Silahkan
                                                                        Pilih
                                                                    </option>
                                                                    @foreach ($vehicleSize as $item)
                                                                        <option value="{{ $item->name }}">
                                                                            {{ $item->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-input">Send Date Doc.</label>
                                                                <input id="my-input" class="form-control" type="date"
                                                                    name="send_date_doc" value="{{ date('Y-m-d') }}"
                                                                    required placeholder="Silahkan isi..">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="float-right">
                                                            <button type="submit" class="btn btn-lg btn-info"><i
                                                                    class="fas fa-truck"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ $menu_confirm ? ' show active' : '' }}" id="confirm"
                                    role="tabpanel" aria-labelledby="confirm">
                                    <div class="card-body p-0">
                                        @if ($header->loading_start == null)
                                            <form action="{{ route('submitLoading') }}" method="post" id="loadingForm"
                                                autocomplete="off">
                                                @csrf
                                                <input type="hidden" name="id_header" value="{{ $header->id }}" />
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Shipment Arrival Date:</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Silahkan isi" name="shipment_arrival_date"
                                                                required id="shipmentArrival" />
                                                            <span class="form-text text-muted"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Loading Start</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Silahkan isi" name="unloading_start" required
                                                                id="unloadingStart" />
                                                            <span class="form-text text-muted"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Loading Finish</label>
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
                                        @else
                                            <form action="{{ route('confirmOutbound') }}" method="post"
                                                id="confirmOutbound">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-bordered">
                                                            <tr class="text-center">
                                                                <th>NO</th>
                                                                <th>
                                                                    <div class="form-check">
                                                                        <input id="my-input"
                                                                            class="form-check-input selectAll"
                                                                            type="checkbox" name=""
                                                                            value="true">
                                                                    </div>
                                                                </th>
                                                                <th>ID CARGO</th>
                                                                <th>SKU</th>
                                                                <th>LOCATION</th>
                                                                <th>QTY</th>
                                                            </tr>
                                                            <tbody>
                                                                @foreach ($cargo->where('picking_flag', 'Yes')->where('scan_flag', 'Yes')->whereNull('confirmed_at') as $item)
                                                                    <tr class="text-center">
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <input id="my-input" class="form-check-input"
                                                                                type="checkbox" name="id[]"
                                                                                value="{{ $item->id . '-' . $item->id_stock }}">
                                                                            <input type="hidden" name="id_header"
                                                                                value="{{ $header->id }}">
                                                                        </td>
                                                                        <td>{{ $item->stock->id_cargo }}</td>
                                                                        <td>{{ $item->stock->sku }}</td>
                                                                        <td>{{ $item->stock->location_code }}</td>
                                                                        <td>{{ $item->qty }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <div class="float-right">
                                                            @if ($btn_confirm)
                                                                <button type="submit" class="btn btn-lg btn-info"><i
                                                                        class="fas fa-truck"></i> Submit</button>
                                                            @else
                                                                <a class="btn btn-lg btn-primary"
                                                                    onclick="alert('Menunggu Scanner Checker..')"><i
                                                                        class="flaticon2-hourglass-1 text-white">
                                                                    </i>
                                                                    Waiting..
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
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


    <div id="addDetail" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="text" value="" class="form-control mb-2 cargoID" autofocus
                                        autocomplete="off" placeholder="Please input your Cargo id..">
                                    <hr>
                                </div>
                            </div>
                            <form action="{{ route('storeOrderDetail') }}" method="post" id="storeOrderDetail">
                                @csrf
                                <input type="hidden" name="id_header" value="{{ $header->id }}" />
                                <input type="hidden" name="id_warehouse" value="{{ $header->id_warehouse }}" />
                                <input type="hidden" name="id_customer" value="{{ $header->id_customer }}" />

                                <table class="table table-bordered">
                                    <tr class="text-center">
                                        <th>
                                            <div class="form-check">
                                                <input id="my-input" class="form-check-input selectAll" type="checkbox"
                                                    name="" value="true">
                                            </div>
                                        </th>
                                        <th>CARGO ID</th>
                                        <th>DESCRIPTION</th>
                                        <th>QTY</th>
                                        <th>LOCATION</th>
                                    </tr>
                                    <tbody id="tableListDetail">

                                    </tbody>
                                </table>
                                <div class="float-right submitOrderDetail">

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
        $(document).on("keydown", "form", function(event) {
            return event.key != "Enter";
        });

        $('#shipmentArrival').datetimepicker();
        $('#unloadingStart').datetimepicker();
        $('#unloadingFinish').datetimepicker();

        function report(type, id) {
            window.open("{{ url('crossDock/outbound/report') }}/" + type + "/" + id);
        }

        function editDespatch(id) {
            $('.appendEditDespatch').html("")
            $.ajax({
                url: "{{ url('crossDock/outbound/editDespatch/') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('.appendEditDespatch').append(`
                        <form action="{{ route('updateDespatch') }}" method="post" autocomplete="off" id="updateDespatch">
                            @csrf
                            <input type="hidden" name="id_header"value="{{ $header->id }}" />
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">Store Name</label>
                                        <input id="my-input" class="form-control" type="text"
                                            name="store_name" required value="${data.store_name}"
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">Reference Number.</label>
                                        <input id="my-input" class="form-control" type="number"
                                            name="ref_number" required value="${data.ref_number}"
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="my-textarea">Store Address</label>
                                        <textarea id="my-textarea" class="form-control" name="store_address" required rows="3">${data.store_address}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="my-input">Customer Name</label>
                                        <input id="my-input" class="form-control" disabled
                                            type="text" name="id_customer"
                                            value="{{ $customer->where('id', $header->id_customer)->first()->name }}" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="my-input">DO Number</label>
                                        <input id="my-input" class="form-control" disabled
                                            type="text" name="reference_number" required
                                            value="{{ $header->do_no }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="my-input">PO Number</label>
                                        <input id="my-input" class="form-control" disabled
                                            type="text" name="reference_number" required
                                            value="{{ $header->po_no }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-input">Carrier Name</label>
                                        <input id="my-input" class="form-control" type="text"
                                            name="carrier_name" required  value="${data.carrier_name}"
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                       <div class="form-group">
                                        <label for="my-input">ETD</label>
                                        <input id="my-input" class="form-control" type="date"
                                            name="etd" value="${data.etd}"  required
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-input">AWB No</label>
                                        <input id="my-input" class="form-control" type="text" value="${data.awb_no}"
                                            name="awb_no" required placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                   <div class="form-group">
                                        <label for="my-input">AWB Date</label>
                                        <input id="my-input" class="form-control" type="date"
                                            name="awb_date" value="${data.awb_date}" required
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-input">Vehicle No</label>
                                        <input id="my-input" class="form-control" type="text"
                                            name="vehicle_no" required value="${data.vehicle_no}"
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-select">Vehicle Type</label>
                                        <select class="form-control" name="vehicle"
                                            id="my-select" required>
                                            <option value="" selected disabled>Silahkan
                                                Pilih
                                            </option>
                                            @foreach ($vehicle as $item)
                                                <option value="{{ $item->name }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-select">Size</label>
                                        <select class="form-control" name="size"
                                            id="my-select" required>
                                            <option value="" selected disabled>Silahkan
                                                Pilih
                                            </option>
                                            @foreach ($vehicleSize as $item)
                                                <option value="{{ $item->name }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="my-input">Driver Name</label>
                                        <input id="my-input" class="form-control" type="text" value="${data.driver_name}"
                                            name="driver_name" required
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">Container No</label>
                                        <input id="my-input" class="form-control" type="text"
                                            name="container_no" value="0" required value="${data.container_no}"
                                            placeholder="Silahkan isi..">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">Send Date Doc.</label>
                                        <input id="my-input" class="form-control" type="date"
                                            name="send_date_doc" value="${data.send_date_doc}"
                                            required placeholder="Silahkan isi..">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info"><i
                                            class="fas fa-truck"></i> Submit</button>
                                </div>
                            </div>
                        </form>`)
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error',
                    })
                }
            });

        }

        $(".selectAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('.cargoID').keypress(function(e) {
            if (e.which == 13) {
                var cargo_id = $(this).val();
                $.ajax({
                    url: "{{ url('crossDock/outbound/getStock') }}/" + cargo_id + '/' +
                        "{{ $header->id_warehouse }}" + '/' + "{{ $header->id_customer }}" + '/' +
                        "{{ $header->id_branch }}" + '/' + "{{ $header->id }}",
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('#tableListDetail').html("")
                        if (data.length > 0) {
                            $('.submitOrderDetail').html("")
                            $.each(data, function(key, value) {
                                $('#tableListDetail').append(`
                                <tr class="text-center">
                                    <td>
                                        <input id="my-input" class="form-check-input" type="checkbox"
                                                name="id[]" value="${value.id}">
                                    </td>
                                    <td>${value.id_cargo}</td>
                                    <td>${value.description}</td>
                                    <td>${value.on_actual}</td>
                                    <td>${value.location_code}</td>
                                </tr>
                            `)
                            })
                            $('.submitOrderDetail').append(
                                `<button type="submit" class="btn btn-lg btn-info mt-4" id="btnSubmitOrder"><i class="fas fa-save"></i> Submit</button>`
                            )
                        } else {
                            $('.submitOrderDetail').html("")
                            Swal.fire({
                                icon: 'warning',
                                title: 'ID Cargo not found..',
                            })
                        }
                    },
                    error: function(error) {
                        $('.submitOrderDetail').html("");
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        })
                    }
                });
            }
        });

        function cancelCargo(id) {
            deleteCargo(id);
        }

        function deleteCargo(id) {
            Swal.fire({
                title: 'Do you want to delete this cargo?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, delete this cargo',
                denyButtonText: 'Cancel',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    location.href = "{{ url('crossDock/outbound/deleteCargo') }}/" + id;
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        function modeEdit() {
            $('.btnEdit').removeClass('hide')
            $('.qtyValue').attr('readonly', false)
            $('.btnKonfirm').addClass('hide')

            $('.modeEdit').html("")
            $('.modeEdit').append(`
                <button type="submit" class="btn btn-info btn-lg mt-4"><i class="flaticon-user-ok"></i>Update</button>
            `)
        }

        $('#scanByPass').on('submit', function(e) {
            e.preventDefault();
            var data = $('#scanByPass').serialize();
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

        $('#loadingForm').on('submit', function(e) {
            e.preventDefault();
            var data = $('#loadingForm').serialize();
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

        $('#postDespatch').on('submit', function(e) {
            e.preventDefault();
            var data = $('#postDespatch').serialize();
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

        $('#storeOrderDetail').on('submit', function(e) {
            e.preventDefault();
            var data = $('#storeOrderDetail').serialize();
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

        $('#updateCargoOutbound').on('submit', function(e) {
            e.preventDefault();
            var data = $('#updateCargoOutbound').serialize();
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

        $('#confirmOutbound').on('submit', function(e) {
            e.preventDefault();
            var data = $('#confirmOutbound').serialize();
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

        $('#postPicking').on('submit', function(e) {
            e.preventDefault();
            var data = $('#postPicking').serialize();
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

        function confirmation(value, idheader) {
            Swal.fire({
                title: 'Are you sure you want to confirm?',
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm',
                denyButtonText: 'Cancel',
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('crossDock/outbound/confirmation') }}/" + value + '/' + idheader,
                        type: "GET",
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
                } else if (result.isDenied) {
                    return false;
                }
            })
        }
    </script>
@endpush
