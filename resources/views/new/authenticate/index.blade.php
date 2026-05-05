@extends('layouts.new.base')
@section('title', 'MKT - Authentication')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
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
                            <div class="card card-custom gutter-b">
                                <div class="card-header card-header-tabs-line">
                                    <div class="card-toolbar">
                                        <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_4">
                                                    <span class="nav-icon"><i class="las la-key"></i></span>
                                                    <span class="nav-text">Authenticate Group</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_4">
                                                    <span class="nav-icon"><i class="las la-tv"></i></span>
                                                    <span class="nav-text">Menu</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#users">
                                                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                                                    <span class="nav-text">Users Group</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_tab_pane_1_4" role="tabpanel"
                                            aria-labelledby="kt_tab_pane_1_4">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="float-right">
                                                        <a href="javascript:void(0)" onclick="addAuth()"
                                                            class="btn btn-lg btn-info mb-4"><i class="fas fa-add"
                                                                aria-hidden="true"></i> Add</a>
                                                    </div>
                                                    <table class="table" id="tableAuth">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Authenticate Name</th>
                                                                <th>User</th>
                                                                <th>#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($auth as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <a href="#"
                                                                            onclick="detailAuth('{{ $item->id }}')">
                                                                            {{ $item->name }}
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        {{ implode(', ',App\User::Where('auth_group_id', $item->id)->pluck('username')->toArray()) }}
                                                                    </td>
                                                                    <td>
                                                                        <a href="javascript:void(0)"
                                                                            onclick="deleteAuth('{{ $item->id }}')"
                                                                            class="btn btn-sm btn-danger"><i
                                                                                class="fa fa-trash" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="kt_tab_pane_2_4" role="tabpanel"
                                            aria-labelledby="kt_tab_pane_2_4">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="float-right">
                                                        <a href="javascript:void(0)" onclick="addPermission()"
                                                            class="btn btn-lg btn-dark mb-4"><i class="fas fa-add"
                                                                aria-hidden="true"></i> Add
                                                        </a>
                                                    </div>
                                                    <table class="table table-hover" id="tablePermission">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Menu Name</th>
                                                                <th>Tools</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($menu as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        {{ $item->name }}
                                                                    </td>
                                                                    <td>
                                                                        <a href="#"
                                                                            onclick="deletePermission('{{ $item->id }}')"
                                                                            class="btn btn-danger btn-sm"><i
                                                                                class="fas fa-trash-alt"></i> </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-hover" id="tableUsers">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Name</th>
                                                                <th>Username</th>
                                                                <th>Authenticate Group</th>
                                                                <th>Tools</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($users as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        {{ $item->name }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $item->username }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $auth->where('id', $item->auth_group_id)->first()->name ?? '-' }}
                                                                    </td>
                                                                    <td>
                                                                        <a href="#"
                                                                            onclick="changeUserAuth('{{ $item->id }}')"
                                                                            class="btn btn-dark btn-sm"><i
                                                                                class="las la-key"></i> </a>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="detailAuth" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ url('auth_group/storeMapping') }}" method="post" id="storeMapping">
                        @csrf
                        <input type="text" name="auth_group_id" value="" hidden class="authGroupValue">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-5">
                                    <h4 class="text-center"><b>ACCESS MENU</b></h4>
                                    <hr>
                                    <div class="siteMenu">

                                    </div>
                                </div>
                                <div class="col-sm-2">

                                </div>
                                <div class="col-sm-5">
                                    <h4 class="text-center"><b>UNACCESS MENU</b></h4>
                                    <hr>
                                    <div class="unsiteMenu">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-lg btn-info btnSave hide"><i
                                                class="fas fa-save"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="detailUsers" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ url('auth_group/storeMappingUsers') }}" method="post" id="">
                        @csrf
                        <input type="hidden" id="idUserValue" name="id_user" value="">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Authenticate Now</label>
                                        <div class="col-10">
                                            <input class="form-control" type="text" disabled value=""
                                                id="authenticateNow" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table borderless">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <label>Choose New Authenticate</label>
                                                </th>
                                            </tr>
                                            @foreach ($auth as $item)
                                                <tr class="text-center">
                                                    <th>
                                                        <div class="form-group">
                                                            <div class="radio-list">
                                                                <label class="radio">
                                                                    <input type="radio" name="auth_group_id" required
                                                                        value="{{ $item->id }}" />
                                                                    <span></span>
                                                                    {{ $item->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                            @endforeach
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-lg btn-info"><i class="fas fa-save"></i>
                                            Simpan</button>
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
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('#tableAuth').DataTable()
        $('#tablePermission').DataTable()
        $('#tableUsers').DataTable()

        $('#postPrice').on('submit', function() {
            $('.btnsave').attr('disabled', true);
        });

        function changeUserAuth(id) {
            $('#idUserValue').val(id);

            $.ajax({
                url: "{{ url('auth_group/detailUsers') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('#detailUsers').modal('show');
                    $('#authenticateNow').val(data);
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function addAuth() {
            let value = prompt("Silahkan isi", "");
            if (value != null) {
                location.href = "{{ url('auth_group/storeAuth') }}/" + value;
            } else {
                return false;
            }
        }

        function addAuth() {
            let value = prompt("Silahkan isi", "");
            if (value != null) {
                location.href = "{{ url('auth_group/storeAuth') }}/" + value;
            } else {
                return false;
            }
        }

        function addPermission() {
            let value = prompt("Silahkan isi", "");
            if (value != null) {
                location.href = "{{ url('auth_group/storePermission') }}/" + value;
            } else {
                return false;
            }
        }

        function deleteAuth(id) {
            let text = "Are you Sure?";
            if (confirm(text) == true) {
                location.href = "{{ url('auth_group/deleteAuth') }}/" + id;
            } else {
                return false;
            }
        }

        function deletePermission(id) {
            let text = "Are you Sure?";
            if (confirm(text) == true) {
                location.href = "{{ url('auth_group/deletePermission') }}/" + id;
            } else {
                return false;
            }
        }

        function detailAuth(id) {
            $.ajax({
                url: "{{ url('auth_group/detailAuth') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $('.authGroupValue').val(id);
                        $('#detailAuth').modal('show');
                        $('.siteMenu').html("")
                        $.each(data.access, function(index, value) {
                            $('.siteMenu').append(`
                            <div class="form-group mt-2">
                                <div class="checkbox-inline">
                                    <label class="checkbox">
                                        <input type="checkbox" value="${value.id}" checked="checked" name="id_permission[]"/>
                                        <span></span>
                                        ${value.name}
                                    </label>
                                </div>
                            </div>`)
                        });

                        $('.unsiteMenu').html("")
                        $.each(data.unaccess, function(index, value) {
                            $('.unsiteMenu').append(`
                            <div class="form-group mt-2">
                                <div class="checkbox-inline">
                                    <label class="checkbox">
                                        <input type="checkbox" value="${value.id}" name="id_permission[]"/>
                                        <span></span>
                                        ${value.name}
                                    </label>
                                </div>
                            </div>`)
                        });
                        $('.btnSave').removeClass('hide');
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }
    </script>
@endpush
