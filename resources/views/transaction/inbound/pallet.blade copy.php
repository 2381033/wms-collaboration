<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/pallet.css') }}">
</head>

<body>
    <?php $page = 1; ?>
    <?php $product_before = ""; ?>
    <?php $lot_before = ""; ?>
    <?php $page = 1; ?>
    @foreach ($listData as $item)

    {{-- @if ($item->product_name != $product_before || $item->lot_no != $lot_before)        
        <?php $page = 1; ?>    
    @endif --}}
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{asset('images/logos.png')}}" />
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
                                Inbound Pallet Tag
                            </h3>

                            <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($item->serial_no, 'QRCODE')}}" width="100px" alt="barcode" />
                            <br>
                            <small>{{$item->serial_no}}</small>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table">
                                <tr>
                                    <td>
                                        Job Number
                                    </td>
                                    <td>
                                        {{$item->job_no}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Job Date
                                    </td>
                                    <td>
                                        {{\Carbon\Carbon::parse($item->job_date)->format('d-m-Y')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        SKU Number
                                    </td>
                                    <td>
                                        {{$item->product_code}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        SKU Name
                                    </td>
                                    <td>
                                        {{$item->product_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Reference No
                                    </td>
                                    <td>
                                        {{$item->document_ref}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        PO / DO No.
                                    </td>
                                    <td>
                                        {{$item->po_number}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Manufactur Date
                                    </td>
                                    <td>
                                        {{\Carbon\Carbon::parse($item->mfg_date)->format('d-m-Y')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Expiry Date
                                    </td>
                                    <td>
                                        {{\Carbon\Carbon::parse($item->exp_date)->format('d-m-Y')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Site Name
                                    </td>
                                    <td>
                                        {{$item->site_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Area Name
                                    </td>
                                    <td>
                                        {{$item->area_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Location Code
                                    </td>
                                    <td>
                                        <h3>{{$item->location_code}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Batch Number
                                    </td>
                                    <td>
                                        <h3>{{$item->lot_no}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        1st Quantity
                                    </td>
                                    <td>
                                        <h3>{{$item->pqty}} {{$item->puom}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2rd Quantity
                                    </td>
                                    <td>
                                        <h3>{{$item->mqty}} {{$item->muom}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        3nd Quantity
                                    </td>
                                    <td>
                                        <h3>{{$item->bqty}} {{$item->buom}}</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Status
                                    </td>
                                    <td>
                                        <h3>{{$item->product_status}}</h3>
                                    </td>
                                </tr>
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
                <tr>
                    <td>
                        <div class="right">Page {{$page}} / {{count($listData)}}</div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            Print Date : {{\Carbon\Carbon::now()->format('d/m/Y H:i:s')}}, Print By {{Auth::user()->username}}
        </div>
    </div>
    <?php $product_before = $item->product_name; ?>
    <?php $lot_before = $item->lot_no; ?>
    <?php $page++; ?>
    @endforeach
</body>

</html>