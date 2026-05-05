<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
</head>

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
                                {{ 'Outbound Picking Report' }}
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table-header-kerangka">
                                <tr>
                                    <td>
                                        <table class="table-header" >
                                            <tr>
                                                <td>JOB NO</td>
                                                <td>:</td>
                                                <td>{{ $header->job_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>JOB DATE</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->created_at)->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL QTY</td>
                                                <td>:</td>
                                                <td>{{ array_sum($order->pluck('qty_cargo')->toArray()) . ' PKGS' }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VOLUME (ACTUAL)</td>
                                                <td>:</td>
                                                <td>{{ array_sum($order->pluck('cbm')->toArray()). ' CBM' }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VGM</td>
                                                <td>:</td>
                                                <td>{{ array_sum($order->pluck('weight')->toArray()) . ' KG' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header" style="margin-left:5%">
                                            <tr>
                                                <td>PRINCIPAL</td>
                                                <td>:</td>
                                                <td>{{  Str::Upper($forwarder) }}</td>
                                            </tr>
                                            <tr>
                                                <td>DESTINATION</td>
                                                <td>:</td>
                                                <td>{{ $header->destination }}</td>
                                            </tr>
                                            <tr>
                                                <td>PICKING DATE</td>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>START PICKING</td>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>FINISH PICKING</td>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="center">No</th>
                                        <th class="center">Consignee Name</th>
                                        <th class="center">Shipper Name</th>
                                        <th class="center">PO No.</th>
                                        <th class="center">PEB No.</th>
                                        <th class="center">Location</th>
                                        <th class="center">Picking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order as $item)
                                        <tr>
                                            <td class="center">{{ $loop->iteration }}</td>
                                            <td class="center">{{ $item->consignee }}</td>
                                            <td class="center">{{ $item->shipper }}</td>
                                            <td class="center">{{ $item->po_number }}</td>
                                            <td class="center">{{ $item->peb_no }}</td>
                                            <td class="center">{{ is_null($item->location_code) ? '-' : $item->location_code }}</td>
                                            <td class="center">
                                                <input type="checkbox" readonly style="zoom: 120%;">
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="center">End Of Report</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        document.title = "Outbound Picking Report "
        // window.print()
    </script>
</body>

</html>
