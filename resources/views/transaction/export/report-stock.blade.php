<!DOCTYPE html>
<html>

<style type="text/css">
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 12pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 297mm;
        min-height: 210mm;
        padding: 10mm;
        margin: 10mm auto;
        border: 1px #333 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        page-break-after: always;
        page-break-inside: avoid;
    }

    @page {
        size: A4 Landscape;
        margin: 20px;
    }

    @media print {
        @page {
            size: A4 Landscape;
        }

        html,
        body {
            width: 297mm;
            height: 200mm;
            margin: 0 !important;
            padding: 0 !important;
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
        }

        .footer {
            position: fixed;
        }

        .signature {
            position: static;
            bottom: 0;
        }
    }

    .header,
    .header-space {
        height: 1px;
    }

    .header:after,
    .header-space {
        height: 80px;
    }

    .footer,
    .footer-space {
        height: 50px;
    }

    .header {
        top: 30px;
        color: black;
        text-align: left;
        line-height: 25px;
        width: 277mm;
    }

    .footer {
        bottom: 0;
        height: 50px;
        font-size: 10px;
        color: black;
        text-align: center;
        line-height: 25px;
        width: 277mm;
    }

    .signature {
        bottom: 0;
        height: 130px;
        font-size: 12px;
        color: black;
        text-align: center;
        line-height: 15px;
        width: 277mm;
    }

    .table-header-kerangka {
        width: 100%;
        margin-bottom: 15px;
    }

    .table-header-kerangka tr td {
        width: auto !important;
        max-width: 50%;
    }

    .table-header {
        color: #000;
        border-collapse: collapse;
        text-align: left;
        font-size: 11px;
        font-family: "Tahoma";
    }

    .table-header tr td {
        border: none;
        text-align: left;
        padding: 4px 4px;
        width: auto !important;
        max-width: 100%;
        white-space: nowrap;
        padding-right: 8px;
    }

    .table-template {
        width: 100%;
    }

    /* FIX: Jangan hilangkan border di tbody */
    .table-template thead tr th {
        border: none;
    }

    .table {
        font-size: 12px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #000;
    }

    .table thead tr th {
        text-align: center;
        padding: 5px;
        border: 1px solid #000;
    }

    .table tbody tr td {
        text-align: center;
        padding: 5px;
        border: 1px solid #000;
    }

    .table .center {
        text-align: center;
    }

    .table .left {
        text-align: left;
    }

    .table .right {
        text-align: right;
    }

    .table tbody tr td .break {
        padding: 0;
        line-height: 1px;
        height: 1px;
        overflow: hidden;
    }

    .logo {
        height: 60px;
    }

    .printer {
        width: 25px;
    }

    .title {
        text-align: center;
    }

    .title-header {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 14px;
        font-family: "Tahoma";
    }

    .blank-cell-qty {
        width: 50px;
    }

    .blank-cell-date {
        width: 60px;
    }

    .blank-cell {
        width: 70px;
    }

    .signature .table .sign {
        width: 25%;
    }

    .report-container,
    table {
        background-color: #fff;
    }

    table {
        border-collapse: collapse;
    }

    td,
    th {
        background-color: #fff;
        /* default putih rata */
    }

    table td:nth-child(8),
    table th:nth-child(8) {
        background-color: #fde9d9;
    }



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
                            @if ($reportType == 'detail')
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center" rowspan="2">Principal</th>
                                            <th class="center" rowspan="2">Shipper</th>
                                            <th class="center" rowspan="2">Receiving Date</th>
                                            <th class="center" rowspan="2">PO Number</th>
                                            <th class="center" rowspan="2">PEB Number</th>
                                            <th class="center" rowspan="2">Destination</th>
                                            <th class="center" rowspan="2" style="background-color: antiquewhite;">
                                                QTY</th>
                                            <th class="center" rowspan="2" style="background-color: antiquewhite;">
                                                Pallet ID</th>
                                            <th class="center" rowspan="2">Vol. Total (Cbm)</th>
                                            {{-- <th class="center" rowspan="2">Vol. Weight (Kg)</th> --}}
                                            <th class="center" rowspan="2">Total Pallet</th>
                                            <th class="center" rowspan="2">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $value)
                                            <tr>
                                                <td class="center">{{ $value['customer_name'] }}</td>
                                                <td class="center">{{ $value['shipper_name'] }}</td>
                                                <td class="center">{{ $value['receiving'] }}</td>
                                                <td class="center">{{ $value['po_number'] }}</td>
                                                <td class="center">{{ $value['peb_no'] == 0 ? '-' : $value['peb_no'] }}
                                                </td>
                                                <td class="center">{{ $value['destination'] }}</td>
                                                <td class="center" style="background-color: antiquewhite;">
                                                    {{ $value['quantity'] }}</td>
                                                <td class="center" style="background-color: antiquewhite;">
                                                    {{ $value['pallet_id'] }}</td>
                                                <td class="center">{{ $value['cbm'] }}</td>
                                                {{-- <td class="center">{{ $value['weight'] }}</td> --}}
                                                <td class="center">{{ $value['total_pallet'] }}</td>
                                                <td class="center">{{ $value['location_code'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center" rowspan="2">Shipper</th>
                                            <th class="center" rowspan="2" style="background-color: antiquewhite;">
                                                QTY</th>
                                            <th class="center" rowspan="2">Vol. Total (Cbm)</th>
                                            {{-- <th class="center" rowspan="2">Vol. Weight (Kg)</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $value)
                                            <tr>
                                                <td class="center">{{ $value['shipper_name'] }}</td>
                                                <td class="center" style="background-color: antiquewhite;">
                                                    {{ $value['quantity'] }}</td>
                                                <td class="center">{{ $value['cbm'] }}</td>
                                                {{-- <td class="center">{{ $value['weight'] }}</td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
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
