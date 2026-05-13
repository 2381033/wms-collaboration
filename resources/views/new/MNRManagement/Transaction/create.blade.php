@extends('layouts.new.base')
@section('title', 'MKT - Add New Job')

@section('content')
    <div class="container-fluid" style="zoom: 110%;">
        <div class="main-body">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Add New Transaction Job</h3>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-2 col-form-label">No PO</label>
                            <div class="col-4">
                                <input class="form-control" type="text" name="no_po" placeholder="Contoh: PO-2024-001"
                                    autocomplete="off" />
                            </div>
                            <label class="col-2 col-form-label text-right">Tanggal PO</label>
                            <div class="col-4">
                                <input class="form-control" value="{{ date('Y-m-d') }}" type="date" name="tanggal_po" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Vendor Name</label>
                            <div class="col-4">
                                <select class="form-control" name="vendor_id">
                                    <option value="" selected disabled> Pilih Vendor </option>
                                    @foreach ($vendors as $item)
                                        <option value="{{ $item->id }}"> {{ $item->vendor_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-2 col-form-label text-right">Branch Name</label>
                            <div class="col-4">
                                <select class="form-control" name="branch_id">
                                    <option value="" selected disabled> Pilih Cabang </option>
                                    @foreach ($branch as $item)
                                        <option value="{{ $item->id }}"> {{ $item->branch_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Sparepart Name</label>
                            <div class="col-4">
                                <select class="form-control" name="sparepart_id">
                                    <option value="" selected disabled> Pilih Sparepart </option>
                                    @foreach ($spareparts as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                    
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Remarks</label>
                            <div class="col-4">
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>
                            <label class="col-2 col-form-label text-right">Upload Image</label>
                            <div class="col-4">
                                <input class="form-control" type="file" name="images" />
                                <small class="form-text text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Status</label>
                            <div class="col-10">
                                <select class="form-control" name="status">
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-10">
                                <button type="submit" class="btn btn-success mr-2">Submit Transaction</button>
                                <a href="#" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
