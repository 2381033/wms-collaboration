@extends('layouts.new.base')
@section('title', 'MKT - Add New Job')

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="mb-3">
                <a href="{{ route('transaction.in') }}" class="btn btn-secondary btn-sm">
                    &larr; Kembali
                </a>
            </div>

            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Detail Transaksi — {{ $data->job_number }}</h3>
                    @if ($data->status == 'open')
                        <span class="badge badge-success" style="font-size: 13px;">OPEN</span>
                    @else
                        <span class="badge badge-danger" style="font-size: 13px;">CLOSED</span>
                    @endif
                </div>

                <div class="card-body">

                    {{-- Informasi PO --}}
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Job Number</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->job_number }}" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">No PO</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->no_po }}" readonly />
                        </div>
                        <label class="col-2 col-form-label text-right">Tanggal PO</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->tanggal_po }}" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Vendor Name</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->vendor_name ?? '-' }}" readonly />
                        </div>
                        <label class="col-2 col-form-label text-right">Branch Name</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->branch_name ?? '-' }}" readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Sparepart Name</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ $data->sparepart_name ?? '-' }}"
                                readonly />
                        </div>
                        <label class="col-2 col-form-label text-right">Status</label>
                        <div class="col-4">
                            <input class="form-control" type="text" value="{{ strtoupper($data->status) }}" readonly
                                style="font-weight: bold; color: {{ $data->status == 'open' ? '#1bc5bd' : '#f64e60' }};" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Remarks</label>
                        <div class="col-4">
                            <textarea class="form-control" rows="2" readonly>{{ $data->remarks ?? '-' }}</textarea>
                        </div>
                        <label class="col-2 col-form-label text-right">Image</label>
                        <div class="col-4">
                            @if ($data->images)
                                <a href="{{ asset('storage/transaction_in/' . $data->images) }}" target="_blank">
                                    <img src="{{ asset('storage/transaction_in/' . $data->images) }}" alt="Bukti Transaksi"
                                        style="max-height: 180px; border-radius: 6px; border: 1px solid #ddd; cursor: pointer;">
                                </a>
                            @else
                                <input class="form-control" type="text" value="Tidak ada gambar" readonly />
                            @endif
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <form action="{{ route('transaction.in.update-status', $data->job_number) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin mengubah status transaksi ini?')">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            @if ($data->status == 'open')
                                Tutup Transaksi (Set CLOSED)
                            @else
                                Buka Kembali (Set OPEN)
                            @endif
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        $('#selectSpareparts').select2();
        $('#selectVendor').select2();
        $('#selectBranch').select2();
    </script>
@endpush
