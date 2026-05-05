@extends('layouts.report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/' . $css . '.css') }}">
@endsection

@section('title')
    {{ $title }}
    <br>
    <small>Period {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($date_to)->format('d/m/Y') }}</small>
@endsection

@section('content')
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th rowspan="2">Job No</th>
                <th rowspan="2">Job Date</th>
                <th rowspan="2">SKU No</th>
                <th rowspan="2">SKU Name</th>
                <th rowspan="2">Batch</th>
                {{-- <th rowspan="2">Document Ref</th> --}}
                {{-- <th rowspan="2">Mfg Date</th> --}}
                <th rowspan="2">Exp Date</th>
                <th colspan="3">Location</th>
                <th rowspan="2">Status</th>
                @if ($principal->multi_level == 'Yes')
                    <th colspan="3">Transaction Quantity</th>
                    <th colspan="3">Balance Quantity</th>
                    <th colspan="3">Unit</th>
                @else
                    <th colspan="3">Quantity</th>
                @endif
                <th rowspan="2">Volume</th>
                <th rowspan="2">Weight</th>
            </tr>
            <tr>
                <th>Site</th>
                <th>Site Area</th>
                <th>Location</th>
                @if ($principal->multi_level == 'Yes')
                    <th>1st</th>
                    <th>2nd</th>
                    <th>3rd</th>
                    <th>1st</th>
                    <th>2nd</th>
                    <th>3rd</th>
                    <th>1st</th>
                    <th>2nd</th>
                    <th>3rd</th>
                @else
                    <th>Trx</th>
                    <th>Balance</th>
                    <th>Unit</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $first_line = true;
                $product_before = '';
                $batch_before = '';
            @endphp
            @foreach ($stock_list as $value)
                @if ($product_before !== $value->product_id || $batch_before != $value->lot_no)
                    @php
                        $stock = $stock_before
                            ->where('product_id', '=', $value->product_id)
                            ->where('lot_no', '=', $value->lot_no)
                            ->first();
                    @endphp

                    @if ($first_line == false)
                        <tr>
                            <td colspan="19"></td>
                        </tr>
                    @endif

                    @if (isset($stock))
                        @php
                            $qty_open = $stock->qty_received - $stock->qty_issue;
                        @endphp
                        <tr>
                            <td colspan="10" class="right"><b>Opening Balance brought forward as
                                    {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }}</b></td>
                            @if ($principal->multi_level == 'Yes')
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">{{ ($qty_open - ($qty_open % $value->uppp)) / $value->uppp }}</td>
                                <td class="right">
                                    {{ (($qty_open % $value->uppp) - (($qty_open % $value->uppp) % $value->muppp)) / $value->muppp }}
                                </td>
                                <td class="right">{{ ($qty_open % $value->uppp) % $value->muppp }}</td>
                                <td class="center">{{ $value->puom }}</td>
                                <td class="center">{{ $value->muom }}</td>
                                <td class="center">{{ $value->buom }}</td>
                            @else
                                <td class="right">0</td>
                                <td class="right">{{ ($qty_open - ($qty_open % $value->uppp)) / $value->uppp }}</td>
                                <td class="center">{{ $value->puom }}</td>
                            @endif
                            <td class="right">{{ $qty_open * $value->volume }}</td>
                            <td class="right">{{ $qty_open * $value->gross_weight }}</td>
                        </tr>
                    @else
                        @php
                            $qty_open = 0;
                        @endphp
                        <tr>
                            <td colspan="10" class="right"><b>Opening Balance brought forward as
                                    {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }}</b></td>
                            @if ($principal->multi_level == 'Yes')
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="center">{{ $value->puom }}</td>
                                <td class="center">{{ $value->muom }}</td>
                                <td class="center">{{ $value->buom }}</td>
                            @else
                                <td class="right">0</td>
                                <td class="right">0</td>
                                <td class="center">{{ $value->puom }}</td>
                            @endif
                            <td class="right">{{ $qty_open * $value->volume }}</td>
                            <td class="right">{{ $qty_open * $value->gross_weight }}</td>
                        </tr>
                    @endif
                @endif

                @php
                    if ($product_before != $value->product_id || $batch_before != $value->lot_no) {
                        $balance = $qty_open;
                    }

                    $kali = 1;
                    if ($value->job_type == 'EXP' || $value->job_type == 'TFRO' || $value->job_type == 'ADJ-') {
                        $kali = -1;
                    }

                    $qty = $kali * $value->qty;
                    $balance = $balance + $qty;

                    switch ($value->job_type) {
                        case 'IMP':
                            $job_desc = 'Inbound';
                            break;
                        case 'TFRI':
                            $job_desc = 'Transfer In';
                            break;
                        case 'ADJ+':
                            $job_desc = 'Adj. Plus';
                            break;
                        case 'EXP':
                            $job_desc = 'Outbound';
                            break;
                        case 'TFRO':
                            $job_desc = 'Transfer Out';
                            break;
                        case 'ADJ-':
                            $job_desc = 'Adj. Minus';
                            break;

                        default:
                            $job_desc = '';
                            break;
                    }
                @endphp

                <tr>
                    <td class="center">{{ $value->job_no }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($value->job_date)->format('d/m/Y') }}</td>
                    <td class="left">{{ $value->product_code }}</td>
                    <td class="left">{{ $value->product_name }}</td>
                    <td class="left">{{ $value->lot_no }}</td>
                    {{-- <td class="left">{{$value->document_ref}}</td> --}}
                    {{-- <td class="center">{{ $value->mfg_date !== null ? \Carbon\Carbon::parse($value->mfg_date)->format('d/m/Y') : ''}}</td> --}}
                    <td class="center">
                        {{ $value->mfg_date !== null ? \Carbon\Carbon::parse($value->exp_date)->format('d/m/Y') : '' }}
                    </td>
                    <td class="left">{{ $value->site_name }}</td>
                    <td class="left">{{ $value->area_name }}</td>
                    <td class="left">{{ $value->location_code }}</td>
                    <td class="left">{{ $job_desc }}</td>
                    @if ($principal->multi_level == 'Yes')
                        <td class="right">{{ number_format($value->pqty, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($value->mqty, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($value->bqty, 0, ',', '.') }}</td>
                        <td class="right">
                            {{ number_format(($balance - ($balance % $value->uppp)) / $value->uppp, 0, ',', '.') }}</td>
                        <td class="right">
                            {{ number_format((($balance % $value->uppp) - (($balance % $value->uppp) % $value->muppp)) / $value->muppp, 0, ',', '.') }}
                        </td>
                        <td class="right">{{ number_format(($balance % $value->uppp) % $value->muppp, 0, ',', '.') }}</td>
                        <td class="center">{{ $value->puom }}</td>
                        <td class="center">{{ $value->muom }}</td>
                        <td class="center">{{ $value->buom }}</td>
                    @else
                        <td class="right">{{ number_format($value->pqty, 0, ',', '.') }}</td>
                        <td class="right">
                            {{ number_format(($balance - ($balance % $value->uppp)) / $value->uppp, 0, ',', '.') }}</td>
                        <td class="center">{{ $value->puom }}</td>
                    @endif
                    <td class="right">{{ $value->qty * $value->volume }}</td>
                    <td class="right">{{ $value->qty * $value->gross_weight }}</td>
                </tr>

                @php
                    $product_before = $value->product_id;
                    $batch_before = $value->lot_no;

                    if ($first_line) {
                        $first_line = false;
                    }
                @endphp
            @endforeach
            <tr>
                <td class="center" colspan="21">End Of Report</td>
            </tr>
        </tbody>
    </table>
@endsection

@section('signature')
    @if (isset($signature))
        <table class="table">
            {!! $signature !!}
        </table>
    @endif
@endsection
