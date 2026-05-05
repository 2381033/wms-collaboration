@extends('layouts.new.base')
@section('title', 'MKT - Scan Cargo')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
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
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 120%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <center>
                                <div id="qr-reader" style="width: 350px;"></div>
                            </center>
                        </div>
                        <div class="col-sm-12">
                            <hr>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center" style="background-color: antiquewhite">
                                        <th colspan="2">
                                            <h3>Carton <label class="jumlahCarton"></label> of <label
                                                    class="totalCarton"></label></h3>
                                        </th>
                                        <th>
                                            <h5>Last Updated: <label class="lasUpdated"></label></h5>
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>PO NO</th>
                                        <th>Cargo ID</th>
                                    </tr>
                                </thead>
                                <tbody id="tableList"></tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <div class="appendScan"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contentConfirm">

    </div>

    <div id="scanCargo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/html5-qrcode.min.js"></script>
    <script src="{{ url('/assets/js/soundmanager2-nodebug-jsmin.js') }}"></script>

    <script type="text/javascript">
        loadData()

        function konfirmJob() {
            var job_no = "{{ $job_no }}";
            Swal.fire({
                title: 'Do you want to save the changes?',
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save',
                denyButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('export/ScanCargoEkspor/konfirmJob') }}/" + job_no,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            location.href = "{{ url('export/ScanCargoEkspor') }}"
                        },
                        error: function(response) {
                            console.log(response);
                            alert('Internal Server Error, Please refresh page and try again..')
                        }
                    });
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        function loadData() {
            var job_no = "{{ $job_no }}";
            $.ajax({
                url: "{{ url('export/ScanCargoEkspor/ajaxEncryptJob') }}/" + job_no,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    appendTable(response.data);
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }

        function appendTable(params) {
            console.log('====================================');
            console.log(params);
            console.log('====================================');
            if (params.btn_confirm == true) {
                $('.contentConfirm').html("")
                $('.contentConfirm').append(
                    `<a href="#" class="float" style="background-color: mediumseagreen; zoom: 120%;" onclick="konfirmJob()"><i class="fa fa-check text-white my-float"></i></a>`
                )
            }
            $('.jumlahCarton').html('');
            $('.totalCarton').html('');
            $('.lasUpdated').html('');
            $('.jumlahCarton').append(`${params.list.length ==  0 ? 1 : params.list.length+1 } `);
            $('.totalCarton').append(`${params.header.qty}`);
            $('.lasUpdated').append(`${params.lastUpdated == null ? '-' : params.lastUpdated }`);

            $('#tableList').html('');
            $.each(params.list, function(key, val) {
                $('#tableList').append(`
                    <tr class="text-center">
                        <td>${key+1}</td>
                        <td>${params.header.po_no}</td>
                        <td>${val.barcode}</td>
                    </tr>`);
            });
        }

        var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 10,
            qrbox: 250,
            rememberLastUsedCamera: true
        });
        html5QrcodeScanner.render(onScanSuccess);

        function scanCargo() {
            $('.content-qr-reader').html("");
            $('.content-qr-reader').append(`<div class="col-sm-12">
                            <center>
                                <div id="qr-reader" style="width: 300px;"></div>
                            </center>
                        </div>`);

        }

        function play_sound(sound) {
            soundManager.onready(function() {
                soundManager.createSound({
                    // id: 'sk4Audio',
                    url: "{{ url('/assets/audio/success.mp3') }}",
                    autoLoad: true,
                    autoPlay: true,
                    volume: 200,
                })
            });
        }

        function sound_error(sound) {
            soundManager.onready(function() {
                soundManager.createSound({
                    // id: 'sk4Audio',
                    url: "{{ url('/assets/audio/error.mp3') }}",
                    autoLoad: true,
                    autoPlay: true,
                    volume: 200,
                })
            });
        }


        function onScanSuccess(barcode, decodedResult) {
            var job_no = "{{ $job_no }}";
            validasiCargo(barcode, job_no)
        }

        function validasiCargo(barcode, job_no) {
            html5QrcodeScanner.pause();
            $.ajax({
                url: "{{ url('export/ScanCargoEkspor/validasiCargo') }}/" + barcode + '/' + job_no,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.message == 'double') {
                        sound_error()
                        html5QrcodeScanner.resume();
                        Swal.fire({
                            icon: "error",
                            title: "Double Item.",
                            text: "This item is already on the pallet",
                        });
                    } else {
                        play_sound()
                        html5QrcodeScanner.resume();
                        $('.appendScan').html("");
                        loadData();
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Data has been successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(response) {
                    sound_error()
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }
    </script>
@endpush
