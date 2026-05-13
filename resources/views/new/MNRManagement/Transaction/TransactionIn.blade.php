@extends('layouts.new.base')
@section('title', 'MKT - TransactionIn')
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
            <div class="mb-5">
                <div class="d-flex">
                    {{-- <button class="btn btn-info btn-sm mr-1" style="border-radius: 4px 0 0 4px;">Refresh</button> --}}
                    <a href="{{ route('transaction.in.create') }}" class="btn btn-primary mb-3">
                        + Add New Job
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>NO PO</th>
                                    <th>Tanggal PO</th>
                                    <th>Vendor Name</th>
                                    <th>Branch Name</th>
                                    <th>Job Number</th>
                                    <th>Spareparts</th>
                                    <th>Imagets</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>
                                
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
