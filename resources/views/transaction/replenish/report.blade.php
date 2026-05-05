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
                {!!$headerOne!!}
            </table>
            @endif
        </td>
        <td>
            @if (isset($headerTwo))
            <table class="table-header">
                {!!$headerTwo!!}
            </table>
            @endif
        </td>
        </tr>
    </table>
	<table class="table">
    <thead class="thead-dark">
        <tr>
            <th colspan="13">Product Name</th>
        </tr>
        <tr>
            <th>Batch No</th>
            <th>Mfg Date</th>
            <th>Exp Date</th>
            <th colspan="2">1st Unit</th>
            <th colspan="2">2nd Unit</th>
            <th colspan="2">3rd Unit</th>
            <th>Transfer</th>
            <th>Site</th>
            <th>Area</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>        
        @php
            $product_before = ""
        @endphp
        @foreach ($dataList as $item)
            @if ($product_before != $item->product_name )                    
                <tr>
                    <td colspan="13"><b>{{$item->product_name}}</b></td>
                </tr>
            @endif
            <tr>
                <td>{{$item->lot_no}}</td>
                <td>{{date("d-m-Y", strtotime($item->mfg_date))}}</td>
                <td>{{date("d-m-Y", strtotime($item->exp_date))}}</td>
                <td>{{$item->pqty}}</td>
                <td>{{$item->puom}}</td>
                <td>{{$item->mqty}}</td>
                <td>{{$item->muom}}</td>
                <td>{{$item->bqty}}</td>
                <td>{{$item->buom}}</td>
                <td>
                    @if ($item->job_type == 'TFRO')
                        Stock Move From
                    @else
                        Stock Move To
                    @endif
                </td>
                <td>
                    {{$item->site_name}}
                </td>
                <td>
                    {{$item->area_name}}
                </td>
                <td>
                    {{$item->location_code}}
                </td>
            </tr>
            @php
                $product_before = $item->product_name
            @endphp
        @endforeach
    </tbody>
  </table>
@endsection

@section('signature')
  @isset($signature)
  <table class="table">
    {!!$signature!!}
  </table>
  @endisset
@endsection