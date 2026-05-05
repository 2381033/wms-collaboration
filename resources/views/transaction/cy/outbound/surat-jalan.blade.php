<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Report</title>

    <link rel="stylesheet" href="{{ asset('assets/css/pallet_one.css') }}">
</head>

<body>
    <div class="page">
        <div class="container">
            <div class="row">
                <div class="column col-30">
                    <img alt="image" src="{{ asset('images/logos.png') }}" alt="" height="50pt">
                </div>
            </div>
            <div class="row">
                <div class="column col-100 center">
                    <div class="title">Surat Jalan</div>
                </div>
            </div>
            <div class="row">
                <div class="column col-break center">

                </div>
            </div>
            <div class="row">
                <div class="column col-100">
                    <table class="table" style="width:100%;">
                        <tr>
                            <td>
                                Principal Name
                            </td>
                            <td>
                                {{ $view->forwarder_name }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Container No
                            </td>
                            <td>
                                {{ $view->container_no }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Container Size
                            </td>
                            <td>
                                {{ $view->size_name }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Container Type
                            </td>
                            <td>
                                {{ $view->type_name }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Container Status
                            </td>
                            <td>
                                {{ $view->container_status }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Vehicle No
                            </td>
                            <td>
                                {{ $view->vehicle_no }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Driver Name
                            </td>
                            <td>
                                {{ $view->driver_name }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Dispatch Date
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($view->dispatch_date)->format('d-m-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Inspected Date
                            </td>
                            <td>
                                @isset($view->inspected_date)
                                    {{ \Carbon\Carbon::parse($view->inspected_date)->format('d-m-Y H:i:s') }}
                                @endisset
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="column col-break center">

                </div>
            </div>
            <div class="row">
                <div class="column col-100">
                    <table class="table" style="width:100%;">
                        <tr>
                            <td class="center" style="width: 33%">
                                Receiver
                            </td>
                            <td class="center" style="width: 33%">
                                Driver Name
                            </td>
                            <td class="center" style="width: 33%">
                                Supervisor
                            </td>
                        </tr>
                        <tr>
                            <td style="height:70px">&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
