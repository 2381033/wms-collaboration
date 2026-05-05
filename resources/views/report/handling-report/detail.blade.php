@extends('layouts.report')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/'. $css .'.css') }}">
@endsection

@section('title')
  {{ $title }}
@endsection

@section('content')
    <table class="table-header-kerangka">
        <tr>
            <td>
                <table class="table-header" width="100%">
                    <tr>
                        <td>Principal Name</td>
                        <td>:</td>
                        <td>{{$principal_name}}</td>
                        <td>Periode</td>
                        <td>:</td>
                        <td>{{$periode}}</td>
                    </tr>
                    <tr>
                        <td>Handling In</td>
                        <td>:</td>
                        <td>{{number_format($qty_inbound, 2, ".", ",")}}</td>
                        <td>Amount</td>
                        <td>:</td>
                        <td>{{number_format($amount_inbound, 2, ".", ",")}}</td>
                    </tr>
                    <tr>
                        <td>Handling Out</td>
                        <td>:</td>
                        <td>{{number_format($qty_outbound, 2, ".", ",")}}</td>
                        <td>Amount</td>
                        <td>:</td>
                        <td>{{number_format($amount_outbound, 2, ".", ",")}}</td>
                    </tr>
                    <tr>
                        <td>Storage</td>
                        <td>:</td>
                        <td>{{number_format($qty_storage, 2, ".", ",")}}</td>
                        <td>Amount</td>
                        <td>:</td>
                        <td>{{number_format($amount_storage, 2, ".", ",")}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
	<table class="table">
        <thead class="thead-dark">
            <tr>
                <th>Date</th>
                <th>Handling In</th>
                <th>Handling Out</th>
                <th>Storage</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <td class="center">{{$item['date']}}</td>
                    <td class="right">{{number_format($item['handling_in'], 2, ".", ",")}}</td>
                    <td class="right">{{number_format($item['handling_out'], 2, ".", ",")}}</td>
                    <td class="right">{{number_format($item['qty_storage'], 2, ".", ",")}}</td>
                </tr>
            @endforeach
            <tr>
                <td class="center" colspan="{{$columnCount}}">End Of Report</td>
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