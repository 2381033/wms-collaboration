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
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <form method="post" id="searchInbound">
                        @csrf
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Branch Name</label>
                                    <select class="form-control selectBranch" name="branch">
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Principal Name</label>
                                    <select class="form-control selectPrincipal" style="width: 100%;" name="principal">
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
                                        value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">Date To</label>
                                    <input type="date" name="date_to" class="form-control dateTo" autocomplete="off"
                                        value="{{ date('Y-m-t') }}">
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
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="col-sm-12">
                                <div class="alert alert-success mb-5 p-5" role="alert">
                                    <h4 class="alert-heading">Pallet Capacity</h4>
                                    <label>
                                        <h3 class="totalPallet"></h3>
                                    </label>
                                    <div class="progress progress-lg bg-white-o-90">
                                        <div class="progress-bar bg-white progressBarTotalPallet" role="progressbar"
                                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                            <h6 class="text-dark mt-2 percentagetotalPallet"></h6>
                                        </div>
                                    </div>
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
                        <div class="col-sm-9">
                            <div id="occupancyCharts"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="styled-table">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">INBOUND TODAY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Total Order</th>
                                        <th>
                                            <label for="" class="totalOrderToday"></label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Truck Gate IN</th>
                                        <th>
                                            <label for="" class="truckGateInToday"></label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Process Unloading</th>
                                        <th>
                                            <label for="" class="ProcessUnloadingToday"></label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Finish Unloading</th>
                                        <th>
                                            <label for="" class="finishUnloadingToday"></label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Job Confirmed</th>
                                        <th>
                                            <label for="" class="jobConfirmToday"></label>
                                        </th>
                                    <tr>
                                        <th>Total Pallet Receiving</th>
                                        <th>
                                            <label for="" class="totalPalletReceiving"></label>
                                        </th>
                                    </tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="custom-col"></div>
                        <div class="styled-table">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="9" class="text-center">INBOUND TRUCK TODAY</th>
                                    </tr>
                                </thead>
                                <tbody id="tableTruck">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div id="truckMonthlyInbound"></div>
                        </div>
                        <div class="col-sm-6">
                            <div id="monthlyInbound"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="styled-table">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr class="active-row">
                                        <th colspan="2" class="text-center">OUTBOUND TODAY</th>
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
                                        <th colspan="2" class="text-center">DESPATCH TRUCK TODAY</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicle">

                                </tbody>
                            </table>

                        </div>

                    </div>
                    <br>
                    <div class="row justify-content-center">
                        <div class="col-sm-6">
                            <div class="float-left">
                                <a href="#" class="btn btn-block btn-info non-clickable-button"> Truck</a>
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

        $('#searchInbound').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('searchInbound') }}",
                data: $('#searchInbound').serialize(),
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    console.log(response.data);
                    if (response.data.countData > 0) {
                        tableInboundToday(response.data)
                        generateTableTruck(response.data.truckToday)
                        monthlyTruckInbound(response.data)
                        cardOccupancy(response.data.cardOccupancy)
                        generateMonthlyInbound(response.data)
                        generateChartOccupancy(response.data)
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data not found!!',
                        })
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        })

        function generateMonthlyInbound(params) {
            Highcharts.chart('monthlyInbound', {
                title: {
                    text: `Pallet Receiving (${params.principal})`,
                    align: 'left'
                },

                subtitle: {
                    text: "Tahun {{ date('Y') }}",
                    align: 'left'
                },

                yAxis: {
                    title: {
                        text: 'Pallet Receiving'
                    },
                },

                xAxis: {
                    accessibility: {},
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                        'Nov',
                        'Dec'
                    ]
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
                    name: 'PALLET',
                    data: params.dataMonthly
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

        function generateTableTruck(params) {
            $('#tableTruck').html("")
            $.each(params, function(key, val) {
                $('#tableTruck').append(`
                    <tr>
                        <th>${val.size_name}</th>
                        <th>${val.count}</th>
                    </tr>
                `)
            });
        }

        function tableInboundToday(params) {
            $('.totalOrderToday').text(params.totalOrderToday);
            $('.truckGateInToday').text(params.truckGateInToday);
            $('.ProcessUnloadingToday').text(params.ProcessUnloadingToday);
            $('.finishUnloadingToday').text(params.finishUnloadingToday);
            $('.totalPalletReceiving').text(params.totalPalletReceiving);
            $('.jobConfirmToday').text(params.jobConfirmedToday);
        }

        function monthlyTruckInbound(params) {
            Highcharts.chart('truckMonthlyInbound', {
                title: {
                    text: `Truck Inbound (${params.principal})`,
                    align: 'left'
                },

                subtitle: {
                    text: "Tahun {{ date('Y') }}",
                    align: 'left'
                },

                yAxis: {
                    title: {
                        text: 'Truck Inbound'
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
                    name: 'TRUCK',
                    data: params.dataMonthlyTruck
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

        function cardOccupancy(params) {
            var percentageOccupiedSlot = Math.round(((params.occupied_slot / params.total_pallet) * 100)) + "%";
            var percentageAvailableSlot = Math.round(((params.available_slot / params.total_pallet) * 100)) + "%";
            $('.totalPallet').text(params.total_pallet)
            $('.occupiedSlot').text(params.occupied_slot)
            $('.availableSlot').text(params.available_slot)
            $(".percentagetotalPallet").text('100%');
            $(".progressBarTotalPallet").css("width", '100%');
            $(".percentageOccupiedSlot").text(percentageOccupiedSlot);
            $(".progressOccupiedSlot").css("width", percentageOccupiedSlot);
            $(".percentageAvailableSlot").text(percentageAvailableSlot);
            $(".progressAvailableSlot").css("width", percentageAvailableSlot);
        }

        function generateChartOccupancy(params) {
            Highcharts.chart('occupancyCharts', {
                title: {
                    text: `Occupancy History (${params.principal})`,
                    align: 'left'
                },
                xAxis: {
                    categories: ['Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ]
                },
                yAxis: {
                    title: {
                        text: 'Value Occupancy'
                    },
                },
                tooltip: {
                    formatter: function() {
                        var tooltipContent = `
                        <b>Occupied Slot: ${this.y}</b> Pallet<br>
                        <b>Pallet Capacity: ${params.palletCapacity}</b> Pallet<br>
                        <b>SOR: ${Math.round(this.y / params.palletCapacity)}%</b><br>
                        `;
                        var label = '<span style="font-size: 10px">Data:</span><br/>';
                        return label + tooltipContent;
                    }
                },
                plotOptions: {
                    series: {
                        borderRadius: '25%'
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    type: 'spline',
                    name: 'Occupied Slot',
                    data: params.dataMonthlyOccupancy
                }, {
                    type: 'line',
                    step: 'center',
                    name: 'Pallet Capacity',
                    data: params.dataMaxPalletCapacity,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }]
            });
        }
    </script>
@endpush
