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
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-append"><span class="input-group-text"><i
                                                        class="fas fa-truck fa-lg"></i></span></div>
                                            <input type="text" class="form-control" id="containerNumber"
                                                placeholder="Input here.." autocomplete="off" autofocus name="container_no"
                                                required />
                                        </div>
                                        <span class="form-text text-muted">Container Number</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-append"><span class="input-group-text"><i
                                                        class="fas fa-barcode fa-lg"></i></span></div>
                                            <input type="text" class="form-control" id="cartonBarcode"
                                                placeholder="Scan Here Pallet Barcode..." autocomplete="off"
                                                name="carton_barcode" required disabled />
                                        </div>
                                        <span class="form-text text-danger">Pallet Barcode</span>
                                    </div>
                                </div>
                                <div class="float-left">
                                    <span class="badge badge-primary p-2" style="zoom: 140%;"> <label class="totalPallet">
                                            0 </label> PALLET</span>
                                    <span class="badge badge-primary p-2" style="zoom: 140%;"> <label class="totalCarton">
                                            0 </label> Carton</span>
                                </div>
                                <div class="float-right">
                                    <button type="button" class="btn btn-md btn-dark mb-3" id="saveToDb"><i
                                            class="fas fa-save"></i> Save Job</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="cartonTable">
                                        <thead>
                                            <tr>
                                                <th>Container No</th>
                                                <th>Pallet Barcode</th>
                                                <th>Qty</th>
                                                <th>Time</th>
                                                <th>Action</th> <!-- Tambahan kolom untuk tombol delete -->
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
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        var success = new Audio("{{ url('assets/audio/success.mp3') }}");
        var error = new Audio("{{ url('assets/audio/error.mp3') }}");

        $(document).ready(function() {
            sessionStorage.removeItem('barcodes');
            if (!sessionStorage.getItem('barcodes')) {
                sessionStorage.setItem('barcodes', JSON.stringify([]));
            }

            $('#containerNumber').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            $('#containerNumber').on('keypress', function(event) {
                if (event.keyCode === 13) {
                    var containerNo = $(this).val().trim();
                    if (containerNo === '') {
                        alert('Container Number cannot be empty!');
                        $(this).focus();
                    } else {
                        var containerNumber = $(this).val();
                        $(this).prop('disabled', true);
                        var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                        barcodes.push({
                            container: containerNumber,
                            pallet: []
                        });
                        sessionStorage.setItem('barcodes', JSON.stringify(barcodes));
                        $('#cartonBarcode').prop('disabled', false).focus();
                    }
                }
            });

            $('#cartonBarcode').on('keypress', function(event) {
                if (event.keyCode === 13) {
                    var cartonBarcode = $(this).val();
                    var currentDateTime = new Date();
                    var day = String(currentDateTime.getDate()).padStart(2, '0');
                    var month = String(currentDateTime.getMonth() + 1).padStart(2, '0');
                    var year = currentDateTime.getFullYear();
                    var hours = String(currentDateTime.getHours()).padStart(2, '0');
                    var minutes = String(currentDateTime.getMinutes()).padStart(2, '0');
                    var formattedDateTime = day + '-' + month + '-' + year + ' ' + hours + ':' + minutes;

                    var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                    var lastEntry = barcodes[barcodes.length - 1];

                    if (lastEntry.pallet.includes(cartonBarcode)) {
                        error.play();
                        alert('Duplicate Pallet Barcode!');
                        $(this).val('');
                        return;
                    }

                    success.play();
                    lastEntry.pallet.push(cartonBarcode);
                    sessionStorage.setItem('barcodes', JSON.stringify(barcodes));

                    $('#cartonTable tbody').append(
                        '<tr data-barcode="' + cartonBarcode + '">' +
                        '<td>' + lastEntry.container + '</td>' +
                        '<td>' + cartonBarcode + '</td>' +
                        '<td class="qty-cell">-</td>' +
                        '<td>' + formattedDateTime + '</td>' +
                        '<td><button class="btn btn-sm btn-danger delete-row"><i class="fas fa-trash-alt"></i></button></td>' +
                        '</tr>'
                    );

                    $(this).val('');
                    $(this).focus();
                    getPalletStuffing();
                }
            });

            function getPalletStuffing() {
                var barcodeRaw = sessionStorage.getItem('barcodes');
                let isiPallet = JSON.parse(sessionStorage.getItem('barcodes'));
                let jumlahLine = isiPallet[0].pallet.length;
                if (barcodeRaw) {
                    var barcodeData = JSON.parse(barcodeRaw);
                    if (barcodeData.length > 0 && barcodeData[0].pallet) {
                        var palletParam = barcodeData[0].pallet.join(',');
                        $.ajax({
                            url: "{{ url('export/ScanCargoEkspor/getPalletStuffing') }}/" + palletParam,
                            success: function(response) {
                                $('.totalCarton').text(response.data.counting)
                                $('.totalPallet').text(jumlahLine)
                                let qtyData = response.data.qty_per_pallet;

                                // Update setiap baris berdasarkan barcode dan isi kolom qty
                                qtyData.forEach(function(item) {
                                    let barcode = item.pallet;
                                    let qty = item.line_count;
                                    // Temukan <tr> berdasarkan data-barcode
                                    let row = $('#cartonTable tbody').find('tr[data-barcode="' +
                                        barcode + '"]');
                                    if (row.length > 0) {
                                        if (row.find('td.qty-cell').length === 0) {
                                            row.find('td').eq(2).after('<td class="qty-cell">' +
                                                qty + '</td>');
                                        } else {
                                            row.find('td.qty-cell').text(qty);
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        $('.totalCarton').text('')
                    }
                } else {
                    console.error("Tidak ada data 'barcodes' di sessionStorage.");
                }

            }
            // Event untuk tombol delete
            $(document).on('click', '.delete-row', function() {
                var row = $(this).closest('tr');
                var barcode = row.data('barcode');
                var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));

                if (barcodes.length > 0) {
                    var lastEntry = barcodes[barcodes.length - 1];
                    lastEntry.pallet = lastEntry.pallet.filter(function(item) {
                        return item !== barcode;
                    });
                    sessionStorage.setItem('barcodes', JSON.stringify(barcodes));
                }

                row.remove();
                getPalletStuffing();
            });

            $('#saveToDb').on('click', function() {
                var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                if (barcodes.length === 0 || barcodes.every(b => b.pallet.length === 0)) {
                    error.play();
                    alert("No data to save!");
                    return;
                }

                $.ajax({
                    url: "{{ route('submitStuffing') }}",
                    method: "POST",
                    data: {
                        barcodes: barcodes,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.original.success) {
                            alert("Data successfully saved!");
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Error: " + error);
                    }
                });
            });
        });
    </script>
@endpush
