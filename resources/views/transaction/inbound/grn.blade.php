<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
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
                                Goods Receipt Report (Detail)
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
                                                <td>{{ $job_view->principal_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job No</td>
                                                <td>:</td>
                                                <td>{{ $job_view->job_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Date</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($job_view->job_date)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header">
                                            <tr>
                                                <td>Unloading Start</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($job_view->unloading_start)->format('d/m/Y H:i:s') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Unloading Finish</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($job_view->unloading_finish)->format('d/m/Y H:i:s') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    @if ($job_view->multi_level == 'Yes')
                                        <tr>
                                            {{-- <th rowspan="2">Reference No</th> --}}
                                            <th rowspan="2">PO / DO No</th>
                                            <th rowspan="2">SKU No.</th>
                                            <th rowspan="2">SKU Name</th>
                                            <th rowspan="2">Batch No</th>
                                            <th rowspan="2">Mfg Date</th>
                                            <th rowspan="2">Exp Date</th>
                                            <th rowspan="2">Volume <p>(CBM)</p>
                                            </th>
                                            <th rowspan="2">Gross Weight<p> (KG)</p>
                                            </th>
                                            <th colspan="6">Quantity</th>
                                            <th rowspan="2">Status</th>
                                        </tr>
                                        <tr>
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
                                            <th rowspan="2">Volume <p>(CBM)</p>
                                            </th>
                                            <th rowspan="2">Gross Weight<p> (KG)</p>
                                            </th>
                                            <th colspan="2">Quantity</th>
                                            <th rowspan="2">Status</th>
                                        </tr>
                                        <tr>
                                            <th>1st</th>
                                            <th>Unit</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @foreach ($detail_list as $detail)
                                        <tr>
                                            {{-- <td rowspan="3">{{ $detail->master_detail->document_ref }}</td> --}}
                                            <td rowspan="3">{{ $detail->master_detail->po_number }}</td>
                                            <td rowspan="3">{{ $detail->master_product->product_code }}</td>
                                            <td rowspan="3">{{ $detail->master_product->product_name }}</td>
                                            <td rowspan="3">{{ $detail->master_detail->lot_no }}</td>
                                            <td rowspan="3" class="center">
                                                @isset($detail->master_detail->mfg_date)
                                                    {{ \Carbon\Carbon::parse($detail->master_detail->mfg_date)->format('d/m/Y') }}
                                                @endisset
                                            </td>
                                            <td rowspan="3" class="center">
                                                @isset($detail->master_detail->mfg_date)
                                                    {{ \Carbon\Carbon::parse($detail->master_detail->exp_date)->format('d/m/Y') }}
                                                @endisset
                                            </td>
                                            {{-- @php
                                                dd($detail->master_product->volume, $detail->master_product->puom);
                                            @endphp --}}
                                            <td rowspan="3" class="right">
                                                {{ number_format($detail->qty_per_pallet * $detail->master_product->volume, 0, ',', '.') }}
                                            </td>
                                            <td rowspan="3" class="right">
                                                {{ number_format($detail->master_product->gross_weight, 0, ',', '.') }}
                                            </td>
                                            {{-- <td class="right">{{number_format($detail->qty_per_pallet, 0, ",", ".")}}</td>
                                            <td class="center">{{$detail->master_product->puom}}</td>
                                            
                                            @if ($job_view->multi_level == 'Yes')   
                                                <td class="right">{{number_format($detail->master_detail->mqty, 0, ",", ".")}}</td>
                                                <td class="center">{{$detail->master_detail->muom}}</td>
                                                <td class="right">{{number_format($detail->master_detail->bqty, 0, ",", ".")}}</td>
                                                <td class="center">{{$detail->master_detail->buom}}</td>
                                            @endif
                                            <td class="center">Expected</td> --}}
                                        </tr>
                                        <tr>
                                            <td class="right">
                                                {{ number_format($detail->qty_per_pallet, 0, ',', '.') }}</td>
                                            <td class="center">{{ $detail->master_product->puom }}</td>
                                            @if ($job_view->multi_level == 'Yes')
                                                <td class="right">
                                                    {{ number_format($detail->master_detail->mqty, 0, ',', '.') }}</td>
                                                <td class="center">{{ $detail->master_detail->muom }}</td>
                                                <td class="right">
                                                    {{ number_format($detail->master_detail->bqty, 0, ',', '.') }}</td>
                                                <td class="center">{{ $detail->master_detail->buom }}</td>
                                            @endif
                                            <td class="center">Goods</td>
                                        </tr>
                                        <tr>
                                            <td class="right">
                                                {{ number_format($detail->master_detail->discrepancy_pqty, 0, ',', '.') }}
                                            </td>
                                            <td class="center">{{ $detail->master_product->puom }}</td>
                                            @if ($job_view->multi_level == 'Yes')
                                                <td class="right">
                                                    {{ number_format($detail->master_detail->discrepancy_mqty, 0, ',', '.') }}
                                                </td>
                                                <td class="center">{{ $detail->master_detail->muom }}</td>
                                                <td class="right">
                                                    {{ number_format($detail->master_detail->discrepancy_bqty, 0, ',', '.') }}
                                                </td>
                                                <td class="center">{{ $detail->master_detail->buom }}</td>
                                            @endif
                                            <td class="center">Damage</td>
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
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
</body>

</html>
