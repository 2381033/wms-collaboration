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
                                    @foreach ($spareparts as $key => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @foreach ($branch as $b)
                                                    @if ($b->id == $item->branch_id)
                                                        {{ $b->branch_name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($tools as $tool)
                                                    @if ($tool->id == $item->tools_id)
                                                        {{ $tool->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($locations as $loc)
                                                    @if ($loc->id == $item->location_id)
                                                        {{ $loc->location_name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->uom }}</td>
                                        </tr>
                                    @endforeach
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
                        <h5 class="modal-title" id="exampleModalLabel">Form Input Spareparts</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('spareparts.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card card-custom">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Branch Name</label>
                                                <div class="col-10">
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
                                                <label class="col-2 col-form-label">Tools Name</label>
                                                <div class="col-10">
                                                    <select class="form-control" name="tools_id" required>
                                                        <option value="" selected disabled>Silahkan pilih...</option>
                                                        @foreach ($tools as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Location Code</label>
                                                <div class="col-10">
                                                    <select class="form-control" name="location_id" required>
                                                        <option value="" selected disabled>Silahkan pilih...</option>
                                                        @foreach ($locations as $item)
                                                            <option value="{{ $item->id }}">{{ $item->location_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Name</label>
                                                <div class="col-10">
                                                    <input class="form-control" type="text" name="name" required
                                                        placeholder="silahkan isi..." value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Type</label>
                                                <div class="col-10">
                                                    <input class="form-control" type="text" name="type" required
                                                        placeholder="silahkan isi..." autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">UOM</label>
                                                <div class="col-10">
                                                    <select class="form-control" name="uom_name" required>
                                                        @foreach ($uom as $item)
                                                            <option value="{{ $item->code }}">{{ $item->uom_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-2">
                                                    </div>
                                                    <div class="col-10">
                                                        <button type="submit" class="btn btn-success mr-2">Submit</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
