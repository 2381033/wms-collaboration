@extends('layouts.new.base')
@section('title', 'LCL Performance Report')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
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
    @php
        use Carbon\Carbon;
        $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek()->format('Y-m-d');

    @endphp
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5 mb-3">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="my-addon">Tanggal Mulai</span>
                                </div>
                                <input class="form-control tglMulai" type="date" name=""
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon"
                                    value="{{ $start }}">
                            </div>
                        </div>
                        <div class="col-sm-5 mb-3">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="my-addon">Tanggal Selesai</span>
                                </div>
                                <input class="form-control tglSelesai" type="date" name=""
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon"
                                    value="{{ date('Y-m-t') }}">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <a href="javascript:void(0)" onclick="cariData()" class="btn btn-block btn-dark btn-search">
                                <i class="fas fa-search"></i> Cari
                            </a>
                        </div>
                        <div class="col-sm-12  mt-4">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>NO</th>
                                            <th>CUSTOMER</th>
                                            <th>SHIPPER</th>
                                            <th>VEHICLE NUMBER</th>
                                            <th>ARRIVAL DATE</th>
                                            <th>ARRIVAL TIME</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                            <th>SJ FROM AO</th>
                                            <th>CHECKER</th>
                                            <th>START BONGKAR</th>
                                            <th>FINISH BONGKAR</th>
                                            <th>NEW QTY</th>
                                            <th>PALLET TOTAL</th>
                                            <th>CRS STATUS</th>
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
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">
        const baseUrl = "{{ url('export/report/post-lcl-performance') }}";


        function cariData() {
            var tgl_mulai = $('.tglMulai').val();
            var tgl_selesai = $('.tglSelesai').val();
            $('.btn-search').hide();

            if (tgl_mulai == '' || tgl_selesai == '') {
                Swal.fire({
                    icon: 'warning',
                    text: 'Tanggal Tidak Boleh Kosong',
                });
                $('.btn-search').show();
            } else {
                const url = baseUrl + "/" + tgl_mulai + "/" + tgl_selesai;

                $('#listTable').DataTable({
                    processing: true,
                    cache: true,
                    serverSide: false,
                    ajax: url,
                    columns: [{
                            data: null,
                            name: 'number',
                            sortable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'shipper',
                            name: 'shipper'
                        },
                        {
                            data: 'nopol',
                            name: 'nopol'
                        },
                        {
                            data: 'arrival_date',
                            name: 'arrival_date'
                        },
                        {
                            data: 'arrival_time',
                            name: 'arrival_time'
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'unit',
                            name: 'unit'
                        },
                        {
                            data: 'sj_from_ao',
                            name: 'sj_from_ao'
                        },
                        {
                            data: 'checker',
                            name: 'checker'
                        },
                        {
                            data: 'start_bongkar',
                            name: 'start_bongkar'
                        },
                        {
                            data: 'striping_finish',
                            name: 'striping_finish'
                        },
                        {
                            data: 'new_qty',
                            name: 'new_qty'
                        },
                        {
                            data: 'total_pallet',
                            name: 'total_pallet'
                        },
                        {
                            data: 'status_flag',
                            name: 'status_flag'
                        },
                    ],
                    bDestroy: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'LEGAL'
                        },
                        'copy', 'excel'
                    ]
                });

                $('.btn-search').show();
            }
        }
    </script>
@endpush
