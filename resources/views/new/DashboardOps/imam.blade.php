@extends('layouts.new.base')
@section('title', 'MKT - Dashboard Ops.')
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
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">

                    <form method="post" id="searchOutbound">
                        @csrf
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Branch Name</label>
                                    <select class="form-control selectBranch" name="branch" id="">
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Principal Name</label>
                                    <select class="form-control selectPrincipal" style="width: 100%;" name="principal"
                                        id="">
                                        @foreach ($principal as $item)
                                            <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Date From</label>
                                    <input type="date" name="date_from" class="form-control dateFrom" autocomplete="off"
                                        id="" value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Date To</label>
                                    <input type="date" name="date_to" class="form-control dateTo" autocomplete="off"
                                        id=""value="{{ date('Y-m-t') }}">
                                </div>
                            </div>
                            <div class="col-sm-2 mt-4">
                                <div class="float-end mt-3">
                                    <button type="submit" class="btn btn-block btn-info"><i class="fas fa-search"></i>
                                        Search</a>
                                </div>
                            </div>
                        </div>
                    </form>



                    <div class="row justify-content-center">
                        <div class="styled-table">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr class="active-row">
                                        <th colspan="2" class="text-center">OUTBOUND</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="active-row">
                                        <th>Total Order</th>
                                        <th>
                                            <label for="" class="totalOrderToday"></label>
                                        </th>
                                    </tr>
                                    <tr class="active-row">
                                        <th>Truck Gate IN</th>
                                        <th>
                                            <label for="" class="truckGateInToday"></label>
                                        </th>
                                    </tr>
                                    <tr class="active-row">
                                        <th>Process loading</th>
                                        <th>
                                            <label for="" class="ProcessloadingToday"></label>
                                        </th>
                                    </tr>
                                    <tr class="active-row">
                                        <th>Finish loading</th>
                                        <th>
                                            <label for="" class="finishloadingToday"></label>
                                        </th>
                                    </tr>
                                    </tr>
                                    <tr class="active-row">
                                        <th>Total Pallet Loading</th>
                                        <th>
                                            <label for="" class="TotalPalleteToday"></label>
                                        </th>
                                    </tr>

                                </tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="custom-col">

                        </div>

                        <div class="styled-table">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">DESPATCH TRUCK</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicle">

                                </tbody>
                            </table>

                        </div>

                    </div>
                    <br>
                    <br>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="float-left">
                                <button class="btn btn-block btn-info non-clickable-button"> Truck</button>
                            </div>
                            <div id="vehicleOutbound"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-left">
                                <button class="btn btn-block btn-info non-clickable-button"> Pallet</button>
                            </div>
                            <div id="palleteOutbound"></div>
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
        $('.selectPrincipal').select2();
        $('#searchOutbound').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('searchOutbound') }}",
                data: $('#searchOutbound').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {

                    // console.log(response.data)
                    // console.log(response.data.total_pallet_month)
                    // console.log(response.data.total_vehicle_month)
                    TableTruckToday(response.data.truck_todays)
                    tableOutboundToday(response.data)
                    MonthlyChartOutboundVehicle(response.data)
                    MonthlyChartOutboundPallet(response.data)


                },
                error: function(error) {
                    console.log(error);
                }
            });
        })

        function tableOutboundToday(p) {
            console.log(p.totalOrderToday);
            $('.totalOrderToday').text(p.totalOrder);
            $('.truckGateInToday').text(p.truck_gate_in);
            $('.ProcessloadingToday').text(p.process_loading);
            $('.finishloadingToday').text(p.finish_loading);
            $('.TotalPalleteToday').text(p.total_pallet_day);
        }

        function TableTruckToday(p) {
            $('#vehicle').html("")
            $.each(p, function(key, val) {
                $('#vehicle').append(`
                    <tr class="active-row">
                        <th>${val.size_name}</th>
                        <th>${val.total}</th>
                    </tr>
                `)
            });
        }

        function MonthlyChartOutboundVehicle(p) {
            Highcharts.chart('vehicleOutbound', {

                title: {
                    text: `Truck Outbound : ${p.principal_name}`,
                },
                yAxis: {
                    title: {
                        text: 'Charts Outbound'
                    },
                },

                xAxis: {
                    accessibility: {},
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                    }
                },

                series: [{
                    name: 'OUTBOUND',
                    data: p.total_vehicle_month
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                },
                credits: {
                    enabled: false
                },

            });
        }

        function MonthlyChartOutboundPallet(p) {
            Highcharts.chart('palleteOutbound', {

                title: {
                    text: `Pallete Outbound : ${p.principal_name}`,
                },
                yAxis: {
                    title: {
                        text: 'Charts Outbound'
                    },
                },

                xAxis: {
                    accessibility: {},
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                    }
                },

                series: [{
                    name: 'OUTBOUND',
                    data: p.total_pallet_month
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                },
                credits: {
                    enabled: false
                },

            });
        }
    </script>
@endpush
