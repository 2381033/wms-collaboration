@extends('layouts.main')

@section('title')
    Export - Outbound
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - Outbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Export - Outbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap">
                <div class="col-md 12">
                    <form id="form-update" method="POST">
                        @csrf
                    </form>
                    <form id="form-job" method="POST">
                        @csrf
                        <input type="hidden" id="id" name="id"
                            @isset($header->id) value="{{ $header->id }}" @endisset>
                        <div class="container mt-3">
                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <div class="btn-group">
                                        <a href="{{ url('/export/outbound/create/0') }}" class="btn btn-primary btn-sm"><i
                                                class="fas fa-plus"></i> <span>Add New Job</span></a>
                                        @if (isset($header->id))
                                            @if ($header->status_flag == 'Open')
                                                <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i
                                                        class="fas fa-save"></i> <span>Save</span></button>
                                                <button type="button" class="btn btn-info btn-sm"
                                                    onclick="submitData();"><i class="fas fa-email"></i>
                                                    <span>Submit</span></button>
                                            @else
                                                <button type="button" onclick="updateData();"
                                                    class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                    <span>Update</span></button>
                                            @endif
                                        @else
                                            <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i
                                                    class="fas fa-save"></i> <span>Save</span></button>
                                        @endif
                                        <a id="clp-print" class="btn btn-default btn-sm"><i class="fas fa-print"></i>
                                            <span>CLP</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="job_no">Job Number</label>
                                        <input type="text" id="Job_no" name="job_no"
                                            @isset($header->job_no) value="{{ $header->job_no }}" @endisset
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="job_date">Job Date</label>
                                        <input type="text" id="job_date" name="job_date"
                                            @isset($header->job_date) value="{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}" @endisset
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="branch_id">Branch Name</label>
                                        <select name="branch_id" id="branch_id" class="custom-select"
                                            @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                            @foreach (Auth::user()->branch as $item)
                                                <option value="{{ $item->id }}"
                                                    @isset($header->branch_id) @if ($item->id == $header->branch_id) selected @endif @endisset>
                                                    {{ $item->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Forwarder Name</label>
                                        <input type="hidden" id="forwarder_id" name="forwarder_id"
                                            @isset($header->forwarder_id) value="{{ $header->forwarder_id }}" @endisset>
                                        <input type="text" id="forwarder_name" name="forwarder_name" class="form-control"
                                            @isset($header->forwarder_name) value="{{ $header->forwarder_name }}" @endisset
                                            @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="container_no">Container No</label>
                                        <input type="text" id="container_no" maxlength="11" minlength="11"
                                            name="container_no"
                                            @isset($header->container_no) value="{{ $header->container_no }}" @endisset
                                            class="form-control"
                                            @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="size_id">Container Size</label>
                                        <select name="size_id" id="size_id" class="custom-select">
                                            <option value=""></option>
                                            @foreach ($size_list as $item)
                                                <option value="{{ $item->id }}"
                                                    @isset($header->size_id) @if ($item->id == $header->size_id) selected @endif @endisset>
                                                    {{ $item->size_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Destination</label>
                                        <input type="text" id="destination" name="destination" class="form-control"
                                            @isset($header->destination) value="{{ $header->destination }}" @endisset
                                            @isset($header->id) @if ($header->status_flag !== 'Open') disabled @endif @endisset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Surveyor Name</label>
                                        <input type="text" id="surveyor_name" name="surveyor_name"
                                            class="form-control"
                                            @isset($header->surveyor_name) value="{{ $header->surveyor_name }}" @endisset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vessel Name</label>
                                        <input type="text" id="vessel_name" name="vessel_name" class="form-control"
                                            @isset($header->vessel_name) value="{{ $header->vessel_name }}" @endisset>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Voyage</label>
                                        <input type="text" id="voyage_no" name="voyage_no" class="form-control"
                                            @isset($header->voyage_no) value="{{ $header->voyage_no }}" @endisset>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Qty Cargo</label>
                                        <input type="text" id="qty_cargo" name="qty_cargo" class="form-control"
                                            @isset($header->qty_cargo) value="{{ $header->qty_cargo }}" @endisset
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Volume ( CBM )</label>
                                        <input type="text" id="cbm" name="cbm" class="form-control"
                                            @isset($header->cbm) value="{{ $header->cbm }}" @endisset
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Weight ( Kg )</label>
                                        <input type="text" id="weight" name="weight" class="form-control"
                                            @isset($header->weight) value="{{ $header->weight }}" @endisset
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Total Pallet</label>
                                        <input type="text" id="total_pallet" name="total_pallet" class="form-control"
                                            @isset($header->total_pallet) value="{{ $header->total_pallet }}" @endisset
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <input type="text" id="remarks" name="remarks" class="form-control"
                                            @isset($header->remarks) value="{{ $header->remarks }}" @endisset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @isset($header)
        <section id="contact" class="contact">
            <div class="container">
                <div class="row info-wrap">
                    <div class="col-lg-10">
                        <div class="table-responsive">
                            <table id="table-order" class="table table-striped table-bordered table-sm" style="width:100%;">
                                <thead class="text-center">
                                    <tr>
                                        <th>Action</th>
                                        <th>Consignee Name</th>
                                        <th>Shipper Name</th>
                                        <th>PO No</th>
                                        <th>PEB No</th>
                                        {{-- <th>AJU No</th> --}}
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        @if (isset($header->id))
                            @if ($header->status_flag == 'Open')
                                <button type="button" id="btn-add" class="btn btn-success btn-sm"><i
                                        class="fas fa-plus"></i> <span>Add</span></button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endisset
@endsection

@section('modal')
    <div class="modal fade" role="dialog" id="modal-order">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-add" id="modal-title-add">List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-order" method="post">
                </form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table-list" class="table table-striped table-bordered table-sm"
                                    style="width:100%;">
                                    <thead class="text-center">
                                        <tr>
                                            <th>
                                                <input type="checkbox" required="required" class="stock-check-all">
                                            </th>
                                            <th>Consignee Name</th>
                                            <th>PO No</th>
                                            <th>PEB No</th>
                                            <th>AJU No</th>
                                            <th>Qty Cargo</th>
                                            <th>Total Pallet</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" onclick="saveOrder();" class="btn btn-primary btn-sm" id="btn-save-add"><i
                            class="fas fa-save"></i> <span>Save</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="modal-detail">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-detail" id="modal-title-detail">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <form id="form-detail" name="form-detail" method="post">
                                    @csrf
                                </form>
                                <input type="hidden" id="order_id" name="order_id">
                                <table id="table-detail" class="table table-striped table-bordered table-sm"
                                    style="width:100%;">
                                    <thead class="text-center">
                                        <tr>
                                            <th>
                                                <input type="checkbox" required="required" class="confirm-check-all">
                                            </th>
                                            <th>Serial No</th>
                                            <th>PO No</th>
                                            <th>PEB No</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" onclick="saveDetail();" class="btn btn-primary btn-sm"
                        id="btn-save-detail"><i class="fas fa-save"></i> <span>Save</span></button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i> <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var d = new Date();
            d.setDate(d.getDate());
            $('#eta').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#forwarder_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getForwarderStockExport') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                branch_id: $("#branch_id").val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#forwarder_id').val(ui.item.forwarder_id);
                        $('#forwarder_name').val(ui.item.forwarder_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.forwarder_name + "</div>")
                        .appendTo(ul);
                };


            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        console.log($('#form-job').serialize());
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('export-outbound.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                                $('#btn-save-job').hide();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });

                                    window.open(data.success, '_top');
                                } else {
                                    var pesan =
                                        "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
                                    }
                                    pesan += '</div>';

                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = pesan;
                                    swal({
                                        icon: "error",
                                        content: wrapper
                                    });
                                }
                                $('#btn-save-job').show();
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $("#loader").hide();
                                $('#btn-save-job').show();
                            }
                        });
                    }
                })
            }

            $('#btn-add').on('click', function() {
                var branch_id = $("#branch_id").val();
                var forwarder_id = $("#forwarder_id").val();

                $('#table-list').DataTable().destroy();
                $('#table-list').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    info: false,
                    ajax: {
                        url: "{{ route('export-order.stock') }}",
                        type: "GET",
                        data: {
                            branch_id: branch_id,
                            forwarder_id: forwarder_id
                        }
                    },
                    columns: [{
                            data: 'check',
                            name: 'check',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'consignee_name',
                            name: 'consignee_name'
                        },
                        {
                            data: 'po_number',
                            name: 'po_number'
                        },
                        {
                            data: 'peb_no',
                            name: 'peb_no'
                        },
                        {
                            data: 'aju_no',
                            name: 'aju_no'
                        },
                        {
                            data: 'qty_cargo',
                            name: 'qty_cargo'
                        },
                        {
                            data: 'total_pallet',
                            name: 'total_pallet'
                        },
                    ],
                    order: [
                        [1, 'asc'],
                        [2, 'asc'],
                        [3, 'asc']
                    ]
                });

                $('#modal-order').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $("#table-order").on("click", ".stock-check", function() {
                if (this.checked == true) {
                    $(".stock-check-all").prop("checked", true);
                } else {
                    $(".stock-check-all").prop("checked", false);
                }
            });

            $("#table-order").on("click", ".stock-check-all", function() {
                $(".stock-check").prop("checked", this.checked);
            });

            $("#table-detail").on("click", ".confirm-check", function() {
                if (this.checked == true) {
                    $(".confirm-check-all").prop("checked", true);
                } else {
                    $(".confirm-check-all").prop("checked", false);
                }
            });

            $("#table-detail").on("click", ".confirm-check-all", function() {
                $(".confirm-check").prop("checked", this.checked);
            });

            load_detail();

            function load_detail() {
                var job_id = $('#id').val();

                $('#table-order').DataTable().destroy();
                $('#table-order').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    searching: false,
                    destroy: true,
                    info: false,
                    ajax: {
                        url: "{{ route('export-order.index') }}",
                        type: "GET",
                        data: {
                            job_id: job_id
                        }
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'consignee_name',
                            name: 'consignee_name'
                        },
                        {
                            data: 'shipper_name',
                            name: 'shipper_name'
                        },
                        {
                            data: 'po_number',
                            name: 'po_number'
                        },
                        {
                            data: 'peb_no',
                            name: 'peb_no'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });
            }

            $(document).on('click', '.delete-order', function() {
                dataId = $(this).attr('id');
                $('#action-delete').val('order')
                $('#modal-konfirmasi').modal('show');
            });

            $(document).on('click', '.edit-order', function() {
                dataId = $(this).attr('id');

                $("#order_id").val(dataId);

                $('#table-detail').DataTable().destroy();
                $('#table-detail').DataTable({
                    "dom": '<"wrapper"flipt>',
                    processing: true,
                    serverSide: true,
                    paging: false,
                    searching: false,
                    destroy: true,
                    info: false,
                    ajax: {
                        url: "{{ route('export-outbound-detail.index') }}",
                        type: "GET",
                        data: {
                            order_id: dataId
                        }
                    },
                    columns: [{
                            data: 'check',
                            name: 'check',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'serial_no',
                            name: 'serial_no'
                        },
                        {
                            data: 'po_number',
                            name: 'po_number'
                        },
                        {
                            data: 'peb_no',
                            name: 'peb_no'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                    ],
                    order: [
                        [0, 'asc']
                    ]
                });

                $('#modal-detail').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            });

            $('#btn-delete').click(function() {
                var action = $('#action-delete').val();
                var requestUrl = "";
                var requestData = {};

                if (action == 'order') {
                    requestUrl = "{{ route('export-order.destroy') }}";
                    requestData = {
                        "_token": "{{ csrf_token() }}",
                        "id": dataId
                    };
                }

                $.ajax({
                    url: requestUrl,
                    type: 'delete',
                    data: requestData,
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(data) {
                        $("#loader").hide();
                        setTimeout(function() {
                            $('#modal-konfirmasi').modal('hide');

                            var oTable = "";
                            if (action == 'order') {
                                oTable = $('#table-order').dataTable();
                            }

                            oTable.fnDraw(false);

                            window.location.reload();
                        });

                        if ($.isEmptyObject(data.error)) {
                            swal({
                                icon: "success",
                                text: "Data Successfully Deleted."
                            });
                        } else {
                            swal({
                                icon: "error",
                                text: data.error
                            });
                        }
                    },
                    error: function(data) {
                        $("#loader").hide();
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                })
            });
        });

        $('body').on('click', '#clp-print', function() {
            var data_id = $('#id').val();

            window.open("{{ url('/export/outbound/report/clp/') }}" + "/" + data_id, 'ExportReport',
                'width=800,height=600')
        });

        function saveOrder() {
            var ata = $("#ata").val();

            $('.hidden-order').remove();
            var oTable = $('#table-list').dataTable();

            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-order').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-order')
                        .val(this.value)
                    );
                }
            });

            $('#form-order').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "branch_id")
                .attr('class', 'hidden-order')
                .val($("#branch_id").val())
            );

            $('#form-order').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "job_id")
                .attr('class', 'hidden-order')
                .val($("#id").val())
            );

            $.ajax({
                data: $('#form-order').serialize(),
                url: "{{ route('export-order.store') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-order').trigger("reset");
                    if (data.validate_loc === true) {
                        swal({
                            icon: "warning",
                            text: "Kargo Belum di putaway oleh stapel!!"
                        });
                    } else if ($.isEmptyObject(data.error)) {
                        var oTable = $('#table-order').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data berhasil diproses."
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }

                },
                error: function(data) {
                    $("#loader").hide();
                }
            });
        }

        function submitData() {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this data!",
                icon: "warning",
                buttons: [
                    'No, cancel it!',
                    'Yes, I am sure!'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        data: {
                            job_id: $("#id").val()
                        },
                        url: "{{ route('export-outbound.submit') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            $("#loader").show();
                        },
                        success: function(data) {
                            $("#loader").hide();
                            if ($.isEmptyObject(data.error)) {
                                swal({
                                    icon: "success",
                                    text: "Data was processed successfully."
                                });

                                window.location.reload();
                            } else {
                                swal({
                                    icon: "error",
                                    text: data.error
                                });
                            }
                        },
                        error: function(data) {
                            console.log(data);
                            $("#loader").hide();
                        }
                    });
                } else {

                }
            })
        }

        function saveDetail() {
            var oTable = $('#table-detail').dataTable();
            $('#form-detail').trigger("reset");

            $('.hidden-detail').remove();
            oTable.$('input[type="checkbox"]').each(function() {
                if (this.checked) {
                    $('#form-detail').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-detail')
                        .val(this.value)
                    );
                }
            });

            $('#form-detail').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "job_id")
                .attr('class', 'hidden-detail')
                .val($("#id").val())
            );

            $('#form-detail').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "order_id")
                .attr('class', 'hidden-detail')
                .val($("#order_id").val())
            );

            $.ajax({
                data: $('#form-detail').serialize(),
                url: "{{ route('export-outbound-detail.store') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-detail').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        var oTable = $('#table-detail').dataTable();
                        oTable.fnDraw(false);

                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });

                        window.location.reload();
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    $("#loader").hide();
                }
            });
        }

        function updateData() {
            $('#form-update').trigger("reset");

            $('.hidden-update').remove();
            $('#form-update').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "job_id")
                .attr('class', 'hidden-update')
                .val($("#id").val())
            );

            $('#form-update').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "surveyor_name")
                .attr('class', 'hidden-update')
                .val($("#surveyor_name").val())
            );

            $('#form-update').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "vessel_name")
                .attr('class', 'hidden-update')
                .val($("#vessel_name").val())
            );

            $('#form-update').append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', "voyage_no")
                .attr('class', 'hidden-update')
                .val($("#voyage_no").val())
            );

            $.ajax({
                data: $('#form-update').serialize(),
                url: "{{ route('export-outbound.update') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $("#loader").hide();
                    $('#form-update').trigger("reset");
                    if ($.isEmptyObject(data.error)) {
                        swal({
                            icon: "success",
                            text: "Data was processed successfully."
                        });

                        window.location.reload();
                    } else {
                        swal({
                            icon: "error",
                            text: data.error
                        });
                    }
                },
                error: function(data) {
                    $("#loader").hide();
                }
            });
        }
    </script>
@endpush
