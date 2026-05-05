@extends('layouts.report')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
@endsection

@section('title')
    PT Masaji Kargosentra Tama Semarang
    <br>
    <small>Warehousing And Distribution Center
    <br>
        <small>Jl. Arteri Yos Sudarso, Kawasan Industri Cipta Kav 10. Semarang 50175 Indonesia</small>
    </small>
@endsection

@section('content')
    <table class="table-header-kerangka">
    <tr>
        <td>
            <table class="table-header">
                <tr>
                    <td>Customer Name</td>
                    <td>:</td>
                    <td>{{$header->forwarder_name}}</td>
                </tr>
                <tr>
                    <td>Invoice No</td>
                    <td>:</td>
                    <td>{{$header->job_no}}</td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td>:</td>
                    <td>{{\Carbon\Carbon::parse($header->job_date)->format("d-m-Y")}}</td>
                </tr>
            </table>
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
                <th>No</th>
                <th>Scope Of Work</th>
                <th>Container</th>
                <th>Size</th>
                <th>Date In</th>
                <th>Date Out</th>
                <th>Days</th>
                <th>Lolo Amount</th>
                <th>Storage Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $row = 1;
                $lolo_amount = 0;
                $storage_amount = 0;
            @endphp
            @foreach ($detail as $item)
                <tr>
                    <td class="center" style="width: 5%;">{{$row}}</td>
                    <td style="width: 20%;">CY Handling</td>
                    <td class="center" style="width: 15%;">{{$item->container_no}}</td>
                    <td class="center" style="width: 15%;">{{$item->size_name}}</td>
                    <td class="center" style="width: 15%;">{{\Carbon\Carbon::parse($item->received_date)->format("d-m-Y")}}</td>
                    <td class="center" style="width: 15%;">{{\Carbon\Carbon::parse($item->dispatch_date)->format("d-m-Y")}}</td>    
                    <td class="center" style="width: 15%;">{{$item->leadtime}}</td>                
                    <td class="right">{{number_format($item->lolo_amount, 2, ".", ",")}}</td>
                    <td class="right">{{number_format($item->storage_amount, 2, ".", ",")}}</td>
                </tr>

                @php
                    $row++;
                    $lolo_amount = $lolo_amount + $item->lolo_amount;
                    $storage_amount = $storage_amount + $item->storage_amount;
                @endphp
            @endforeach
            <tr>
                <td colspan="9"></td>
            </tr>
            <tr>
                <td class="right" colspan="7"><b>Sub Total</b></td>
                <td class="right">{{number_format($lolo_amount, 2, ".", ",")}}</td>
                <td class="right">{{number_format($storage_amount, 2, ".", ",")}}</td>
            </tr>
            <tr>
                <td colspan="9"></td>
            </tr>
            @php
                $row = 1;
            @endphp
            <tr>
                <td class="right" colspan="8"><b>Total</b></td>
                <td class="right">{{number_format($lolo_amount + $storage_amount, 2, ".", ",")}}</td>
            </tr>
            <tr>
                <td class="right" colspan="8"><b>Administration</b></td>
                <td class="right">{{number_format($header->adm_amount, 2, ".", ",")}}</td>
            </tr>
            <tr>
                <td class="right" colspan="8"><b>Vat (10%)</b></td>
                <td class="right">{{number_format($header->tax_amount, 2, ".", ",")}}</td>
            </tr>
            <tr>
                <td class="right" colspan="8"><b>Grand Total</b></td>
                <td class="right">{{number_format($header->invoice_amount, 2, ".", ",")}}</td>
            </tr>
        </tbody>
    </table>
@endsection

@section('signature')
@endsection