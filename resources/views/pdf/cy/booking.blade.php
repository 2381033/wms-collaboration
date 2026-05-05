<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="column col-logo">
                <img alt="image" class="logo" src="{{ asset('images/logos.png') }}" />
            </div>
        </div>
        <div class="row">
            <div class="column col-center-padding">
                <h2>
                    PT Masaji Kargosentra Tama Semarang
                    <br><small>Booking Order</small>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="column col-center-padding">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($header->booking_no, 'C39') }}"
                    alt="barcode" />
            </div>
        </div>
        <div class="row">
            <div class="column col-center-padding">
                <table class="table">
                    <tr>
                        <td>Booking No</td>
                        <td class="center">:</td>
                        <td>{{ $header->booking_no }}</td>
                    </tr>
                    <tr>
                        <td>Booking Date</td>
                        <td class="center">:</td>
                        <td>{{ \Carbon\Carbon::parse($header->booking_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Company Name</td>
                        <td class="center">:</td>
                        <td>{{ $header->forwarder_name }}</td>
                    </tr>
                    <tr>
                        <td>Reference No</td>
                        <td class="center">:</td>
                        <td>{{ $header->reference_no }}</td>
                    </tr>
                    <tr>
                        <td>Vehicle No</td>
                        <td class="center">:</td>
                        <td>{{ $header->vehicle_no }}</td>
                    </tr>
                    <tr>
                        <td>Driver Name</td>
                        <td class="center">:</td>
                        <td>{{ $header->driver_name }}</td>
                    </tr>
                    <tr>
                        <td>Container Status</td>
                        <td class="center">:</td>
                        <td>{{ $header->container_status }}</td>
                    </tr>
                    <tr>
                        <td>Container Size</td>
                        <td class="center">:</td>
                        <td>{{ $header->size_name }}</td>
                    </tr>
                    <tr>
                        <td>Container Type</td>
                        <td class="center">:</td>
                        <td>{{ $header->type_name }}</td>
                    </tr>
                    <tr>
                        <td>Container No</td>
                        <td class="center">:</td>
                        <td>{{ $header->container_no }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
