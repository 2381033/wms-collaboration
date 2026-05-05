<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Barcode PDF</title>

    <style>
        @page {
            size: portrait;
            margin: 10mm;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;
        }

        /* 🔥 vertical center pakai table */
        .outer-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        .outer-table td {
            text-align: center;
            vertical-align: middle;
        }

        .qr img {
            width: 60mm;
            height: 60mm;
        }

        .text-table {
            width: 85%;
            border-collapse: collapse;
            margin-left: 15%;
            margin-top: 5%
        }

        .text-table td {
            border: 1px solid #000;
            font-size: 16px;
            padding: 8px;
            text-align: center;
            font-weight: 900;
        }
    </style>

</head>

<body>
    @foreach ($list_data as $item)
        <div class="page">
            <table class="outer-table">
                <tr>
                    <td>
                        <br>
                        <br>
                        <div class="qr">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->encrypted_id, 'QRCODE') }}">
                        </div>
                        <table class="text-table">
                            <tr style="margin-left: 60%;">
                                <td>{{ $item->product_code ?? '-' }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->location_code ?? '-' }}</td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>

</html>
