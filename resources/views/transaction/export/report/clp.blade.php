<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
</head>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{ asset('images/logos.png') }}" />
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
                        <div class="content">
                            <table class="table-header-kerangka">
                                <tr>
                                    <td>
                                        <table class="table-header">
                                            <tr>
                                                <td>Forwarder Name</td>
                                                <td>:</td>
                                                <td>{{ $job->forwarder_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job No</td>
                                                <td>:</td>
                                                <td>{{ $job->job_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Date</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($job->job_date)->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Surveyor Name</td>
                                                <td>:</td>
                                                <td>{{ $job->surveyor_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Container No</td>
                                                <td>:</td>
                                                <td>{{ $job->container_no }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header">
                                            <tr>
                                                <td>Destination</td>
                                                <td>:</td>
                                                <td>{{ $job->destination }}</td>
                                            </tr>
                                            <tr>
                                                <td>Qty Cargo</td>
                                                <td>:</td>
                                                <td>{{ $job->qty_cargo }}</td>
                                            </tr>
                                            <tr>
                                                <td>CBM</td>
                                                <td>:</td>
                                                <td>{{ $job->cbm }}</td>
                                            </tr>
                                            <tr>
                                                <td>Weight</td>
                                                <td>:</td>
                                                <td>{{ $job->weight }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Pallet</td>
                                                <td>:</td>
                                                <td>{{ $job->total_pallet }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Pallet ID</th>
                                        <th>Shipper Name</th>
                                        <th>Consignee Name</th>
                                        <th>PO Number</th>
                                        <th>PEB No</th>
                                        <th>Qty Cargo</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order_list as $detail)
                                        <tr>
                                            <td class="center">{{ $detail->serial_no }}</td>
                                            <td>{{ $detail->shipper_name }}</td>
                                            <td>{{ $detail->consignee_name }}</td>
                                            <td>{{ $detail->po_number }}</td>
                                            <td>{{ $detail->peb_no }}</td>
                                            <td class="right">{{ $detail->quantity }}</td>
                                            <td class="center">
                                                <input type="checkbox">
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td class="center" colspan="7">                                                                                            
                                                @php
                                                    $list = $detail_list->where("order_id", $detail->id);
                                                    $row = 1;
                                                @endphp                                            
                                                
                                                <table class="table" style="width:30%;">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Pallet ID</th>
                                                            <th>Quantity</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($list as $item)    
                                                            <tr>
                                                                <td class="center">{{$item->serial_no}}</td>
                                                                <td class="center">{{$item->quantity}}</td>
                                                                <td class="center">
                                                                    <input type="checkbox">    
                                                                </td>
                                                            </tr>

                                                            @php
                                                                $row++;
                                                            @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr> --}}
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

        <div class="signature">
            <table class="table">
                <tr>
                    <td class="sign">Checker Name:</td>
                    <td class="sign">Surveyor Name:</td>
                    <td class="sign">Supervisor Name:</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>Date:</td>
                    <td>Date:</td>
                </tr>
                <tr>
                    <td>Signature:</td>
                    <td>Signature:</td>
                    <td>Signature:</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
</body>

</html>
