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
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select id="my-select" class="form-control" name="id_warehouse"
                                    onchange="searchJob(this.value)">
                                    <option value="" selected disabled>Warehouse</option>
                                    @foreach ($warehouse as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Job No</th>
                                            <th>Customer</th>
                                            <th>Warehouse</th>
                                            <th>PO No</th>
                                            <th>DO No</th>
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
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        function searchJob(id_warehouse) {
            // $('.table').DataTable();
            $('.table').DataTable().clear().destroy()
            $('.table').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ordering: false,
                paging: false,
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                ajax: {
                    url: "{{ url('crossDock/scanCargo/searchJob') }}/" + id_warehouse,
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a href="{{ url('crossDock/scanCargo/detailJobFrontend/${data.id}') }}">${data.job_no}</a>`;
                        },
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse'
                    },
                    {
                        data: 'po_no',
                        name: 'po_no'
                    },
                    {
                        data: 'do_no',
                        name: 'do_no'
                    },
                ],
            });
        }
    </script>
@endpush
