@extends('layouts.new.base')
@section('title', 'MKT - Cycle Count')
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
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon">
                            </div>
                        </div>
                        <div class="col-sm-5 mb-3">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="my-addon">Tanggal Selesai</span>
                                </div>
                                <input class="form-control tglSelesai" type="date" name=""
                                    placeholder="Recipient's text" aria-label="Recipient's " aria-describedby="my-addon">
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
                                            <th colspan="10">LIST MONITORING CYCLE COUNT</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Job No</th>
                                            <th>Match</th>
                                            <th>Product Code</th>
                                            <th>Qty</th>
                                            <th>Location System</th>
                                            <th>Move To Location</th>
                                            <th>Remakrs</th>
                                            <th>Count By</th>
                                            <th>Count Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->where('scan_flag', 'Yes') as $item)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->job_no }}</td>
                                                <td>
                                                    @if ($item->match_flag == 'Yes')
                                                        <badge class="btn btn-success btn-shadow btn-lg mr-3"><i
                                                                class="fas fa-check" style="border-radius: 25px;"></i>
                                                            Sesuai
                                                        </badge>
                                                    @else
                                                        <badge class="btn btn-danger btn-shadow btn-lg mr-3"><i
                                                                class="fas fa-window-close"
                                                                style="border-radius: 25px;"></i> Tidak
                                                        </badge>
                                                    @endif
                                                </td>
                                                <td>{{ $item->product_code }}</td>
                                                <td>{{ $item->stock . ' ' . $item->puom }}</td>
                                                <td>{{ $item->location_code }}</td>
                                                <td>{{ $item->match_flag == 'No' ? 'Lock Area' : '-' }}</td>
                                                <td>{{ $item->remarks == null ? '-' : $item->remarks }}</td>
                                                <td>{{ $item->scan_by }}</td>
                                                <td>{{ formatTanggalWaktuIndonesia2($item->scan_at) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if (!is_null($header) and $data->where('scan_flag', 'Yes')->count() > 0)
                                @if ($header->confirmed_flag == 'No')
                                    <div class="float-right">
                                        <a href="#" onclick="confirmation('{{ $header->job_no }}')"
                                            class="btn btn-lg btn-info"><i class="fas fa-check"></i>
                                            Confirm</a>
                                    </div>
                                @endif
                            @endif
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
        @if (!is_null($header))
            @if ($header->confirmed_flag == 'No')
                Swal.fire({
                    icon: 'info',
                    text: 'Your job status is not confirmed..',
                })
            @endif
        @endif

        function confirmation(job_no) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Konfirm',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('inventory/cycleCount/confirm') }}/" + job_no,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data has been confirmed..',
                            });
                            location.reload();
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'error',
                                title: data,
                            })
                        }
                    });
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        var tbl = $('#listTable').dataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },
                'copy', 'excel'
            ],
        });

        function cariData() {
            var tgl_mulai = $('.tglMulai').val();
            var tgl_selesai = $('.tglSelesai').val();
            $('.btn-search').hide();
            if (tgl_mulai == '' || tgl_selesai == '') {
                Swal.fire({
                    icon: 'warning',
                    text: 'Tanggal Tidak Boleh Kosong',
                })
                $('.btn-search').show();
            } else {
                tbl.DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "{{ url('inventory/cycleCount/cariData') }}/" + tgl_mulai + "/" + tgl_selesai,
                    columns: [{
                            data: null,
                            name: 'number',
                            sortable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1
                            }
                        },
                        {
                            data: 'job_no',
                            name: 'job_no',
                        },
                        {
                            data: 'match_flag',
                            name: 'match_flag',
                            sortable: false,
                            render: function(match_flag) {
                                if (match_flag == 'Yes') {
                                    var matching =
                                        '<badge class="btn btn-success btn-shadow btn-lg mr-3"><i class="fas fa-check" style="border-radius: 25px;"></i> Sesuai</badge>'
                                } else {
                                    var matching =
                                        '<badge class="btn btn-danger btn-shadow btn-lg mr-3"><i class="fas fa-window-close" style="border-radius: 25px;"></i> Tidak</badge>'
                                }
                                return matching;
                            },
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(row, meta) {
                                return row.stock + ' ' + row.puom;
                            },
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(row, meta) {
                                if (row.match_flag == 'No') {
                                    var loca_actual =
                                        'Lock Area'
                                } else {
                                    var loca_actual =
                                        '-'
                                }
                                return loca_actual;
                            },
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(row, meta) {
                                if (row.remarks == null) {
                                    var remarks = '-'
                                } else {
                                    var remarks = row.remarks;
                                }
                                return remarks;
                            },
                        },
                        {
                            data: 'scan_by',
                            name: 'scan_by'
                        },
                        {
                            data: null,
                            name: null,
                            sortable: false,
                            render: function(row, meta) {
                                return formatTanggalWaktuIndonesia2(row.scan_at);
                            },
                        },
                    ],
                    "bDestroy": true,
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
