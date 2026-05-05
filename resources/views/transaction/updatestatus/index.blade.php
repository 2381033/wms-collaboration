@extends('layouts.main')

@section('title')
    Inventory - Update Status Product
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Inventory - Update Status Product</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Inventory - Update Status Product</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;"
                            style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Principal</th>
                                    <th>Product Code</th>
                                    <th>#</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-show" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th colspan="6">Product: <b id="productText"></b></th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>#</th>
                                        <th>Product Code</th>
                                        <th>Location Code</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="listingSKU">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close"></i>
                        <span>Close</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-edit" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('submitUpdateStatusProduct') }}" method="POST" id="submitUpdateStatusProduct">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">FORM UPDATE STATUS PRODUCT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="idLedger" name="id_ledger" required>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Product Code</label>
                                    <input type="text" name="" id="productCode" disabled class="form-control"
                                        placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Status Product</label>
                                    <input type="text" name="" id="statusNow" disabled class="form-control"
                                        placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Location Code</label>
                                    <input type="text" name="" id="locationNow" disabled class="form-control"
                                        placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Stock</label>
                                    <input type="text" name="" id="stockNow" disabled class="form-control"
                                        placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr>
                                <div class="form-group">
                                    <label for="">Status Terbaru</label>
                                    <select class="form-control" name="status" required id="">
                                        <option value="" selected disabled>PILIH STATUS</option>
                                        <option value="B">BAD</option>
                                        <option value="G">GOODS</option>
                                        <option value="K">QUARANTINE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i>
                            <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-lg btn-save"><i class="fas fa-save"></i>
                            Submit
                        </button>
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
            load_data();

            function load_data() {
                $('#table_list').DataTable().destroy();
                $('#table_list').DataTable({
                    "dom": '<"toolbar">frtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('inventory/updateStatusProduct/getList') }}",
                        type: "GET",
                    },
                    columns: [{
                            data: 'principal',
                            name: 'principal'
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(data, type, row) {
                                return `<a href="#" onclick="showData('${row.product_id}')"
                                                    class="btn btn-sm btn-dark"><i class="fas fa-eye"></i> Show
                                                </a>`;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ]
                });
            }

            $('#form-edit').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-edit').serialize(),
                    url: "{{ url('inventory/updateBatch/submit') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(data) {
                        $('#modal-edit').modal('hide');
                        $("#loader").hide();
                        load_data();
                    }
                });
            });

        });


        function showData(product_id) {
            $.ajax({
                url: "{{ url('inventory/updateStatusProduct/showData') }}/" + product_id,
                type: "GET",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    $('#modal-show').modal('show');
                    $("#loader").hide();
                    $('#productText').text(data[0].product_code);
                    $("#listingSKU").html("");
                    $.each(data, function(key, val) {
                        if (val.status == 'G') {
                            var status = 'GOODS'
                        }
                        else if (val.status == 'K') {
                            var status = 'QUARANTINE'
                        }
                         else {
                            var status = 'BAD'
                        }
                        $("#listingSKU").append(`
                            <tr class="text-center">
                                <td>${key+1}</td>
                                <td><a href="#" onclick="editData('${val.id}')" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>
                                </td>
                                <td>${val.product_code}</td>
                                <td>${val.location_code}</td>
                                <td>${val.qtya + ' '+val.puom}</td>
                                <td>${status}</td>
                            </tr>
                        `);
                    });
                }
            })
        }

        function editData(id) {
            $.ajax({
                url: "{{ url('inventory/updateStatusProduct/editData') }}/" + id,
                type: "GET",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    if (data.status == 'G') {
                        var statusNow = 'GOODS'
                    } else {
                        var statusNow = 'BAD'
                    }
                    $('#idLedger').val(data.id);
                    $("#loader").hide();
                    $('#modal-show').modal('hide');
                    $('#modal-edit').modal('show');
                    $('#productCode').val(data.product_code);
                    $('#statusNow').val(statusNow);
                    $('#locationNow').val(data.location_code);
                    $('#stockNow').val(data.qtya + ' ' + data.puom);
                }
            })
        }

        $('#submitUpdateStatusProduct').on('submit', function(e) {
            e.preventDefault();
            $('.btn-save').attr('disabled', true);
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function(response) {
                    swal({
                        icon: "success",
                        text: 'Berhasil Di ubah..',
                    });
                    $('#modal-show').modal('hide');
                    $('#modal-edit').modal('hide');
                    load_data();
                }
            });
        });
    </script>
@endpush
