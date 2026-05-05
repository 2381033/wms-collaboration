@extends('layouts.new.base')
@section('title', 'MKT - Outstanding Surveyor')

@section('content')
    <div class="container-fluid">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body pt-4">
                    <div class="row">
                        <div class="col-sm-12" style="zoom: 90%;">
                            <div class="navi navi-bold navi-hover navi-active navi-link-rounded"
                                style="height:500px;
                            overflow-y: scroll;">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="9" class="text-center" id="appendProgress">

                                            </th>
                                        </tr>
                                        <tr class="text-center">
                                            <th><label style="margin-bottom: 30px;">No.</label></th>
                                            <th><label style="margin-bottom: 30px;">Principal</label></th>
                                            <th><label style="margin-bottom: 30px;">Master BL</label></th>
                                            <th><label style="margin-bottom: 30px;">House BL</label></th>
                                            <th><label style="margin-bottom: 30px;">Container No</label></th>
                                            <th><label style="margin-bottom: 30px;">QTY</label></th>
                                            <th><label style="margin-bottom: 30px;">Progress By</label></th>
                                            <th><label style="margin-bottom: 30px;">Start Time</label></th>
                                            <th><label style="margin-bottom: 30px;">Status</label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->principal }}</td>
                                                <td>{{ $item->masterbl }}</td>
                                                <td>{{ $item->housebl }}</td>
                                                <td>{{ $item->container }}</td>
                                                <td>{{ $item->qty . ' ' . $item->package }}</td>
                                                <td>{{ $item->created_by }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }} WIB
                                                </td>
                                                <td>
                                                    <a href="#" onclick="showFoto('{{ $item->token }}')"
                                                        class="btn btn-warning btn-md"><i class="fas fa-stopwatch"></i> On
                                                        Progress
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
    </div>

    <div class="modal fade" id="modalFoto" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><b>Klik foto untuk menghapus, Klik kanan untuk melihat/mengunduh</b></h4>
                                <br>
                                <div class="text-center">
                                    <a href="#" onclick="addImage()" class="btn btn-lg btn-dark"><i
                                            class="fas fa-plus-square"></i> Add
                                        image</a>
                                </div>
                            </div>
                            <div class="container" id="img-js">
                                <div class="image-gallery">
                                    <div id="image"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFoto" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ url('foto-management/uploadFoto') }}" method="post" id="uploadFoto"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="token" id="tokenValue">
                                <div class="form-group">
                                    <label for="">Add Image</label>
                                    <input type="file" class="form-control-file" name="foto[]" id="" multiple
                                        aria-describedby="helpId" placeholder="" required accept="image/*">
                                    <small class="text-danger">*png,jpg,jpeg</small>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-upload"><i class="fas fa-upload"></i> Upload</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        $('#selectMobil').select2();

        $('#exportExcel').on('submit', function() {
            // $('.btnExport').attr('disabled', true);
        });

        $('#uploadFoto').on('submit', function() {
            $('.btn-upload').attr('disabled', true);
        });

        function addImage() {
            var token = sessionStorage.getItem('token')
            $('#tokenValue').val(token);
            $('#modalFoto').modal('hide');
            $('#addFoto').modal('show');
        }

        function showFoto(token) {
            $.ajax({
                url: "{{ url('foto-management/showFoto/') }}/" + token,
                type: "GET",
                dataType: 'json',
                success: function(res) {
                    if (res.data.length > 0) {
                        sessionStorage.setItem('token', token);
                        $('#modalFoto').modal('show');
                        $('#image').html('');
                        $.each(res.data, function(key, val) {
                            $('#image').append(
                                `<img class="m-2" onClick="deleteFoto('${val.id}')" src="data:image/png;base64,${val.foto}" alt="images" style="height: 150px; width: 150px; border-radius: 10px;" />`
                            );
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Foto Not Found!',
                        })
                    }
                },
                error: function(data) {
                    console.log(token);
                }
            });
        }

        function deleteFoto(id) {
            let text = "Apakah anda yakin untuk menghapus foto ini?";
            if (confirm(text) == true) {
                var token = sessionStorage.getItem('token');
                $.ajax({
                    url: "{{ url('foto-management/deleteFoto/') }}/" + id,
                    type: "GET",
                    dataType: 'json',
                    success: function(res) {
                        $('#modalFoto').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Foto berhasil di hapus..',
                        })
                    },
                    error: function(data) {
                        console.log(token);
                    }
                });
            } else {
                return false;
            }
        }
    </script>
@endpush
