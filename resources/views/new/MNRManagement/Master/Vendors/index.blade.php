@extends('layouts.new.base')
@section('title', 'MKT - Master Vendors')
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
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalVendor">+
                                Add</button>
                        </div>

                        <div class="col-sm-12">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50px">No</th>
                                        <th>Branch</th>
                                        <th>Vendor Code</th>
                                        <th>Vendor Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vendors as $key => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @foreach ($branch as $b)
                                                    @if ($b->id == $item->branch_id)
                                                        {{ $b->branch_name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $item->vendor_code }}</td>
                                            <td>{{ $item->vendor_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalVendor" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Vendor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form action="{{ route('vendors.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Branch Name</label>
                                <div class="col-9">
                                    <select class="form-control" name="branch_id" required>
                                        <option value="" selected disabled>Silahkan pilih...</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Vendor Code</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" name="vendor_code"
                                        placeholder="Masukkan Kode Vendor" required autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Vendor Name</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" name="vendor_name"
                                        placeholder="Masukkan Nama Vendor" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
