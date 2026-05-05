@extends('layouts.main')

@section('title')
    Scan Carton Partial
@endsection

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .custom-col {
            flex: 0 0 23.16667%;
            /* Adjust the percentage as needed */
            max-width: 23.16667%;
        }

        .status-ok {
            color: green;
        }

        .status-not-ok {
            color: red;
        }
    </style>
@endpush

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Inbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Inbound</li>
                    <li>Scan Carton</li>
                    <li>List Partial</li>
                </ol>
            </div>
        </div>
    </section>

    <div class="container">
        <section id="contact" class="contact">
            <div class="row">
                <div class="col-sm-12">
                    <a href="{{ url('export/inbound/scanCtn') }}" class="btn btn-md btn-dark mb-2"> <i
                            class="fas fa-reply text-white"></i> Back</a>
                </div>
                <div class="col-sm-12">
                    <form id="kirim" method="post" action="{{ url('export/inbound/scanCtn/submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="po">PO Number</label>
                                    <input type="text" id="po" name="po" class="form-control"
                                        autocomplete="off" placeholder="Enter PO Number" autofocus="on">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="qty">QTY Carton</label>
                                    <input type="number" id="qty" readonly name="qty" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr style="border-top: 1px dashed black;">
                            </div>
                            <div class="col-sm-8 hide">
                                <div class="form-group">
                                    <label for="Scanpo">Scan PO Number</label>
                                    <input type="text" id="Scanpo" name="Scanpo" class="form-control"
                                        autocomplete="off" placeholder="Scan PO number">
                                </div>
                                <div class="form-group">
                                    <label for="Scanctn">Scan Carton ID</label>
                                    <input type="text" id="Scanctn" name="Scanctn" class="form-control"
                                        autocomplete="off" placeholder="Scan Carton">
                                </div>
                                <div class="counting"></div>
                                <button type="button" class="btn btn-success btn-lg hide" id="confirmButton"
                                    onclick="confirms()">Confirm Job
                                </button>
                            </div>
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="scanTable">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>PO Number</th>
                                                <th>Carton ID</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic_field">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        var success = new Audio("{{ url('assets/audio/success.mp3') }}");
        var error = new Audio("{{ url('assets/audio/error.mp3') }}");

        function showTable() {
            var po = $('#po').val();
            var qty = $('#qty').val();
            if (po == '' || qty == '') {
                error.play();
                alert('Please scan PO Number!')
                $('#startScanButton').show();
            } else {
                $('.hide').removeClass('hide');
                $('#po').attr('readonly', true);
                $('#qty').attr('readonly', true);
                $('#startScanButton').hide();
            }
        }

        $('#po').on('keydown', function(event) {
            event.preventDefault();
            var po = $('#po').val();
            if (event.keyCode === 13) {
                if (po == "" || po == null) {
                    error.play();
                    $('#po').val("")
                    alert('PO is required!')
                } else {
                    resumeScan(po);
                }
            }
        })

        $('#Scanpo').on('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                var po = $('#po').val();
                var scanPo = $('#Scanpo').val();
                var qty = parseInt($('#qty').val());
                if (po === scanPo) {
                    success.play();
                    $('#Scanpo').attr('readonly', true);
                    $('#Scanctn').focus();
                    $('#po').val(po);
                } else {
                    error.play();
                    alert('PO Number tidak cocok');
                    $('#Scanpo').val('').focus();
                }
            }
        });

        function confirms() {
            var po = $('#po').val();
            var qtyBooking = $('#qty').val();
            var qtyActual = sessionStorage.getItem('qtyCarton');
            if (qtyActual < qtyBooking) {
                var message =
                    'Qty receiving lebih kecil dari qty booking, sistem akan memberitahu spv untuk melakakukan request konfirmasi';
            } else if (qtyActual > qtyBooking) {
                var message =
                    'Qty receiving lebih besar dari qty booking, sistem akan memberitahu spv untuk melakakukan request konfirmasi';
            } else {
                var message = 'Apakah anda yakin ingin mengkonfirmasi?';
            }
            if (confirm(message)) {
                $('#dynamic_field').html('');
                $('#confirmButton').hide();
                $('#partialButton').hide();
                updateWhenConfirm(po);
                window.location.reload();
            }
        }

        function partial() {
            var po = $('#po').val();
            var message = 'Apakah anda yakin ingin menandai PO ini sebagai pengiriman parsial?';
            if (confirm(message)) {
                $('#dynamic_field').html('');
                $('#confirmButton').hide();
                $('#partialButton').hide();
                location.href = "{{ url('export/inbound/scanCtn/tagPartial') }}/" + po;
            }
        }

        function resumeScan(po) {
            $.ajax({
                url: "{{ url('export/inbound/scanCtn/resumeScan') }}/" + po,
                type: 'GET',
                success: function(response) {
                    if (response.data.length > 0) {
                        var qty = response.data[0].qty;
                        $('#qty').val(qty)
                        $('#po').val(po);
                        $('#po').attr('readonly', true);
                        $('#qty').attr('readonly', true);
                        $('.hide').removeClass('hide');
                        $('#Scanpo').focus();
                        updateTable(response.data);
                        appendCounting(response.data.length);
                    } else {
                        $('#po').val("");
                        error.play();
                        alert('No data found!');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while deleting the item.');
                }
            });
        }

        $('#Scanctn').on('keydown', function(event) {
            event.preventDefault();
            if (event.keyCode === 13) {
                var scanCt = $('#Scanctn').val().trim();
                var povalue = $('#Scanpo').val();
                if (povalue == '' || povalue == null) {
                    error.play();
                    alert('Scan PO Number is required')
                }
                if (scanCt == '' || scanCt == null) {
                    error.play();
                    alert('Scan Carton ID is required')
                } else {
                    var qty = parseInt($('#qty').val().trim());
                    $.ajax({
                        url: $('#kirim').attr('action'),
                        type: 'POST',
                        data: $('#kirim').serialize(),
                        success: function(response) {
                            if (response.data) {
                                if (response.data == 'duplicate') {
                                    error.play();
                                    $('#Scanctn').val('').focus();
                                    alert('carton has been scanned!');
                                } else {
                                    success.play();
                                    updateTable(response.data);
                                    appendCounting(response.data.length);
                                    sessionStorage.setItem('qtyCarton', response.data.length);
                                    $('#Scanctn').val('');
                                    $('#Scanpo').attr('readonly', false);
                                    $('#Scanpo').val('');
                                    $('#Scanpo').focus();
                                    if (response.data.length > 0) {
                                        $('#confirmButton').show();
                                        $('#partialButton').show();
                                    }
                                }
                            }
                        },
                        error: function(xhr) {
                            alert('An error occurred while processing your request.');
                        }
                    });
                }
            }
        });

        function appendCounting(number) {
            $('.counting').html("")
            if (number > 0) {
                $('.counting').append("<label for'' class='text-success'><span class='counting'>" + number +
                    "</span> CTN Success!</label>")
            }
        }

        function updateTable(data) {
            $('#dynamic_field').html('');
            var hasNotConfirmed = false;
            appendCounting(data.length)
            sessionStorage.setItem('qtyCarton', data.length);
            $(data).each(function(index, item) {
                var num = parseInt(index + 1);
                var rowHtml = `
            <tr>
                <td>${num}</td>
                <td>${item.po_number}</td>
                <td>${item.barcode_carton}</td>
                <td class='status-ok'>Success</td> 
                <td class='act'>
                    <a href='#' onclick='deleted(${item.id})' class='btn btn-danger btn-sm'>
                        <i class='fa fa-trash-alt text-white'></i>
                    </a>
                </td>
            </tr>
            `;
                $('#dynamic_field').append(rowHtml);
            });

            // if (hasNotConfirmed) {
            //     $('#confirmButton').show();
            // } else {
            //     $('#confirmButton').hide();
            // }
        }



        function deleted(id) {
            if (confirm('Are you sure you want to delete this item?')) {

                var qty = parseInt($('#qty').val().trim());
                var formData = {
                    qty: qty,
                };
                $.ajax({
                    url: "{{ url('export/inbound/scanCtn/delete') }}/" + id,
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        // console.log(response.data);

                        updateTable(response.data);
                        if (response.data.length > 0) {
                            $('#confirmButton').show();
                            $('#partialButton').show();
                        } else {
                            $('#confirmButton').hide();
                            $('#partialButton').hide();

                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the item.');
                    }
                });
                1
            }
        }

        function updateWhenConfirm(po) {
            $.ajax({
                url: "{{ url('/export/inbound/scanCtn/save') }}" + "/" + po,
                type: 'GET',
                success: function(response) {

                },
                error: function(xhr) {
                    alert('An error occurred while updating the status.');
                }
            });
        }
    </script>
@endpush
