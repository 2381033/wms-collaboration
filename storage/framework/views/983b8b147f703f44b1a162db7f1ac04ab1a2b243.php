

<?php $__env->startSection('title'); ?>
    Home
<?php $__env->stopSection(); ?>

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

    .loading-overlay {
        display: none;
        background: rgba(255, 255, 255, 0.7);
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        top: 0;
        z-index: 9998;
        align-items: center;
        justify-content: center;
    }

    .loading-overlay.is-active {
        display: flex;
    }

    .code {
        font-family: monospace;
        color: #dd4a68;
        background-color: rgb(238, 238, 238);
        padding: 0 3px;
    }

    /* .select2-container {
        width: 100% !important;
    } */

    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex;
        align-items: center;
    }
</style>
<?php
    $auth = DB::table('auth_group')
        ->where('id', Auth::user()->auth_group_id)
        ->value('name');
?>


<?php $__env->startSection('content'); ?>
    <section id="contact" class="contact">
        <div class="container-fluid">
            <div class="loading-overlay justify-content-center">
                <div class="drawing">
                    <div class="loading-dot"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body">
                            <form method="post" id="searchData">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Branch Name</label>
                                            <select class="form-control selectBranch" name="branch">
                                                <?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->branch_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Site Name</label>
                                            <select class="form-control selectsite" name="site">
                                                <?php $__currentLoopData = $site; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->site_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Principal Name</label>
                                            <select class="form-control selectPrincipal" id="selectPrincipal"
                                                name="principal" style="width:100%">
                                                <?php if(!$isVendor): ?>
                                                    <option value="ALL">ALL PRINCIPAL</option>
                                                <?php endif; ?>
                                                <?php $__currentLoopData = $principal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->principal_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-2 mt-3">
                                        <div class="float-end mt-3 mb-3">
                                            <button type="submit" class="btn btn-block btn-info"><i
                                                    class="fas fa-search"></i> Search</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="col-sm-12">
                                        <div class="alert alert-success mb-4 p-3" role="alert"
                                            style="background-color: #1BC5BD">
                                            <h4 class="alert-heading text-white">Pallet Capacity</h4>
                                            <label>
                                                <h3 class="totalPallet text-white"></h3>
                                            </label>
                                            <div class="progress progress-lg bg-white-o-90">
                                                <div class="progress-bar bg-white progressBarTotalPallet" role="progressbar"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 100%;">
                                                    <h6 class="text-dark mt-2 percentagetotalPallet"></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="alert alert-danger mb-4 p-3" role="alert"
                                            style="background-color: #F64E60">
                                            <h4 class="alert-heading text-white">Occupied Slot (Today)</h4>
                                            <label>
                                                <h3 class="occupiedSlot text-white"></h3>
                                            </label>
                                            <div class="progress progress-lg bg-white-o-90">
                                                <div class="progress-bar bg-white progressOccupiedSlot" role="progressbar"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 100%;">
                                                    <h6 class="text-dark mt-2 percentageOccupiedSlot"></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="alert alert-success mb-4 p-3 showHideAvailableSlot" role="alert"
                                            style="background-color: #009879">
                                            <h4 class="alert-heading text-white">Available Slot (Today)</h4>
                                            <label>
                                                <h3 class="availableSlot text-white"></h3>
                                            </label>
                                            <div class="progress progress-lg bg-white-o-90">
                                                <div class="progress-bar bg-white progressAvailableSlot" role="progressbar"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 100%;">
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
                                                <th>Process Unloading</th>
                                                <th>
                                                    <label for="" class="ProcessUnloadingToday"></label>
                                                </th>
                                            </tr>
                                            <tr class="active-row">
                                                <th>Finish Unloading</th>
                                                <th>
                                                    <label for="" class="finishUnloadingToday"></label>
                                                </th>
                                            </tr>
                                            <tr class="active-row">
                                                <th>Job Confirmed</th>
                                                <th>
                                                    <label for="" class="jobConfirmToday"></label>
                                                </th>
                                            <tr class="active-row">
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
                                                    <label for="" class="totalOrderTodayOut"></label>
                                                </th>
                                            </tr>
                                            <tr class="active-row">
                                                <th>Truck Gate IN</th>
                                                <th>
                                                    <label for="" class="truckGateInTodayOut"></label>
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
                                                    <label for="" class="TotalPalleteTodayOut"></label>
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

                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Truck Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Size Name</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="modal-body-vehicle">

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-sm-6">
                                    <div id="vehicleOutbound"></div>
                                </div>
                                <div class="col-sm-6">
                                    <div id="palleteOutbound"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="vehicleTable">
                        <table id="vehicles">
                            <!-- Table content will be dynamically added here -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="detailVehicle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">BULAN <label class="labelBulanText"
                                for=""></label></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Vehicle Type</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableDetailTruck">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="occupancyHistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Occupancy History</label></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-center">OPENING BALANCE</th>
                                            <th id="openingBalance">0</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>DATE</th>
                                            <th>IN</th>
                                            <th>OUT</th>
                                            <th>STOCK</th>
                                        </tr>
                                    </thead>
                                    <tbody id="OccupancyHistori">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('styles'); ?>
