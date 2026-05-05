@extends('layouts.main')

@section('title')
    User
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>User</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Change Password User</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;"
                            style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Name</th>
                                    {{-- <th>Branch</th> --}}
                                    <th>E-mail</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="ubahPassword('{{ $item->id }}')"
                                                class="btn btn-sm btn-dark"><i class="fas fa-key"></i> Ubah
                                                Password</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <div class="modal fade" id="change-password" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Ganti Password</h5>
                </div>
                <form action="{{ route('post-change-password') }}" method="POST" id="ubahPw">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="password" name="password" id="" class="form-control Password"
                                        placeholder="Masukan Password Baru" aria-describedby="helpId" required>
                                    <input type="text" name="id_user" class="idUserValue" hidden>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-4">
                                <div class="form-group">
                                    <input type="password" name="password_konfirm" id=""
                                        class="form-control Password" placeholder="Konfirmasi Password"
                                        aria-describedby="helpId" required>
                                </div>
                                <input type="checkbox" class="ml-2" id="showPass"> Show Password
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"> Update</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table_list').DataTable();
        });

        function ubahPassword(id_user) {
            $('.idUserValue').val(id_user);
            $('#change-password').modal('show');
        }

        $('#showPass').on('click', function() {
            var passInput = $(".Password");
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');
            } else {
                passInput.attr('type', 'password');
            }
        });

        $('#ubahPw').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'gagal') {
                        swal({
                            icon: "warning",
                            text: 'Password Tidak Sama!'
                        });
                    } else if (response.status == 'kurang') {
                        swal({
                            icon: "warning",
                            text: 'Password minimal 6 karakter!',
                        });
                    } else {
                        swal({
                            icon: "success",
                            text: 'Berhasil Di ubah..',
                        });
                        location.reload();
                    }
                }
            });
        });
    </script>
@endpush
