@extends('layouts.report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <table class="table-header-kerangka">
        <tr>
            <td>
                @if (isset($headerOne))
                    <table class="table-header">
                        {!! $headerOne !!}
                    </table>
                @endif
            </td>
        </tr>
    </table>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th class="center" rowspan="2">No.</th>
                <th class="center" rowspan="2">PO / DO No.</th>
                <th class="center" rowspan="2">SKU No.</th>
                <th class="center" rowspan="2">SKU Name</th>
                <th class="center" rowspan="2">Batch No.</th>
                <th class="center" rowspan="2">Conversion Qty</th>
                <th class="center" rowspan="2">Exp Date</th>
                <th class="center" rowspan="2">Site</th>
                <th class="center" rowspan="2">Area</th>
                <th class="center" rowspan="2">Location</th>
                <th class="center" colspan="6">Quantity</th>
                <th class="center" rowspan="2"><b>Quantum</b></th>
            </tr>
            <tr>
                <th>1st Qty</th>
                <th>1st Unit</th>
                <th>2nd Qty</th>
                <th>2nd Unit</th>
                <th>3rd Qty</th>
                <th>3rd Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">{{ $item->po_number }}</td>
                    <td class="center">{{ $item->product_code }}</td>
                    <td class="center">{{ $item->product_name }}</td>
                    <td class="center">{{ $item->lot_no }}</td>
                    <td class="center">
                        {{ $item->muppp }}
                    </td>
                    <td class="center">
                        {{ $item->exp_date != null ? \Carbon\Carbon::parse($item->mfg_date)->format('d-M-Y') : '-' }}
                    </td>
                    <td class="center">{{ $item->site_name }}</td>
                    <td class="center">{{ $item->area_name }}</td>
                    <td class="center">{{ $item->location_code }}</td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="center">{{ $item->puom }}</td>
                    <td class="center">{{ $item->mqty }}</td>
                    <td class="center">{{ $item->muom }}</td>
                    <td class="center">{{ $item->bqty }}</td>
                    <td class="center">{{ $item->buom }}</td>
                    <td class="center">
                        {{ $item->qty * $item->muppp + $item->mqty . ' ' . $item->muom }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="center" colspan="10"><b>SUMMARY</b></td>
                <td class="center" colspan="2">
                    <b>{{ $data->sum('qty') }} {{ count($data) > 0 ? $data[0]->puom : ' ' }}</b>
                </td>
                <td class="center" colspan="2">
                    <b>{{ $data->sum('mqty') }}</b>
                </td>
                <td class="center" colspan="2"> <b>{{ $data->sum('bqty') }}
                    </b>
                </td>
                <td class="center" colspan="2"><b>{{ $quantum }}
                    </b>
                </td>
                {{-- <td class="center" colspan="2">total quantum</td> --}}
            </tr>
            <tr>
                <td class="center" colspan="17">End Of Report</td>
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
