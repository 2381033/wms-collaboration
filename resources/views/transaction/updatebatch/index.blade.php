@extends('layouts.main')

@section('title')
    Inventory - Update Batch&ED
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Inventory - Update Batch&ED</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Inventory - Update Batch&ED</li>
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
                                    <th>Job No</th>
                                    <th>Batch</th>
                                    <th>Expired Date</th>
                                    <th>Product Code</th>
                                    <th>Location</th>
                                    <th>Stock</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-edit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">FORM UPDATE BATCH&ED</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-edit" name="form-edit" method="post">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Product Code</label>
                                    <input type="text" autocomplete="off" id="product_code" name="name"
                                        class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Batch</label>
                                    <input type="text" autocomplete="off" id="batch_old" name="name"
                                        class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Exp Date</label>
                                    <input type="text" autocomplete="off" id="exp_old" name="name"
                                        class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>New Batch</label>
                                    <input type="text" autocomplete="off" placeholder="isi jika ada perubahan.."
                                        id="batch_value" name="batch" class="form-control" value="">
                                </div>
                                <div class="form-group">
                                    <label>New Exp</label>
                                    <input type="date" autocomplete="off" id="exp_value" name="exp_date"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i
                                class="fas fa-window-close"></i> <span>Close</span></button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save"><i class="fas fa-save"></i>
                            <span> Update</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function kasihNol($data) {
            if ($data < 10) {
                return '0' + $data;
            } else {
                return $data;
            }
        }

        function formatTanggalIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

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
                        url: "{{ url('inventory/updateBatch/getList') }}",
                        type: "GET",
                    },
                    columns: [{
                            data: 'job_no',
                            name: 'job_no'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: null,
                            name: 'number',
                            sortable: false,
                            render: function(data, type, row) {
                                return formatTanggalIndonesia2(data.exp_date)
                            }
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: null,
                            name: 'number',
                            sortable: false,
                            render: function(data, type, row) {
                                return data.qtya + ' ' + data.puom;
                            }
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(data, type, row) {
                                return `<a href="#" onclick="editData('${row.id}')"
                                                    class="btn btn-sm btn-dark"><i class="fas fa-edit"></i>
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


        function editData(id) {
            $.ajax({
                url: "{{ url('inventory/updateBatch/getData') }}/" + id,
                type: "GET",
                dataType: 'json',
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(data) {
                    var exp = moment(data.exp_date).format('YYYY-MM-DD');

                    $("#loader").hide();
                    $('#id').val(data.id);
                    $('#product_code').val(data.product_code);
                    $('#batch_old').val(data.lot_no);
                    $('#exp_old').val(formatTanggalIndonesia2(data.exp_date));
                    $('#exp_value').val(exp);
                    $('#batch_value').val(data.lot_no);
                    $('#modal-edit').modal('show');
                }
            })
        }
    </script>
@endpush
