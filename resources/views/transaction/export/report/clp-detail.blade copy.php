<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>

<body>
    @foreach ($container_list as $item)
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
                                Outbound Checker Report
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        &nbsp;Container No : {{$item->container_no}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Job No</th>
                                        <th>Job Date</th>
                                        <th>Forwarder Name</th>
                                        <th>Container No</th>
                                        <th>Shipper Name</th>
                                        <th>Consignee Name</th>
                                        <th>PO Number</th>
                                        <th>PEB No</th>
                                        <th>Destination</th>
                                        <th>Pallet ID</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $before = "";
                                    $data = "";
                                    @endphp
                                    @foreach ($detail_list->where("container_no", $item->container_no) as $detail)
                                    <tr>
                                        <td>{{$detail->job_no}}</td>
                                        <td>{{\Carbon\Carbon::parse($detail->job_date)->format("d-m-Y")}}</td>
                                        <td>{{$detail->forwarder_name}}</td>
                                        <td>{{$detail->container_no}}</td>
                                        <td>{{$detail->shipper_name}}</td>
                                        <td>{{$detail->consignee_name}}</td>
                                        <td>{{$detail->po_number}}</td>
                                        <td>{{$detail->peb_no}}</td>
                                        <td>{{$detail->destination}}</td>
                                        <td>{{$detail->serial_no}}</td>
                                        <td class="right">{{$detail->quantity}}</td>
                                        <td>
                                            {{$detail->action}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
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
            </tfoot>
        </table>
        <div class="footer">
            Print Date : {{\Carbon\Carbon::now()->format('d/m/Y H:i:s')}}, Print By {{Auth::user()->username}}
        </div>
    </div>
    @endforeach
</body>

</html>