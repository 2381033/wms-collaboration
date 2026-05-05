@extends('layouts.new.base')
@section('title', 'MKT - Collecting Price')
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
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right">
                                <a href="#add" data-toggle="modal" class="btn btn-lg btn-dark mb-3"><i
                                        class="fas fa-plus-circle"></i>
                                    Add</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>NO</th>
                                            <th>PRINCIPAL</th>
                                            <th>HANDLING IN (CBM/TON)</th>
                                            <th>HANDLING OUT (CBM/TON)</th>
                                            <th>STORAGE (CBM DAY)</th>
                                            <th>STORAGE (Sqm Per MONTH)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($price as $item)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->principal }}</td>
                                                <td>{{ number_format($item->handling_in, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->handling_out, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->cbm_day, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->sqm_permonth, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ url('collectingPrice/delete/' . $item->id) }}"
                                                        class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i>
                                                        Delete</a>
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
        <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('postPrice') }}" method="post" id="postPrice">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <select id="my-select" class="form-control" name="principal" required>
                                            <option value="" disabled selected>PRINCIPAL</option>
                                            @foreach ($principal as $item)
                                                <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">HANDLING IN(CBM/TON)</label>
                                        <input id="my-input" class="form-control handlingIN" type="number" value="0"
                                            name="handling_in" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="my-input">STORAGE(CBM DAY)</label>
                                        <input id="my-input" class="form-control cbmDay" type="number" value="0"
                                            name="cbm_day">
                                        <small class="text-muted"> *isi jika ada</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="my-input">HANDLING OUT(CBM/TON)</label>
                                        <input id="my-input" class="form-control handlingOUT" value="0" type="number"
                                            name="handling_out" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="my-input">Sqm Per Month</label>
                                        <input id="my-input" class="form-control sqmMonth" value="0" type="number"
                                            name="sqm_per_month">
                                        <small class="text-muted"> *isi jika ada</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="float-right">
                                <button type="submit" class="btn btn-md btn-info btnsave"><i class="fas fa-save"></i>
                                    Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#postPrice').on('submit', function() {
            $('.btnsave').attr('disabled', true);
        });

        $('#table').DataTable();
    </script>
@endpush
