@extends('layouts.report')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
@endsection

@section('title')
  Pending Report
@endsection

@section('content')
    <table class="table-header-kerangka">
    <tr>
        <td>
            <table class="table-header">
                <tr>
                    <td>Principal Name</td>
                    <td>:</td>
                    <td>{{$principal_name}}</td>
                </tr>
                <tr>
                    <td>Period</td>
                    <td>:</td>
                    <td>{{\Carbon\Carbon::parse($date_from)->format("d/m/Y")}} - {{\Carbon\Carbon::parse($date_to)->format("d/m/Y")}}</td>
                </tr>
            </table>
        </td>
        <td>
        </td>
    </tr>
    </table>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th>Job No</th>
                <th>Job Date</th>
                <th>Description</th>
                <th>Customer Name</th>
                <th>Order No</th>
                <th>SKU No</th>
                <th>SKU Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $value)
                <tr>
                    <td class="center">{{$value->job_no}}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($value->job_date)->format('d/m/Y') }}</td>
                    <td class="left">{{$value->description}}</td>
                    <td class="left">{{$value->customer_name}}</td>
                    <td class="left">{{$value->order_no}}</td>
                    <td class="left">{{$value->product_code}}</td>
                    <td class="left">{{$value->product_name}}</td>
                    <td class="right">{{number_format($value->qty, 0, ",", ".")}}</td>                    
                </tr>
            @endforeach

            <tr>
                <td class="center" colspan="8">End Of Report</td>
            </tr>
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