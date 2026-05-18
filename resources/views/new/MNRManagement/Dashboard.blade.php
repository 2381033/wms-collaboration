@extends('layouts.new.base')
@section('title', 'MKT - Dashboard')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        
        .message {
            transition-duration: 0.7ms;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card card-custom card-stretch gutter-b">
                                <div class="card-body d-flex p-0">
                                    <div class="flex-grow-1 p-8 card-rounded bgi-no-repeat d-flex align-items-center"
                                        style="background-color: #FFF4DE; background-position: left bottom; background-size: auto 100%; background-image: url({{ asset('assets/new/media/svg/humans/custom-2.svg') }})">
                                        <div class="row">
                                            <div class="col-12 col-xl-5"></div>
                                            <div class="col-12 col-xl-7">
                                                <h4 class="text-danger font-weight-bolder">Master Data</h4>
                                                <p class="text-dark-50 my-5 font-size-xl font-weight-bold">Click for setup
                                                    vendors,locations,spareparts,equipment</p>
                                                <div class="d-flex flex-wrap" style="gap: 10px;">
                                                    <a href="{{ route('vendors.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Vendors</a>
                                                    <a href="{{ route('locations.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Locations</a>
                                                    <a href="{{ route('spareparts.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Spareparts</a>
                                                    <a href="{{ route('equipment.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Equipment</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card card-custom card-stretch gutter-b">
                                <div class="card-body d-flex p-0">
                                    <div class="flex-grow-1 bg-danger p-8 card-rounded flex-grow-1 bgi-no-repeat"
                                        style="background-position: calc(100% + 0.5rem) bottom; background-size: auto 70%; background-image: url({{ asset('assets/new/media/svg/humans/custom-3.svg') }})">
                                        <h4 class="text-inverse-danger mt-2 font-weight-bolder">Transaction</h4>
                                        <p class="text-inverse-danger my-6">Click for setup transaction in, transaction out, transaction remark</p>
                                        <a href="#" class="btn btn-warning font-weight-bold py-2 px-6">Transaction In</a>
                                        <a href="#" class="btn btn-warning font-weight-bold py-2 px-6">Transaction Out</a>
                                        <a href="#" class="btn btn-warning font-weight-bold py-2 px-6">Transaction Remark</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
