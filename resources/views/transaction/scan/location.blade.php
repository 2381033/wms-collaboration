@extends('layouts.main')

@section('title')
    Scan QR Location
@endsection

<style>

</style>

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Scan QR Location</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Scan Location </li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 p-2" style="outline: solid; border-radius: 10px;">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" onclick="typeScan('location')"
                                                name="optradio" style="width: 23px; height: 23px;"><b
                                                class="ml-1 text-lg">Per
                                                Location</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-1"></div>
                                <div class="col-sm-4 p-2" style="outline: solid; border-radius: 10px;">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" onclick="typeScan('blok')"
                                                name="optradio" style="width: 23px; height: 23px;"><b
                                                class="ml-1 text-lg">Per Blok</b>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center dotted ">
                                    <hr style="outline-style: dotted;">
                                </div>
                                <div class="col-sm-12">
                                    <div class="contentAppend">

                                    </div>
                                    <div class="contentBlok">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <div class="modal fade" id="modal-scan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true"
        data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="contentBlokScan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/html5-qrcode.min.js"></script>
    <script>
        function typeScan(type) {
            if (type == 'blok') {
                $('.contentBlok').html("");
                $('.contentAppend').html("");
                ajaxgetBlokLocation()
            } else {
                $('.contentAppend').html("");
                $('.contentBlok').html("");
                $('.contentAppend').append(`
                    <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="alert">
                                        <div class="alert alert-warning mb-4" role="alert" id="">
                                            Silahkan Scan Lokasi
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
                            <div class="row">
                                <div class="col-sm-12">
                                    <center>
                                        Atau
                                        <input type="text" class="form-control mt-3 inputManual" onkeyup="scanManual()" name="" id="" aria-describedby="helpId" placeholder="Masukan Lokasi..">
                                    </center>
                                </div>
                            </div>`);

                var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
                    fps: 10,
                    qrbox: 250,
                    rememberLastUsedCamera: true
                });
                html5QrcodeScanner.render(onScanSuccess);
            }
        }

        function scanManual() {
            var value = $('.inputManual').val();
            if (event.keyCode === 13) {
                onScanSuccess(value, value);
            }
        }


        function ajaxgetBlokLocation() {
            $.ajax({
                url: "{{ url('warehouse/scan-qr-location/getBlokLocation') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    thisBlok(response.data)
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

        function thisBlok(array) {
            $('.contentAppend').append(
                ` <div class="form-group"><select class="form-control" onchange="locationSelect(this.value)" name="" id="selectLocation" style="width: 100%;"></select></div>`
            )
            $('#selectLocation').html('')
            $('#selectLocation').append(`<option value="" selected disabled>Choose Blok</option>`)
            $.each(array, function(index, value) {
                $('#selectLocation').append(`<option value="${value}">BLOK - ${value}</option>`)
            });
            $('#selectLocation').select2();

        }

        function locationSelect(value) {
            ajaxGetSkuOnBlok(value);
        }

        function ajaxGetSkuOnBlok(blok) {
            $.ajax({
                url: "{{ url('warehouse/scan-qr-location/getSkuOnBlok') }}/" + blok,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.contentBlok').html("");
                    $('.contentBlok').append(`
                    <hr style="outline-style: dotted;">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="float-left">
                                    <input type="text" style="background-color: antiquewhite;" class="form-control bySKU" onkeyup="searchSKU(this.value)" placeholder="Search SKU or status.." style="zoom: 150%;">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-right">
                                    <input type="text" style="background-color: bisque;" class="form-control byLoc" onkeyup="searchLoc(this.value)" placeholder="Search location or status..." style="zoom: 150%;">
                                </div>
                            </div>
                        </div>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 cross-card mb-5 row-append">
                          
                        </div>
                    `)
                    $('.row-append').html("");
                    $.each(response.data, function(index, value) {
                        if (value.status == 'G') {
                            var status = '<span class="badge badge-pill badge-success">GOOD</span>'
                        } else {
                            var status = '<span class="badge badge-pill badge-danger">BAD</span>'
                        }
                        $('.row-append').append(`
                            <div class="col">
                                <div class="card box-5 shadow-sm h-70 p-0 shadow-lg mt-2 mb-2 card-item" style="border-radius: 15px;">
                                        <div class="card-body posiction-relative">
                                            <div class="float-right"><i
                                                    class="fas fa-info-circle fa-lg text-primary"></i>
                                            </div>
                                            <div class="card-title d-flex mb-2">
                                                <h4 class="day"><b>${value.location_code}</b></h4>
                                            </div>
                                            <h1 class="card-text my-4"><a class="">${value.product_code}</a></h1>
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>SOH</th>
                                                        <th>SOA</th>
                                                        <th>SOB</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="text-center">
                                                        <td>${value.qtys}</td>
                                                        <td>${value.qtya}</td>
                                                        <td>${value.qtyp}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer"> <span>Status: ${status}</span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                    });
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

        function appendContentScan(array) {
            $('#modal-scan').modal('show');
            $('.contentBlokScan').html("");
            $('.contentBlokScan').append(`
                        <div class="row row-append">
                          
                        </div>
                    `)
            $('.row-append').html("");
            $.each(array, function(index, value) {
                if (value.status == 'G') {
                    var status = '<span class="badge badge-pill badge-success">GOOD</span>'
                } else {
                    var status = '<span class="badge badge-pill badge-danger">BAD</span>'
                }
                $('.row-append').append(`
                            <div class="col-sm-6">
                                <div class="card box-5 shadow-sm h-60 p-0 shadow-lg mt-3 mb-3 card-item" style="border-radius: 15px;">
                                        <div class="card-body posiction-relative">
                                            <div class="float-right"><i
                                                    class="fas fa-info-circle fa-lg text-primary"></i>
                                            </div>
                                            <div class="card-title d-flex mb-2">
                                                <h4 class="day"><b>${value.location_code}</b></h4>
                                            </div>
                                            <h3 class="card-text my-4"><a class="">${value.product_code}</a></h3>
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>SOH</th>
                                                        <th>SOA</th>
                                                        <th>SOB</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="text-center">
                                                        <td>${value.qtys}</td>
                                                        <td>${value.qtya}</td>
                                                        <td>${value.qtyp}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer text-center"> <span>Status: ${status}</span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`);
            });
        }

        function searchSKU(val) {
            var $targets = $('.card-item');
            $('.byLoc').val("");
            $targets.show();
            var text = val.toLowerCase();
            if (text) {
                $targets.filter(':visible').each(function() {
                    var $target = $(this);
                    var $matches = 0;
                    // Search only in targeted element
                    $target.find('card-text').add($target).each(function() {
                        if ($(this).text().toLowerCase().indexOf("" + text + "") !== -1) {
                            $matches++;
                        }
                    });
                    if ($matches === 0) {
                        $target.hide();
                    }
                });
            }
        }

        function searchLoc(val) {
            var $targets = $('.card-item');
            $('.bySKU').val("");
            $targets.show();
            var text = val.toLowerCase();
            if (text) {
                $targets.filter(':visible').each(function() {
                    var $target = $(this);
                    var $matches = 0;
                    // Search only in targeted element
                    $target.find('card-title').add($target).each(function() {
                        if ($(this).text().toLowerCase().indexOf("" + text + "") !== -1) {
                            $matches++;
                        }
                    });
                    if ($matches === 0) {
                        $target.hide();
                    }
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            $.ajax({
                url: "{{ url('warehouse/scan-qr-location/doScanLocation') }}/" + decodedText,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.inputManual').val("");
                    if (response.data == 'location') {
                        swal({
                            icon: 'error',
                            title: 'Location not found..',
                        })
                    } else if (response.data == 'null') {
                        swal({
                            icon: 'warning',
                            title: 'Item is not available at this location..',
                        })
                    } else {
                        appendContentScan(response.data);
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
    </script>
@endpush
