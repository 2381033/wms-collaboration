@extends('layouts.new.base')
@section('title', 'MKT - Generate Pallet ID')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
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
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-sm-5 mb-3">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="my-addon">Tanggal Mulai</span>
                                </div>
                                <input class="form-control tglMulai" type="date" name=""
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon">
                            </div>
                        </div>
                        <div class="col-sm-5 mb-3">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="my-addon">Tanggal Selesai</span>
                                </div>
                                <input class="form-control tglSelesai" type="date" name=""
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <a href="javascript:void(0)" onclick="cariData()" class="btn btn-md btn-info btn-search"><i
                                    class="fas fa-search"></i>
                                Cari</a>
                        </div> --}}
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>NO</th>
                                            <th>#</th>
                                            <th>JOB NO</th>
                                            <th>LOCATION</th>
                                            <th>WAKTU DI BUAT</th>
                                            <th>BY</th>
                                            <th>QRCODE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="javascript:void(0)"
                                                        onclick="showListSKU('{{ $item->job_no }}')"
                                                        class="btn btn-sm btn-info"><i class="fas fa-eye"></i>
                                                        Show
                                                    </a>
                                                    {{-- @if (explode(' ', $item->created_at)[0] == date('Y-m-d'))
                                                        <a href="javascript:void(0)"
                                                            onclick="deleteMasterData('{{ $item->job_no }}')"
                                                            class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i>
                                                            Delete
                                                        </a>
                                                    @endif --}}
                                                </td>
                                                <td>{{ $item->job_no }}</td>
                                                <td>{{ $item->location_code_from }}</td>
                                                <td>
                                                    {{ formatTanggalWaktuIndonesia2($item->created_at) }}
                                                </td>
                                                <td>{{ $item->created_by }}</td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="printQR('{{ $item->job_no }}')">
                                                        {!! QrCode::generate($item->qrcode) !!}
                                                    </a>
                                                    <p>
                                                        <b> {{ $item->qrcode }} </b>
                                                    </p>
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

    <div id="show-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right">
                                <a class="btn btn-sm btn-info mb-2" onclick="addSKU()"><i class="fas fa-plus-circle"></i> Add SKU</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>#</th>
                                            <th>QTY</th>
                                            <th>LOCATION</th>
                                            <th>WAKTU DIBUAT</th>
                                            <th>BY</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyid">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-md btn-outline-dark" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div>

    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{url('/inventory/generatePalletID/postSKUParsial')}}" method="post" id="postGenerate">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="float-right">
                                    <a href="javascript:void(0)" class="btn btn-lg btn-dark mb-3 add hide"><i
                                            class="fas fa-plus-circle"></i> Add</a>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="typeGenerate" name="type_generate" style="width: 100%;" required>
                                        <option value="" disabled selected>TYPE</option>
                                            <option value="baru">INBOUND BARU</option>
                                            <option value="lama">BARANG LAMA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="job_no" class="jobNoValue">
                        <div class="row">
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th scope="row" colspan="3" class="text-center">
                                            <label for="customFieldName">MAPPING PALLET ID PER BIN LOCATION</label>
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>SKU</th>
                                        <th>QTY</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tr class="text-center">
                                </tr>
                            </table>
                        </div>
                        <div class="float-right">
                            <button type="submit" class="btn btn-lg btn-info btnsave"><i class="fas fa-save"></i> Save</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0)" class="btn btn-md btn-outline-dark" data-dismiss="modal">Close</a>
                    </div>
                </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">

    function deleteMasterData(job_no){
        if (confirm('Apakah anda yakin?')) {
            alert('tahap pengembangan')
        } else {
            return false;
        }
    }

    $('#postGenerate').on('submit', function(){
        $('.btnsave').attr('disabled', true);
    })
    var tbl = $('#listTable').dataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            },
            'copy', 'excel'
        ]
    });

    function printQR(job_no) {
        window.open("{{ url('inventory/generatePalletID/encryptqr') }}/" + job_no, '_blank');
    }

    function showListSKU(job_no) {
        $('.jobNoValue').val(job_no);
        $.ajax({
            url: "{{ url('inventory/generatePalletID/showListSKU') }}/" + job_no,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#show-modal').modal('show');
                $('#tbodyid').html('')
                $.each(response.data, function(key, value) {
                    console.log(value);
                    var add = `<a class="btn btn-sm btn-dark hide" onclick="checkidMaster('${value.id}')"><i class="fas fa-cogs"></i> show id</a>`;
                    $('#tbodyid').append(`
                                <tr>
                                    <td>${value.product_code}</td>
                                    <td>${add}</td>
                                    <td>${value.qty}</td>
                                    <td>${value.location_code_from}</td>
                                    <td>${formatTanggalIndonesia2(value.created_at)}</td>
                                    <td>${value.created_by}</td>
                                </tr>
                    `)
                });
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function checkidMaster(id){
        location.href = "{{url('inventory/generatePalletID/checkidMaster')}}/" + id;
    }

    function addSKU() {
        $('#show-modal').modal('hide');
        $('#add-modal').modal('show');
    }

    function deleteSKU(id) {
        location.href = "{{url('inventory/generatePalletID/deleteSKU')}}/" + id;
    }

    function cariData() {
        var tgl_mulai = $('.tglMulai').val();
        var tgl_selesai = $('.tglSelesai').val();
        $('.btn-search').hide();
        if (tgl_mulai == '' || tgl_selesai == '') {
            Swal.fire({
                icon: 'warning',
                text: 'Tanggal Tidak Boleh Kosong',
            })
            $('.btn-search').show();
        } else {
            // var tbl = $('#idTable').DataTable();
            tbl.DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('inventory/generatePalletID/cariData') }}/" + tgl_mulai + "/" + tgl_selesai,
                columns: [{
                        data: null,
                        name: 'number',
                        sortable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1
                        }
                    },
                    {
                        data: 'location_code_to',
                        name: 'location_code_to',
                        sortable: false,
                        render: function(location_code_to) {
                            if (location_code_to == null) {
                                var status =
                                    '<span class = "badge badge-info">OK</span>'
                            } else {
                                var status =
                                    '<span class="badge badge-warning">Stok Transfer</span>'
                            }
                            return status;
                        },
                    },
                    {
                        data: 'status',
                        name: 'status',
                        sortable: false,
                        render: function(status) {
                            if (status == 'G') {
                                var status =
                                    '<span class = "badge badge-success">GOODS</span>'
                            } else {
                                var status =
                                    '<span class="badge badge-danger">BAD</span>'
                            }
                            return status;
                        },
                    },
                    {
                        data: 'location_code_from',
                        name: 'location_code_from'
                    },
                    {
                        data: 'location_code_to',
                        name: 'location_code_to'
                    },
                    {
                        data: 'cyclecount_no',
                        name: 'cyclecount_no'
                    },
                    {
                        data: 'principal_name',
                        name: 'principal_name'
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
                        data: 'site_name',
                        name: 'site_name'
                    },
                    {
                        data: 'area_name',
                        name: 'area_name'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'puom',
                        name: 'puom'
                    },
                    {
                        data: 'confirmed_by',
                        name: 'confirmed_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        sortable: false,
                        render: function(created_at) {
                            return formatTanggalWaktuIndonesia2(created_at);
                        },
                    },
                    {
                        data: 'id',
                        name: 'id',
                        sortable: false,
                        render: function(id) {
                            return '-';
                        },
                    }
                ],
                "bDestroy": true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    },
                    'copy', 'excel'
                ]
            });
            $('.btn-search').show();
        }
    }

        
    $('#typeGenerate').on('change', function(){
        var type = $(this).val();
        sessionStorage.setItem('type', type);
        $('.appendKonten').html("");
        $('.btnsave').removeClass('hide');
        $('.add').removeClass('hide');
        $.ajax({
            url: "{{url('inventory/generatePalletID/typeGenerate')}}/" + type,
            type: 'GET',
            dataType : 'json',
            success: function(response){
                $('.appendKonten').append(`
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th scope="row" colspan="3" class="text-center">
                                <label for="customFieldName">MAPPING PALLET ID PER BIN LOCATION</label>
                            </th>
                        </tr>
                        <tr class="text-center">
                            <th>SKU</th>
                            <th>QTY</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tr class="text-center">
                        <td>
                            <div class="form-group">
                                <select class="form-control" id="selectProductCode" name="product_code[]" style="width: 100%;">
                                    <option value="" disabled selected>PILIH SKU</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="qty[]" id="" required placeholder="QTY.." autocomplete="off">
                        </td>
                        <td>
                            <a class="btn btn-md btn-danger deleted"><i class="fas fa-trash-alt"></i> Delete</a>
                        </td>
                    </tr>
                </table>`);
                $.each(response.data, function(key, value){
                    $('#selectProductCode').append(`<option value="${value.product_code},${value.id}">${value.product_code} -> ${value.location_code} | <b>QTY : ${value.qtya} CTN</b></option>`)
                });
            $('#selectProductCode').select2();
            $('.selectLocation').select2();
            },
            error: function(response){

            }
        })
    });
     
    $(".add").click(function(){
        var type = sessionStorage.getItem('type', type);
        $.ajax({
            url: "{{url('inventory/generatePalletID/typeGenerate')}}/" + type,
            type: 'GET',
            dataType : 'json',
            success: function(response){
                $("#table").append(`
                            <tr class="text-center">
                                <td>
                                    <div class="form-group">
                                        <select class="form-control selectProductCode" name="product_code[]" style="width: 100%;">
                                            <option value="" disabled selected>PILIH SKU</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="qty[]" id="" required placeholder="QTY.." autocomplete="off">
                                </td>
                                <td>
                                    <a class="btn btn-md btn-danger deleted"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>`);
                $.each(response.data, function(key, value){
                    $('.selectProductCode').append(`<option value="${value.product_code},${value.id}">${value.product_code} -> ${value.location_code} | <b>QTY : ${value.qtya} CTN</b></option>`)
                });
                $('.selectProductCode').select2();
            },
            error: function(response){

            }
        })
        
    });

        
    $("#table").on('click','.deleted',function(){
        $(this).parent().parent().remove();
    });

    $('#selectProductCode').select2();
    </script>
@endpush
