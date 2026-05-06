@extends('layouts.main')

@section('title')
    Export - Inbound
@endsection

<style>
    .page {
        width: 125mm;
        min-height: 148mm;
        padding: 3mm;
        margin: 5mm auto;
        border: 1px #333 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        page-break-after: always;
        page-break-inside: avoid;
    }

    .container {
        width: 100%;
        margin: 0px auto;
    }

    .row:after {
        content: "";
        display: grid;
        clear: both;
    }

    .column {
        float: left;
        padding-bottom: 5px;
    }

    .col-100 {
        width: 100%;
    }

    .center {
        text-align: center;
    }

    .hide {
        display: none;
    }

    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #0C9;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
    }

    .my-float {
        margin-top: 22px;
    }

    .float .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        top: -5%;
        right: 105%;
        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .float:hover .tooltiptext {
        visibility: visible;
    }

    #ajax-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: black;
        /* Ganti ke white jika mau putih */
        opacity: 0.8;
        z-index: 9999;
    }

    #modal-images .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        grid-gap: 8px;
        padding: 5px;
    }

    #modal-images .gallery img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        transition: .3s;
    }

    #modal-images .gallery img:hover {
        transform: scale(1.05);
    }
</style>

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - Inbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Export - Inbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-job" method="POST">
                @csrf
                <input type="hidden" id="id" name="id"
                    @isset($header->id) value="{{ $header->id }}" @endisset>
                <div class="row info-wrap p-3 m-3" style="border-radius: 13px; text-shadow: 13px;">
                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_no">Job Number</label>
                                    <input type="text" autocomplete="off" id="Job_no" name="job_no"
                                        value="{{ $header->job_no }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_date">Job Date</label>
                                    <input type="text" autocomplete="off" id="job_date" name="job_date"
                                        value="{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select id="my-select" required class="form-control" name="branch_id" readonly>
                                        <option value="{{ $branchme->id }}" readonly>{{ $branchme->branch_name }} </option>
                                    </select>
                                </div>
                            </div>
                            @if ($detail->count() > 0 and $header->status_flag == 'Open')
                                <div class="col-sm-6 mb-2">
                                    <a class="btn btn-info btn-block text-white" href="#addQty" data-toggle="modal"><i
                                            class="fas fa-plus-circle"></i> Weight
                                    </a>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <a onclick="showImages()" class="btn btn-primary btn-block text-white"><i
                                            class="fas fa-images"></i> Image
                                    </a>
                                </div>
                                @if ($header->checker_flag == 'Confirmed')
                                    <div class="col-sm-12">
                                        <a onclick="backtoChecker()"
                                            class="btn btn-danger text-white btn-block mb-3 mt-2"><i class="fas fa-reply"
                                                style="border-radius: 12px;"></i> Return to checker
                                        </a>
                                    </div>
                                @endif
                            @endif
                            <div class="col-sm-12">
                                <a id="pallet-print" class="btn btn-dark text-white btn-block"><i class="fas fa-print"
                                        style="border-radius: 12px;"></i> Pallet Tag Print
                                </a>
                                @can('button-confirm-export', 'button-confirm-export')
                                    @if ($header->status_flag == 'Confirmed')
                                        <a href="javascript:void(0)" onclick="editPalletize('{{ $header->id }}')"
                                            class="btn btn-secondary btn-block"><i class="fas fa-clipboard-list"></i> <span>
                                                Edit
                                                Palletize</span>
                                        </a>
                                    @endif
                                @endcan
                                <hr>
                                @can('button-confirm-export', 'button-confirm-export')
                                    @if ($detail->count() > 0 and $header->status_flag == 'Open')
                                        @if ($header->checker_flag == 'Confirmed')
                                            @if (!is_null($header->remarks))
                                                <a href="javascript:void(0)" onclick="submitData()"
                                                    class="btn btn-success btn-block"><i class="fas fa-check-circle"></i> <span>
                                                        Konfirm</span>
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                @endcan
                                @if ($header->status_flag == 'Confirmed')
                                    <a href="javascript:void(0)" id="tally-print-detail" class="btn btn-block text-white"
                                        style="background-color: #DD1C21"><i class="fas fa-file-pdf"></i> <span>
                                            Tally Sheet (PDF)</span>
                                    </a>
                                    <a href="javascript:void(0)" id="tally-print-download"
                                        class="btn btn-block btn-success text-white"><i class="fas fa-file-excel"></i>
                                        <span>
                                            Tally Sheet (Excel)
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="hidden" id="forwarder_id" name="forwarder_id"
                                        @isset($header->forwarder_id) value="{{ $header->forwarder_id }}" @endisset>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.."
                                        id="forwarder_name" name="forwarder_name" class="form-control" required
                                        @isset($header->forwarder_name) value="{{ $header->forwarder_name }}" @endisset
                                        @isset($header->id)  @endisset
                                        @if ($header->status_flag == 'Confirmed') readonly @endif>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Shipper Name</label>
                                    <input type="hidden" id="shipper_id" name="shipper_id"
                                        @isset($header->shipper_id) value="{{ $header->shipper_id }}" @endisset>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="shipper_name" name="shipper_name" class="form-control"
                                        @isset($header->shipper_name) value="{{ $header->shipper_name }}" @endisset
                                        @isset($header->id) @endisset
                                        @if ($header->status_flag == 'Confirmed') readonly @endif>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Consignee Name</label>
                                    <input type="hidden" id="consignee_id" name="consignee_id"
                                        @isset($header->consignee_id) value="{{ $header->consignee_id }}" @endisset>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="consignee_name" name="consignee_name" class="form-control"
                                        @isset($header->consignee_name) value="{{ $header->consignee_name }}" @endisset
                                        @isset($header->id) @endisset
                                        @if ($header->status_flag == 'Confirmed') readonly @endif>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PEB No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="peb_no"
                                        name="peb_no" class="form-control" required value="{{ $header->peb_no }}"
                                        @if ($header->status_flag == 'Confirmed') readonly @endif />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>AJU No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="aju_no"
                                        name="aju_no" class="form-control" required value="{{ $header->aju_no }}"
                                        @if ($header->status_flag == 'Confirmed') readonly @endif />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="vehicle_no_by_ao">Vehicle No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="vehicleNumber" value="{{ $header->vehicle_no_by_ao }}"
                                        class="form-control" @isset($header) readonly @endisset />
                                             <input type="hidden" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="vehicle_no" value="{{ $header->vehicle_no }}"
                                        class="form-control" @isset($header) readonly @endisset />
                                </div>
                            </div>
                               
                                <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="gate_in_by_ao">Gate In</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="gateIn" value="{{ $header->gate_in_by_ao }}"
                                        class="form-control" @isset($header) readonly @endisset />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>VGM (kg)</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vgm" name="vgm" class="form-control"
                                        value="{{ $header->vgm }}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="destination" name="destination" class="form-control"
                                        value="{{ $header->destination }}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Final Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="final_destination" name="final_destination" class="form-control"
                                        value="{{ $header->final_destination }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Checker</label>
                                    <select id="checkerName" required class="form-control" name="pic_name"
                                        style="background-color: bisque;">
                                        @if (!is_null($header->pic_name))
                                            <option value="{{ Str::upper($header->pic_name) }}" selected>
                                                {{ Str::upper($header->pic_name) }}
                                            @else
                                            <option value="" selected disabled> Silahkan Pilih </option>
                                        @endif
                                        @foreach ($checker as $item)
                                            <option value="{{ $item->username }}">{{ $item->name }}
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if ($header->status_flag == 'Confirmed')
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Stapel</label>
                                        <select class="form-control" name="stapel_name"
                                            onchange="updateStaple(this.value)" style="background-color: bisque;"
                                            id="stapelSelect">
                                            @if (is_null($header->stapel_name))
                                                <option value="" disabled selected>Silahkan Pilih</option>
                                            @else
                                                <option value="{{ Str::upper($header->stapel_name) }}" selected>
                                                    {{ Str::upper($header->stapel_name) }}
                                            @endif
                                            @foreach ($stapel as $item)
                                                <option value="{{ $item->username }}">{{ $item->name }}
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            {{-- @if ($qty_actual != $header->qty_cargo and $header->checker_confirmed_flag != null)
                                <div class="col-sm-12">
                                    <div class="alert alert-warning" role="alert">
                                        <h4 class="alert-heading">Peringatan!</h4>
                                        Qty document tidak sama dengan dengan qty actual,
                                        <a class="alert-link">Hubungi IT jika ada perubahan data atau abaikan pesan ini
                                            jika kondisi actual sudah sesuai
                                        </a>.
                                    </div>
                                </div>
                            @endif --}}
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <a class="btn btn-link" data-toggle="collapse" data-target="#draft"
                                                    aria-expanded="true" aria-controls="draft">
                                                    CRS Draft Version <i class="fa fa-arrow-circle-down"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="draft" class="collapse show" aria-labelledby="headingOne"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No.</th>
                                                            <th>PO No.</th>
                                                            <th>Qty</th>
                                                            <th>Vol. CBM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $row_count = count($draft['po_number']);
                                                            $totalQty = 0;
                                                            $totalVol = 0;
                                                        @endphp
                                                        @for ($i = 0; $i < $row_count; $i++)
                                                            <tr class="text-center">
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $draft['po_number'][$i] }}</td>
                                                                <td>{{ $draft['qty_cargo'][$i] }}</td>
                                                                <td>{{ $draft['cbm'][$i] }}</td>
                                                                @php
                                                                    $totalQty += $draft['qty_cargo'][$i];
                                                                    $totalVol += $draft['cbm'][$i];
                                                                @endphp
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="text-center">
                                                            <td colspan="2"><strong>SUMMARY</strong></td>
                                                            <td><strong>{{ $totalQty }}</strong></td>
                                                            <td><strong>{{ number_format($totalVol, 3, '.', '') }}</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <a class="btn btn-link" data-toggle="collapse" data-target="#checker"
                                                    aria-expanded="true" aria-controls="checker">
                                                    CRS Checker Version <i class="fa fa-arrow-circle-down"></i>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="checker" class="collapse show" aria-labelledby="headingOne"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Pallet ID</th>
                                                            <th>PO No.</th>
                                                            <th>Qty</th>
                                                            <th>P</th>
                                                            <th>L</th>
                                                            <th>T</th>
                                                            <th>Vol. CBM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $groupedItems = $detail
                                                                ->sortBy('pallet_id')
                                                                ->groupBy('pallet_id')
                                                                ->map(function ($items) {
                                                                    return $items->groupBy(function ($item) {
                                                                        return explode('-', $item->serial_no)[0];
                                                                    });
                                                                });

                                                            $totalQuantity = 0;
                                                            $totalVolume = 0;
                                                        @endphp

                                                        @foreach ($groupedItems as $palletId => $poGroups)
                                                            @foreach ($poGroups as $poNumber => $items)
                                                                @php
                                                                    $rowspan = $items->count();
                                                                    $groupQuantity = 0;
                                                                    $groupVolume = 0;
                                                                @endphp

                                                                @foreach ($items as $index => $item)
                                                                    @php
                                                                        $volume =
                                                                            (($item->length *
                                                                                $item->width *
                                                                                $item->height) /
                                                                                1000000) *
                                                                            $item->quantity;
                                                                        $groupQuantity += $item->quantity;
                                                                        $groupVolume += $volume;
                                                                        $totalQuantity += $item->quantity;
                                                                        $totalVolume += $volume;
                                                                    @endphp
                                                                    <tr class="text-center">
                                                                        @if ($index == 0)
                                                                            <td rowspan="{{ $rowspan }}">
                                                                                {{ $palletId }}</td>
                                                                            <td rowspan="{{ $rowspan }}">
                                                                                {{ strtoupper($poNumber) }}</td>
                                                                        @endif
                                                                        <td>{{ $item->quantity . ' ' . $item->unit }}</td>
                                                                        <td>{{ $item->length }}</td>
                                                                        <td>{{ $item->width }}</td>
                                                                        <td>{{ $item->height }}</td>
                                                                        <td>{{ number_format($volume, 3, '.', '') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="text-center">
                                                            <td colspan="2"><strong>SUMMARY</strong></td>
                                                            <td><strong>{{ $totalQuantity }}</strong></td>
                                                            <td colspan="3"><strong></td>
                                                            <td><strong>{{ number_format($totalVolume, 3, '.', '') }}</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('modal')
    <div class="modal fade" role="dialog" id="addQty">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            @isset($header)
                                <section id="contact" class="contact">
                                    <form action="{{ url('export/inbound/updateWeight') }}" method="post">
                                        @csrf
                                        <input type="hidden" id="job_id" name="job_id"
                                            @isset($header->id) value="{{ $header->id }}" @endisset />
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-sm" style="width:100%;">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Pallet ID</th>
                                                        <th>Quantity</th>
                                                        <th>Weight</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($detail as $key => $value)
                                                        <tr class="text-center">
                                                            <td class="text-center">{{ $value->pallet_id }}</td>
                                                            <td class="text-center">{{ $value->quantity }}</td>
                                                            <td class="text-center">
                                                                <input type="number" autocomplete="off" name="weight[]"
                                                                    value="{{ $value->weight }}" class="form-control"
                                                                    required>
                                                                <input type="hidden" autocomplete="off"
                                                                    name="id_detail_inbound[]" value="{{ $value->id }}"
                                                                    class="form-control">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($header->status_flag == 'Open' and $detail->count() > 0)
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-md btn-success"><i
                                                        class="fas fa-save"></i> Submit
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                </section>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="pallet_tag_preview">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5> Pallet Tag Preview </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page">
                                <div class="container">
                                    <div class="row">
                                        <div class="column col-100">
                                            <table class="table">
                                                <tr>
                                                    <td colspan="2">
                                                        <img alt="image" src="{{ asset('images/logos.png') }}"
                                                            alt="" height="25pt">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="center">
                                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Preview', 'QRCODE', 4, 4) }}"
                                                            alt="barcode" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Forwarder Name</small><br>
                                                        <b class="forwardertext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Shipper Name</small><br>
                                                        <b class="shippertext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>PO Number</small>
                                                        <br>
                                                        <b class="POtext"></b></span>
                                                    </td>
                                                    <td>
                                                        <small>PEB Number</small><br>
                                                        <b class="pebtext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Consignee Name</small>
                                                        <br>
                                                        <b class="consigneetext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <small>Destination</small><br>
                                                        <span style="font-size: 20px;">
                                                            <b class="destinationtext"></b></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>Quantity</small><br>
                                                        <b class="qty_actualtext"></b> <b>of</b> <b
                                                            class="qty_cargotext"></b>
                                                    </td>
                                                    <td>
                                                        <small>Total Pallet</small><br>
                                                        <b class="total_pallettext"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <small>Checker</small><br>
                                                        <b class="checkertext"></b>
                                                    </td>
                                                    <td>
                                                        <small>Tanggal Bongkar</small><br>
                                                        <b class="tanggal_bongkartext"></b>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table>
                                                <tr style="border-style : hidden;">
                                                    <td colspan="2">
                                                        <span style="font-size: 9px; margin-left: 21em;"><b>PT. MASAJI
                                                                KARGOSENTRA TAMA<b></small>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <a data-dismiss="modal" class="btn btn-primary btn-sm text-white" onclick="klikOK()"><i
                            class="fas fa-check"></i>
                        <span>OK</span></a>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="update_palletize" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <section id="contact" class="contact">
                                <form action="{{ url('export/inbound/update_palletize') }}" method="post"
                                    id="updatePalletize">
                                    @csrf
                                    <input type="hidden" id="job_id" name="job_id" value="{{ $header->id }}" />
                                    <input type="hidden" id="job_id" name="peb_no"
                                        value="{{ $header->peb_no }}" />
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm" style="width:100%;"
                                            id="table_palletize">
                                            <div class="mb-2">
                                                <button type="button" class="btn btn-sm btn-primary btn-add"
                                                    onclick="addRow()" style="display: none;">
                                                    <i class="fas fa-plus"></i> Add Row
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark" onclick="coLoud()">
                                                    <i class="fas fa-pencil-alt"></i> Make Coloud
                                                </button>
                                            </div>
                                            <thead>
                                                <tr class="text-center">
                                                    <th>PO No</th>
                                                    <th>Pallet ID</th>
                                                    <th>Quantity</th>
                                                    <th>L</th>
                                                    <th>W</th>
                                                    <th>H</th>
                                                    <th>Uom</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-md btn-success btn-update"><i
                                                class="fas fa-save"></i>
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="modal-images" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-body">
                        <div class="gallery"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($header != null)
        <a href="javascript:void(0)" class="float" onclick="updateHeader()">
            <i class="fa fa-edit my-float"></i>
            <span class="tooltiptext">Update Header</span>
        </a>
    @endif
@endsection

@push('scripts')
    <script>
        function setQtyCargo(val) {
            $('#qty_cargo').val(val);
        }

        function setQtyActual(value) {
            $('#qty_actual').val(value);
        }

        $('#updatePalletize').on('submit', function(e) {
            e.preventDefault(); // Hindari reload form default
            $('.btn-update').hide(); // Sembunyikan tombol saat submit
            let form = $(this);
            let formData = new FormData(this);
            $.ajax({
                url: form.attr('action'), // pastikan action sudah di-set di form
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#ajax-overlay').show(); // Munculkan overlay
                },
                success: function(response) {
                    if (response.success) {
                        // Reload halaman jika sukses
                        location.reload();
                    } else if (response.error) {
                        alert('Error: ' + response.error);
                        $('.btn-update').show(); // Tampilkan kembali tombol
                    }
                },
                complete: function() {
                    $('#ajax-overlay').hide(); // Sembunyikan overlay setelah selesai
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan pada server.');
                    $('.btn-update').show(); // Tampilkan kembali tombol
                }
            });
        });

        function showImages() {
            var job_id = '{{ $header->id }}';
            $.ajax({
                url: "{{ url('/export/inbound/showImages') }}/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {

                    let container = $('#modal-images .modal-body .gallery');
                    container.empty();

                    $.each(response.data, function(index, item) {
                        var url =
                            `{{ asset('public/foto/warehouse-export/inbound-cargo/') }}/${item.file}`;
                        container.append(`
                            <img src="${url}" data-id="${item.id}" style="cursor:pointer;max-width:130px;margin:5px;border-radius:6px;"
                                onclick="deleteImage(${item.id})">
                        `);
                    });
                    $('#modal-images').modal('show');
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function deleteImage(id) {

            if (!confirm('Yakin hapus foto ini?')) return;
            $.ajax({
                url: "{{ url('/export/inbound/deleteImage') }}/" + id,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(res) {
                    showImages();
                },
                error: function() {
                    alert("Gagal hapus");
                }
            })
        }


        function backtoChecker() {
            confirmation = confirm("Are you sure want to return this job to checker?");
            if (confirmation) {
                executeBacktoChecker();
            } else {
                return false;
            }

        }

        function executeBacktoChecker() {
            var job_id = '{{ $header->id }}';
            $.ajax({
                url: "{{ url('/export/inbound/backtoChecker') }}/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    alert('Job has been returned to checker successfully.');
                    location.reload();
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function editPalletize(job_id) {
            $.ajax({
                url: "{{ url('/export/inbound/getPalletize') }}/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#table_palletize tbody').empty();

                    $.each(response, function(index, item) {
                        var row = `
                    <tr class="text-center">
                          <td>
                            <input type="text" class="form-control" hidden name="location_id[]" value="${item.location_id ?? 0}">
                            <input type="text" class="form-control" hidden name="location_code[]" value="${item.location_code ?? 0}">
                            <select id="my-select" required class="form-control" name="po_number[]">
                                <option value="${item.po_number}">${item.po_number}</option>
                                @foreach ($po_number as $key => $val)
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="pallet_id[]" value="${item.pallet_id ?? ''}"></td>
                        <td><input type="number" class="form-control" name="quantity[]" value="${item.quantity ?? ''}"></td>
                        <td><input type="number" class="form-control" name="length[]" value="${item.length ?? ''}"></td>
                        <td><input type="number" class="form-control" name="width[]" value="${item.width ?? ''}"></td>
                        <td><input type="number" class="form-control" name="height[]" value="${item.height ?? ''}"></td>
                        <td>
                            <select id="my-select" required class="form-control" name="unit[]">
                                <option value="${item.unit}">${item.unit}</option>
                                @foreach ($uom as $val)
                                    <option value="{{ $val }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `;
                        $('#table_palletize tbody').append(row);
                    });

                    $('#update_palletize').modal('show');
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function deleteRow(job_id, id_detail) {
            // Hapus baris (tr) dari tabel
            $(button).closest('tr').remove();
        }

        function coLoud() {
            swal({
                    title: "Apakah kamu yakin?",
                    text: "Simpan Tally yang lama sebelum melakukan aksi ini, karena data lama akan di hapus!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Belum, Saya simpan dulu",
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, saya sudah simpan",
                            closeModal: true,
                        },
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        deleteStock();
                    } else {
                        return false;
                    }
                });
        }

        function deleteStock() {
            var job_id = '{{ $header->id }}';
            $.ajax({
                url: "{{ url('/export/inbound/deleteStock') }}/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.btn-add').show();
                    $('#update_palletize').modal('hide');
                    editPalletize(job_id);
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function addRow() {
            var row = `
        <tr class="text-center">
            <td><input type="text" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="po_number[]" value=""></td>
            <td><input type="text" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="pallet_id[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="quantity[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="length[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="width[]" value=""></td>
            <td><input type="number" required placeholder="Input here.." autocomplete="off" class="form-control form-control-sm" name="height[]" value=""></td>
            <td>
                <select id="my-select" required class="form-control" name="unit[]">
                    @foreach ($uom as $val)
                        <option value="{{ $val }}">{{ $val }}</option>
                    @endforeach
                </select>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
            $('#table_palletize tbody').append(row);
        }

        function deleteRow(button) {
            $(button).closest('tr').remove();
        }


        function updateHeader() {
            var qtyCargo = $('#qty_cargo').val();
            var qtyActual = $('#qty_actual').val();
            $.ajax({
                data: $('#form-job').serialize(),
                url: "{{ route('export-inbound.store') }}",
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

        function klikOK() {
            $('.saveJob').removeClass('hide');
        }

        function updateStaple(value) {
            var job_id = '{{ $header->id }}';
            let urlRequest = "{{ url('export/inbound/updateStaple') }}/" + job_id + "/" + value;
            if (confirm("Are You sure?") == true) {
                location.href = urlRequest;
            } else {
                $('#stapelSelect').val("")
                return false;
            }
        }

        function previewPalletTag() {
            $('#pallet_tag_preview').modal('show')
            var peb = $('#peb_no').val();
            var aju = $('#aju_no').val();
            var pebText = '';
            if (peb == 0) {
                var pebText = aju
            } else {
                var pebText = peb
            }

            $('.forwardertext').text($('#forwarder_name').val());
            $('.shippertext').text($('#shipper_name').val());
            $('.potext').text($('#po_number').val());
            $('.pebtext').text(pebText);
            $('.consigneetext').text($('#consignee_name').val());
            $('.destinationtext').text($('#destination').val());
            $('.qty_actualtext').text($('#qty_actual').val());
            $('.qty_cargotext').text($('#qty_cargo').val());
            $('.total_pallettext').text($('#total_pallet').val());
            $('.checkertext').text($('#checkerName').val());
            $('.tanggal_bongkartext').text($('#tglBongkar').val());
        }

        function qtyActual() {

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
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#forwarder_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getForwarder') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                service_name: "Export",
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#forwarder_id').val(ui.item.forwarder_id);
                        $('#forwarder_name').val(ui.item.forwarder_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.forwarder_name + "</div>")
                        .appendTo(ul);
                };

            $("#shipper_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getShipper') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#shipper_id').val(ui.item.shipper_id);
                        $('#shipper_name').val(ui.item.shipper_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.shipper_name + "</div>")
                        .appendTo(ul);
                };

            $("#consignee_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getConsignee') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#consignee_id').val(ui.item.consignee_id);
                        $('#consignee_name').val(ui.item.consignee_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.consignee_name + "</div>")
                        .appendTo(ul);
                };



            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        console.log($('#form-job').serialize());
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('export-inbound.store') }}",
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

            if ($("#form-detail").length > 0) {
                $("#form-detail").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-detail').serialize(),
                            url: "{{ route('export-detail.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-detail').trigger("reset");

                                    var oTable = $('#table-detail').dataTable();
                                    oTable.fnDraw(false);

                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                    location.reload();
                                } else {
                                    swal({
                                        icon: "error",
                                        text: data.error
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
        });

        $('body').on('click', '#pallet-print', function() {
            var data_id = $('#id').val();

            window.open("{{ url('/export/inbound/pallet-tag/') }}" + "/" + data_id, 'palletExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-detail', function() {
            var data_id = $('#id').val();

            window.open("{{ url('/export/inbound/tally_sheet/detail/') }}" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-download', function() {
            var data_id = $('#id').val();
            window.location.href = "{{ url('export/inbound/tally_sheet/download') }}/" + data_id;
        });

        $('body').on('click', '#tally-download-detail', function() {
            var data_id = $('#id').val();

            window.open("{{ url('/export/inbound/tally_sheet/detail/') }}" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        $('body').on('click', '#tally-print-summary', function() {
            var data_id = $('#id').val();

            window.open("{{ url('/export/inbound/tally_sheet/summary/') }}" + "/" + data_id,
                'tallySheetExportReport',
                'width=800,height=600')
        });

        function submitData() {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this data!",
                icon: "warning",
                buttons: [
                    'No, cancel it!',
                    'Yes, I am sure!'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        data: {
                            job_id: $("#id").val()
                        },
                        url: "{{ route('export-inbound.submit') }}",
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
                            console.log(data);
                            $("#loader").hide();
                        }
                    });
                } else {

                }
            })
        }
    </script>
@endpush
