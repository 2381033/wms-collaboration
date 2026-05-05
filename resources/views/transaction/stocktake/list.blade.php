@extends('layouts.new.base')
@section('title', 'MKT - STO LIST')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        #progress-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .row-label {
            min-width: 50px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
            user-select: none;
        }

        .cell-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .cell {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            transition: transform 0.2s ease-in-out;
        }

        .green {
            background-color: #27ae60;
        }

        .red {
            background-color: #e74c3c;
        }

        .cell:hover {
            transform: scale(1.15);
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        /* Responsive adjustment */
        @media (max-width: 768px) {
            .cell {
                width: 18px;
                height: 18px;
            }

            .row-label {
                min-width: 40px;
                font-size: 12px;
            }

            .cell-wrapper {
                gap: 4px;
            }
        }

        .circular-progress-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .circular-progress {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .circular-progress svg {
            transform: rotate(-90deg);
            width: 60px;
            height: 60px;
        }

        .circular-progress circle {
            fill: none;
            stroke-width: 6;
            stroke-linecap: round;
        }

        .circular-progress .bg {
            stroke: #eee;
        }

        .circular-progress .progress-bar {
            stroke: #2ecc71;
            transition: stroke-dashoffset 0.5s ease;
        }

        .circular-progress .percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            font-size: 14px;
            color: #34495e;
        }

        .black {
            background-color: #000;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div class="card card-custom gutter-b">
                <div class="card-header card-header-tabs-line">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_4">
                                    <span class="nav-icon"><i class="flaticon-list"></i></span>
                                    <span class="nav-text">RECONCILIATION</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_4">
                                    <span class="nav-icon"><i class="flaticon-imac"></i></span>
                                    <span class="nav-text">SUMMARY</span>
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
                                <div class="col-sm-5 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="my-addon">Tanggal Mulai</span>
                                        </div>
                                        <input class="form-control tglMulai" type="date" name=""
                                            value="{{ date('Y-m-01') }}" placeholder="Recipient's text"
                                            aria-label="Recipient's " aria-describedby="my-addon">
                                    </div>
                                </div>
                                <div class="col-sm-5 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="my-addon">Tanggal Selesai</span>
                                        </div>
                                        <input class="form-control tglSelesai" value="{{ date('Y-m-d') }}" type="date"
                                            name="" placeholder="Recipient's text" aria-label="Recipient's "
                                            aria-describedby="my-addon">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <a href="javascript:void(0)" onclick="cariData()"
                                        class="btn btn-block btn-dark btn-search">
                                        <i class="fas fa-search"></i> Cari
                                    </a>
                                </div>
                                <div class="col-sm-12  mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="listTable">
                                            <thead>
                                                <tr class="text-center">
                                                    <th colspan="10">STO LIST</th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th>No</th>
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Location Code</th>
                                                    <th>Qty System</th>
                                                    <th>Qty Actual</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="kt_tab_pane_2_4" role="tabpanel" aria-labelledby="kt_tab_pane_2_4">
                            <div class="container-fluid"> <!-- Ini penting -->
                                <div class="row">
                                    <div class="col-12">
                                        <div id="progress-summary" class="progress-summary"></div>
                                        <div id="progress-container"></div>
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
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr("href"); // 
                if (target === "#kt_tab_pane_1_4") {
                    cariData();
                } else {
                    listMonitoring();
                }
            });

            if ($('#kt_tab_pane_1_4').hasClass('show active')) {
                cariData();
            }
        });

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
            console.log(tgl_mulai);

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
                    ajax: "{{ url('inventory/stock-take/getList') }}/" + tgl_mulai + "/" + tgl_selesai,
                    columns: [{
                            data: null,
                            name: 'number',
                            sortable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1
                            }
                        },
                        {
                            data: 'product_code',
                            name: 'product_code',
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'actual_qty',
                            name: 'actual_qty'
                        },
                        {
                            data: 'variance',
                            name: 'variance'
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

        function listMonitoring() {
            var tgl_mulai = $('.tglMulai').val();
            var tgl_selesai = $('.tglSelesai').val();
            if (tgl_mulai == '' || tgl_selesai == '') {
                Swal.fire({
                    icon: 'warning',
                    text: 'Tanggal Tidak Boleh Kosong',
                })
            } else {
                $.ajax({
                    url: "{{ url('inventory/stock-take/getMonitoring') }}/" + tgl_mulai + "/" + tgl_selesai,
                    type: "GET",
                    dataType: 'json',
                    success: function(response) {
                        const locations = response.data;
                        const scannedStatus = response.scanned_codes || {};

                        const grouped = {};

                        locations.forEach(loc => {
                            const code = (loc.location_code || loc.location_aisle || '').trim()
                                .toUpperCase(); // ✅ NORMALIZE
                            const segment = code.includes('-') ? code.split('-')[0] : code;

                            loc.location_code = code;

                            if (!grouped[segment]) grouped[segment] = [];
                            grouped[segment].push(loc);
                        });

                        const container = document.getElementById('progress-container');
                        const summary = document.getElementById('progress-summary');

                        container.innerHTML = '';
                        summary.innerHTML = '';

                        const keys = Object.keys(grouped).sort();

                        let grandTotal = 0;
                        let grandScanned = 0;

                        keys.forEach(key => {
                            const rowDiv = document.createElement('div');
                            rowDiv.classList.add('row');

                            const label = document.createElement('div');
                            label.classList.add('row-label');
                            label.textContent = key;
                            rowDiv.appendChild(label);

                            const gridWrapper = document.createElement('div');
                            gridWrapper.classList.add('cell-wrapper');

                            let total = 0;
                            let scanned = 0;

                            grouped[key].forEach(loc => {
                                const cell = document.createElement('div');
                                cell.classList.add('cell');
                                const code = loc.location_code;
                                cell.title = code;

                                const status = scannedStatus[code]; // ✅ MATCH PROPERLY

                                if (status) {
                                    if (status.scan_flag === 'Yes' && status.variance ===
                                        'Yes') {
                                        cell.classList.add('black'); // variance
                                        scanned++;
                                    } else if (status.scan_flag === 'Yes' && status
                                        .variance ===
                                        'No') {
                                        cell.classList.add('green'); // match
                                        scanned++;
                                    } else {
                                        cell.classList.add('red');
                                    }
                                } else {
                                    cell.classList.add('red');
                                }

                                gridWrapper.appendChild(cell);
                                total++;
                            });

                            grandTotal += total;
                            grandScanned += scanned;

                            rowDiv.appendChild(gridWrapper);
                            container.appendChild(rowDiv);
                        });

                        const grandPercent = grandTotal > 0 ? Math.round((grandScanned / grandTotal) *
                            100) : 0;

                        summary.innerHTML = `
                                <div class="circular-progress-wrapper">
                                    <div class="circular-progress">
                                        <svg>
                                            <circle class="bg" cx="30" cy="30" r="26"></circle>
                                            <circle class="progress-bar" cx="30" cy="30" r="26"></circle>
                                        </svg>
                                        <div class="percentage">${grandPercent}%</div>
                                    </div>
                                    <div>
                                        <div><strong>Total:</strong> ${grandTotal} Location</div>
                                        <div><strong>Scanned:</strong> ${grandScanned} Location</div>
                                    </div>
                                </div>
                            `;
                        setCircularProgress(grandPercent);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to load monitoring',
                            text: xhr.responseText || 'Unknown error',
                        });
                    }
                });
            }
        }



        // Update circular progress
        function setCircularProgress(percent) {
            const circle = document.querySelector('.progress-bar');
            const radius = circle.r.baseVal.value;
            const circumference = 2 * Math.PI * radius;
            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = circumference;

            const offset = circumference - (percent / 100) * circumference;
            circle.style.strokeDashoffset = offset;

            // Optional: change color based on percentage
            if (percent < 50) {
                circle.style.stroke = '#e74c3c'; // red
            } else if (percent < 80) {
                circle.style.stroke = '#f1c40f'; // yellow
            } else {
                circle.style.stroke = '#2ecc71'; // green
            }
        }
        // setCircularProgress(grandPercent);
    </script>
@endpush
