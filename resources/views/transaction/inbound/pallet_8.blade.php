<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>

    <link rel="stylesheet" href="{{ asset('assets/css/pallet_8.css') }}">
</head>

<body>
    @php
        $pages = $list_data->chunk(8);
    @endphp
    @foreach ($pages as $page)
        <div class="page">
            <div class='wrapper'>
                @php
                    $i = 1;
                @endphp
                @foreach ($page as $item)
                    <div class="box a{{ $i }}">
                        <div class="container">
                            <div class="row">
                                <div class="column col-50">
                                    <img alt="image" src="{{ asset('images/logos.png') }}" alt=""
                                        height="40pt">
                                </div>
                                <div class="column col-50">
                                    <div class="center-barcode">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->serial_no, 'QRCODE') }}"
                                            alt="barcode" />
                                        <div class="title_serial">
                                            <small>{{ $item->serial_no }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-break center">

                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-100 center">
                                    <b>{{ $item->product_code }}</b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-100 center">
                                    {{ $item->product_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-break center">

                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30 center">
                                    {{ $item->site_name }}
                                </div>
                                <div class="column col-30 center">
                                    {{ $item->area_name }}
                                </div>
                                <div class="column col-30 center">
                                    <b>{{ $item->location_code }}</b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-break center">
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30 center">
                                    {{ $item->pqty }} {{ $item->puom }}
                                </div>
                                <div class="column col-30 center">
                                    {{ $item->mqty }} {{ $item->muom }}
                                </div>
                                <div class="column col-30 center">
                                    {{ $item->bqty }} {{ $item->buom }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $i++;
                    @endphp
                @endforeach
            </div>
        </div>
    @endforeach
</body>

</html>
