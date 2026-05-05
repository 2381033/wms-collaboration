@extends('layouts.new.base')
@section('title', 'Stock Ledger Export')
@push('styles')
    <link href="{{ asset('public/assets/new/plugins/custom/datatables/datatables.bundle.css') }} " rel="stylesheet" />
    <link href="{{ asset('public/assets/new/plugins/custom/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        #exportButtons .dt-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .styled-hr {
            border: none;
            height: 3px;
            background: linear-gradient(to right, #007bff, #00bcd4, transparent);
            border-radius: 2px;
            margin: 0 0 1.5rem 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="card card-custom gutter-b">
                        <div class="card-header card-header-tabs-line">
                            <div class="card-toolbar">
                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_3">
                                            <span class="nav-icon"><i class="flaticon2-paper"></i></span>
                                            <span class="nav-text">Report Detail</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_3">
                                            <span class="nav-icon"><i class="flaticon2-list"></i></span>
                                            <span class="nav-text">Report Summary</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#occupancy">
                                            <span class="nav-icon"><i class="flaticon2-chart"></i></span>
                                            <span class="nav-text">Occupancy Warehouse</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_tab_pane_1_3" role="tabpanel"
                                    aria-labelledby="kt_tab_pane_1_3">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="form-group">
                                                <label for="branch_id">Branch Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="branch_id" id="branch_id" required class="custom-select">
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="form-group">
                                                <label for="searchBy">Search By <span class="text-danger">*</span></label>
                                                <select name="searchBy" id="searchBy" required class="custom-select">
                                                    <option value="">-- Choose Search Type --</option>
                                                    <option value="shipper">Shipper</option>
                                                    <option value="forwarder">Forwarder</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-group" id="shipper_container" style="display:none;">
                                                <label for="shipper_id">Shipper Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="shipper_id[]" id="shipper_id" class="shipper-select" multiple
                                                    style="height: 150px !important; border-color: #ced4da !important; width: 110%;">
                                                    <option value="ALL">ALL SHIPPER</option>
                                                    @foreach ($shipper as $item)
                                                        <option value="{{ $item->id }}">{{ $item->shipper_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="forwarder_container" style="display:none;">
                                                <label for="forwarder_id">Forwarder Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="forwarder_id[]" id="forwarder_id" class="forwarder-select"
                                                    multiple
                                                    style="height: 150px !important; border-color: #ced4da !important; width: 110%;">
                                                    <option value="ALL">ALL FORWARDER</option>
                                                    @foreach ($forwarder as $item)
                                                        <option value="{{ $item->id }}">{{ $item->forwarder_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-group" id="location" style="display:none;">
                                                <label for="location_code">Location Code <span
                                                        class="text-danger">*</span></label>
                                                <select name="location_code[]" id="location_code" class="shipper-select"
                                                    multiple
                                                    style="height: 150px !important; border-color: #ced4da !important; width: 110%;">
                                                    <option value="ALL">ALL LOCATION</option>
                                                    @foreach ($location as $item)
                                                        <option value="{{ $item->id }}">{{ $item->location_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 mt-4">
                                            <a href="javascript:void(0)" onclick="cariData()"
                                                class="btn btn-block btn-dark btn-search mt-3" id="buttonSearch"
                                                style="display:none;">
                                                <i class="fas fa-search"></i> Cari
                                            </a>
                                        </div>
                                        <div class="col-sm-12 mt-4">
                                            <div id="exportButtons" class="mb-3"></div> <!-- Tambahkan ini -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="listTable">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Principal</th>
                                                            <th>Shipper</th>
                                                            <th>Receiving Date</th>
                                                            <th>PO Number</th>
                                                            <th>PEB Number</th>
                                                            <th>AJU Number</th>
                                                            <th>Destination</th>
                                                            <th>QTY</th>
                                                            <th>Pallet ID</th>
                                                            <th>Vol. Total (Cbm)</th>
                                                            <th>Total Pallet</th>
                                                            <th>Location</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="kt_tab_pane_2_3" role="tabpanel"
                                    aria-labelledby="kt_tab_pane_2_3">
                                    <div class="row">
                                        <div class="col-sm-2 mb-3">
                                            <div class="form-group">
                                                <label for="branch_id">Branch Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="branch_id" id="branch_id_sum" required
                                                    class="custom-select">
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="searchBy">Search By <span class="text-danger">*</span></label>
                                                <select name="searchBy" id="searchBySum" required class="custom-select">
                                                    <option value="">-- Choose Search Type --</option>
                                                    <option value="shipper">Shipper</option>
                                                    <option value="forwarder">Forwarder</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 mb-3">
                                            <div class="form-group" id="shipper_container_sum" style="display:none;">
                                                <label for="shipper_id">Shipper Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="shipper_id[]" id="shipper_id_sum" class="shipper-select"
                                                    multiple
                                                    style="height: 150px !important; border-color: #ced4da !important; width: 110%;">
                                                    <option value="ALL">ALL SHIPPER</option>
                                                    @foreach ($shipper as $item)
                                                        <option value="{{ $item->id }}">{{ $item->shipper_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="forwarder_container_sum" style="display:none;">
                                                <label for="forwarder_id">Forwarder Name <span
                                                        class="text-danger">*</span></label>
                                                <select name="forwarder_id[]" id="forwarder_id_sum"
                                                    class="forwarder-select" multiple
                                                    style="height: 150px !important; border-color: #ced4da !important; width: 110%;">
                                                    <option value="ALL">ALL FORWARDER</option>
                                                    @foreach ($forwarder as $item)
                                                        <option value="{{ $item->id }}">{{ $item->forwarder_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 mt-4">
                                            <a href="javascript:void(0)" onclick="cariDataSum()"
                                                class="btn btn-block btn-dark btn-search mt-3" id="buttonSearchSum"
                                                style="display:none;">
                                                <i class="fas fa-search"></i> Cari
                                            </a>
                                        </div>
                                        <div class="col-sm-12 mt-4">
                                            <div id="exportButtons" class="mb-3"></div> <!-- Tambahkan ini -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="listTableSum">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Forwarder</th>
                                                            <th>Shipper</th>
                                                            <th>Qty</th>
                                                            <th>Vol. Total (Cbm)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="occupancy" role="tabpanel" aria-labelledby="occupancy">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="col-sm-12">
                                                <div class="alert alert-dark mb-5 p-5" role="alert">
                                                    <h4 class="alert-heading">Pallet Capacity</h4>
                                                    <label>
                                                        <h3 class="totalPallet"></h3>
                                                    </label>
                                                    <div class="progress progress-lg bg-white-o-90">
                                                        <div class="progress-bar bg-white" style="width: 100%;"
                                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            <h6 class="text-dark mt-2">100%</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-danger mb-5 p-5" role="alert">
                                                    <h4 class="alert-heading">Occupied Slot (Today)</h4>
                                                    <label>
                                                        <h3 class="badgeOccupiedSlot"> </h3>
                                                    </label>
                                                    <div class="progress progress-lg bg-white-o-90">
                                                        <div class="progress-bar bg-white progressOccupiedSlot"
                                                            role="progressbar" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            <h6 class="text-dark mt-2 badgePercentageOccupied">
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-success mb-5 p-5" role="alert">
                                                    <h4 class="alert-heading">Available Slot (Today)</h4>
                                                    <label>
                                                        <h3 class="availableSlot"></h3>
                                                    </label>
                                                    <div class="progress progress-lg bg-white-o-90">
                                                        <div class="progress-bar bg-white progressAvailableSlot"
                                                            role="progressbar" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            <h6 class="text-dark mt-2 badgeAvailableSlot"></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div id="container" style="height: 500px;"></div>
                                        </div>
                                        <div class="col-sm-12">
                                            <hr class="styled-hr">
                                            <h3 class="fw-semibold mb-3 text-primary">Occupancy Daily</h3>
                                            <table class="table table-bordered table-hover" id="stockLedgerTable">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Kapasitas (PP)</th>
                                                        <th>Receiving</th>
                                                        <th>Stuffing</th>
                                                        <th>Stock (PP)</th>
                                                        <th>SOR (%)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $row)
                                                        @if ($row['tanggal'] === 'Total')
                                                            <tr class="fw-bold table-secondary">
                                                                <td>{{ $row['tanggal'] }}</td>
                                                                <td class="text-center">{{ $row['kapasitas'] }}</td>
                                                                <td class="text-end">{{ $row['in'] }}</td>
                                                                <td class="text-end">{{ $row['out'] }}</td>
                                                                <td class="text-end">{{ $row['stock'] }}</td>
                                                                <td class="text-center">{{ $row['sor'] }}</td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td>{{ $row['tanggal'] }}</td>
                                                                <td class="text-center">
                                                                    {{ number_format($row['kapasitas']) }}</td>
                                                                <td class="text-end">{{ $row['in'] ?: '-' }}</td>
                                                                <td class="text-end">{{ $row['out'] ?: '-' }}</td>
                                                                <td class="text-end">{{ $row['stock'] }}</td>
                                                                <td class="text-end">{{ $row['sor'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/jszip.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/pdfmake.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/vfs_fonts.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/highcharts.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/exporting.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/export-data.js') }}"></script>
    <script src="{{ asset('public/assets/js/highcharts/accessibility.js') }}"></script>

    <script type="text/javascript">
        const baseUrl = "{{ route('stock-export.report') }}";
        $(document).ready(function() {
            $('a[href="#occupancy"]').on('shown.bs.tab', function() {
                loadCharts();
            });
            $('#shipper_id').select2({
                placeholder: 'Choose..',
                width: '100%'
            });
            $('#forwarder_id').select2({
                placeholder: 'Choose..',
                width: '100%'
            });
            $('#location_code').select2({
                placeholder: 'Choose..',
                width: '100%'
            });

            $('#shipper_id_sum').select2({
                placeholder: 'Choose..',
                width: '100%'
            });

            $('#forwarder_id_sum').select2({
                placeholder: 'Choose..',
                width: '100%'
            });

            $('#searchBy').on('change', function() {
                let val = $(this).val();
                $('#location').show();
                $('#buttonSearch').show();
                if (val === 'shipper') {
                    $('#shipper_container').show();
                    $('#forwarder_container').hide();
                    $('#forwarder_id').val(null).trigger('change');
                } else if (val === 'forwarder') {
                    $('#forwarder_container').show();
                    $('#shipper_container').hide();
                    $('#shipper_id').val(null).trigger('change');
                } else {
                    $('#shipper_container').hide();
                    $('#forwarder_container').hide();
                }
            });

            $('#searchBySum').on('change', function() {
                let val = $(this).val();
                $('#buttonSearchSum').show();
                if (val === 'shipper') {
                    $('#shipper_container_sum').show();
                    $('#forwarder_container_sum').hide();
                    $('#forwarder_id_sum').val(null).trigger('change');
                } else if (val === 'forwarder') {
                    $('#forwarder_container_sum').show();
                    $('#shipper_container_sum').hide();
                    $('#shipper_id_sum').val(null).trigger('change');
                } else {
                    $('#shipper_container_sum').hide();
                    $('#forwarder_container_sum').hide();
                }
            });
        });

        function loadCharts() {

            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts belum terload');
                return;
            }

            $.ajax({
                url: "{{ url('/') }}/export/report/loadCharts/" + $('#branch_id').val(),
                type: 'GET',
                success: function(response) {

                    const badge = response.badge || {};
                    const chartData = response.charts || {};

                    $('.totalPallet').text(badge.total_pallet || '0');
                    $('.badgeOccupiedSlot').text(badge.occupied_slot || '0');
                    $('.availableSlot').text(badge.available_slot || '0');

                    $('.progressOccupiedSlot').css('width', badge.percentage_occupied);
                    $('.progressAvailableSlot').css('width', badge.percentage_available);
                    $('.badgePercentageOccupied').text(badge.percentage_occupied);
                    $('.badgeAvailableSlot').text(badge.percentage_available);

                    Highcharts.chart('container', {
                        title: {
                            text: 'Warehouse Occupancy Chart'
                        },
                        xAxis: {
                            categories: chartData.categories || []
                        },
                        series: [{
                            name: 'Occupied Slot',
                            data: chartData.occupied || []
                        }, {
                            name: 'Pallet Capacity',
                            data: chartData.capacity || []
                        }]
                    });
                }
            });
        }


        function cariData() {
            const branch_id = $('#branch_id').val();
            const searchBy = $('#searchBy').val();
            const shipper_id = $('#shipper_id').val(); // array (multiple)
            const forwarder_id = $('#forwarder_id').val(); // array (multiple)
            const location_code = $('#location_code').val(); // array (multiple)

            $.ajax({
                url: baseUrl, // sudah didefinisikan di atas
                type: 'POST',
                data: {
                    branch_id: branch_id,
                    searchBy: searchBy,
                    shipper_id: shipper_id,
                    forwarder_id: forwarder_id,
                    location_code: location_code,
                    reportType: 'detail',
                    _token: '{{ csrf_token() }}' // penting untuk Laravel!
                },
                success: function(response) {

                    const data = response.data || [];
                    const reportType = response.reportType;

                    // Destroy datatable jika sudah ada sebelumnya
                    if ($.fn.DataTable.isDataTable('#listTable')) {
                        $('#listTable').DataTable().clear().destroy();
                    }

                    // Kosongkan tbody
                    $('#listTable tbody').empty();

                    // Loop data dan render ke table
                    data.forEach(item => {
                        let row = `
                                <tr class="text-center">
                                    <td>${item.customer_name || '-'}</td>
                                    <td>${item.shipper_name || '-'}</td>
                                    <td>${item.receiving || '-'}</td>
                                    <td>${item.po_number || '-'}</td>
                                    <td>${item.peb_no || '-'}</td>
                                    <td>${item.aju_no || '-'}</td>
                                    <td>${item.destination || '-'}</td>
                                    <td>${item.quantity || '-'}</td>
                                    <td>${item.pallet_id || '-'}</td>
                                    <td>${item.cbm || '-'}</td>
                                    <td>${item.total_pallet || '-'}</td>
                                    <td>${item.location_code || '-'}</td>
                                </tr>
                            `;
                        $('#listTable tbody').append(row);
                    });
                    // Inisialisasi ulang DataTable
                    const table = $('#listTable').DataTable({
                        responsive: true,
                        destroy: true,
                        pageLength: 25,
                        order: [],
                        dom: 'Bfrtip', // tetap gunakan ini untuk memunculkan tombol
                        buttons: [{
                                extend: 'excelHtml5',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ],
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            zeroRecords: "Data tidak ditemukan",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(difilter dari _MAX_ total data)"
                        }
                    });
                    table.buttons().container().appendTo('#exportButtons');
                },
                error: function(xhr, status, error) {
                    $('#buttonSearch').show();
                }
            });
        }

        function cariDataSum() {
            const branch_id = $('#branch_id_sum').val();
            const searchBy = $('#searchBy').val();
            const shipper_id = $('#shipper_id_sum').val(); // array (multiple)

            $.ajax({
                url: baseUrl, // sudah didefinisikan di atas
                type: 'POST',
                data: {
                    branch_id: branch_id,
                    searchBy: searchBy,
                    shipper_id: shipper_id,
                    reportType: 'summary',
                    _token: '{{ csrf_token() }}' // penting untuk Laravel!
                },
                success: function(response) {

                    const data = response.data || [];
                    const reportType = response.reportType;

                    // Destroy datatable jika sudah ada sebelumnya
                    if ($.fn.DataTable.isDataTable('#listTableSum')) {
                        $('#listTableSum').DataTable().clear().destroy();
                    }

                    // Kosongkan tbody
                    $('#listTableSum tbody').empty();

                    // Loop data dan render ke table
                    data.forEach(item => {
                        let row = `
                                <tr class="text-center">
                                    <td>${item.forwarder_name || '-'}</td>
                                    <td>${item.shipper_name || '-'}</td>
                                    <td>${item.quantity || '-'}</td>
                                    <td>${item.cbm || '-'}</td>
                                </tr>
                            `;
                        $('#listTableSum tbody').append(row);
                    });
                    // Inisialisasi ulang DataTable
                    const table = $('#listTableSum').DataTable({
                        responsive: true,
                        destroy: true,
                        pageLength: 25,
                        order: [],
                        dom: 'Bfrtip', // tetap gunakan ini untuk memunculkan tombol
                        buttons: [{
                                extend: 'excelHtml5',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Stock Ledger Export',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ],
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            zeroRecords: "Data tidak ditemukan",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(difilter dari _MAX_ total data)"
                        }
                    });
                    table.buttons().container().appendTo('#exportButtons');
                },
                error: function(xhr, status, error) {
                    $('#buttonSearchSum').show();
                }
            });
        }
    </script>
@endpush
