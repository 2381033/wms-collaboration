<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
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
                                Inbound Confirmation Report
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
                                        <table class="table-header">
                                            <tr>
                                                <td>PEB Number</td>
                                                <td>:</td>
                                                <td>{{ $header->peb_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>PO Number</td>
                                                <td>:</td>
                                                <td>{{ $header->po_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Number</td>
                                                <td>:</td>
                                                <td>{{ $header->job_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Date</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Bongkar</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->tgl_bongkar)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Voulume (CBM)</td>
                                                <td>:</td>
                                                <td>{{ $header->cbm }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header" style="margin-left: 20%;">
                                            <tr>
                                                <td>Forwarder Name</td>
                                                <td>:</td>
                                                <td>{{ $header->forwarder_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Shipper Name</td>
                                                <td>:</td>
                                                <td>{{ $header->shipper_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Consignee Name</td>
                                                <td>:</td>
                                                <td>{{ $header->consignee_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Destination</td>
                                                <td>:</td>
                                                <td>{{ $header->destination }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Pallet</td>
                                                <td>:</td>
                                                <td>{{ $header->total_pallet }}</td>
                                            </tr>
                                            <tr>
                                                <td>Weight (KG)</td>
                                                <td>:</td>
                                                <td>{{ $header->weight }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th rowspan="2">Pallet</th>
                                        <th rowspan="2">Qty</th>
                                        <th rowspan="2">PIC Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $item)
                                        <tr>
                                            <td class="center">{{ $item->pallet_id }}</td>
                                            <td class="center">{{ $item->quantity }}</td>
                                            <td class="center">{{ $item->user_id }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        window.print()
    </script>
</body>

</html>
