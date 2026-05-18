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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            <div class="mb-5">
                <div class="d-flex">
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
                                    <th>No</th>
                                    <th>Job Number</th>
                                    <th>Branch Name</th>
                                    <th>NO PO</th>
                                    <th>Tanggal PO</th>
                                    <th>Vendor Name</th>
                                    <th>Spareparts</th>
                                    <th>Images</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>

                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('transaction.in.show', $row->job_number) }}">
                                                {{ $row->job_number }}
                                            </a>
                                        </td>
                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                        <td>{{ $row->no_po }}</td>
                                        <td>{{ $row->tanggal_po }}</td>
                                        <td>{{ $row->vendor_name ?? '-' }}</td>
                                        <td>{{ $row->sparepart_name ?? '-' }}</td>
                                        <td>
                                            @if ($row->images)
                                                <a href="{{ asset('storage/transaction_in/' . $row->images) }}" download>
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $row->remarks ?? '-' }}</td>
                                        <td>
                                            @if ($row->status == 'open')
                                                <span class="badge badge-success">OPEN</span>
                                            @else
                                                <span class="badge badge-danger">CLOSED</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
