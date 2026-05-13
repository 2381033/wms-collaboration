@extends('layouts.new.base')
@section('title', 'MKT - Master Tools')
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
                        <div class="col-sm-12">
                            <button class="btn btn-primary mb-3" data-toggle="modal"
                                data-target="#exampleModalScrollable">+Add</button>
                        </div>
                        <div class="col-sm-12">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tools as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $item->id }}" data-code="{{ $item->code }}"
                                                    data-name="{{ $item->name }}">
                                                    Edit
                                                </button>

                                                <a href="{{ route('tools.delete', $item->id) }}"
                                                    class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
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
        <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
            aria-labelledby="staticBackdrop"aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Input Master Tools</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form action="{{ route('tools.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tools Code</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="code"
                                        placeholder="Contoh: T001"required autocomplete="off"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tools Name</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="name"
                                        placeholder="Contoh: Forklift Toyota" required autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Master Tools</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('tools.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">
                                    Tools Code
                                </label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="code" id="edit_code" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">
                                    Tools Name
                                </label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="name" id="edit_name" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    <script>
    $(document).ready(function() {
        $(document).on('click', '.btn-edit', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_code').val($(this).data('code'));
            $('#edit_name').val($(this).data('name'));

            $('#editModal').modal('show');
        });
    });
</script>
    @endpush
