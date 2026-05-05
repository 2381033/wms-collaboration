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
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-left">
                                <h6>
                                    Customer : {{ $customer }}
                                </h6>
                                <h6>
                                    Warehouse : {{ $warehouse }}
                                </h6>
                                <hr>
                            </div>
                            <div class="float-right mb-4">
                                <a href="{{ url('crossDock') }}" class="btn btn-md btn-dark" style="border-radius: 15px;"><i
                                        class="flaticon2-arrow-2"></i>
                                    Dashboard</a>
                            </div>
                        </div>
                        @foreach ($data->where('scan_flag', 'No')->where('picking_flag', 'Yes') as $item)
                            <div class="col-sm-4 mt-4">
                                <div class="card bg-light-warning"
                                    onclick="scanCargo('{{ $item->id_detail }}', '{{ $item->id_stock }}')"
                                    style="border-radius: 20px;">
                                    <div class="card-body d-flex align-items-center py-0 mt-2">
                                        <div class="d-flex flex-column flex-grow-1  py-lg-5">
                                            <a
                                                class="card-title font-weight-bolder text-warning font-size-h5 mb-2 text-hover-primary">{{ $item->stock->description }}</a>
                                            <label for="">{{ $item->stock->sku }}</label>
                                            <a
                                                class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">
                                                {{ $item->stock->on_booking . ' ' . $item->stock->unit }}
                                            </a>
                                            <span class="font-weight-bold text-muted  font-size-lg">
                                                ID
                                                CARGO:
                                                {{ $item->stock->id_cargo }}
                                            </span>
                                        </div>
                                        <img src="{{ asset('images/scan.png') }}" alt=""
                                            class="align-self-end h-100px">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="scanCargo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <center>
                                <div id="qr-reader" style="width: 400px;"></div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/html5-qrcode.min.js"></script>

    <script type="text/javascript">
        var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 10,
            qrbox: 250,
            rememberLastUsedCamera: true
        });

        function scanCargo(id_detail, id_stock) {
            $('#scanCargo').modal('show');
            sessionStorage.setItem('id_detail', id_detail);
            sessionStorage.setItem('id_stock', id_stock);

            html5QrcodeScanner.render(onScanSuccess);
        }

        function onScanSuccess(qr, decodedResult) {
            var id_detail = sessionStorage.getItem('id_detail')
            var id_stock = sessionStorage.getItem('id_stock')
            html5QrcodeScanner.clear();
            $.ajax({
                url: "{{ url('crossDock/scanCargo/validasiCargo') }}/" + qr + '/' + id_detail + '/' + id_stock,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.message == 'notfound') {
                        Swal.fire({
                            icon: 'error',
                            title: 'qr code does not match!',
                        });
                        $('#scanCargo').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cargo has been successfully scanned..',
                        });
                        location.reload();
                    }
                    console.log(response);
                    // html5QrcodeScanner.clear();
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
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
    </script>
@endpush
