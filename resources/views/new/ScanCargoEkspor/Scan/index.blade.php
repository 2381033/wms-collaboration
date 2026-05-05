@extends('layouts.new.base')
@section('title', 'MKT - List Scan')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #0C9;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            box-shadow: 2px 2px 3px #999;
        }

        .my-float {
            margin-top: 22px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom gutter-b">
                <div class="card-header card-header-tabs-line">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_4">
                                    <span class="nav-icon"><i class="flaticon-cart"></i></span>
                                    <span class="nav-text">Receiving</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_4">
                                    <span class="nav-icon"><i class="flaticon2-lorry"></i></span>
                                    <span class="nav-text">Stuffing</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="kt_tab_pane_1_4" role="tabpanel"
                            aria-labelledby="kt_tab_pane_1_4">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="start" id="startReceive"
                                            aria-describedby="helpId" placeholder="" value="{{ date('Y-m-d') }}">
                                        <small id="helpId" class="form-text text-danger">Start Date</small>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="end" id="endReceive"
                                            aria-describedby="helpId" placeholder="" value="{{ date('Y-m-d') }}">
                                        <small id="helpId" class="form-text text-danger">End Date</small>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-dark btn-md" onclick="searchReceive()"><i class="fas fa-search"></i>
                                        Search</a>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="tableReceive">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Action</th>
                                                <th>Job No</th>
                                                <th>Date</th>
                                                <th>Scan By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="kt_tab_pane_2_4" role="tabpanel" aria-labelledby="kt_tab_pane_2_4">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="start" id="startStuffing"
                                            aria-describedby="helpId" placeholder="" value="{{ date('Y-m-d') }}">
                                        <small id="helpId" class="form-text text-danger">Start Date</small>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="end" id="endStuffing"
                                            aria-describedby="helpId" placeholder="" value="{{ date('Y-m-d') }}">
                                        <small id="helpId" class="form-text text-danger">End Date</small>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-success btn-md" onclick="searchStuffing()"><i
                                            class="fas fa-search"></i>
                                        Search</a>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="tableStuffing">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Action</th>
                                                <th>Job No</th>
                                                <th>Container No</th>
                                                <th>Date</th>
                                                <th>Scan By</th>
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
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">
        function searchReceive() {
            var start = $('#startReceive').val();
            var end = $('#endReceive').val();

            $('#tableReceive').DataTable().destroy();
            $('#tableReceive').DataTable({
                "dom": '<"wrapper"flipt>',
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                destroy: true,
                info: false,
                ajax: {
                    url: "{{ route('getListReceive') }}",
                    type: "GET",
                    data: {
                        start: start,
                        end: end,
                    }
                },
                columns: [{
                        data: null,
                        name: 'no',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'job_no',
                        name: 'job_no',
                        render: function(data, type, row) {
                            var url = `{{ url('export/ScanCargoEkspor/downloadReceiving') }}/${data}`;
                            return `<a href="${url}" class="btn btn-dark btn-sm"><i class="fas fa-download"></i> Download</a>`;
                        }
                    },
                    {
                        data: 'job_no',
                        name: 'job_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            var date = new Date(data);
                            var year = date.getFullYear();
                            var month = ("0" + (date.getMonth() + 1)).slice(-2);
                            var day = ("0" + date.getDate()).slice(-2);
                            var hours = ("0" + date.getHours()).slice(-2);
                            var minutes = ("0" + date.getMinutes()).slice(-2);
                            return day + '-' + month + '-' + year + ' ' + hours + ':' + minutes;
                        }
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        function searchStuffing() {
            var start = $('#startStuffing').val();
            var end = $('#endStuffing').val();

            $('#tableStuffing').DataTable().destroy();
            $('#tableStuffing').DataTable({
                "dom": '<"wrapper"flipt>',
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                destroy: true,
                info: false,
                ajax: {
                    url: "{{ route('getListStuffing') }}",
                    type: "GET",
                    data: {
                        start: start,
                        end: end,
                    }
                },
                columns: [{
                        data: null,
                        name: 'no',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'job_no', // Assuming the job_no column exists in the backend response
                        name: 'job_no',
                        render: function(data, type, row) {
                            return `<a href="{{ url('export/ScanCargoEkspor/downloadStuffing') }}/${data}" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>`;
                        }
                    },
                    {
                        data: 'job_no',
                        name: 'job_no'
                    },
                    {
                        data: 'container_no',
                        name: 'container_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            var date = new Date(data);
                            var year = date.getFullYear();
                            var month = ("0" + (date.getMonth() + 1)).slice(-2);
                            var day = ("0" + date.getDate()).slice(-2);
                            var hours = ("0" + date.getHours()).slice(-2);
                            var minutes = ("0" + date.getMinutes()).slice(-2);
                            return day + '-' + month + '-' + year + ' ' + hours + ':' + minutes;
                        }
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }
    </script>
@endpush
