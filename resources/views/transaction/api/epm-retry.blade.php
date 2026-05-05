@extends('layouts.main')

@section('title')
    Retry Api
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Retry Api</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Transaction</a></li>
                    <li>Retry Api</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Table Id</th>
                                    <th>Activity</th>
                                    <th>Job Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-entry">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-entry" name="form-entry" method="post">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Activity</label>
                                    <input type="text" autocomplete="off" id="activity" name="activity"
                                        class="form-control" readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Job Number</label>
                                    <input type="text" autocomplete="off" id="job_no" name="job_no"
                                        class="form-control" readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status Code</label>
                                    <input type="text" autocomplete="off" id="status" name="status"
                                        class="form-control" readonly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Status Error</label>
                                    <textarea name="error" id="error" rows="2" class="form-control" readonly disabled></textarea>
                                    {{-- <div id="idstatuserror"></div> --}}
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Body</label>
                                    <textarea name="body" id="body" rows="5" class="form-control" readonly disabled></textarea>
                                </div>
                            </div>
                        </div> --}}
                        <div id="part_inbound">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shipment Number</label>
                                        <input type="text" autocomplete="off" id="header_shipment_number"
                                            name="header_shipment_number" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shipping ORG</label>
                                        <input type="text" autocomplete="off" id="header_shipping_org"
                                            name="header_shipping_org" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Destination ORG</label>
                                        <input type="text" autocomplete="off" id="header_destination_org"
                                            name="header_destination_org" class="form-control" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Receipt Date</label>
                                        <input type="text" autocomplete="off" id="header_receipt_date"
                                            name="header_receipt_date" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shipping QTY</label>
                                        <input type="text" autocomplete="off" id="header_shipment_qty"
                                            name="header_shipment_qty" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Destination UOM</label>
                                        <input type="text" autocomplete="off" id="header_shipment_uom"
                                            name="header_shipment_uom" class="form-control" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Detail Item On Shipping</label>
                                        <div id="bodyDetail"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Detail Item On Receiving</label>
                                        <div id="bodyBatch"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="part_movement">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Movement ID</label>
                                        <input type="text" autocomplete="off" id="movement_MOVEMENT_ID"
                                            name="movement_MOVEMENT_ID" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Transaction Date</label>
                                        <input type="text" autocomplete="off" id="movement_TRANSACTION_DATE"
                                            name="movement_TRANSACTION_DATE" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ORG Code</label>
                                        <input type="text" autocomplete="off" id="movement_ORG_CODE"
                                            name="movement_ORG_CODE" class="form-control" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Movement Detail</label>
                                        <div id="bodyDetailMovement"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="part_outbound">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Delivery Number</label>
                                        <input type="text" autocomplete="off" id="out_delivery_number"
                                            name="out_delivery_number" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shipment Date</label>
                                        <input type="text" autocomplete="off" id="out_shipment_date"
                                            name="out_shipment_date" class="form-control" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ISO Number</label>
                                        <input type="text" autocomplete="off" id="out_iso_number"
                                            name="out_iso_number" class="form-control" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Detail</label>
                                        <div id="bodyDetailOutbound"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-resend"><i
                                class="fas fa-reload"></i>
                            <span>ReSend</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $('[data-toggle="tooltip"]').tooltip();
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                destroy: true,
                scrollx: true,
                ajax: {
                    url: "{{ route('retry-api-epm.index') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },{
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'job_no',
                        name: 'job_no'
                    },
                    {
                        data: 'status_tooltip',
                        name: 'status_tooltip'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });

            $(document).on('click', '.delete', function() {
                dataId = $(this).attr('id');
                $('#modal-konfirmasi').modal('show');
            });

            $('body').on('click', '.edit-data', function() {
                var data_id = $(this).data('id');
                var url = "{{ url('retry-api-epm') }}";

                $.get(url + '/' + data_id + '/edit', function(data) {
                    $('#modal-title').html("Edit");
                    $('#btn-resend').val("Edit");
                    $('#modal-entry').modal('show');

                    $('#id').val(data.id);
                    $('#activity').val(data.activity);
                    $('#job_no').val(data.job_no);
                    $('#error').val(data.error);
                    $('#status').val(data.status);
                    // $('#idstatuserror').html(data.bodyHeader);

                    $('#body').val(data.body);
                    $('#email_bcc').val(data.email_bcc);
                    $('#active').val(data.active);

                    console.log(data);
                    if (data.partData == 'part_inbound') {
                        $('#part_inbound').show();
                        $('#part_outbound').hide();
                        $('#part_movement').hide();
                        $('#bodyDetail').html(data.bodyDetail);
                        $('#bodyBatch').html(data.bodyBatch);

                        $('#header_shipment_number').val(data.bodyHeader.shipment_number);
                        $('#header_receipt_date').val(data.bodyHeader.receipt_date);
                        $('#header_shipping_org').val(data.bodyHeader.shipping_org);
                        $('#header_shipment_qty').val(data.bodyHeader.shipment_qty);
                        $('#header_destination_org').val(data.bodyHeader.destination_org);
                        $('#header_shipment_uom').val(data.bodyHeader.shipment_uom);
                    } else if (data.partData == 'part_movement') {
                        $('#part_inbound').hide();
                        $('#part_outbound').hide();
                        $('#part_movement').show();
                        $('#bodyDetailMovement').html(data.bodyDetail);

                        $('#movement_MOVEMENT_ID').val(data.bodyHeader.SM_MOVEMENT_ID);
                        $('#movement_ORG_CODE').val(data.bodyHeader.SM_ORG_CODE);
                        $('#movement_TRANSACTION_DATE').val(data.bodyHeader.SM_TRANSACTION_DATE);
                    } else if (data.partData == 'part_outbound') {
                        $('#part_inbound').hide();
                        $('#part_outbound').show();
                        $('#part_movement').hide();
                        $('#bodyDetailOutbound').html(data.bodyBatch);

                        $('#out_delivery_number').val(data.bodyHeader.out_delivery_number);
                        $('#out_shipment_date').val(data.bodyHeader.out_shipment_date);
                        $('#out_iso_number').val(data.bodyHeader.out_iso_number);
                    }

                })
            });

            if ($("#form-entry").length > 0) {
                $("#form-entry").validate({
                    submitHandler: function(form) {
                        var actionType = $('#btn-resend').val();
                        $('#btn-resend').html('Sending..');

                        $.ajax({
                            data: $('#form-entry').serialize(),
                            url: "{{ route('retry-api-epm.resend') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                console.log(data);
                                if ($.isEmptyObject(data.error)) {
                                    $('#form-entry').trigger("reset");
                                    $('#modal-entry').modal('hide');
                                    $('#btn-resend').html('ReSend');
                                    var oTable = $('#table_list').dataTable();
                                    oTable.fnDraw(false);
                                    if (data.status == 200) {
                                        swal({
                                            icon: "success",
                                            text: "Resend API Success with no Error"
                                        });
                                    } else {
                                        let message = data.messages;
                                        swal({
                                            icon: "error",
                                            text: message
                                        });
                                    }
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
                                    $('#btn-resend').html('ReSend');
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $('#btn-resend').html('ReSend');
                            }
                        });
                    }
                })
            }
        });
    </script>
@endpush
