@extends('layouts.main')

@section('title')
    Scan Pallet Tag
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Scan Pallet Tag</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Scan Pallet Tag</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="outline: solid; border-radius: 13px;">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="alert">
                                        <div class="alert alert-warning mb-4" role="alert" id="">
                                            SILAHKAN SCAN PALLET TAG
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <center>
                                        <div id="qr-reader" style="width: 300px;"></div>
                                    </center>
                                </div>
                            </div>
                            <div class="grey-bg container-fluid content">

                            </div>
                            <div class="grey-bg container-fluid content-transaction">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/html5-qrcode.min.js"></script>
    <script>
        var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 10,
            qrbox: 250,
            rememberLastUsedCamera: true
        });
        html5QrcodeScanner.render(onScanSuccess);

        function onScanSuccess(decodedText, decodedResult) {
            // alert(decodedText);
            html5QrcodeScanner.clear();
            $.ajax({
                url: "{{ url('warehouse/scan-pallet-tag/doScan') }}/" + decodedText,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data.stock.length > 0) {
                        swal({
                            icon: 'success',
                            title: 'Successfully',
                        })
                        appendContent(response.data)
                    } else {
                        swal({
                            text: "Data not found",
                            // text: "Please Reprint your pallet tag!",
                            icon: "warning",
                            buttons: 'Close!',
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                location.reload();
                            } else {
                                return false;
                            }
                        });
                    }
                },
                error: function(error) {
                    swal({
                        icon: 'error',
                        title: 'Internal Server Error',
                    })
                    // location.reload();
                }
            });
        }

        function appendContent(array) {
            console.log(array);
            $('.alert').html('')
            $('.alert').append(`<div class="alert alert-success mb-4" role="alert" id="">
                                    BERHASIL DI SCAN <a class="btn btn-dark btn-md ml-4 text-white" onclick="location.reload()"><i class="fas fa-camera text-white"></i> Scan Kembali</a>
                                </div>`)
            $('.content').html("")
            $.each(array.stock, function(key, value) {
                $('.content').append(`
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card shadow-lg mt-2 mb-2" style="border-radius: 20px; overflow: hidden;">
                                                <div class="card-content">
                                                    <div class="card-body cleartfix">
                                                            <div class="media-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-borderless" style="zoom: 110%">
                                                                        <thead>
                                                                            <tr class="text-center">
                                                                                <th colspan="2">${value.product_code}</th>
                                                                                <th colspan="2">${value.location_code}</th>
                                                                            </tr>
                                                                            <tr class="text-center">
                                                                                <th>SOH</th>
                                                                                <th>SOA</th>
                                                                                <th>SOB</th>
                                                                                <th>UOM</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr class="text-center">
                                                                                <td>${value.qtys}</td>
                                                                                <td>${value.qtya}</td>
                                                                                <td>${value.qtyp}</td>
                                                                                <td>${value.puom}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="align-self-center">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`)
            });

            $('.content-transaction').html("")
            $('.content-transaction').append(`
                                <div class="row">
                                    <div class="col-sm-12 mt-2">
                                        <div class="card shadow-lg" style="border-radius: 20px; overflow: hidden;">
                                            <div class="card-content">
                                                <div class="card-body cleartfix">
                                                        <div class="media-body">
                                                                <table class="table table-bordered" style="zoom: 110%">
                                                                    <thead>
                                                                        <tr class="text-center">
                                                                            <th class="bg-dark text-white" colspan="4">Transaction History</th>
                                                                        </tr>
                                                                        <tr class="text-center">
                                                                            <th>Tanggal</th>
                                                                            <th>Job Type</th>
                                                                            <th>Qty</th>
                                                                            <th>Location Code</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="bodyTransaction">
                                                                        
                                                                    </tbody>
                                                                </table>
                                                        </div>
                                                        <div class="align-self-center">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`)
            $.each(array.transaction, function(key, value) {
                if (value.job_type == 'IMP') {
                    var job_type = 'Inbound'
                } else if (value.job_type == 'EXP') {
                    var job_type = 'Outbound'
                } else if (value.job_type == 'TFRO') {
                    var job_type = 'Transfer Out'
                } else if (value.job_type == 'TFRI') {
                    var job_type = 'Transfer In'
                } else if (value.job_type == 'ADJ-') {
                    var job_type = 'Adjusment Minus'
                } else {
                    var job_type = 'Adjusment Plus'
                }
                $('.bodyTransaction').append(`
                    <tr class="text-center">
                        <td>${value.tanggal}</td>
                        <td>${job_type}</td>
                        <td>${value.qty} ${value.puom}</td>
                        <td>${value.location_code}</td>
                    </tr>`)
            });
        }
    </script>
@endpush
