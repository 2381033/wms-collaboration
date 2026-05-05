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
                <th class="center" rowspan="2">SKU No</th>
                <th class="center" rowspan="2">Batch No</th>
                <th class="center" rowspan="2">Mfg Date</th>
                <th class="center" rowspan="2">Exp Date</th>
                <th class="center" colspan="3">Quantity</th>
                <th class="center" colspan="3" style="background-color: antiquewhite">From</th>
                <th class="center" colspan="3" style="background-color: aquamarine">To</th>
            </tr>
            <tr>
                <th class="center">1st Unit</th>
                @if ($job->multi_level == 'Yes')
                    <th class="center">2nd Unit</th>
                    <th class="center">3rd Unit</th>
                @endif
                <th class="center">Site</th>
                <th class="center">Area</th>
                <th class="center">Location</th>
                <th class="center">Site</th>
                <th class="center">Area</th>
                <th class="center">Location</th>
            </tr>
            <tr>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataList->where('job_type', 'TFRO') as $item)
                <tr>
                    <td class="center">{{ $item->product_code }}</td>
                    <td class="center">{{ $item->lot_no }}</td>
                    <td class="center">{{ $item->mfg_date != null ? date('d-m-Y', strtotime($item->mfg_date)) : '-' }}</td>
                    <td class="center">{{ $item->mfg_date != null ? date('d-m-Y', strtotime($item->exp_date)) : '-' }}</td>
                    <td class="center">{{ $item->pqty . ' ' . $item->puom }}</td>
                    @if ($job->multi_level == 'Yes')
                        <td class="center">{{ $item->mqty . ' ' . $item->muom }}</td>
                        <td class="center">{{ $item->bqty . ' ' . $item->buom }}</td>
                    @endif
                    <td>{{ $item->site_name }}</td>
                    <td>{{ $item->area_name }}</td>
                    <td>{{ $item->location_code }}</td>
                    <td class="center">
                        {{ $dataList->where('job_type', 'TFRI')->where('line_id', $item->line_id)->first()->site_name ?? '-' }}
                    </td>
                    <td class="center">
                        {{ $dataList->where('job_type', 'TFRI')->where('line_id', $item->line_id)->first()->area_name ?? '-' }}
                    </td>
                    <td class="center">
                        {{ $dataList->where('job_type', 'TFRI')->where('line_id', $item->line_id)->first()->location_code ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('signature')
    @isset($signature)
        <table class="table">
            {!! $signature !!}
        </table>
    @endisset
@endsection
