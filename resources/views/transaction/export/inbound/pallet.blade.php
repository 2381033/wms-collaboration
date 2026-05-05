<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>

    <link rel="stylesheet" href="{{ asset('assets/css/barcode_small.css') }}">
</head>

<body>
    @php
        $pages = $list_data->chunk(4);
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
                                <div class="column col-30">
                                    Shipper Name
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $view->shipper_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30">
                                    PO Number
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $view->po_number }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30">
                                    Buyer
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $view->consignee_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30">
                                    PEB Number
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $view->peb_no }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30">
                                    Destination
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $view->destination }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-30">
                                    Quantity
                                </div>
                                <div class="column col-10">
                                    :
                                </div>
                                <div class="column col-50 padLeft">
                                    {{ $item->quantity }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-100 center">
                                    Pallet {{ $item->pallet_id }} of {{ $view->total_pallet }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="column col-100 center">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->serial_no == null ? '' : $item->serial_no, 'QRCODE') }}"
                                        width="80px" alt="barcode" />
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
