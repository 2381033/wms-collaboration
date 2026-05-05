@extends('layouts.new.base')
@section('title', 'MKT - Bea Cukai Outbound Report')
@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <input type="date" min="2024-01-01" max="2045-01-01" class="form-control" required
                                value="{{ date('Y-m-01') }}" id="startDate">
                        </div>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" required value="{{ date('Y-m-t') }}" id="endDate">
                        </div>
                        <div class="col-sm-2">
                            <a href="#" onclick="searchData()" class="btn btn-block btn-dark"><i
                                    class="fas fa-search"></i>
                            </a>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableList">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>PEB Number</th>
                                            <th>Container Number</th>
                                            <th>Size</th>
                                            <th>Shipper Name</th>
                                            <th>Stuffing Date</th>
                                            <th>Qty</th>
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

    <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
        <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Export To Excel"
            data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-dark btn-hover-success" href="#">
                <i class="flaticon-settings"></i>
            </a>
        </li>
    </ul>

    <div id="kt_demo_panel" class="offcanvas offcanvas-right p-10">
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
            <h4 class="font-weight-bold m-0">
                Toggle Quick Menu
            </h4>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_demo_panel_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="offcanvas-content">
            <div class="offcanvas-wrapper mb-5 scroll-pull">
                <div class="row">
                    <a href="#" style="border-radius: 25px;" onclick="reportInbound()" class="btn btn-dark btn-block">
                        Report Inbound <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportOutbound()"
                        class="btn btn-info btn-block"> Report Outbound <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportStock()"
                        class="btn btn-primary btn-block">
                        Report Stock <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportMonthly()"
                        class="btn btn-success btn-block">
                        Report Monthly <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        function reportInbound() {
            var url = "{{ url('export/BeaCukai/Report/inbound/') }}";
            window.open(url, '_blank');
        }

        function reportOutbound() {
            var url = "{{ url('export/BeaCukai/Report/outbound/') }}";
            window.open(url, '_blank');
        }

        function reportStock() {
            var url = "{{ url('export/BeaCukai/Report/stock-report/') }}";
            window.open(url, '_blank');
        }

        function reportMonthly() {
            var url = "{{ url('export/BeaCukai/Report/monthly/') }}";
            window.open(url, '_blank');
        }

        function searchData() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $('#tableList').DataTable().clear().destroy()
            $('#tableList').DataTable({
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
                    url: "{{ url('export/BeaCukai/Report/outbound') }}/" + startDate + "/" +
                        endDate,
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
                        data: 'peb_no',
                        name: 'peb_no'
                    },
                    {
                        data: 'container_no',
                        name: 'container_no'
                    },
                    {
                        data: 'size',
                        name: 'size'
                    },
                    {
                        data: 'shipper_name',
                        name: 'shipper_name'
                    },
                    {
                        data: 'stuffing_date',
                        name: 'stuffing_date'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                ],
                order: [
                    [0, 'asc']
                ],
                "bDestroy": true,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'pdfHtml5',
                    orientation: 'potrait',
                    pageSize: 'A4'
                }, 'excel']
            });
        }

        function downloadICR(id) {
            var url = "{{ url('export/BeaCukai/Report/inbound/icr') }}/" + id;
            window.open(url, '_blank');
        }
    </script>
@endpush
