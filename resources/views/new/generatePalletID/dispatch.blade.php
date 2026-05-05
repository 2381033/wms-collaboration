@extends('layouts.new.base')
@section('title', 'MKT - DISPATCH PALLET ID')
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
            <div class="card" style="border-radius: 15px; zoom: 130%;">
                <div class="card-body">
                    <form action="{{ url('inventory/generatePalletID/postDispatch') }}" method="post" id="form_post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <center>
                                    <div class="alert alert-warning mb-4" role="alert">
                                        SILAHKAN SCAN QRCODE <div class="btntoggle float-right">
                                            <a href="javascript:void(0)" onclick="stop()"
                                                class="btn btn-sm btn-danger mb-4"><i class="fas fa-stop"></i> STOP
                                            </a>
                                        </div>
                                        <video id="preview" style="width: 220px;  outline: solid; border-radius: 13px;">
                                        </video>
                                        <input type="text" class="form-control mt-3 kodeqr"
                                            placeholder="Atau Masukan Kode QR" autocomplete="off">
                                        <div class="float-right">
                                            <a href="javascript:void(0)" class="btn btn-md btn-info mt-3"
                                                onclick="manual()"><i class="fas fa-search"></i> Cari</a>
                                        </div>
                                </center>
                                <br>
                                <div class="konten">

                                </div>
                            </div>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-lg btn-dark hide btnsave" onclick="confirmation()"><i
                                    class="fas fa-check-circle"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/instascan.min.js"></script>
    <script type="text/javascript">
        function confirmation() {
            if (confirm('Stok akan dipotong langsung, Apakah anda yakin?')) {
                $('#form_post').submit();
            } else {
                return false;
            }
        }

        function manual() {
            var value_qr = $('.kodeqr').val();
            if (value_qr == '') {
                alert('Silahkan isi kode qr');
            } else {
                stop();
                $.ajax({
                    url: "{{ url('inventory/generatePalletID/getDispatchSKU') }}/" + value_qr,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data.length == 0) {
                            Swal.fire({
                                icon: 'warning',
                                text: 'KODE QRCODE TIDAK DI KENALI!',
                            });
                        } else {
                            $('.btnsave').removeClass('hide');
                            $('.konten').html('')
                            $('.konten').append(`
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="table">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>#</th>
                                                            <th>SKU</th>
                                                            <th>QTY</th>
                                                            <th>LOCATION</th>
                                                            <th>CREATE TIME</th>
                                                            <th>CREATE BY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodyid">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                `)
                            $('#tbodyid').html('')
                            $.each(response.data, function(key, value) {
                                var btnhapus = '<a class="btn btn-sm btn-danger deleted"> Delete</a>'
                                var qty =
                                    `<input type="number" name="qty[]" autocomplete="off" value="${value.stok}" class="form-control" />`
                                $('#tbodyid').append(`
                                                <tr class="text-center">
                                                    <td>${btnhapus}</td>
                                                    <td>${value.product_code}</td>
                                                    <td>
                                                        ${qty}
                                                        <input type="hidden" name="id[]" value="${value.id}" class="form-control" />
                                                        <input type="hidden" name="qrcode[]" value="${value.qrcode}" class="form-control" />
                                                    </td>
                                                    <td>${value.location_code_from}</td>
                                                    <td>${formatTanggalIndonesia2(value.created_at)}</td>
                                                    <td>${value.created_by}</td>
                                                </tr>
                                    `)
                            });
                        }
                        $("#table").on('click', '.deleted', function() {
                            $(this).parent().parent().remove();
                        });
                    },
                });
            }
        }

        function stop() {
            scanner.stop();
            $('.btnsave').addClass('hide');
            $('#preview').addClass('hide', true);
            $('.btntoggle').html('')
            $('.btntoggle').append(`
                <a href="javascript:void(0)" onclick="start()" class="btn btn-sm btn-info">
                    <i class="fas fa-play"></i> START
                </a>
            `)
        }

        function start() {
            $('.btntoggle').html('')
            $('.konten').html('')
            $('.btnsave').addClass('hide');
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
                url: "{{ url('inventory/generatePalletID/getDispatchSKU') }}/" + qrcode,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.btnsave').removeClass('hide');
                    $('.konten').html('')
                    $('.konten').append(`
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="table">
                                        <thead>
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th>SKU</th>
                                                <th>QTY</th>
                                                <th>LOCATION</th>
                                                <th>CREATE TIME</th>
                                                <th>CREATE BY</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyid">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `)
                    $('#tbodyid').html('')
                    $.each(response.data, function(key, value) {
                        var btnhapus = '<a class="btn btn-sm btn-danger deleted"> Delete</a>'
                        var qty =
                            `<input type="number" name="qty[]" autocomplete="off" value="${value.stok}" class="form-control" />`
                        $('#tbodyid').append(`
                                    <tr class="text-center">
                                        <td>${btnhapus}</td>
                                        <td>${value.product_code}</td>
                                        <td>
                                            ${qty}
                                            <input type="hidden" name="id[]" value="${value.id}" class="form-control" />
                                            <input type="hidden" name="qrcode[]" value="${value.qrcode}" class="form-control" />
                                        </td>
                                        <td>${value.location_code_from}</td>
                                        <td>${formatTanggalIndonesia2(value.created_at)}</td>
                                        <td>${value.created_by}</td>
                                    </tr>
                        `)
                    });

                    $("#table").on('click', '.deleted', function() {
                        $(this).parent().parent().remove();
                    });
                },
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
