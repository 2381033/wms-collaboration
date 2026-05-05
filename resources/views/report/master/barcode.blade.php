<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('assets/css/barcode_small.css') }}">
</head>
<body>
    @php
        $pages = $list->chunk(3);        
    @endphp

    @foreach ($pages as $page)        
        <div class="page">
            <div class="container">
                <div class="row">
                    @foreach ($page as $item)    
                        <div class="column-loc col-30-loc">
                            <div class="row">
                                <div class="column-loc">
                                    <div class="col-100-barcode">
                                        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($item->location_code, 'QRCODE', 8, 8)}}"  alt="barcode" />
                                    </div>
                                    <div class="col-100-loc">
                                        <div class="locationText">
                                            {{$item->location_code}}
                                        </div>                   
                                    </div>                 
                                </div>
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>