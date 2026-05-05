@extends('layouts.report')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
@endsection

@section('title')
  Dispatch Delivery
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
    <thead>
        <tr>
            <th>No</th>
            <th>Job No</th>
            <th>Job Date</th>
            <th>Description</th>     
            <th>Vehicle No</th>         
            <th>Transporter Name</th>                             
            <th>Driver Name</th>
            <th>Vendor Code</th>
            <th>Vendor Name</th>
            <th>SKU Code</th>
            <th>SKU Name</th>
            <th>Qty</th>
            <th>Unit</th>
        </tr>
    </thead>
    <tbody>
        @php
            $row = 1;
        @endphp
        @foreach ($list as $item)
            @php
                $i = 1;
            @endphp
            <tr>
                <td>{{$row}}</td>
                <td>{{$item->job_no}}</td>
                <td>{{\Carbon\Carbon::parse($item->job_date)->format('d-m-Y')}}</td>
                <td>{{$item->description}}</td>
                <td>{{$item->vehicle_no}}</td>
                <td>{{$item->transporter_name}}</td>
                <td>{{$item->driver_name}}</td>        
                <td>{{$item->manufactur_code}}</td>
                <td>{{$item->manufactur_name}}</td>
                <td>{{$item->product_code}}</td>
                <td>{{$item->product_name}}</td>
                <td>{{$item->qty}}</td>
                <td>{{$item->puom}}</td>
            </tr>

            @php
                $row++;
            @endphp
        @endforeach
    </tbody>
</table>
@endsection

@section('signature')
  @if (isset($signature))
  <table class="table">
    {!!$signature!!}
  </table>
  @endif
@endsection