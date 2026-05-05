@extends('layouts.new.base')
@section('title', 'MKT - Freeze Stock DC')
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
                    <form action="" method="post" id="formFreezeStock">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select class="form-control" name="branch_id" required id="">
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Principal</label>
                                    <select class="form-control" name="principal_id" required id="principalSelect">
                                        @foreach ($principal as $item)
                                            <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Freeze Activity</label>
                                    <select class="form-control" name="activity" required>
                                        <option value="INBOUND">INBOUND</option>
                                        <option value="OUTBOUND">OUTBOUND</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Reason/Body Email</label>
                                    <textarea class="form-control" name="body_email" id="" rows="5" required>Dear Operasional Team,</textarea>
                                </div>
                                <div class="float-right">
                                    <button class="btn btn-md btn-dark">
                                        <i class="fas fa-save"></i>
                                        Submit And Freeze Now!
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <hr>
            <div class="card">
                <div class="card-body">
                    <table class="table table-vertical-center">
                        <thead>
                            <tr>
                                <th colspan="3">Data Freeze Stock DC</th>
                            </tr>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Branch</th>
                                <th>Principal</th>
                                <th>Freeze Date</th>
                                <th>Activity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->branch_name }}</td>
                                    <td>{{ $item->principal_name }}</td>
                                    <td>{{ Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge badge-danger"> <i class="fas fa-lock text-white"></i> Freeze
                                        </span>
                                    </td>
                                    <td>{{ $item->freeze_activity }}</td>
                                    <td>
                                        <a href="#"
                                            onclick="unfreezeNow('{{ $item->principal_id }}', '{{ $item->branch_id }}', '{{ $item->id }}')"
                                            class="btn btn-sm btn-success" id="unfreezeBtn">
                                            <i class="fas fa-unlock"></i> Unfreeze Now!
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

    <div class="modal fade" id="modal-unfreeze" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form method="post" id="formUnfreezeStock">
                    @csrf
                    <div>
                        <div class="modal-body">
                            <input type="hidden" name="branch_id_unf" id="branch_id_unf">
                            <input type="hidden" name="principal_id_unf" id="principal_id_unf">
                            <input type="hidden" name="id_unf" id="id_unf">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="">Reason/Body Email</label>
                                        <textarea class="form-control" name="mail_body" id="" rows="5" required>Dear Operasional Team,</textarea>
                                    </div>
                                    <div class="float-right">
                                        <button class="btn btn-md btn-success btn-un">
                                            <i class="fas fa-save"></i>
                                            Submit And Unreeze Now!
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#principalSelect').select2({
            width: '100%',
        });

        function unfreezeNow(principal_id, branch_id, id) {
            $('#modal-unfreeze').modal('show')
            $('#principal_id_unf').val(principal_id)
            $('#branch_id_unf').val(branch_id)
            $('#id_unf').val(id)
        }

        $('#formUnfreezeStock').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin melakukan unfreeze stock ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('unfreezeStockDC') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('.btn-un').attr('disabled', 'disabled');
                            $('.btn-un').html(
                                '<i class="fa fa-spinner fa-spin"></i> Please wait...');
                        },
                        success: function(response) {
                            if (response.message == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText,
                            });
                        },
                        complete: function() {
                            $('.btn-un').removeAttr('disabled');
                            $('.btn-un').html(
                                '<i class="fas fa-save"></i> Submit And UnFreeze Now!');
                        }
                    });
                }
            });
        });

        $('#formFreezeStock').on('submit', function(e) {
            e.preventDefault();
            let dataForm = new FormData(this);
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin melakukan freeze stock ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('freezeStockDC') }}",
                        data: dataForm,
                        processData: false,
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('.btn').attr('disabled', 'disabled');
                            $('.btn').html(
                                '<i class="fa fa-spinner fa-spin"></i> Please wait...');
                        },
                        success: function(response) {
                            if (response.message == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText,
                            });
                        },
                        complete: function() {
                            $('.btn').removeAttr('disabled');
                            $('.btn').html(
                                '<i class="fas fa-save"></i> Submit And Freeze Now!');
                        }
                    });
                }
            });
        });
    </script>
@endpush
