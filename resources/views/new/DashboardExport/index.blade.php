@extends('layouts.new.base')
@section('title', 'MKT - Dashboard Export')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .custom-col {
            flex: 0 0 23.16667%;
            /* Adjust the percentage as needed */
            max-width: 23.16667%;
        }

        .non-clickable-button {
            pointer-events: none;
            /* Disable pointer events */
            opacity: 0.9;
            /* Optionally reduce opacity to indicate it's not interactive */
            cursor: default;
            /* Set default cursor */
        }

        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 300px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .styled-table tbody tr.active-row {
            font-weight: bold;
            color: #009879;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div>
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch Name</label>
                                    <select class="form-control selectBranch" name="branch">
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Principal Name</label>
                                    <select class="form-control selectPrincipal" style="width: 100%;" name="principal">
                                        @foreach ($principal as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 mt-6">
                                <div class="float-end mt-3 mb-3">
                                    <a href="#" class="btn btn-block btn-info" onclick="getData()"><i
                                            class="fas fa-search"></i>
                                        Search</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="col-sm-12">
                                    <div class="alert alert-dark mb-5 p-5" role="alert">
                                        <h4 class="alert-heading">Pallet Capacity</h4>
                                        <label>
                                            <h3 class="totalPallet"></h3>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="alert alert-danger mb-5 p-3" role="alert">
                                        <h4 class="alert-heading">Occupied Slot (Today)</h4>
                                        <label>
                                            <h3 class="occupiedSlot"></h3>
                                        </label>
                                        <div class="progress progress-lg bg-white-o-90">
                                            <div class="progress-bar bg-white progressOccupiedSlot" role="progressbar"
                                                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                <h6 class="text-dark mt-2 percentageOccupiedSlot"></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="alert alert-success mb-5 p-5" role="alert">
                                        <h4 class="alert-heading">Available Slot (Today)</h4>
                                        <label>
                                            <h3 class="availableSlot"></h3>
                                        </label>
                                        <div class="progress progress-lg bg-white-o-90">
                                            <div class="progress-bar bg-white progressAvailableSlot" role="progressbar"
                                                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                <h6 class="text-dark mt-2 percentageAvailableSlot"></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div id="occupancyCharts"></div>
                                <div class="text-center">
                                    <a href="#" class="btn btn-sm btn-dark hide" id="occupancyButton">
                                        <i class="fas fa-hourglass"></i> Occupancy IN OUT
                                    </a>
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script type="text/javascript">
        // $(document).ready(function() {
        //     setInterval(function() {
        //         getData()
        //     }, 3600000);
        //     Highcharts.AST.allowedAttributes.push('onclick');
        // });
        $('.selectPrincipal').select2();

        function getData() {
            var branch = $('.selectBranch').val();
            var principal = $('.selectPrincipal').val();
            $.ajax({
                url: "{{ url('export/dashboard/search') }}/" + branch + '/' + principal,
                type: "get",
                dataType: 'json',
                success: function(response) {
                    cardOccupancy(response.data.cardOccupancy)
                    generateChartOccupancy(response.data)
                    // if (response.data.countData > 0) {
                    // } else {
                    //     Swal.fire({
                    //         icon: 'warning',
                    //         title: 'Data not found!!',
                    //     })
                    // }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }


        $('#searchData').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ url('searchData') }}",
                data: $('#searchData').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    cardOccupancy(response.data.cardOccupancy)
                    generateChartOccupancy(response.data)
                    // if (response.data.length > 0) {} else {
                    //     Swal.fire({
                    //         icon: 'warning',
                    //         title: 'Data not found!!',
                    //     } cx x bnxb x xxxdxnxxnxy
                    // }
                },
                error: function(error) {
                    console.log('error');
                }
            });
        });

        $(document).ready(function() {
            $('#occupancyButton').on('click', function() {
                var branch = $('.selectBranch').val();
                var principal = $('.selectPrincipal').val();
                getListOccupancy(branch, principal, start, end);
            });
        });

        function getListOccupancy(branch, principal, start, end) {
            $.ajax({
                url: `/dashboard-ops/getListOccupancy/${branch}/${principal}/${start}/${end}`,
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    $('#occupancyHistory').modal('show');
                    $('#OccupancyHistori').html("");

                    var openingBalance = response.opening_balance;

                    $('#openingBalance').text(openingBalance);
                    $.each(response.data, function(key, val) {
                        $('#OccupancyHistori').append(`
                                <tr class="text-center">
                                    <td>${val.transaction_date}</td>
                                    <td>${val.in}</td>
                                    <td>${val.out}</td>
                                    <td>${val.stock}</td> 
                                </tr>`);
                    });
                    // $('#OccupancyHistori').append('<tr><td colspan="4" class="text-center">No data available</td></tr>');
                },
                error: function(error) {
                    console.log('Error fetching data:', error);
                    $('#OccupancyHistori').html(
                        '<tr><td colspan="4" class="text-center">Failed to fetch data</td></tr>');
                }
            });
        }


        function cardOccupancy(params) {
            var percentageOccupiedSlot = Math.round(((params.occupied_slot / params.total_pallet) * 100)) + "%";
            var percentageAvailableSlot = Math.round(((params.available_slot / params.total_pallet) * 100)) + "%";
            $('.totalPallet').text(params.total_pallet)
            $('.occupiedSlot').text(params.occupied_slot)
            $('.availableSlot').text(params.available_slot)
            $(".progressBarTotalPallet").css("width", '100%');
            $(".percentageOccupiedSlot").text(percentageOccupiedSlot);
            $(".progressOccupiedSlot").css("width", percentageOccupiedSlot);
            $(".percentageAvailableSlot").text(percentageAvailableSlot);
            $(".progressAvailableSlot").css("width", percentageAvailableSlot);
        }

        function generateChartOccupancy(params) {
            Highcharts.chart('occupancyCharts', {
                title: {
                    text: 'Occupancy This Month',
                    align: 'left'
                },
                xAxis: {
                    categories: params.category,
                    title: {
                        text: 'Day'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Occupied Slot (Pallet)'
                    },
                    allowDecimals: false
                },
                tooltip: {
                    formatter: function() {
                        return `<b>Day: ${this.x + 1}</b><br>Occupied Slot: <b>${this.y}</b> Pallet`;
                    }
                },
                plotOptions: {
                    series: {
                        marker: {
                            enabled: true
                        },
                        borderRadius: '25%'
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    type: 'spline',
                    name: 'Occupied Slot',
                    data: params.dataMonthlyOccupancy,
                    color: '#7cb5ec' // Opsional: warna garis
                }]
            });
        }
    </script>
@endpush
