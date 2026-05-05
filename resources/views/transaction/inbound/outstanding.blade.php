@extends('layouts.main')

@section('title')
    Scan Carton
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
                </ol>
            </div>
        </div>
    </section>

    <div class="container">
        <section id="contact" class="contact">
            <form method="post" id="outstand">
                @csrf
                <div class="row">
                    <div class="col-sm-2">
                        <label for="startDate">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" required value="{{ date('Y-m-01') }}" id="startDate"
                            name="start">
                    </div>
                    <div class="col-sm-2">
                        <label for="endDate">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" required value="{{ date('Y-m-t') }}" id="endDate"
                            name="end">
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="po">PO Number <span class="text-muted">(Optional)</span></label>
                            <input type="text" id="po" name="po" class="form-control" autocomplete="off"
                                placeholder="Enter PO Number" autofocus="on">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="custom-select" id="status_code" name="status_code">
                                <option value="No">Open</option>
                                <option value="Yes">Confirmed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2 mt-4">
                        <button type="submit" class="btn btn-dark" id="tombolCari">Search</button>
                    </div>
                </div>
            </form>
            <div class="col-sm-12 mt-4">
                <div class="table-responsive">
                    <table class="table table-striped" id="scanTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Shipper Name</th>
                                <th>Forwarder Name</th>
                                <th>PO Number</th>
                                <th>Checker</th>
                                <th>Job Date</th>
                                <th>QTY Booking</th>
                                <th>QTY Actual</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic_field">
                            <!-- Data will be populated here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">PO Number Details</h5>
                    <button type="button" class="btn btn-info" id="confirmButton" onclick="confirmAllItems()">Confirm All
                        Items
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Carton ID</th>
                                <th>Scan By</th>
                                <th>Scan Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="modal_body">
                        </tbody>
                    </table>
                    <form id="addRowForm">
                        @csrf
                        <input type="hidden" id="hiddenPoNumber" name="po_number" value="">
                        <input type="hidden" id="hiddenStartDate" name="start_date" value="">
                        <input type="hidden" id="hiddenEndDate" name="end_date" value="">
                        <input type="hidden" id="hiddenStatus" name="status" value="">
                        <input type="hidden" id="hiddenScanBy" name="scan_by" value="{{ Auth::user()->username }}">
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="newCartonID" class="form-label">PO Number</label>
                                <input type="text" class="form-control" id="po_number_new" name="po_number_new"
                                    required autocomplete="off" placeholder="Silahkan di isi..">
                            </div>
                            <div class="col-sm-3">
                                <label for="newCartonID" class="form-label">Carton ID</label>
                                <input type="text" class="form-control" id="newCartonID" name="carton_id" required
                                    autocomplete="off" placeholder="Silahkan di isi..">
                            </div>
                            <div class="col-sm-3">
                                <label for="newScanDate" class="form-label">Scan Date</label>
                                <input type="date" class="form-control" id="newScanDate" name="scan_date" required>
                            </div>
                            <div class="col-sm-3 mt-2">
                                <button type="submit" class="btn btn-primary btn-md mt-4">
                                    Add Row
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $('#po').on('keypress', function(event) {
            if (event.which === 13) {
                event.preventDefault();
            }
        });

        $('#outstand').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ url('/export/inbound/scanCtn/outstanding/search') }}",
                type: 'POST',
                data: $('#outstand').serialize(),
                success: function(response) {
                    populateTable(response.data);
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        })

        function populateTable(params) {
            $('#dynamic_field').html("");
            $.each(params.data, function(index, item) {
                var status = item.confirmed_flag == 'Yes' ? "<label class='text-success'>Confirmed</label>" :
                    "<label class='text-danger'>Open</label>";
                var btnQtyBooking = "";
                var btnEdit = "";
                if (item.confirmed_flag == 'Yes') {
                    var btnQtyBooking = '';
                    var btnEdit = '';
                } else {
                    var btnEdit =
                        `<button type="button" class="btn btn-primary mb-2" onclick="showModalList('${item.po_number}')">Edit Qty Actual</button>`;
                    var btnQtyBooking =
                        `<button type="button" class="btn btn-dark" onclick="editQtyActual('${item.po_number}')">Edit Qty Booking</button>`
                }
                $('#dynamic_field').append(`
                <tr>
                    <td>${parseInt(index+1)}</td>
                    <td>${item.shipper}</td>
                    <td>${item.customer}</td>
                    <td>${item.po_number}</td>
                    <td>${item.checker}</td>
                    <td>${m_d_y(item.job_date)}</td>
                    <td>${item.qtyBooking} CTN</td>
                    <td>${params.actual[item.po_number]} CTN</td>
                    <td>${status}</td>
                    <td>
                        ${btnEdit}
                        ${btnQtyBooking}
                    </td>
                </tr>
            `);
            });
        }

        function m_d_y(dateString) {
            var date = new Date(dateString);
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function editQtyActual(po) {
            let qty = prompt("Masukan Qty terbaru:", "");
            if (qty != null) {
                location.href = "{{ url('export/inbound/scanCtn/editQtyActual/') }}/" + po + '/' + qty;
            }
        }


        function showModalList(po) {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $.ajax({
                url: "{{ url('export/inbound/scanCtn/details') }}",
                type: 'GET',
                data: {
                    po_number: po,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    var modalBodyHtml = '';
                    $.each(response.data, function(index, item) {
                        var status = item.confirmed_flag === 'Yes' ?
                            '<span class="text-success">Confirmed</span>' :
                            '<span class="text-danger">Open</span>';

                        var deleteButton =
                            '<button type="button" class="btn btn-danger btn-sm" onclick="deleteItem(\'' +
                            item.id + '\', \'' + item.po_number + '\')">Delete</button>';

                        modalBodyHtml += '<tr>';
                        modalBodyHtml += '<td>' + item.po_number + '</td>';
                        modalBodyHtml += '<td>' + item.barcode_carton + '</td>';
                        modalBodyHtml += '<td>' + item.scan_by + '</td>';
                        modalBodyHtml += '<td>' + formatDateTime(item.scan_at) + '</td>';
                        modalBodyHtml += '<td>' + deleteButton + '</td>';
                        modalBodyHtml += '</tr>';
                    });

                    $('#modal_body').html(modalBodyHtml);
                    $('#hiddenPoNumber').val(po);
                    $('#confirmButton').data('po-number', po); // Store PO number for bulk confirmation

                    // Show the modal
                    $('#detailsModal').modal('show');
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        }

        function formatDateTime(dateTimeStr) {
            var date = new Date(dateTimeStr);
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            var hours = ('0' + (date.getHours() % 12 || 12)).slice(-2); // 12-hour format
            var minutes = ('0' + date.getMinutes()).slice(-2);
            // var period = date.getHours() >= 12 ? 'PM' : 'AM'; // AM/PM period

            return day + '-' + month + '-' + year + ' ' + hours + ':' + minutes; // + ' ' + period;
        }


        function confirmAllItems() {
            var poNumber = $('#confirmButton').data('po-number');

            if (!poNumber) {
                alert('PO Number is missing.');
                return;
            }

            if (confirm('Are you sure you want to confirm all items for PO Number ' + poNumber + '?')) {
                $.ajax({
                    url: "{{ url('export/inbound/scanCtn/confirmAll') }}", // Ensure this is the correct route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        po_number: poNumber
                    },
                    success: function(response) {
                        showTable(); // Refresh the table
                        $('#detailsModal').modal('hide');
                        // if (response.success) {
                        //     alert(response.message); // Success message
                        // } else {
                        //     alert('An error occurred: ' + response.error);
                        // }
                    },
                    error: function(xhr) {
                        alert('An error occurred while confirming all items.');
                    }
                });
            }
        }

        $('#addRowForm').on('submit', function(e) {
            e.preventDefault();
            var poNumber = $('#hiddenPoNumber').val(); // Get the PO number from the hidden field
            var cartonID = $('#newCartonID').val();
            var scanDate = $('#newScanDate').val(); // This will be in YYYY-MM-DD format

            $('#hiddenStartDate').val($('#startDate').val())
            $('#hiddenEndDate').val($('#endDate').val())
            $('#hiddenStatus').val($('#status_code').val())
            $.ajax({
                url: "{{ url('/export/inbound/scanCtn/add-row') }}",
                type: 'POST',
                data: $('#addRowForm').serialize(),
                success: function(response) {
                    if (response.message == 'not_same') {
                        alert('PO number is not the same');
                    } else if (response.message == 'duplicate') {
                        alert('Carton ID has already been added');
                    } else {
                        $('#detailsModal').modal('hide');
                        alert('Row added successfully.');
                        showModalList(poNumber);
                        populateTable(response.data);
                        $('#newCartonID').val("");
                        $('#po_number_new').val("");
                        $('#newScanDate').val("");
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        });

        function deleteItem(id, po) {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var status = $('#status_code').val();
            if (confirm('Are you sure you want to delete this item?')) {
                // var poNumber = $('#hiddenPoNumber').val(); 
                $.ajax({
                    url: "{{ url('/export/inbound/scanCtn/modal/deleteQty') }}/" + id + '/' + po + '/' +
                        startDate + '/' + endDate + '/' + status,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.data) {
                            $('#detailsModal').modal('hide');
                            alert('Item deleted successfully.');
                            showModalList(po);
                            populateTable(response.data);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the item.');
                    }
                });
            }
        }


        // Function to handle barcode edits
        // function deletePerRowModal(id) {
        //     if (!confirm('Are you sure you want to delete this item?')) {
        //         return;
        //     }

        //     $.ajax({
        //         url: '{{ url('/export/inbound/deleteQty') }}', // Your delete endpoint
        //         type: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             id: id
        //         },
        //         success: function(response) {
        //             alert('Item deleted successfully!');
        //             // Optionally, refresh the modal or table data
        //             $('#detailsModal').modal('hide');
        //             // You may need to call showModalList again if you want to refresh the list
        //         },
        //         error: function(xhr) {
        //             console.error('An error occurred:', xhr.responseText);
        //         }
        //     });
        // }
    </script>
@endpush
