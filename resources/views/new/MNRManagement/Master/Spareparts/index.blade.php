@extends('layouts.new.base')
@section('title', 'MKT - Master Spareparts')
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
                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModalScrollable">+
                                Add</button>
                        </div>

                        <div class="col-sm-12">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Branch Name</th>
                                        <th>Tools Name</th>
                                        <th>Location Name</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>UOM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>jakarta</td>
                                        <td>MTKI</td>
                                        <td>Masaji Tata Kontainer Indonesia</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table">
                                <thead class="thead-light">
                                    ...
                                </thead>
                                <tbody>
                                    ...
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Input</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="">


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card card-custom"> <!--begin::Form-->

                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Branch Name</label>
                                                <div class="col-10">

                                                    <select class="form-control" id="exampleSelect1">
                                                        <option>Jakarta</option>
                                                        <option>Belawan</option>
                                                        <option>Semarang</option>
                                                        <option>Surabaya</option>
                                                        <option>Bogor</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Tools Name</label>
                                                <div class="col-10">

                                                    <select class="form-control" id="exampleSelect1">
                                                        <option>Jakarta</option>
                                                        <option>Belawan</option>
                                                        <option>Semarang</option>
                                                        <option>Surabaya</option>
                                                        <option>Bogor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Location Code</label>
                                                <div class="col-10">
                                                    <input class="form-control" type="text" placeholder="silahkan isi..."
                                                        value="" id="example-text-input" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Name</label>
                                                <div class="col-10">
                                                    <input class="form-control" type="text" placeholder="silahkan isi..."
                                                        value="" id="example-text-input" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Type</label>
                                                <div class="col-10">

                                                    <select class="form-control" id="exampleSelect1">
                                                        <option>Jakarta</option>
                                                        <option>Belawan</option>
                                                        <option>Semarang</option>
                                                        <option>Surabaya</option>
                                                        <option>Bogor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">UOM</label>
                                                <div class="col-10">

                                                    <select class="form-control" id="exampleSelect1">
                                                        <option>Jakarta</option>
                                                        <option>Belawan</option>
                                                        <option>Semarang</option>
                                                        <option>Surabaya</option>
                                                        <option>Bogor</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-2">
                                                    </div>
                                                    <div class="col-10">
                                                        <button type="reset" class="btn btn-success mr-2">Submit</button>
                                                        <button type="reset" class="btn btn-secondary">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
    @endpush