<?php $__env->stopPush(); ?>
<?php if(Auth::user()->is_maintenance == 'No' and $auth != 'BC'): ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/highcharts/highcharts.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/highcharts/exporting.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/highcharts/export-data.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/highcharts/accessibility.js')); ?>"></script>
        <script type="text/javascript">
            function initSelect2() {
                $('.selectBranch, .selectsite, .selectPrincipal').select2({
                    width: '100%',
                });
            }

            $(document).ready(function() {
                initSelect2();
            });


            $(document).ready(function() {
                initSelect2();
                setInterval(function() {
                    getData()
                }, 3600000);
                Highcharts.AST.allowedAttributes.push('onclick');
                setTimeout(() => {
                    initSelect2(); // re-init after layout stable
                }, 300);
            });

            $(document).ajaxSend(function(event, jqxhr, settings) {
                // Start pace
                $('.loading-overlay').addClass('d-flex');
                // $('#clock-loader').fadeIn('normal');
            });

            $(document).ajaxComplete(function(event, jqxhr, settings) {
                $('.loading-overlay').removeClass('d-flex');
                initSelect2();
            });

            $(document).ajaxError(function(event, jqxhr, settings) {
                // Stop pace
                $('.loading-overlay').removeClass('d-flex');
                // $('#clock-loader').fadeOut('normal')
            });

            function getData() {
                var branch = $('.selectBranch').val();
                var principal = $('.selectPrincipal').val();
                var start = $('.dateFrom').val();
                var end = $('.dateTo').val();
                $.ajax({
                    url: "<?php echo e(url('dashboard-ops/getData')); ?>/" + branch + '/' + principal + '/' + start + '/' + end,
                    type: "get",
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response.data);
                        if (response.data.countData > 0) {
                            tableInboundToday(response.data)
                            generateTableTruck(response.data.truckToday)
                            monthlyTruckInbound(response.data)
                            cardOccupancy(response.data.cardOccupancy)
                            generateMonthlyInbound(response.data)
                            generateChartOccupancy(response.data)

                            //outbound 
                            TableTruckToday(response.data.truck_todays)
                            tableOutboundToday(response.data)
                            MonthlyChartOutboundVehicle(response.data)
                            MonthlyChartOutboundPallet(response.data)
                        } else {
                            alert('Data Not Found!')
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            function getMonthTruck(p) {
                var branch = $('.selectBranch').val();
                var principal = $('.selectPrincipal').val();

                // Clear previous content in modal body
                $('#modal-body-vehicle').html("");

                $.ajax({
                    url: "<?php echo e(url('dashboard-ops/getMonthTruck')); ?>/" + p + '/' + branch + '/' + principal,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        // Assuming response is an array of objects
                        $.each(response, function(key, val) {
                            $('#modal-body-vehicle').append(`
                            <tr>
                                <td>${val.size_name}</td>
                                <td>${val.total}</td>
                            </tr>
                        `);
                        });
                        // Show the modal
                        $('#myModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            $('#searchData').on('submit', function(e) {
                // occupancyButton
                e.preventDefault();
                $.ajax({
                    url: "<?php echo e(route('searchData')); ?>",
                    data: $('#searchData').serialize(),
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        if (response.data.countData > 0) {
                            var toggle = $('#selectPrincipal').val();
                            console.log(toggle);
                            if (toggle == 'ALL') {
                                $('.showHideAvailableSlot').show()
                            } else {
                                $('.showHideAvailableSlot').hide()
                            }
                            tableInboundToday(response.data)
                            generateTableTruck(response.data.truckToday)
                            monthlyTruckInbound(response.data)
                            cardOccupancy(response.data.cardOccupancy)
                            generateMonthlyInbound(response.data)
                            generateChartOccupancy(response.data)

                            //outbound 
                            TableTruckToday(response.data.truck_todays)
                            tableOutboundToday(response.data)
                            MonthlyChartOutboundVehicle(response.data)
                            MonthlyChartOutboundPallet(response.data)
                            // $('#occupancyButton').removeClass('hide')
                        } else {
                            alert('Data not found!')
                        }
                    },
                    error: function(error) {
                        console.log('error');
                    }
                });
            });

            function generateMonthlyInbound(params) {
                Highcharts.chart('monthlyInbound', {
                    title: {
                        text: `Pallet Receiving (${params.principal})`,
                        align: 'left'
                    },

                    subtitle: {
                        text: "Tahun <?php echo e(date('Y')); ?>",
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
                            allowPointSelect: true,
                            label: {
                                connectorAllowed: false
                            },
                            point: {
                                events: {
                                    select: function(e) {}
                                }
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
                    <tr class="active-row">
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

            function showMoreDetails() {
                console.log('test');
            }

            function monthlyTruckInbound(params) {
                Highcharts.chart('truckMonthlyInbound', {
                    title: {
                        text: `Truck Inbound (${params.principal})`,
                        align: 'left'
                    },

                    subtitle: {
                        text: "Tahun <?php echo e(date('Y')); ?>",
                        align: 'left'
                    },
                    chart: {
                        events: {
                            load: function() {
                                console.log('onclick in allowedAttributes?', Highcharts.AST.allowedAttributes
                                    .includes('onclick'))
                            }
                        }
                    },
                    tooltip: {
                        useHTML: true,
                        // formatter: function() {
                        //     return 'Total Truck: ' + this.y + '<br/>' + '<b>Click point for detaills!</b>';
                        // },
                        hideDelay: 1000,
                        formatter: function() {
                            const {
                                point
                            } = this;
                            return `<span>
                                <span>Total Truck = ${point.y}</span><br>
                                <button type="button" onclick="showMoreDetails()">More Details</button>
                            </span>`
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Truck Inbound'
                        },
                    },

                    xAxis: {
                        accessibility: {},
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                            'Dec'
                        ],
                        labels: {
                            events: {
                                click: function(event) {
                                    // Handle click on X-axis label or tick
                                    var clickedCategory = event.target.textContent;
                                    alert('Clicked on X-axis category: ' + clickedCategory);
                                    // Perform additional actions here based on the clicked category
                                }
                            }
                        },
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        series: {
                            allowPointSelect: true,
                            label: {
                                connectorAllowed: true
                            },
                            point: {
                                events: {
                                    select: function(e) {
                                        getDetailVehicle(this.x)
                                    }
                                }
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

            function getDetailVehicle(params) {
                var branch = $('.selectBranch').val()
                var principal = $('.selectPrincipal').val()
                $.ajax({
                    url: "<?php echo e(url('dashboard-ops/getDetailVehicle')); ?>/" + params + '/' + branch + '/' + principal,
                    type: "get",
                    dataType: 'json',
                    success: function(response) {
                        $('#detailVehicle').modal('show')
                        $('.labelBulanText').text(parseInt(params + 1))
                        $('#tableDetailTruck').html("")
                        $.each(response, function(key, val) {
                            $('#tableDetailTruck').append(`
                                        <tr>
                                            <th>${val.size_name}</th> 
                                            <th>${val.count}</th> 
                                        </tr>`)
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
            $(document).ready(function() {
                $('#occupancyButton').on('click', function() {
                    var branch = $('.selectBranch').val();
                    var principal = $('.selectPrincipal').val();
                    var start = $('.dateFrom').val();
                    var end = $('.dateTo').val();

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
                $(".percentagetotalPallet").text('100%');
                $(".progressBarTotalPallet").css("width", '100%');
                $(".percentageOccupiedSlot").text(percentageOccupiedSlot);
                $(".progressOccupiedSlot").css("width", '100%');
                $(".percentageAvailableSlot").text(percentageAvailableSlot);
                $(".progressAvailableSlot").css("width", '100%');
            }

            function generateChartOccupancy(params) {
                Highcharts.chart('occupancyCharts', {
                    title: {
                        text: `Occupancy History (${params.principal || 'All Principal'})`,
                        align: 'left'
                    },
                    xAxis: {
                        categories: params.category
                    },
                    yAxis: {
                        title: {
                            text: 'Value Occupancy'
                        },
                    },
                    tooltip: {
                        formatter: function() {
                            var tooltipContent = `<b> Occupied Slot: ${this.y}</b> Pallet<br> 
                                            <b>Pallet Capacity: ${params.palletCapacity}</b> Pallet<br>
                                    `;
                            var label = '<span style="font-size: 10px">Data:</span><br/>';
                            return label + tooltipContent;
                        }
                    },
                    plotOptions: {
                        series: {
                            borderRadius: '25%'
                        },
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

            function tableOutboundToday(p) {
                // console.log(p.totalOrderToday);
                $('.totalOrderTodayOut').text(p.totalOrder);
                $('.truckGateInTodayOut').text(p.truck_gate_in);
                $('.ProcessloadingToday').text(p.process_loading);
                $('.finishloadingToday').text(p.finish_loading);
                $('.TotalPalleteTodayOut').text(p.total_pallet_day);
            }

            function TableTruckToday(p) {
                $('#vehicle').html("")
                $.each(p, function(key, val) {
                    $('#vehicle').append(`<tr class = "active-row">
                                        <th>${val.size_name}</th>
                                        <th>${val.total}</th>
                                    </tr>
                                    `)
                });
            }

            function MonthlyChartOutboundVehicle(p) {
                Highcharts.chart('vehicleOutbound', {
                    title: {
                        text: `Truck Outbound(${p.principal})`,
                    },
                    subtitle: {
                        text: "Tahun <?php echo e(date('Y')); ?>",
                        align: 'left'
                    },
                    yAxis: {
                        title: {
                            text: 'Charts Outbound'
                        },
                    },

                    xAxis: {
                        accessibility: {},
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                            'Dec'
                        ],
                        // labels: {
                        //     formatter: function () {
                        //         return '<al>' + this.value + '</a>'
                        //     },
                        //     useHTML: true
                        // }
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
                            allowPointSelect: true,
                            point: {
                                events: {
                                    select: function(e) {
                                        // alert(this.x)
                                        // getMonthTruck(this.x)
                                    }
                                },
                            },
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            var tooltipContent = `
                        <b> Total Truck : ${this.y}<br>
                        `;
                            var label = '<span style="font-size: 10px">Data:</span><br/>';
                            return label + tooltipContent;
                        }
                    },

                    series: [{
                        name: 'OUTBOUND TRUCK',
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
                        text: `Pallet Outbound(${p.principal})`,
                    },
                    subtitle: {
                        text: "Tahun <?php echo e(date('Y')); ?>",
                        align: 'left'
                    },
                    yAxis: {
                        title: {
                            text: 'Charts Outbound'
                        },
                        labels: {
                            format: '{value:,.0f}'
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
                        name: 'OUTBOUND PALLET',
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
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/dashboard/index.blade.php ENDPATH**/ ?>