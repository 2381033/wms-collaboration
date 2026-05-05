@extends('layouts.blank')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">               
                <div class="card">
                    <div class="card-header">
                        Receipt Status Today
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td>Total Orders</td>
                                <td class="text-right" style="width: 50%;">{{number_format($inbound_daily->total_orders, 0)}}</td>
                            </tr>
                            <tr>
                                <td>Receipt</td>
                                <td class="text-right">{{number_format($inbound_daily->receipt, 0)}}</td>
                            </tr>
                            <tr>
                                <td>Put Away</td>
                                <td class="text-right">{{number_format($inbound_daily->putaway, 0)}}</td>
                            </tr>
                            <tr>
                                <td>Confirmed</td>
                                <td class="text-right">{{number_format($inbound_daily->confirmed, 0)}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">          
                <div class="card">
                    <div class="card-header">
                        Order Status Today
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td>Total Orders</td>
                                <td class="text-right" style="width: 50%;">{{number_format($outbound_daily->total_orders, 0)}}</td>
                            </tr>
                            <tr>
                                <td>Release Pick</td>
                                <td class="text-right">{{number_format($outbound_daily->release_pick, 0)}}</td>
                            </tr>
                            <tr>
                                <td>In Pick</td>
                                <td class="text-right">{{number_format($outbound_daily->in_pick, 0)}}</td>
                            </tr>
                            <tr>
                                <td>Picked</td>
                                <td class="text-right">{{number_format($outbound_daily->picked, 0)}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-12">                        
                <div class="card">
                    <div class="card-header">
                        Inbound (MTD) {{ $periode }}
                    </div>
                    <div class="card-body">
                        {!! $inboundMDTChart->container() !!}
                        <script src="{{ $inboundMDTChart->cdn() }}"></script>
                        {!! $inboundMDTChart->script() !!}
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-12">                        
                <div class="card">
                    <div class="card-header">
                        Outbound (MTD) {{ $periode }}
                    </div>
                    <div class="card-body">
                        {!! $outboundMDTChart->container() !!}
                        <script src="{{ $outboundMDTChart->cdn() }}"></script>
                        {!! $outboundMDTChart->script() !!}
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12">                        
                        <div class="card">
                            <div class="card-header">
                                Warehouse Status
                            </div>
                            <div class="card-body">
                                {!! $occupancyChart->container() !!}
                                <script src="{{ $occupancyChart->cdn() }}"></script>
                                {!! $occupancyChart->script() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12">                        
                        <div class="card">
                            <div class="card-header">
                                Issue Reason
                            </div>
                            <div class="card-body">
                                {!! $issueChart->container() !!}
                                <script src="{{ $issueChart->cdn() }}"></script>
                                {!! $issueChart->script() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-12">                        
                <div class="card">
                    <div class="card-header">
                        Warehouse Status
                    </div>
                    <div class="card-body">
                        {!! $occupancyMTDChart->container() !!}
                        <script src="{{ $occupancyMTDChart->cdn() }}"></script>
                        {!! $occupancyMTDChart->script() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>        
        @media print {
            .row {
                white-space: pre-line;
            }

            .row .col-lg-6 {
                white-space: pre-line;
            }
        }
    </style>
@endpush