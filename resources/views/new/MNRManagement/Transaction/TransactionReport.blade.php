@extends('layouts.new.base')
@section('title', 'MKT - TransactionReport')
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
                                                    vendors,locations,spareparts,tools</p>
                                                <div class="d-flex flex-wrap" style="gap: 10px;">
                                                    <a href="{{ route('vendors.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Vendors</a>
                                                    <a href="{{ route('locations.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Locations</a>
                                                    <a href="{{ route('spareparts.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Spareparts</a>
                                                    <a href="{{ route('tools.index') }}"
                                                        class="btn btn-danger font-weight-bold py-2 px-6">Tools</a>
                                                </div>
                                            </div>
                                        </div>
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
