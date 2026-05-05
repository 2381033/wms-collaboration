@extends('layouts.new.base')
@section('title', 'Location Available')
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

    @section('content')

        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-sm-4">
                            <div class="form-group">
                                <label>Site Name</label>
                                <select class="custom-select" id="principal_id" name="principal_id">
                                    @foreach (Auth::user()->principal as $item)
                                        <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                    </div>
                    <form id="form-freeze" name="form-freeze" method="post">
                        @csrf

                    </form>
                    </fieldset>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group mb-3">
                                <button type="button" id="refresh" name="refresh"
                                    class="btn btn-info btn-sm">Retrieve</button>
                                <button type="button" class="btn btn-danger btn-sm" id="btn-process-picking"
                                    onclick="processFreeze();"><i class="fas fa-gear"></i> <span>Process</span></button>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row info-wrap" data-aos="fade-up">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table id="table_list" class="table table-bordered table-sm" style="width:100%;"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th colspan="5" class="text-center bg-warning text-white">LIST LOCATION AVALIABLE
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>No.</th>
                                            <th>Site Name</th>
                                            <th>Area Name</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
        <script>
            // $(document).ready(function() {
            //     var tbl = $('#table_list').dataTable({
            //         dom: 'Bfrtip',
            //         buttons: [{
            //                 extend: 'pdfHtml5',
            //                 orientation: 'landscape',
            //                 pageSize: 'LEGAL'
            //             },
            //             'copy', 'excel'
            //         ],
            //     })
            // });

            $('#table_list').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    },
                    'copy', 'excel'
                ],
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                },
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('getLocationAvailable') }}",
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: 'number',
                        sortable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1
                        }
                    },
                    {
                        data: 'site_name',
                        name: 'site_name'
                    },
                    {
                        data: 'area_name',
                        name: 'area_name'
                    },
                    {
                        data: 'location_code',
                        name: 'location_code'
                    },
                    {
                        data: null,
                        name: 'status',
                        sortable: false,
                        render: function() {
                            return 'Avaliable';
                        },
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        </script>
    @endpush
