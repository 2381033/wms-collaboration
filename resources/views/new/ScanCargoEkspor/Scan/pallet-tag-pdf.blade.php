<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0cm;
            size: 100mm 50mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }

        .page {
            width: 100mm;
            height: 50mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            box-sizing: border-box;
            padding-top: 10mm;
        }

        .page:not(:last-child) {
            page-break-after: always;
        }


        .barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
        }

        .barcode-text {
            margin-top: 2mm;
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
        }

        .page:not(:last-child) {
            page-break-after: always;
        }

        .barcode img {
            display: block;
            /* margin-left: auto; */
            margin-right: auto;
            max-width: 70mm;
            max-height: 20mm;
            margin-left: 17mm;
        }
    </style>
</head>

<body>
    @foreach ($barcodes as $barcode)
        <div class="page">
            <div class="barcode">
                <img src="data:image/png;base64,{{ $barcode['image_base64'] }}" alt="{{ $barcode['kode'] }}">
            </div>
            <div class="barcode-text">{{ $barcode['kode'] }}</div>
        </div>
    @endforeach
</body>

</html>
