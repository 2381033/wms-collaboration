<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eee;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            padding: 10px;
            border-radius: 20px;
            /* max-width: 300px; */
        }

        .card-content {
            text-align: center;
            padding: 0 5px;
        }

        .card-content h2 {
            font-size: 22px;
            color: rgb(0, 0, 0);
        }

        .card-content p {
            color: #626262;
        }

        .some-page-wrapper {
            margin: 5px;
        }

        .row {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            width: 100%;
        }

        .column {
            display: flex;
            flex-direction: column;
            flex-basis: 100%;
            flex: 1
        }

        .double-column {
            display: flex;
            flex-direction: column;
            flex-basis: 100%;
            flex: 2
        }

        .table {
            table-layout: auto;
            margin-top: 10px;
            font-size: 16px;
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid rgb(0, 0, 0);
        }

        .table tr td {
            padding: 0;
            margin: 0;
            text-align: left;
            padding: 6px;
            border: 1px solid rgb(0, 0, 0);
            width: 1px;
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
    </style>
</head>

<body>
    <div class="card">
        <div class='some-page-wrapper'>
            <div class='row'>
                <div class='column' style="margin-right: 160px!important">
                    <img alt="image" src="{{ asset('images/logos.png') }}" alt="" height="40pt">
                </div>
            </div>
        </div>
        <div class="card-content">
            <br>
            {!! $qr !!}
            <h2 style="padding: 0px; margin: 0px;">{{ $list_sku[0]->qrcode }}</h2>
            <hr>
            <p style="padding: 0px; margin: 0px;"><b>LOCATION : {{ $data[0]->location_code_from }}</b></p>
        </div>
        <table class="table">
            <!--<tr>
                <td>Principal Name</td>
                <td>
                   {{$list_sku[0]->principal}} 
                </td>
            </tr>
            <tr>
                <td>SKU NO</td>
                <td>
                    @foreach ($list_sku as $item)
                        {{ $item->product_code }},
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>SKU NAME</td>
                <td>
                    @foreach ($list_sku as $item)
                        {{ $item->product_name }},
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>MFG DATE & EXP DATE</td>
                <td>
                    @foreach ($list_sku as $item)
                        {{ $item->mfg_date . '&' . $item->exp_date }},
                    @endforeach
                </td>
            </tr>
             <tr>
                <td>QTY</td>
                <td>
                    @foreach ($list_sku as $item)
                        {{ $item->qtya }},
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>EXPIRED DATE</td>
                <td>
                @foreach ($list_sku as $item)
                    @if ($item->exp_date == null)
                    -
                    @else
                    {{ formatTanggalIndonesia2($item->exp_date) }}
                    @endif
                @endforeach
                </td>
            </tr> -->
            <tr>
                <td>UPDATED</td>
                <td>
                    @if ($data[0]->updated_at == null)
                        {{ formatTanggalIndonesia2($data[0]->created_at) }}
                    @else
                        {{ formatTanggalIndonesia2($data[0]->updated_at) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>BY</td>
                <td>
                    @if ($data[0]->updated_by == null)
                        {{ $data[0]->created_by }}
                    @else
                        {{ $data[0]->updated_by }}
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <tr style="border-style : hidden!important;">
                <td colspan="2">
                    <span style="font-size: 9px; margin-left: 21em;"><b>PT. MASAJI KARGOSENTRA TAMA<b></small>
                </td>
            </tr>
        </table>
    </div>


</body>
<script type="text/javascript">
    window.print();
</script>

</html>
