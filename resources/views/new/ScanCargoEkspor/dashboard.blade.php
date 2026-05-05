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
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-append"><span class="input-group-text"><i
                                                        class="fas fa-barcode fa-lg"></i></span></div>
                                            <input type="text" class="form-control palletBarcode"
                                                placeholder="Scan Here Pallet.." autocomplete="off" autofocus
                                                name="pallet_barcode" required />
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="#" class="btn btn-md btn-dark mb-3" onclick="newPallet()"><i
                                                class="fas fa-plus-circle"></i> New Pallet</a>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-append"><span class="input-group-text"><i
                                                        class="fas fa-boxes fa-lg"></i></span></div>
                                            <input type="text" class="form-control kodeWarehouse"
                                                placeholder="Scan Here Barcode Top.." autocomplete="off"
                                                name="kode_warehouse" required disabled />
                                        </div>
                                        <span class="form-text text-danger">Scan the top barcode on the carton</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-append"><span class="input-group-text"><i
                                                        class="fas fa-boxes fa-lg"></i></span></div>
                                            <input type="text" class="form-control cartonBarcode"
                                                placeholder="Scan Here Barcode Bottom.." autocomplete="off"
                                                name="carton_barcode" required disabled />
                                        </div>
                                        <span class="form-text text-danger">Scan the bottom barcode on the carton</span>
                                    </div>
                                </div>
                                <div class="float-left">
                                    <span class="label label-lg label-light-warning label-inline mb-3"><b class="mr-2"
                                            id="totalPallet">0 </b>
                                        Pallet
                                    </span>
                                    <br>
                                    <span class="label label-lg label-light-warning label-inline mb-3"><b class="mr-2"
                                            id="totalCarton">0 </b>
                                        Carton
                                    </span>
                                </div>
                                <div class="float-right">
                                    <button type="button" class="btn btn-md btn-success mb-3" id="saveToDb"><i
                                            class="fas fa-save"></i> Save Job</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="barcodeTable">
                                        <thead>
                                            <tr>
                                                <th>Pallet</th>
                                                <th>Warehouse</th>
                                                <th>Carton</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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

        function newPallet() {
            let text = "Are you sure to next pallet?";
            if (confirm(text) == true) {
                $('.palletBarcode').prop('disabled', false);
                $('.kodeWarehouse').prop('disabled', true);
                $('.palletBarcode').val('');
            } else {
                return false
            }
        }

        $(document).ready(function() {
            sessionStorage.removeItem('barcodes');
            if (!sessionStorage.getItem('barcodes')) {
                sessionStorage.setItem('barcodes', JSON.stringify([]));
            }

            // Klik pada palletBarcode akan reset value dan memungkinkan pemindaian ulang
            $('.palletBarcode').on('click', function() {
                $(this).prop('disabled', false); // Enable input palletBarcode
                $('.kodeWarehouse').prop('disabled', true); // Disable input kode warehouse
                $('.cartonBarcode').prop('disabled', true); // Disable input cartonBarcode
                $(this).focus(); // Fokuskan input palletBarcode setelah diklik
                $(this).val(''); // Reset nilai palletBarcode
            });

            // Tampilkan data yang ada di sessionStorage ke dalam tabel
            function displayScannedData() {
                var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                $('#barcodeTable tbody').empty(); // Hapus semua baris di tabel
                var totalPallet = new Set(); // Untuk menghitung jumlah pallet unik
                var totalCarton = 0; // Untuk menghitung jumlah carton
                barcodes.forEach(function(item, index) {
                    if (item.pallet && item.kode_warehouse && item.carton) {
                        var row = `<tr data-index="${index}">
                <td>${item.pallet}</td>
                <td>${item.kode_warehouse}</td>
                <td>${item.carton}</td>
                <td><button class="btn btn-danger deleteBtn"><i class="fas fa-trash-alt"></i></button></td>
            </tr>`;
                        $('#barcodeTable tbody').append(row); // Tambahkan baris baru ke tabel
                    }
                    totalPallet.add(item.pallet); // Menambahkan pallet ke Set (pallet unik)
                    totalCarton++; // Menambahkan 1 untuk setiap carton
                });

                $('#totalPallet').text(totalPallet.size); // Ukuran Set adalah jumlah pallet unik
                $('#totalCarton').text(totalCarton);

                // Hapus data ketika tombol delete ditekan
                $('.deleteBtn').on('click', function() {
                    var rowIndex = $(this).closest('tr').data('index');
                    deleteBarcode(rowIndex);
                });
            }

            function deleteBarcode(index) {
                var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                barcodes.splice(index, 1); // Hapus data berdasarkan index
                sessionStorage.setItem('barcodes', JSON.stringify(barcodes)); // Simpan perubahan
                displayScannedData(); // Update tampilan tabel
            }

            // Ketika scan barcode pallet
            $('.palletBarcode').on('keypress', function(event) {
                if (event.keyCode === 13) {
                    var palletBarcode = $(this).val();
                    if (palletBarcode.length > 20 || palletBarcode.length < 20) {
                        $(this).val("");
                        error.play();
                        alert('Invalid barcode. It must be at least 20 characters long.');
                        $(this).focus();
                        return;
                    }
                    var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));

                    // Cek apakah sudah ada pallet yang dipindai
                    if (barcodes.length === 0 || barcodes[barcodes.length - 1].carton !== null) {
                        barcodes.push({
                            pallet: palletBarcode,
                            kode_warehouse: null,
                            carton: null
                        });
                        sessionStorage.setItem('barcodes', JSON.stringify(
                            barcodes)); // Simpan ke sessionStorage
                        success.play(); // Putar suara sukses

                        // Ubah status input
                        $('.palletBarcode').prop('disabled', true);
                        $('.kodeWarehouse').prop('disabled', false);
                        $('.kodeWarehouse').focus();
                    } else {
                        alert('Pallet barcode already scanned!');
                    }
                }
            });

            // Ketika scan barcode warehouse
            $('.kodeWarehouse').on('keypress', function(event) {
                if (event.keyCode === 13) {
                    var kodeWarehouse = $(this).val();

                    if (kodeWarehouse.length != 8) {
                        $(this).val(""); // Fokus ulang ke input
                        error.play(); // Putar suara error
                        alert('Invalid barcode. It must be at least 8 characters long.');
                        $(this).focus(); // Fokus ulang ke input
                        return; // Hentikan proses
                    }

                    var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));

                    // Temukan pallet yang belum ada kode warehouse
                    for (var i = barcodes.length - 1; i >= 0; i--) {
                        if (barcodes[i].kode_warehouse === null && barcodes[i].pallet !== null) {
                            barcodes[i].kode_warehouse = kodeWarehouse;
                            break;
                        }
                    }

                    sessionStorage.setItem('barcodes', JSON.stringify(barcodes));
                    success.play();
                    $('.kodeWarehouse').prop('disabled', true);
                    $('.cartonBarcode').prop('disabled', false);
                    $('.cartonBarcode').focus();
                    displayScannedData(); // Tampilkan data yang dipindai
                    saveToSessionStorage();
                }
            });


            $('.cartonBarcode').on('keypress', function(event) {
                if (event.keyCode === 13) {
                    var cartonBarcode = $(this).val();

                    if (cartonBarcode.length > 20 || cartonBarcode.length < 20) {
                        $(this).val("");
                        error.play();
                        alert('Invalid barcode. It must be at least 20 characters long.');
                        $(this).focus();
                        return;
                    }

                    var palletBarcode = $('.palletBarcode').val();
                    var kodeWarehouse = $('.kodeWarehouse').val();
                    var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));

                    var isCartonExist = barcodes.some(function(item) {
                        return item.carton === cartonBarcode;
                    });

                    if (isCartonExist) {
                        alert('Carton barcode already scanned!');
                        return;
                    }

                    var foundIncomplete = false;
                    for (var i = barcodes.length - 1; i >= 0; i--) {
                        if (barcodes[i].carton === null && barcodes[i].kode_warehouse !== null) {
                            barcodes[i].carton = cartonBarcode; // Set barcode carton
                            barcodes[i].pallet = palletBarcode; // Set pallet barcode
                            foundIncomplete = true;
                            break;
                        }
                    }
                    if (!foundIncomplete) {
                        barcodes.push({
                            pallet: palletBarcode,
                            kode_warehouse: kodeWarehouse, // Set kode warehouse dengan nilai terakhir yang dipindai
                            carton: cartonBarcode // Set carton barcode
                        });
                    }

                    sessionStorage.setItem('barcodes', JSON.stringify(
                        barcodes)); // Simpan ke sessionStorage
                    success.play(); // Putar suara sukses
                    $('.kodeWarehouse').val(''); // Reset kode warehouse
                    $('.cartonBarcode').prop('disabled', true); // Disable input cartonBarcode
                    $('.kodeWarehouse').prop('disabled', false); // Enable input kodeWarehouse
                    $('.kodeWarehouse').focus(); // Fokuskan input kodeWarehouse
                    $(this).val(''); // Reset input cartonBarcode
                    displayScannedData(); // Tampilkan data yang dipindai
                    saveToSessionStorage(); // Simpan data ke sessionStorage
                }
            });


            function saveToSessionStorage() {
                var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
                if (barcodes.length === 0) {
                    error.play();
                    alert("No data to save!");
                    return;
                }
                console.log("Data saved to sessionStorage:", barcodes); // Untuk melihat data yang disimpan
            }

            displayScannedData(); // Inisialisasi tampilan data saat halaman pertama kali dimuat
        });

        $('#saveToDb').on('click', function() {
            var barcodes = JSON.parse(sessionStorage.getItem('barcodes'));
            if (barcodes.length === 0) {
                error.play();
                alert("No data to save!");
                return;
            }

            $.ajax({
                url: "{{ route('submitReceiving') }}",
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
    </script>
@endpush
