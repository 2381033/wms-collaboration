<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    <style>
        /* body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 12pt "Tahoma";
} */
        /* * {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
} */
        .page {
            width: 105mm;
            min-height: 148mm;
            padding: 3mm;
            margin: 5mm auto;
            border: 1px #333 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            page-break-after: always;
            page-break-inside: avoid;
        }

        /* @page {
    size: A6 Landscape;
}

@media print {
    html, body {
        padding: 0;
        margin: 0;
    }

    .page {
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
        page-break-inside: avoid;
        -webkit-border-radius: initial;
        -moz-border-radius: initial;
        -ms-border-radius: initial;
        -o-border-radius: initial;
    }

    .footer,
    #non-printable {
        display: none !important;
    }
    #printable {
        display: block;
    }
} */

        /* .wrapper {
    display: grid;
    grid-template-columns: 100mm 5mm 100mm;
    grid-template-rows: auto 5mm auto;
    background-color: #fff;
    color: #444;
} */

        .container {
            width: 100%;
            margin: 0px auto;
        }

        .row:after {
            content: "";
            display: grid;
            clear: both;
        }

        .column {
            float: left;
            padding-bottom: 5px;
        }

        .col-100 {
            width: 100%;
        }

        .center {
            text-align: center;
        }

        .table {
            font-size: 15px;
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid rgb(0, 0, 0);
        }

        .table tbody tr td {
            text-align: left;
            padding: 5px;
            border: 1px solid rgb(0, 0, 0);
        }

        .table .center {
            text-align: center;
        }
    </style>
</head>

<body>
    @foreach ($list_data->groupBy('pallet_id') as $key => $item)
        @php
            // Ambil semua serial number dalam satu pallet
            $serials = $item->pluck('serial_no')->unique();

            // Ambil PO number dari serial_no yang berbeda-beda (dipisah dari awal string)
            $poFromSerial = $serials
                ->map(function ($serial) {
                    return explode('-', $serial)[0];
                })
                ->unique()
                ->implode(', ');
        @endphp

        <div class="page">
            <div class="container">
                <div class="row">
                    <div class="column col-100">
                        <table class="table">
                            <tr>
                                <td colspan="2">
                                    <img alt="image" src="{{ asset('images/logos.png') }}" alt=""
                                        height="25pt">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="center">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->where('pallet_id', $key)->first()->serial_no, 'QRCODE', 4, 4) }}"
                                        alt="barcode" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small>Job No</small><br>
                                    <b>{{ $view->job_no }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small>Forwarder Name</small><br>
                                    <b>{{ $view->forwarder_name }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small>Shipper Name</small><br>
                                    <b>{{ $view->shipper_name }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <small>{{ $view->shipper_id == 38 ? 'PO Number - HBL' : 'PO Number' }}</small>
                                    <br>
                                    <span style="font-size: 12px; font-weight: bold;">
                                        {{ $poFromSerial }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $view->peb_no == 0 ? 'AJU Number' : 'PEB Number' }}</small><br>
                                    <span
                                        style="font-size: 35px"><b>{{ $view->peb_no == 0 ? $view->aju_no : $view->peb_no }}</b></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small>Consignee Name</small>
                                    <br>
                                    <span style="font-size :15px;"><b>{{ $view->consignee_name }}</b></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small>Destination</small><br>
                                    <span style="font-size: 15px;"> <b>{{ $view->destination }}</b></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <small>Quantity</small><br>
                                    <b>{{ $item->where('pallet_id', $key)->sum('quantity') }} &nbsp; of &nbsp;
                                        {{ $total_receipt }}</b>
                                </td>
                                <td>
                                    <small>Total Pallet</small><br>
                                    <b>{{ $item->where('pallet_id', $key)->first()->pallet_id }} &nbsp; / &nbsp;
                                        {{ $total_pallet }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <small>Checker</small><br>
                                    <b>{{ $checker_name == '-' ? '-' : Str::Upper($checker_name) }}</b>
                                </td>
                                <td>
                                    <small>Tanggal Bongkar</small><br>
                                    <b>{{ \Carbon\carbon::parse($view->created_at)->format('d-m-Y') ?? '-' }}</b>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr style="border-style : hidden;">
                                <td colspan="2">
                                    <span style="font-size: 9px; margin-left: 21em;"><b>PT. MASAJI KARGOSENTRA
                                            TAMA<b></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <script>
        // window.print();
    </script>
</body>

</html>
