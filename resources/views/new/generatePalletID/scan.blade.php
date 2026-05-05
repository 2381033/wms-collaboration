@extends('layouts.new.base')
@section('title', 'MKT - SCAN PALLET ID')
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
    <div class="container">
        <div class="main-body">
            <div class="card" style="border-radius: 15px; zoom: 130%;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <center>
                                <div class="alert alert-warning mb-4" role="alert">
                                    SILAHKAN SCAN QRCODE <div class="btntoggle float-right">
                                        <a href="javascript:void(0)" onclick="stop()" class="btn btn-sm btn-danger mb-4"><i
                                                class="fas fa-stop"></i> STOP
                                        </a>
                                    </div>
                                </div>
                                <video id="preview" style="width: 220px;  outline: solid; border-radius: 13px;">
                                </video>
                                <input type="text" class="form-control mt-3 kodeqr" placeholder="Atau Masukan Kode QR"
                                    autocomplete="off">
                                <div class="float-right">
                                    <a href="javascript:void(0)" class="btn btn-md btn-info mt-3" onclick="manual()"><i
                                            class="fas fa-search"></i> Cari</a>
                                </div>
                            </center>
                            <div class="konten">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div id="show-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                         <div class="appendscan">

                         </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-md btn-outline-dark" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/instascan.min.js"></script>
    <script type="text/javascript">
        function stop() {
            scanner.stop();
            $('#preview').addClass('hide', true);
            $('.btntoggle').html('')
            $('.btntoggle').append(`
                <a href="javascript:void(0)" onclick="start()" class="btn btn-sm btn-info">
                    <i class="fas fa-play"></i> START
                </a>
            `)
        }

        function manual() {
            var value_qr = $('.kodeqr').val();
            if (value_qr == '') {
                alert('Silahkan isi kode qr');
            } else {
                stop();
                $.ajax({
                    url: "{{ url('inventory/generatePalletID/doScan') }}/" + value_qr,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data.length == 0) {
                            Swal.fire({
                                icon: 'warning',
                                text: 'KODE QRCODE TIDAK DI KENALI!',
                            });
                        } else {
                            $('.konten').html('')
                            $('.konten').append(`
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="timeline timeline-justified timeline-4 mt-4">
                                                <div class="timeline-bar"></div>
                                                <div class="timeline-items">

                                                </div>
                                            </div>
                                        </div>
                                `)
                            $('.timeline-items').html('')
                            $.each(response.data, function(key, value) {
                                if (value.type == 'in') {
                                    var bg = 'success';
                                    var tanggal = formatTanggalIndonesia2(value.created_at);
                                    var konten = `
                                            ${value.created_by} Memasukan SKU ${value.product_code} ke lokasi ${value.location_code_from} sebanyak <b>${value.qty}</b> CTN
                                        `
                                } else if (value.type == 'out') {
                                    var bg = 'danger';
                                    var tanggal = formatTanggalIndonesia2(value.dispatch_at);
                                    var konten = `
                                            ${value.created_by} Mengambil SKU ${value.product_code} di lokasi ${value.location_code_from} sebanyak <b>${value.dispatch_qty}</b> CTN, STOK TERBARU SAAT INI : <b>${value.stok}</b> CTN
                                        `
                                }
                                $('.timeline-items').append(`
                                        <div class="timeline-item">
                                            <div class="timeline-badge">
                                                <div class="bg-${bg}" style="zoom: 120%;"></div>
                                            </div>
                                            <div class="timeline-label text-primary">
                                                <span class="text-primary font-weight-bold">${tanggal}</span>
                                            </div>
                                            <div class="timeline-content">
                                                ${konten}
                                            </div>
                                        </div>
                                    `)
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Internal Server Error!',
                        });
                    }
                });
            }
        }

        function start() {
            $('.btntoggle').html('')
            $('.konten').html('')
            $('#preview').removeClass('hide', true);
            $('.btntoggle').append(`
                        <a href="javascript:void(0)" onclick="stop()" class="btn btn-sm btn-danger">
                            <i class="fas fa-stop"></i> STOP
                        </a>
            `)
            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[1]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function(e) {
                console.error(e);
            });
        }

        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview'),
            mirror: false,
            scanPeriod: 5
        });

        scanner.addListener('scan', function(qrcode) {
            stop();
            $.ajax({
                url: "{{ url('inventory/generatePalletID/doScan') }}/" + qrcode,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data == 'null') {
                        Swal.fire({
                            icon: 'warning',
                            text: 'QRCODE TIDAK DI KENALI!',
                        });
                    } else {
                        $('.konten').html('')
                        $('.konten').append(`
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="timeline timeline-4">
                                        <div class="timeline-bar"></div>
                                            <div class="timeline-items">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        `)
                        $('.timeline-items').html('')
                        $.each(response.data, function(key, value) {
                            if (value.type == 'in') {
                                var posisi = 'left';
                                var bg = 'success';
                                var tanggal = formatTanggalIndonesia2(value.created_at);

                            } else if (value.type == 'out') {
                                var posisi = 'right';
                                var bg = 'danger';
                                var tanggal = formatTanggalIndonesia2(value.dispatch_at);
                            }
                            $('.timeline-items').append(`
                                <div class="timeline-item timeline-item-${posisi}">
                                    <div class="timeline-badge">
                                        <div class="bg-${bg}"></div>
                                    </div>
                                    <div class="timeline-label">
                                        <span class="text-primary font-weight-bold">${tanggal}</span>
                                    </div>
                                    <div class="timeline-content">
                                    </div>
                                </div>
                            `)
                        });
                    }
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Internal Server Error!',
                    });
                }
            });
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[1]);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });
    </script>
@endpush
