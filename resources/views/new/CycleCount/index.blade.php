@extends('layouts.new.base')
@section('title', 'MKT - Cycle Count')
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
    <div class="container" style="zoom: 120%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    @if ($check > 0)
                        <form action="{{ url('inventory/cycleCount/store') }}" method="POST" id="form-post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">SITE</label>
                                        <select id="" class="form-control selectSite" name="site"
                                            onchange="selectSite(this.value)">
                                            <option value="" selected disabled>{{ 'Select Site' }}</option>
                                            @foreach ($site as $item)
                                                <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group ">
                                        <label for="">LOCATION</label>
                                        <select id="selectLocation" class="form-control " name="location"
                                            onchange="changeLocation(this.value)">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#addLocation" onclick="locationAdd()" data-toggle="modal"
                                        class="btn btn-lg btn-primary mt-4"><i class="fas fa-camera"></i> Add Location</a>
                                </div>
                                <div class="col-sm-12 mt-4 appendList">

                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-primary" role="alert">
                            <h4 class="alert-heading text-center">CYCLE COUNT BELUM DI SETUP</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="stokTransfer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card" style="zoom: 120%;">
                        <div class="card-header">
                            <div class="card-title">
                                <h4 class="card-title-text">FORM STOK TRANSFER <b id="SKUCode"></b>
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ url('inventory/cycleCount/postStokTransfer') }}"
                                id="postStokTransfer">
                                @csrf
                                <input type="hidden" id="idDetailValue" name="id_detail" value="">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="my-select">LOCATION FROM</label>
                                            <input class="form-control" type="text" name="location_from"
                                                id="locationFrom" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="my-select">QTY</label>
                                            <input class="form-control" type="text" name="qty" id="qtyValue"
                                                value="" readonly>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label for="my-select">LOCATION TO</label>
                                            <input class="form-control" type="text" name="location_to" placeholder=""
                                                value="Lock Area" readonly>
                                        </div>
                                        <hr>
                                        <br>
                                        <div class="form-group">
                                            <label for="my-textarea">Remarks</label>
                                            <textarea id="my-textarea" class="form-control" name="remarks" required rows="2" placeholder="Silahkan isi.."></textarea>
                                        </div>
                                        <div class="float-right">
                                            <button type="submit" class="btn btn-lg btn-info mt-4"><i
                                                    class="fas fa-save"></i>
                                                Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                class="fas fa-window-close"></i>
                            CLOSE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="addLocation" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('importCargo') }}" method="post" id="formUpload"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <center>
                                            <div id="qr-reader" style="width: 400px;"></div>
                                        </center>
                                    </div>
                                </div>
                            </form>
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
        sessionStorage.setItem('location', 'All');

        var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 10,
            qrbox: 250,
            rememberLastUsedCamera: true
        });

        function locationAdd() {
            var site_id = sessionStorage.getItem('site');
            if (site_id == null) {
                $('#addLocation').modal('hide');
                Swal.fire({
                    icon: 'warning',
                    title: 'Please Choose Site..',
                })
            } else {
                html5QrcodeScanner.render(onScanSuccess);
            }
        }

        function onScanSuccess(qr, decodedResult) {
            var site_id = sessionStorage.getItem('site');

            html5QrcodeScanner.clear();
            $.ajax({
                url: "{{ url('inventory/cycleCount/addLocationByChecker') }}/" + site_id + '/' + qr,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.message == 'location') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Location not found, Please check your barcode and try again',
                        });
                    } else if (response.message == 'stock') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock is not available at the location you selected..',
                        });
                    } else if (response.message == 'exist') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Your location exists..'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data has been successfully..'
                        });
                        $('#addLocation').modal('hide');
                        location.reload();
                    }
                },
                error: function(response) {
                    alert('Internal Server Error, Please refresh page and try again..')
                }
            });
        }

        function changeLocation(loc) {
            sessionStorage.setItem('location', loc);
            searchData();
        }

        function selectSite(val) {
            sessionStorage.setItem('site', val);
            searchData();
        }

        function searchData() {
            var site_id = sessionStorage.getItem('site');
            var location = sessionStorage.getItem('location');
            $.ajax({
                type: "GET",
                url: "{{ url('inventory/cycleCount/getListData') }}/" + site_id + '/' + location,
                success: function(data) {
                    if (data.message == "not_found") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data not found',
                        });
                        $('.appendList').html('');
                        $('#selectLocation').html('');
                    } else {
                        appendKonten(data)
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error',
                    });
                }
            });
        }

        function klikOK(id) {
            $.ajax({
                url: "{{ url('inventory/cycleCount/countByChecker') }}/" + id,
                method: 'GET',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        searchData();
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        }

        $('#form-post').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var product_code_value = $('.selectSKU').val();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.data == 'ok') {
                        if (product_code_value == 'ALL') {
                            search();
                        } else {
                            cariMaterial(product_code_value);
                        }
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        })

        $('#postStokTransfer').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        searchData();
                        $('#stokTransfer').modal('hide');
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: error,
                    })
                }
            });
        })

        function appendKonten(array) {
            $('.appendList').html('');
            $('#selectLocation').html('');
            $('#selectLocation').append(`<option value="" selected disabled>Choose</option>`);
            $.each(array.location, function(key, value) {
                $('#selectLocation').append(
                    `<option value="${value}">${value}</option>`);
            });
            $('#selectLocation').append(`<option value="All">ALL LOCATION</option>`);
            $.each(array.data, function(key, value) {
                $('.appendList').append(`<div class="card card-custom bg-light-warning mb-4" style="outline: solid; border-radius: 15px;">
                                            <div class="card-header ribbon ribbon-right">
                                                <div class="ribbon-target bg-info" style="top: 10px; right: -5px; zoom:150%;"><a href="javascript:void(0)" onclick="klikOK(${value.id})" class="text-white">OK
                                                    </a>
                                                </div>
                                                <h1 class="card-title">${value.stock.product_code} | ${value.product_name} </h1>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" value="${value.stock.id_ledger}" name="id_ledger" />
                                                            <div class="col-sm-8">
                                                                <h1>${value.stock.qtya} ${value.stock.puom} </h1>
                                                                <hr>
                                                                <h5>
                                                                    <b>Location : ${value.stock.location_code} </b> 
                                                                    <a class="btn btn-md btn-dark mb-3 ml-4" onclick="moveLocation('${value.id}')"><i class="fas fa-sign-in-alt"></i> Transfer
                                                                        Location</a>
                                                                        </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>`)
            });
        }

        function moveLocation(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm Move Location',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    getTransferLokasi(id)
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        function getTransferLokasi(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('inventory/cycleCount/getTransferLokasi') }}/" + id,
                dataType: "json",
                success: function(response) {
                    $('#stokTransfer').modal('show');
                    $('#idDetailValue').val(id);
                    $('#SKUCode').text(response.product_code);
                    $('#qtyValue').val(response.qtya + ' ' + response.puom);
                    $('#locationFrom').val(response.location_code);
                },
                error: function(response) {
                    console.log(response);
                }
            })
        }
    </script>
@endpush
