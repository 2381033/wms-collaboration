<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>
<style type="text/css">
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }
    }
</style>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{ asset('images/logos.png') }}" />
        </div>
        <table class="table-template">
            <thead>
                <tr>
                    <td>
                        <div class="header-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="title">
                            <h3 class="title-header">
                                {{ $tittle }}
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Job No</th>
                                        <th rowspan="2">Customer Name</th>
                                        {{-- <th rowspan="2">Date IN</th> --}}
                                        <th colspan="5">Vehicle Detail</th>
                                        <th colspan="6">Cargo Detail</th>
                                        <th colspan="2">KPI</th>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Number</th>
                                        <th>Container Number</th>
                                        <th>Transporter Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Shipment Arrival Date</th>
                                        <th>Unloading Start</th>
                                        <th>Unloading Finish</th>
                                        <th>Qty</th>
                                        <th>Vol. (Cbm)</th>
                                        <th>Weight (Kg)</th>
                                        <th>Waiting Time</th>
                                        <th>Unloading Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $item)
                                        @php
                                            $shipment_arrival_date = \Carbon\Carbon::parse($item['shipment_arrival_date']);
                                            $unloading_start = \Carbon\Carbon::parse($item['unloading_start']);
                                            $unloading_finish = \Carbon\Carbon::parse($item['unloading_finish']);
                                            $waiting_time = $unloading_start->diff($shipment_arrival_date)->format('%H:%I');
                                            $unloading_time = $unloading_finish->diff($unloading_start)->format('%H:%I');
                                        @endphp
                                        <tr>
                                            <td>{{ $item['job_no'] }}</td>
                                            <td style="background-color: antiquewhite">{{ $item['customer'] }}</td>
                                            <td>{{ $item['vehicle_number'] }}</td>
                                            <td>{{ $item['container_number'] }}</td>
                                            <td>{{ $item['transporter_name'] }}</td>
                                            <td>{{ $item['vehicle'] }}</td>
                                            <td>{{ $item['size'] }}</td>
                                            <td style="background-color: antiquewhite">
                                                {{ \Carbon\carbon::parse($item['shipment_arrival_date'])->format('d-m-Y H:i') }}
                                            </td>
                                            <td style="background-color: antiquewhite">
                                                {{ \Carbon\carbon::parse($item['unloading_start'])->format('d-m-Y H:i') }}
                                            </td>
                                            <td style="background-color: antiquewhite">
                                                {{ \Carbon\carbon::parse($item['unloading_finish'])->format('d-m-Y H:i') }}
                                            </td>
                                            <td style="background-color: antiquewhite">{{ $item['qty_total'] }}</td>
                                            <td style="background-color: antiquewhite">
                                                {{ number_format($item['cbm_total'], 2, ',', '.') }}</td>
                                            <td style="background-color: antiquewhite">
                                                {{ number_format($item['w_total'], 0, ',', '.') }}</td>
                                            <td>
                                                {{ $waiting_time }}
                                            </td>
                                            <td>{{ $unloading_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        document.title = "{{ $tittle }}"
        // window.print()
    </script>
</body>

</html>
