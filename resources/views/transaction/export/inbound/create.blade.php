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

    .table {
        font-size: 18px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid rgb(0, 0, 0);
    }

    .table tbody tr td {
        text-align: left;
        padding: 5px;
        border: 1px solid rgb(0, 0, 0);
    }

    .table .center {
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
                                <div class="text-left">
                                    <a href="{{ url('/export/inbound/create/0') }}" class="btn btn-primary btn-sm"><i
                                            class="fas fa-plus"></i> <span>Add New Job</span>
                                    </a>
                                </div>
                                <hr>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_no">Job Number</label>
                                    <input type="text" autocomplete="off" id="Job_no" name="job_no"
                                        @isset($header->job_no) value="{{ $header->job_no }}" @endisset
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_date">Job Date</label>
                                    <input type="text" autocomplete="off" id="job_date" name="job_date"
                                        @isset($header->job_date) value="{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}" @endisset
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select id="my-select" required class="form-control" name="branch_id">
                                        @if ($header != null)
                                            <option value="{{ $branchme->id }}" readonly>
                                                {{ $branchme->branch_name }}
                                            </option>
                                        @else
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                            @foreach ($branch as $item)
                                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            @if (!isset($header->id))
                                <div class="col-sm-12 mb-2" style="border-radius: 17px; outline-color: black solid">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <a href="javascript:void(0)" onclick="previewPalletTag()" class="text-dark">
                                                    <i class="fas fa-eye"></i> Preview Pallet
                                                    Tag</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-12">
                                @if (isset($header->id))
                                    <a id="pallet-print" class="btn btn-dark text-white btn-block"><i class="fas fa-print"
                                            style="border-radius: 12px;"></i> Pallet Tag
                                        Print</a>
                                @else
                                    <button type="submit" id="btn-save-job"
                                        class="btn btn-success btn-block rounded-2 hide saveJob"><i class="fas fa-save"></i>
                                        <span>Save</span></button>
                                @endif
                                @isset($header)
                                    @if ($header->status_flag == 'Open')
                                        <a class="btn btn-info btn-block text-white" href="#addQty" data-toggle="modal"><i
                                                class="fas fa-plus"></i> Add/Update Qty</a>
                                        <hr>
                                        <a href="javascript:void(0)" onclick="submitData()" class="btn btn-success btn-block"><i
                                                class="fas fa-check-circle"></i> <span>
                                                Konfirm</span>
                                        </a>
                                    @endif
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-8">
                        <div class="row ">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="hidden" id="forwarder_id" name="forwarder_id"
                                        @isset($header->forwarder_id) value="{{ $header->forwarder_id }}" @endisset>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.."
                                        id="forwarder_name" name="forwarder_name" class="form-control" required
                                        @isset($header->forwarder_name) value="{{ $header->forwarder_name }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
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
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
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
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="po_number">PO Number</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="po_number" name="po_number"
                                        @isset($header->po_number) value="{{ $header->po_number }}" @endisset
                                        class="form-control"
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PEB No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="peb_no"
                                        name="peb_no" class="form-control" required
                                        @isset($header->peb_no) value="{{ $header->peb_no }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>AJU No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="aju_no"
                                        name="aju_no" class="form-control" required
                                        @isset($header->aju_no) value="{{ $header->aju_no }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="vehicle_no">Vehicle No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vehicle_no" name="vehicle_no"
                                        @isset($header->vehicle_no) value="{{ $header->vehicle_no }}" @endisset
                                        class="form-control"
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="destination" name="destination" class="form-control"
                                        @isset($header->destination) value="{{ $header->destination }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Checker</label>
                                    <select id="checkerName" style="width: 100%;" required class="form-control"
                                        name="pic_name">
                                        @if ($header != null)
                                            <option value="{{ $header->pic_name }}" selected>{{ $header->pic_name }}
                                                @foreach ($checker as $item)
                                            </option>
                                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>SILAHKAN PILIH</option>
                                        @foreach ($checker as $item)
                                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Document</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="qty_cargo" name="qty_cargo" class="form-control"
                                        @isset($header->qty_cargo) value="{{ $header->qty_cargo }}" onchange="setQtyCargo(this.value)" @endisset>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Actual</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="qty_actual" name="qty_actual" class="form-control"
                                        @isset($header->qty_actual) value="{{ $header->qty_actual }}" onchange="setQtyActual(this.value)" @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Volume ( CBM )</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="cbm"
                                        name="cbm" class="form-control" required
                                        @isset($header->cbm) value="{{ $header->cbm }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Weight ( Kg )</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="weight"
                                        name="weight" class="form-control" required
                                        @isset($header->weight) value="{{ $header->weight }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Total Pallet</label>
                                    <input type="number" autocomplete="off" placeholder="Silahkan isi.."
                                        id="total_pallet" name="total_pallet" class="form-control" required
                                        @isset($header->total_pallet) value="{{ $header->total_pallet }}" @endisset
                                        @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">GATE IN </span>
                                    </div>
                                    <input class="form-control floating-label" type="text" name="gate_in" required
                                        placeholder="Click for input" id="gate_in"
                                        @isset($header->gate_in) @if (!is_null($header->gate_in)) value="{{ \Carbon\Carbon::parse($header->gate_in)->format('d/m/Y H:i') }}" @endif @endisset>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="my-addon">Tanggal Bongkar</span>
                                    </div>
                                    <input class="form-control" type="date" name="tgl_bongkar" required
                                        placeholder="" id="tglBongkar" aria-label="Recipient's "
                                        @if ($header != null) value="{{ $header->tgl_bongkar }}" @else value="{{ date('Y-m-d') }}" @endif>
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
                                    <form id="form-detail" method="POST">
                                        @csrf
                                        <input type="hidden" id="job_id" name="job_id"
                                            @isset($header->id) value="{{ $header->id }}" @endisset />
                                        <div class="table-responsive">
                                            <table id="table-detail" class="table table-striped table-bordered table-sm"
                                                style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Pallet ID</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        @if (isset($header->id))
                                            @if ($header->status_flag == 'Open')
                                                <div class="float-right">
                                                    <button type="submit" id="btn-update" class="btn btn-success btn-sm"><i
                                                            class="fas fa-save"></i>
                                                        <span>Update</span></button>
                                                </div>
                                            @endif
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

    @if ($header != null)
        @if ($header->status_flag != 'Confirmed')
            <a href="javascript:void(0)" class="float" onclick="updateHeader()">
                <i class="fa fa-edit my-float"></i>
                <span class="tooltiptext">Update Header</span>
            </a>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        $('#gate_in').bootstrapMaterialDatePicker({
            format: 'DD/MM/YYYY HH:mm'
        });
        $('#checkerName').select2();

        function setQtyCargo(val) {
            $('#qty_cargo').val(val);
        }

        function setQtyActual(value) {
            $('#qty_actual').val(value);
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

            load_detail();

            function load_detail() {
                var job_id = $('#job_id').val();

                $('#table-detail').DataTable().destroy();
                $('#table-detail').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    searching: false,
                    destroy: true,
                    info: false,
                    ajax: {
                        url: "{{ route('export-detail.index') }}",
                        type: "GET",
                        data: {
                            job_id: job_id
                        }
                    },
                    columns: [{
                            data: 'pallet_id',
                            name: 'pallet_id'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
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
