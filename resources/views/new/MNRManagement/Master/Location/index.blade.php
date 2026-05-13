@extends('layouts.new.base')
@section('title', 'MKT - Master Location')
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
                                        <th>Location Code</th>
                                        <th>Location Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locations as $key => $loc)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if ($loc->branch_id == 1)
                                                    Jakarta
                                                @elseif($loc->branch_id == 2)
                                                    Belawan
                                                @elseif($loc->branch_id == 3)
                                                    Semarang
                                                @elseif($loc->branch_id == 4)
                                                    Surabaya
                                                @else
                                                    {{ $loc->branch_id }}
                                                @endif
                                            </td>
                                            <td>{{ $loc->location_code }}</td>
                                            <td>{{ $loc->location_name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                    data-toggle="modal" data-target="#editModal"
                                                    data-id="{{ $loc->id }}" data-branch="{{ $loc->branch_id }}"
                                                    data-code="{{ $loc->location_code }}"
                                                    data-name="{{ $loc->location_name }}">
                                                    Edit
                                                </button>
                                                <a href="{{ route('locations.delete', $loc->id) }}"
                                                    class="btn btn-danger btn-sm">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                    <form action="{{ route('locations.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card card-custom">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Branch Name</label>
                                            <div class="col-10">
                                                <select class="form-control" name="branch_id">
                                                    <option value="" id="" selected disabled>
                                                        silahkan pilih
                                                    </option>
                                                    @foreach ($branch as $item)
                                                        <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Locations Code</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" name="location_code"
                                                    placeholder="silahkan isi..." value="" id="example-text-input" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="example-number-input" class="col-2 col-form-label">Locations
                                                Name</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" name="location_name"
                                                    placeholder="silahkan isi..." value=""
                                                    id="example-number-input" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-2">
                                            </div>
                                            <div class="col-10">
                                                <button type="submit" class="btn btn-success mr-2">Submit</button>
                                                <button type="button"
                                                    class="btn btn-secondary"data-dismiss="modal">Cancel</button>
                                            </div>
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
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Form Edit Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('locations.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card card-custom">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Branch Name</label>
                                            <div class="col-10">
                                                <select class="form-control" name="branch_id" id="edit_branch_id">
                                                    @foreach ($branch as $item)
                                                        <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Locations Code</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" name="location_code"
                                                    id="edit_location_code" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Locations Name</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" name="location_name"
                                                    id="edit_location_name" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-2"></div>
                                            <div class="col-10">
                                                <button type="submit" class="btn btn-primary mr-2">Update
                                                    Changes</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                            </div>
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
    <script>
        $(document).ready(function() {
            $('.btn-edit').on('click', function() {
                // Ambil data dari atribut tombol
                const id = $(this).data('id');
                const branch = $(this).data('branch');
                const code = $(this).data('code');
                const name = $(this).data('name');

                // Masukkan ke dalam input modal
                $('#edit_id').val(id);
                $('#edit_branch_id').val(branch);
                $('#edit_location_code').val(code);
                $('#edit_location_name').val(name);
            });
        });
    </script>
@endpush
