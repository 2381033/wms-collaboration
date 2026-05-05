<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>

<body>
    @foreach ($job_view as $item)
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
                                    Inbound Checker Report ( Goods Receipt )
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
                                                    <td>Principal Name</td>
                                                    <td>:</td>
                                                    <td>{{ $item->principal_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Job No</td>
                                                    <td>:</td>
                                                    <td>{{ $item->job_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Job Date</td>
                                                    <td>:</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->job_date)->format('d/m/Y') }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table-header">
                                                <tr>
                                                    <td>Vehicle No</td>
                                                    <td>:</td>
                                                    <td>{{ $item->vehicle_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Unloading Start</td>
                                                    <td>:</td>
                                                    <td>_____________________</td>
                                                </tr>
                                                <tr>
                                                    <td>Unloading Finish</td>
                                                    <td>:</td>
                                                    <td>_____________________</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table class="table">
                                    <thead class="thead-dark">
                                        @if ($item->multi_level == 'Yes')
                                            <tr>
                                                {{-- <th rowspan="2">Reference No</th> --}}
                                                <th rowspan="2">PO / DO No</th>
                                                <th rowspan="2">SKU No.</th>
                                                <th rowspan="2">SKU Name</th>
                                                <th rowspan="2">Batch No</th>
                                                <th rowspan="2">Mfg Date</th>
                                                <th rowspan="2">Exp Date</th>
                                                <th rowspan="2">Volume</th>
                                                <th rowspan="2">Gross Weight</th>
                                                <th colspan="6">Expected Quantity</th>
                                                <th colspan="6">Actual Quantity</th>
                                            </tr>
                                            <tr>
                                                <th>1st</th>
                                                <th>Unit</th>
                                                <th>2nd</th>
                                                <th>Unit</th>
                                                <th>3rd</th>
                                                <th>Unit</th>
                                                <th>1st</th>
                                                <th>Unit</th>
                                                <th>2nd</th>
                                                <th>Unit</th>
                                                <th>3rd</th>
                                                <th>Unit</th>
                                            </tr>
                                        @else
                                            <tr>
                                                {{-- <th rowspan="2">Reference No</th> --}}
                                                <th rowspan="2">PO / DO No</th>
                                                <th rowspan="2">SKU No.</th>
                                                <th rowspan="2">SKU Name</th>
                                                <th rowspan="2">Batch No</th>
                                                <th rowspan="2">Mfg Date</th>
                                                <th rowspan="2">Exp Date</th>
                                                <th rowspan="2">Volume</th>
                                                <th rowspan="2">Gross Weight</th>
                                                <th colspan="2">Expected Quantity</th>
                                                <th colspan="2">Actual Quantity</th>
                                            </tr>
                                            <tr>
                                                <th>1st</th>
                                                <th>Unit</th>
                                                <th>1st</th>
                                                <th>Unit</th>
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody>
                                        @foreach ($detail_list as $detail)
                                            @if ($item->multi_level == 'Yes')
                                                @if ($item->vehicle_no == $detail->vehicle_no)
                                                    <tr>
                                                        <td>{{ $detail->po_number }}</td>
                                                        <td>{{ $detail->product_code }}</td>
                                                        <td>{{ $detail->product_name }}</td>
                                                        <td>{{ $detail->lot_no }}</td>
                                                        <td class="center">
                                                            @isset($detail->mfg_date)
                                                                {{ \Carbon\Carbon::parse($detail->mfg_date)->format('d/m/Y') }}
                                                            @endisset
                                                        </td>
                                                        <td class="center">
                                                            @isset($detail->mfg_date)
                                                                {{ \Carbon\Carbon::parse($detail->exp_date)->format('d/m/Y') }}
                                                            @endisset
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->pqty * $detail->volume, 3, ',', '.') }}
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->gross_weight, 3, ',', '.') }}
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->pqty, 0, ',', '.') }}</td>
                                                        <td class="center">{{ $detail->puom }}</td>
                                                        <td class="right">
                                                            {{ number_format($detail->mqty, 0, ',', '.') }}</td>
                                                        <td class="center">{{ $detail->muom }}</td>
                                                        <td class="right">
                                                            {{ number_format($detail->bqty, 0, ',', '.') }}</td>
                                                        <td class="center">{{ $detail->buom }}</td>
                                                        <td class="center">&nbsp;&nbsp;</td>
                                                        <td class="center">{{ $detail->puom }}</td>
                                                        <td class="center">&nbsp;&nbsp;</td>
                                                        <td class="center">{{ $detail->muom }}</td>
                                                        <td class="center">&nbsp;&nbsp;</td>
                                                        <td class="center">{{ $detail->buom }}</td>
                                                    </tr>
                                                @endif
                                            @else
                                                @if ($item->vehicle_no == $detail->vehicle_no)
                                                    <tr>
                                                        <td>{{ $detail->po_number }}</td>
                                                        <td>{{ $detail->product_code }}</td>
                                                        <td>{{ $detail->product_name }}</td>
                                                        <td>{{ $detail->lot_no }}</td>
                                                        <td class="center">
                                                            @isset($detail->mfg_date)
                                                                {{ \Carbon\Carbon::parse($detail->mfg_date)->format('d/m/Y') }}
                                                            @endisset
                                                        </td>
                                                        <td class="center">
                                                            @isset($detail->mfg_date)
                                                                {{ \Carbon\Carbon::parse($detail->exp_date)->format('d/m/Y') }}
                                                            @endisset
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->pqty * $detail->volume, 3, ',', '.') }}
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->gross_weight, 3, ',', '.') }}
                                                        </td>
                                                        <td class="right">
                                                            {{ number_format($detail->pqty, 0, ',', '.') }}</td>
                                                        <td class="center">{{ $detail->puom }}</td>
                                                        <td class="center">&nbsp;&nbsp;</td>
                                                        <td class="center">{{ $detail->puom }}</td>
                                                    </tr>
                                                @endif
                                            @endif
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
                        <td class="sign">Driver Name:</td>
                        <td class="sign">Security:</td>
                        <td class="sign">Checker Name:</td>
                        <td class="sign">Supervisor Naacame:</td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td>Date:</td>
                        <td>Date:</td>
                        <td>Date:</td>
                    </tr>
                    <tr>
                        <td>Signature:</td>
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
    @endforeach
</body>

</html>
