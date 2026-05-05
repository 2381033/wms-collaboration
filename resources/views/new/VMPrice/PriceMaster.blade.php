@extends('layouts.new.base')
@section('title', 'MKT - PRICE DATABASE')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }
        table {
            text-align: left;
            position: relative;
        }
        th {
            background: white;
            position: sticky;
            top: 0;
        }
        .table-responsive {
            max-height: 900px;
            overflow: auto;
        }
        tr:hover {background-color: orange;}
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 110%">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-header">
                    <div class="card-tittle">
                        <h5>Price Database</h2>
                        <div class="float-right">
                            <a href="#upload" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fas fa-file-excel"></i> Upload From Excel</a>
                            <a href="#modal-edit-excel" data-toggle="modal" class="btn btn-sm text-white" style="background-color: green"><i class="fas fa-edit text-white"></i> Update From Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <select class="form-control selectService" onchange="selectService(this.value)" name=""  required>
                                <option value="" selected disabled>Service</option>
                                @foreach ($service as $item)
                                    <option value="{{$item->service}}">{{$item->service}}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <select class="form-control selectMOT" onchange="selectMOT(this.value)" name=""  required>
                                <option value="" selected disabled>MOT</option>
                              </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <select class="form-control selectVendor" name="" required>
                                <option value="" selected disabled>VENDOR</option>
                              </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <a href="javascript:void(0)" onclick="searchData()" class="btn btn-md btn-dark"><i class="fas fa-search"></i> Search</a>
                        </div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>VENDOR</th>
                                            <th>ORIGIN</th>
                                            <th>KOTA/KAB</th>
                                            <th>DESTINATION</th>
                                            <th>PRODUCT TYPE</th>
                                            <th>VEHICLE TYPE</th>
                                            <th>UOM</th>
                                            <th>MIN. CHARGE</th>
                                            <th>PRICE</th>
                                            <th>VALID UNTIL</th>
                                            <th>CREATED TIME</th>
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

    <div id="upload" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Upload
                                <a onclick="downloadTemplate()" href="javascript::void(0)" class="btn btn-md btn-danger">
                                    <i class="fas fa-download"></i> Template
                                </a>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('uploadPrice') }}" method="post" id="uploadPrice" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <select class="form-control serviceModal" onchange="selectService(this.value)" name="service" required>
                                            <option value="" selected disabled>Service Choose</option>
                                            @foreach ($service as $item)
                                                <option value="{{$item->service}}">{{$item->service}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control MOTModal" name="mot" onchange="selectMOT(this.value)" required>
                                            <option value="" selected disabled>MOT Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control" type="file" name="file" placeholder="" required>
                                        </div>
                                        <div class="float-right ">
                                            <button type="submit" class="btn btn-lg btn-danger mt-4" id="btnUpload"><i
                                                    class="fas fa-save"></i>
                                                Upload
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

    <div id="modal-edit-excel" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Harga From Excel
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form action="{{ route('templateEditHarga') }}" method="post" id="templateEditHarga" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <select class="form-control serviceEditHarga" onchange="selectService(this.value)" name="service" required>
                                            <option value="" selected disabled>Service Choose</option>
                                            @foreach ($service as $item)
                                                <option value="{{$item->service}}">{{$item->service}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control motEditHarga" name="mot" onchange="selectMOTEditHarga(this.value)" required>
                                            <option value="" selected disabled>MOT Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control vendorEditHarga" style="width: 100%" multiple name="vendor[]" required>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-block mb-3 text-white" style="border-radius: 13px; background-color: green;">
                                            <i class="fas fa-database text-white"></i> Download Data
                                        </button>
                                    </form>
                                    <form action="{{ route('updatePriceExcel') }}" method="post" id="updatePriceExcel" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="service" class="serviceValue">
                                        <input type="hidden" name="mot" class="motValue">
                                        <div class="form-group">
                                            <input class="form-control" type="file" name="file" placeholder="" required>
                                        </div>
                                        <div class="float-right">
                                            <button type="submit" class="btn btn-lg text-white" style="background-color: green;"><i
                                                    class="fas fa-save text-white"></i>
                                                Upload
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Update Data
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('updateData') }}" method="post" id="updateData" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id_master" class="idMaster" required>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered" id="tableEdit">
                                            <tr class="">
                                                <th>VENDOR</th>
                                                <th>ORIGIN</th>
                                                <th>KOTA/KAB</th>
                                                <th>DESTINATION</th>
                                                <th>MOT</th>
                                                <th>PRODUCT TYPE</th>
                                                <th>VEHICLE TYPE</th>
                                                <th>UOM</th>
                                                <th>MIN. CHARGE</th>
                                                <th>PRICE</th>
                                                <th>VALID UNTIL</th>
                                            </tr>
                                            <tbody>
                                                <tr class="tbodyEdit">
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row appendEdit">
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="float-right ">
                                            <button type="submit" class="btn btn-lg btn-info mt-4"><i
                                                    class="fas fa-save"></i>
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-history" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title">Data History
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>VENDOR</th>
                                        <th>ORIGIN</th>
                                        <th>KOTA/KAB</th>
                                        <th>DESTINATION</th>
                                        <th>MOT</th>
                                        <th>PRODUCT TYPE</th>
                                        <th>VEHICLE TYPE</th>
                                        <th>UOM</th>
                                        <th>MIN. CHARGE</th>
                                        <th>PRICE</th>
                                        <th>VALID UNTIL</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyHistory">
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="appendHistory">
        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">
        $('.selectService').select2();
        $('.selectMOT').select2();
        $('.selectVendor').select2();
        $('.vendorEditHarga').select2();

        function editData(id){
            $('.idMaster').val(id);
            $.ajax({
                    url: "{{ url('vm-price/editData') }}/" + id,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        console.log(data.object.master);
                        $('#modal-edit').modal('show')
                        $('.tbodyEdit').html("")
                        $('.appendEdit').html("")
                        $('.tbodyEdit').append(`
                            <td>${data.object.master.vendor}</td>
                            <td>${data.object.master.origin}</td>
                            <td>${data.object.master.kota_kab}</td>
                            <td>${data.object.master.destination}</td>
                            <td>${data.object.master.mot}</td>
                            <td>${data.object.master.product_type}</td>
                            <td>${data.object.master.vehicle_type}</td>
                            <td>${data.object.master.uom}</td>
                            <td>${data.object.master.min_charge}</td>
                            <td>${formatRupiah(data.object.master.price)}</td>
                            <td>${formatTanggalIndonesia2(data.object.master.valid_untill)}</td>
                        `)
                        if(data.object.master.min_charge != null){
                            $('.appendEdit').append(`
                                <div class="col-sm-3">
                                    <label>Min. Charge</label>
                                    <input type="text" class="form-control" name="min_charge" value="${data.object.master.min_charge}">
                                </div>`)
                        }
                        if(data.object.master.service =='FCL' && data.object.master.mot == 'SEA'){
                            detailFCLSEA(data.object.detail)
                        }
                        $('.appendEdit').append(`
                            <div class="col-sm-3">
                                <label>New Price</label>
                                <input type="number" class="form-control" name="price" value="${data.object.master.price}" required>
                            </div>
                            <div class="col-sm-3">
                                <label>New Valid Untill</label>
                                <input type="date" class="form-control" name="valid_untill" value="${data.object.master.valid_untill}" required>
                            </div>
                        `)
                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'error',
                            title: data,
                        });
                    }
                });
        }

        function detailFCLSEA(params){
            $('.appendEdit').append(`
                <div class="col-sm-3 mt-1 mb-1">
                    <label>Shipping Line</label>
                    <input type="text" class="form-control" name="" disabled value="${params.shipping_line}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>TRUCKING ORIGIN</label>
                    <input type="number" class="form-control" name="trucking_origin" required value="${params.trucking_origin}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>ADM BL</label>
                    <input type="number" class="form-control" name="adm_bl" required value="${params.adm_bl}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>SEGEL</label>
                    <input type="number" class="form-control" name="segel" required value="${params.segel}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>MATERAI</label>
                    <input type="number" class="form-control" name="materai" required value="${params.materai}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>APBS</label>
                    <input type="number" class="form-control" name="apbs" required value="${params.apbs}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>THC LOLO</label>
                    <input type="number" class="form-control" name="thc_lolo" required value="${params.thc_lolo}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>FFS</label>
                    <input type="number" class="form-control" name="ffs" required value="${params.ffs}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>OCF</label>
                    <input type="number" class="form-control" name="ocf" required value="${params.ocf}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>THC LOLO DESTINASI</label>
                    <input type="number" class="form-control" name="thc_lolo_destinasi" required value="${params.thc_lolo_destinasi}">
                </div>
                <div class="col-sm-3 mt-1 mb-1">
                    <label>TRUCKING DESTINASI</label>
                    <input type="number" class="form-control" name="trucking_destinasi" required value="${params.trucking_destinasi}">
                </div>
            `)
        }
        
        var tbl = $('#listTable').dataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },
                'copy', 'excel'
            ],
        });

        function selectService(value){
            $('.serviceValue').val(value);
            $.ajax({
                    url: "{{ url('vm-price/getMOT') }}/" + value,
                    type: "GET",
                    dataType: 'json',
                    success: function(data){
                        $(".selectMOT").html("");
                        $(".MOTModal").html("");
                        $(".motEditHarga").html("");
                        $(".selectMOT").append('<option value="" disabled selected>Choose..</option>');
                        $(".motEditHarga").append('<option value="" disabled selected>Choose..</option>');
                        $.each(data,function(key,value){
                            $(".selectMOT").append('<option value="'+value.mot+'">'+value.mot+'</option>');
                            $(".MOTModal").append('<option value="'+value.mot+'">'+value.mot+'</option>');
                            $(".motEditHarga").append('<option value="'+value.mot+'">'+value.mot+'</option>');
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

        function downloadTemplate(){
            var service  = $('.serviceModal').val();
            var mot  = $('.MOTModal').val();
            if(service == null || mot == null){
                Swal.fire({
                        icon: 'info',
                        text: 'Please Choose Service and MOT..',
                    })
            }else{
                location.href = "{{url('vm-price/templateUploadPrice')}}/" + service + "/" + mot
            }
            
        }

        $('#templateEditHarga').on('submit', function(){
            $('.loading-overlay').addClass('d-flex');
            var service  = $('.serviceEditHarga').val();
            var mot  = $('.motEditHarga').val();
            var vendor  = $('.vendorEditHarga').val();
            if(service == null || mot == null || vendor == null){
                $('.loading-overlay').removeClass('d-flex');
                Swal.fire({
                        icon: 'info',
                        text: 'Please Choose Service, MOT & VENDOR..',
                    })
            }else{
                $('.loading-overlay').removeClass('d-flex');
                $('#templateEditHarga').submit();
            }
        });

        function selectMOT(mot){
            var service  = $('.selectService').val();
            $.ajax({
                    url: "{{ url('vm-price/getVendor') }}/" + service + '/'+mot,
                    type: "GET",
                    dataType: 'json',
                    success: function(data){
                        $(".selectVendor").html("");
                        $.each(data,function(key,value){
                            $(".selectVendor").append('<option value="'+value.vendor+'">'+value.vendor+'</option>');
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

        function selectMOTEditHarga(mot){
            var service  = $('.serviceEditHarga').val();
            $('.motValue').val(mot);
            $.ajax({
                    url: "{{ url('vm-price/getVendor') }}/" + service + '/'+mot,
                    type: "GET",
                    dataType: 'json',
                    success: function(data){
                        $(".vendorEditHarga").html("");
                        $.each(data,function(key,value){
                            $(".vendorEditHarga").append('<option value="'+value.vendor+'">'+value.vendor+'</option>');
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

        function searchData(){
            var service  = $('.selectService').val();
            var mot  = $('.selectMOT').val();
            var vendor  = $('.selectVendor').val();
            if(service == null || mot == null || vendor == null){
                Swal.fire({
                        icon: 'info',
                        text: 'Please Choose Service, MOT & VENDOR..',
                    })
            }else{
                generateTable(service,mot,vendor);
            }
        }

        function disablePrice(id){
            let text = "Are you sure??";
            if (confirm(text) == true) {
                $.ajax({
                        url: "{{ url('vm-price/disablePrice') }}/" + id,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            if(data.success){
                                var service = $('.selectService').val();
                                var mot = $('.selectMOT').val();
                                var vendor = $('.selectVendor').val();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data has been removed..',
                                });
                                generateTable(service, mot, vendor);
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'error',
                                title: data,
                            })
                        }
                });
            } else {
                return false;
            }
        }

        function generateTable(service,mot,vendor){
            tbl.DataTable({
                    processing : true,
                    serverSide : true,
                    "ordering": false,
                    ajax: "{{ url('vm-price/getListMasterPrice') }}/" + service+ '/'+ mot + '/'+ vendor,
                    columns: [
                        {
                            data: null,
                            name: null,
                            render: function(row, meta) {
                                return `<a href="#" onclick="disablePrice('${row.id}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                <a href="#" onclick="editData('${row.id}')" class="btn btn-dark btn-sm mt-1"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" onclick="historyData('${row.id}')" class="btn btn-info btn-sm mt-1"><i class="fas fa-money-bill-wave-alt"></i></a>`
                            },
                        },
                        {
                            data: 'vendor',
                            name: 'vendor'
                        },
                        {
                            data: 'origin',
                            name: 'origin'
                        },
                        {
                            data: 'kota_kab',
                            name: 'kota_kab'
                        },
                        {
                            data: 'destination',
                            name: 'destination'
                        },
                        {
                            data: 'product_type',
                            name: 'product_type',
                        },
                        {
                            data: 'vehicle_type',
                            name: 'vehicle_type',
                        },
                        {
                            data: 'uom',
                            name: 'uom',
                        },
                        {
                            data: 'min_charge',
                            name: 'min_charge',
                        },
                        {
                            data: 'price',
                            name: 'price',
                        },
                        {
                            data: 'valid_untill',
                            name: 'valid_untill',
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                        },
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
        }

        function historyData(id){
            $.ajax({
                    url: "{{ url('vm-price/historyData') }}/" + id,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                       $('#modal-history').modal('show')
                       $('.appendHistory').html("")
                       $('#bodyHistory').html("")
                       $('#bodyHistory').append(`
                        <td>${data.data.master.vendor}</td>
                        <td>${data.data.master.kota_kab}</td>
                        <td>${data.data.master.origin}</td>
                        <td>${data.data.master.destination}</td>
                        <td>${data.data.master.mot}</td>
                        <td>${data.data.master.product_type}</td>
                        <td>${data.data.master.vehicle_type}</td>
                        <td>${data.data.master.uom}</td>
                        <td>${data.data.master.min_charge}</td>
                        <td>${formatRupiah(data.data.master.price)}</td>
                        <td>${formatTanggalIndonesia2(data.data.master.valid_untill)}</td>
                       `)
                       $.each(data.data.history,function(key,value){
                       $('.appendHistory').append(`
                       <div class="timeline timeline-justified timeline-4">
                        <div class="timeline-bar"></div>
                            <div class="timeline-items">
                                <div class="timeline-item">
                                    <div class="timeline-badge">
                                        <div class="bg-danger"></div>
                                    </div>
                                    <div class="timeline-label">
                                        <span class="text-primary font-weight-bold">${formatTanggalIndonesia2(value.created_at)} By: ${value.created_by}</span>
                                    </div>
                                    <div class="timeline-content">
                                        Updated Price : Rp ${formatRupiah(value.price_new)}
                                        <br>
                                        Price Old : Rp ${formatRupiah(value.price_old)}
                                        <hr>
                                        Valid Untill New : ${formatTanggalIndonesia2(value.valid_untill_new)}
                                        <br>
                                        Valid Untill Old : ${formatTanggalIndonesia2(value.valid_untill_old)}
                                    </div>
                                </div>
                            </div>
                        </div>`)
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

        $('#uploadPrice').on('submit', function() {
            $('.loading-overlay').addClass('d-flex');
        });

        $('#updatePriceExcel').on('submit', function() {
            $('.loading-overlay').addClass('d-flex');
        });

        $('#updateData').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                data: $('#updateData').serialize(),
                url: "{{ url('vm-price/updateData') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    var service = $('.selectService').val();
                    var mot = $('.selectMOT').val();
                    var vendor = $('.selectVendor').val();
                    generateTable(service,mot,vendor);
                    $('#modal-edit').modal('hide')
                    Swal.fire({
                            icon: 'success',
                            title: 'Data has been saved successfilly..',
                        });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
		
    </script>
@endpush
