@extends('layouts.new.base')
@section('title', 'MKT - Bea Cukai Module')
@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Choose PEB Number</label>
                                <select class="form-control searching" name="peb_no" id=""
                                    onchange="selectPEB(this.value)" required autocomplete="off" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="appendContent">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailBarang" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('storeDetail') }}" method="post" id="formDetail">
                        @csrf
                        <input type="hidden" name="id_header_bc" class="idHeaderBCValue">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">
                                                    Jenis Barang
                                                </th>
                                                <th colspan="2" class="text-center">
                                                    Kemasan
                                                </th>
                                                <th rowspan="2" class="text-center">
                                                    Nilai Export Barang
                                                </th>
                                                <th rowspan="2" class="text-center">
                                                    Nomor Peti Kemas
                                                </th>
                                                <th rowspan="2" class="text-center">
                                                    #
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-center">Satuan</th>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-center">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">

                                        </tbody>
                                    </table>
                                </div>
                                <button class="btn btn-md btn-dark" id="addBtn" type="button">
                                    Add
                                </button>
                            </div>
                            <div class="col-sm-12">
                                <div class="float-right">
                                    <input type="hidden" class="qtyReceivingValue">
                                    <button type="submit" class="hide btn btn-primary btn-lg btnSaveDetail">
                                        Submit</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $('#formDetail').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $('.btnSaveDetail').attr('disabled', true);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {
                    $('.btnSaveDetail').attr('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Data berhasil di simpan..',
                        });
                        $('#modalDetailBarang').modal('hide')
                },
                error: function(error) {
                    $('.btnSaveDetail').attr('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    });
                }
            });
        })

        $('#addBtn').click(function() {
            $('.btnSaveDetail').removeClass('hide');
            let dynamicRowHTML = `
            <tr class="rowClass""> 
                <td class="row-index text-center"> 
                    <input type="number" class="form-control" autocomplete="off" required name="jumlah_jenis_barang[]" id="">
                </td> 
                <td class="row-index text-center"> 
                    <div class="form-group">
                        <select class="form-control selectKemasan" name="satuan_jenis_barang[]" id="" required autocomplete="off"
                            style="width: 100%;">
                            <option value="" selected disabled>Pilih..</option>
                            @foreach ($uom as $item)
                                <option value="{{ $item->code }}">{{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </td> 
                <td class="row-index text-center"> 
                    <input type="number" class="form-control jumlahKemasanValue" autocomplete="off" required name="jumlah_kemasan[]" id="">
                </td> 
                <td class="row-index text-center"> 
                    <div class="form-group">
                        <select class="form-control selectKemasan" name="satuan_kemasan[]" id="" required autocomplete="off"
                            style="width: 100%;">
                            <option value="" selected disabled>Pilih..</option>
                            @foreach ($uom as $item)
                                <option value="{{ $item->code }}">{{ $item->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </td> 
                <td class="row-index text-center"> 
                    <input type="text" class="form-control nilaiBarang" autocomplete="off" required name="nilai_barang[]" id="">
                </td> 
                <td class="row-index text-center"> 
                    <input type="text" class="form-control" autocomplete="off" required name="no_peti_kemas[]" id="">
                </td> 
                <td class="text-center"> 
                    <button class="btn btn-danger remove" type="button">
                        <i class="fas fa-trash-alt"></i>
                    </button> 
                </td> 
            </tr>`;
            $('#tbody').append(dynamicRowHTML);
            $('.selectKemasan').select2();
        });
        // Removing Row on click to Remove button
        $('#tbody').on('click', '.remove', function() {
            $(this).parent('td.text-center').parent('tr.rowClass').remove();
        });

        $('.searching').select2({
            placeholder: 'Search...',
            ajax: {
                url: "{{ url('export/BeaCukai/getPEB') }}",
                dataType: 'json',
                type: 'POST',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.peb_no + ' (' + item.shipper_name + ')',
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        function selectPEB(id) {
            $.ajax({
                url: "{{ url('export/BeaCukai/detailPEB') }}/" + id,
                method: 'GET',
                success: function(response) {
                    appendContent(response.data);
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        }

        function appendContent(data) {
            if (data.input_flag == false) {
                var headerTab = `<li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#body">
                                        <span class="nav-icon"><i class="flaticon2-edit"></i></span>
                                        <span class="nav-text">Update Bea Cukai Reporting</span>
                                    </a>
                                </li>`
            } else {
                var headerTab = `<li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#body">
                                        <span class="nav-icon"><i class="flaticon2-add-1"></i></span>
                                        <span class="nav-text">Input Bea Cukai Reporting</span>
                                    </a>
                                </li>`
            }
            $('.appendContent').html("")
            $('.appendContent').append(`
            <div class="card card-custom gutter-b shadow-lg">
                                    <div class="card-header card-header-tabs-line">
                                        <div class="card-toolbar">
                                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#header">
                                                        <span class="nav-icon"><i class="flaticon2-information"></i></span>
                                                        <span class="nav-text">Job Information</span>
                                                    </a>
                                                </li>
                                                ${headerTab}
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="header" role="tabpanel"
                                                aria-labelledby="header">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Forwarder Name</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.forwarder_name}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Shipper Name</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.shipper_name}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Consignee Name</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.consignee_name}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>PO Number</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.po_number}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>PEB No.</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.peb_no}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>AJU No.</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.aju_no}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Vehicle No.</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.vehicle_no}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Destination</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.destination}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Checker</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.pic_name}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty Document</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.qty_cargo}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty Actual</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.qty_actual}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Volume ( CBM )</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.cbm}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Weight ( Kg )</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.weight}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label>Total Pallet</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.total_pallet}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Tanggal Bongkar</label>
                                                            <input type="text" readonly class="form-control"
                                                                value="${data.jobHeader.tgl_bongkar}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show active" id="body" role="tabpanel"
                                                aria-labelledby="body">
                                                    <div class="bodyInput"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`)
            if (data.input_flag == false) {
                formUpdate(data.data_bc)
            } else {
                formInput(data.jobHeader)
            }
            $('.selectValuta').select2();
            appendJobInformation(data.jobHeader);
            $('.qtyReceivingValue').val(data.jobHeader.qty_actual)
        }

        function formInput(params) {
            $('.bodyInput').html("")
            $('.bodyInput').append(`
                        <form action="{{ route('storeBC') }}" method="post" id="formBC">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">PEB</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control pebValue" name="peb_no"
                                                            id="" aria-describedby="helpId" readonly placeholder=""
                                                            required autocomplete="off" value="${params.peb_no}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="peb_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">NPE</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="npe_no"
                                                            id="" aria-describedby="helpId"
                                                            placeholder="Silahkan isi.." required autocomplete="off">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="npe_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">PKBE</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="pkbe_no"
                                                            id="" aria-describedby="helpId"
                                                            placeholder="Silahkan isi.." required autocomplete="off">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="pkbe_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <hr style="border: 1px solid;">
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Eksportir</label>
                                        <input type="text" name="eksportir" readonly required autocomplete="off" id="" class="form-control"
                                            placeholder="Silahkan di isi.." aria-describedby="helpId" value="${params.shipper_name}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Qty Receiving</label>
                                        <input type="number" readonly name="qty_receiving" required autocomplete="off" id=""
                                            class="form-control" placeholder="Silahkan di isi.."
                                            aria-describedby="helpId" value="${params.qty_actual}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Valuta</label>
                                        <select class="form-control selectValuta" name="valuta" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="" selected disabled>Silahkan Pilih</option>
                                            <option value="EURO">EURO</option>
                                            <option value="IDR">IDR</option>
                                            <option value="MYR">MYR</option>
                                            <option value="SGD">SGD</option>
                                            <option value="USD">USD</option>
                                            <option value="YEN">YEN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Asal Barang</label>
                                        <select class="form-control asalBarang" name="asal_barang" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="" selected disabled>Silahkan Pilih</option>
                                            <option value="Umum">10 Umum</option>
                                            <option value="TPB Kawasan Berikat">41 TPB Kawasan Berikat</option>
                                            <option value="Fasilitas Pengembalian">22 Fasilitas Pengembalian</option>
                                            <option value="Fasilitas Pembebasan">21 Fasilitas Pembebasan</option>
                                            <option value="Barang Pindahan">34 Barang Pindahan</option>
                                            <option value="Barang Contoh">37 Barang Contoh</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Negara Tujuan</label>
                                        <select class="form-control selectNegara" name="negara_tujuan" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="" selected disabled>Silahkan Pilih</option>
                                            @foreach ($negara as $list)
                                            <option value="{{ $list->name }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" name="id_header" value="${params.id}" required hidden>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-lg btn-info btnSave"><i
                                                class="fas fa-save"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>`)
            $('.selectNegara').select2();
            $('.asalBarang').select2();
            $('#formBC').on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                $('.btnSave').attr('disabled', true);
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $('.btnSave').attr('disabled', false);
                        if (response.message == 'validate') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Terdapat inputan yang belum terisi..',
                            })
                        }
                        if (response.message == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil di simpan..',
                            });
                            selectPEB(params.id)
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.error,
                            });
                        }
                    },
                    error: function(error) {
                        $('.btnSave').attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        });
                    }
                });
            })
        }

        function formUpdate(params) {
            $('.bodyInput').html("")
            $('.bodyInput').append(`
                        <form action="{{ route('updateBC') }}" method="post" id="formUpdateBC">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">PEB</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control pebValue" name="peb_no"
                                                            id="" aria-describedby="helpId" readonly placeholder=""
                                                            required autocomplete="off" value="${params.peb_no}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="peb_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="${params.peb_date}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">NPE</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="npe_no"
                                                            id="" aria-describedby="helpId" value="${params.npe_no}"
                                                            placeholder="Silahkan isi.." required autocomplete="off">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="npe_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="${params.npe_date}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">PKBE</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Nomor</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="pkbe_no"
                                                            id="" aria-describedby="helpId" value="${params.pkbe_no}"
                                                            placeholder="Silahkan isi.." required autocomplete="off">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" name="pkbe_date"
                                                            id="" aria-describedby="helpId" placeholder=""
                                                            required autocomplete="off" value="${params.pkbe_date}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <hr style="border: 1px solid;">
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Eksportir</label>
                                        <input type="text" name="eksportir" readonly required autocomplete="off" id="" class="form-control"
                                            placeholder="Silahkan di isi.." aria-describedby="helpId" value="${params.eksportir}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Qty Receiving</label>
                                        <input type="number" readonly name="qty_receiving" required autocomplete="off" id=""
                                            class="form-control" placeholder="Silahkan di isi.."
                                            aria-describedby="helpId" value="${params.qty_receiving}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Valuta</label>
                                        <select class="form-control selectValuta" name="valuta" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="${params.valuta}" selected>${params.valuta}</option>
                                            <option value="EURO">EURO</option>
                                            <option value="IDR">IDR</option>
                                            <option value="MYR">MYR</option>
                                            <option value="SGD">SGD</option>
                                            <option value="USD">USD</option>
                                            <option value="YEN">YEN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Asal Barang</label>
                                        <select class="form-control asalBarang" name="asal_barang" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="${params.asal_barang}" selected>${params.asal_barang}</option>
                                            <option value="Umum">10 Umum</option>
                                            <option value="TPB Kawasan Berikat">41 TPB Kawasan Berikat</option>
                                            <option value="Fasilitas Pengembalian">22 Fasilitas Pengembalian</option>
                                            <option value="Fasilitas Pembebasan">21 Fasilitas Pembebasan</option>
                                            <option value="Barang Pindahan">34 Barang Pindahan</option>
                                            <option value="Barang Contoh">37 Barang Contoh</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Negara Tujuan</label>
                                        <select class="form-control selectNegara" name="negara_tujuan" id="" required autocomplete="off"
                                            style="width: 100%;">
                                            <option value="${params.negara_tujuan}" selected>${params.negara_tujuan}</option>
                                            @foreach ($negara as $list)
                                            <option value="{{ $list->name }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" name="id" value="${params.id}" required hidden>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-lg btn-dark btnUpdate"><i
                                                class="fas fa-save"></i> Update Header
                                        </button>
                                        <button type="button" class="btn btn-lg btn-primary" onclick="addDetail('${params.id}')"><i
                                                class="fas fa-eye"></i> Show Barang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>`)
            $('.selectNegara').select2();
            $('.asalBarang').select2();
            $('#formUpdateBC').on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                $('.btnUpdate').attr('disabled', true);
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        console.log(response.message);
                        $('.btnUpdate').attr('disabled', false);
                        if (response.message == 'validate') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Terdapat inputan yang belum terisi..',
                            })
                        }
                        if (response.message == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil di simpan..',
                            });
                            selectPEB(params.id_header)
                        }
                    },
                    error: function(error) {
                        $('.btnUpdate').attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        });
                    }
                });
            })
        }

        function addDetail(id_header) {
            $('.idHeaderBCValue').val(id_header)
            $.ajax({
                url: "{{ url('export/BeaCukai/detailBC') }}/" + id_header,
                method: 'GET',
                success: function(response) {
                    $('#modalDetailBarang').modal('show')
                    appendDetailTable(response.data)
                    $('.btnSaveDetail').addClass('hide');
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        }

        function appendDetailTable(array) {
            console.log(array);
            $('#tbody').html("")
            $.each(array, function(key, val) {
                $('#tbody').append(` <tr class="rowClass""> 
                            <td class="row-index text-center"> 
                                <input type="number" class="form-control" autocomplete="off" required name="jumlah_jenis_barang[]" id="" value="${val.jumlah_jenis_barang}">
                            </td> 
                            <td class="row-index text-center"> 
                                <div class="form-group">
                                    <select class="form-control selectKemasan" name="satuan_jenis_barang[]" id="" required autocomplete="off"
                                        style="width: 100%;">
                                        <option value="${val.satuan_jenis_barang}">${val.satuan_jenis_barang}</option>
                                        @foreach ($uom as $item)
                                            <option value="{{ $item->code }}">{{ $item->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td> 
                            <td class="row-index text-center"> 
                                <input type="number" class="form-control" autocomplete="off" required name="jumlah_kemasan[]" id="" value="${val.jumlah_kemasan}">
                            </td> 
                            <td class="row-index text-center"> 
                                <div class="form-group">
                                    <select class="form-control selectKemasan" name="satuan_kemasan[]" id="" required autocomplete="off"
                                        style="width: 100%;">
                                        <option value="${val.satuan_kemasan}">${val.satuan_kemasan}</option>
                                        @foreach ($uom as $item)
                                            <option value="{{ $item->code }}">{{ $item->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td> 
                            <td class="text-center"> 
                                <input type="text" class="form-control" autocomplete="off" required name="nilai_barang[]" id="" value="${val.nilai_barang}">
                            </td> 
                            <td class="text-center"> 
                                <input type="text" class="form-control" autocomplete="off" required name="no_peti_kemas[]" id="" value="${val.no_peti_kemas}">
                            </td> 
                            <td class="text-center"> 
                                <button class="btn btn-danger" type="button" onclick="removeDetail('${val.id}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button> 
                            </td> 
                        </tr>`)
            });
        }

        function removeDetail(id) {
            let text = "Apakah anda yakin untuk menghapus data ini?";
            if (confirm(text) == true) {
                $.ajax({
                    url: "{{ url('export/BeaCukai/deleteDetail') }}/" + id,
                    method: 'GET',
                    success: function(response) {
                        appendDetailTable(response.data);
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        })
                    }
                });
            } else {
                return false;
            }
        }

        function appendJobInformation(header) {
            $('.jobInformation').html("")
            $('.jobInformation').append(`
            <div class="row">
                <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="text" readonly class="form-control" value="${header.forwarder_name}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Shipper Name</label>
                                    <input type="text" readonly class="form-control" value="${header.shipper_name}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Consignee Name</label>
                                    <input type="text" readonly class="form-control" value="${header.consignee_name}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>PO Number</label>
                                    <input type="text" readonly class="form-control" value="${header.po_number}" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PEB No.</label>
                                    <input type="text" readonly class="form-control" value="${header.peb_no}" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>AJU No.</label>
                                    <input type="text" readonly class="form-control" value="${header.aju_no}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Vehicle No.</label>
                                    <input type="text" readonly class="form-control" value="${header.vehicle_no}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" readonly class="form-control" value="${header.destination}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Checker</label>
                                    <input type="text" readonly class="form-control" value="${header.pic_name}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Document</label>
                                    <input type="text" readonly class="form-control" value="${header.qty_cargo}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Qty Actual</label>
                                    <input type="text" readonly class="form-control" value="${header.qty_actual}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Volume ( CBM )</label>
                                    <input type="text" readonly class="form-control" value="${header.cbm}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Weight ( Kg )</label>
                                    <input type="text" readonly class="form-control" value="${header.weight}" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Total Pallet</label>
                                    <input type="text" readonly class="form-control" value="${header.total_pallet}" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Tanggal Bongkar</label>
                                    <input type="text" readonly class="form-control" value="${header.tgl_bongkar}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`)
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
        }
    </script>
@endpush
