@extends('layouts.new.base')
@section('title', 'MKTxSHAD - Sales RPS')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
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
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tbl-stock" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Product Code</th>
                                            <th class="text-center">Product Name</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Uom</th>
                                            <th class="text-center" style="background-color: yellow">IP</th>
                                            <th class="text-center" style="background-color: yellow">Week</th>
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
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


    <script type="text/javascript">
        loadData();

        function loadData() {
            let table = $('#tbl-stock').DataTable({
                ordering: false,
                processing: true,
                serverSide: false,
                destroy: true,
                ajax: {
                    url: "{{ url('warehouse/inboundPlanningDC/getListStock') }}",
                    type: "GET",
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'product_code'
                    },
                    {
                        data: 'product_name'
                    },
                    {
                        data: 'qtys'
                    },
                    {
                        data: 'puom'
                    },
                    {
                        data: 'ip'
                    },
                    {
                        data: 'week'
                    }
                ],

                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Stock List',
                        text: 'Export Excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Stock List',
                        text: 'Export PDF',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ]
            });
        }
    </script>
@endpush
