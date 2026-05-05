@extends('layouts.new.base')
@section('title', 'MKT - Occupancy Daily DC')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
            margin-top: 15px;
        }

        .calendar-cell {
            background: #f4f6f9;
            border: 1px solid #e2e8f0;
            transition: all .2s ease;
        }

        .calendar-cell:hover {
            background: #eef1f5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        /* Qty box */
        .qty-box {
            margin-top: 14px;
            padding: 8px;
            border-radius: 8px;
            background: #e2e8f0;
            /* darker than card */
            color: #1f2937;
            font-weight: 600;
        }

        .qty-box:hover {
            background: #cbd5e1;
        }

        /* Empty state */
        .qty-empty {
            background: #e5e7eb;
            color: #9ca3af;
        }

        /* Tooltip (Bootstrap 5 override) */
        .tooltip {
            --bs-tooltip-bg: #1f2937;
            /* dark slate */
            --bs-tooltip-color: #f9fafb;
            font-size: 13px;
        }

        .tooltip-inner {
            max-width: 260px;
            text-align: left;
            padding: 10px 12px;
        }

        /* Calendar header */
        .calendar-header {
            color: #475569;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <form action="{{ route('searchOccupancyDaillyDC') }}" method="post" id="searchOccupancyDaillyDC">
                        @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select class="form-control" name="branch_id" required id="">
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Principal</label>
                                    <select class="form-control" name="principal_id" required id="principalSelect">
                                        @foreach ($principal as $item)
                                            <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <select class="form-control" name="month" id="month" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 mt-4">
                                <button class="btn btn-dark btn-lg mt-2" style="border-radius: 10px"><i
                                        class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="card mt-5" style="border-radius: 15px;">
                                <div class="card-body">
                                    <h4 id="calendarTitle"></h4>
                                    <div id="occupancyCalendar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <table class="table table-bordered" id="listTable">
                                <thead>
                                    <tr class="text-center">
                                        <th colspan="10">History Occupancy Daily</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>IN</th>
                                        <th>OUT</th>
                                        <th>Occupancy</th>
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
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>
    <script>
        $('#principalSelect').select2({
            width: '100%'
        });

        $('#searchOccupancyDaillyDC').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (!res.success) return;
                    renderTable(res.data);
                    renderCalendar(res.data);
                },
                error: function(err) {
                    Swal.fire('Error', 'Failed load data', 'error');
                }
            });
        });

        function renderTable(data) {

            if ($.fn.DataTable.isDataTable('#listTable')) {
                table.clear().destroy();
            }

            table = $('#listTable').DataTable({
                data: data,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Occupancy_Daily',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Occupancy_Daily',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger btn-sm'
                    }
                ],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'date',
                        render: data => {
                            const d = new Date(data + 'T00:00:00');
                            return d.toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'in',
                        className: 'text-end',
                        render: data => data.toLocaleString()
                    },
                    {
                        data: 'out',
                        className: 'text-end',
                        render: data => data.toLocaleString()
                    },
                    {
                        data: 'qty',
                        className: 'text-end fw-bold',
                        render: data => data.toLocaleString()
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                paging: true,
                searching: false,
                info: false
            });
        }

        function renderCalendar(data) {
            const month = $('#month').val();
            const year = new Date().getFullYear();

            const firstDay = new Date(year, month - 1, 1);
            const daysInMonth = new Date(year, month, 0).getDate();
            const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay();

            let map = {};
            data.forEach(item => map[item.date] = item.qty);

            let html = `<div class="calendar">`;
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(d => {
                html += `<div class="calendar-header">${d}</div>`;
            });

            for (let i = 1; i < startDay; i++) {
                html += `<div></div>`;
            }

            for (let d = 1; d <= daysInMonth; d++) {
                let dateStr = `${year}-${month.padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                let qty = map[dateStr];

                html += `
            <div class="calendar-cell">
                <div class="date">${d}</div>
                <div class="qty-box ${qty ? '' : 'qty-empty'}">
                    ${qty ? qty.toLocaleString() : '-'}
                </div>
            </div>
            `;
            }

            html += `</div>`;

            $('#calendarTitle').text(
                new Date(year, month - 1).toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                })
            );
            $('#occupancyCalendar').html(html);
        }
    </script>
@endpush
