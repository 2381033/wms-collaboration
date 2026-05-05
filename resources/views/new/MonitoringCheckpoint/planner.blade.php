@extends('layouts.new.base')
@section('title', 'MKT - Planner Checkpoint Driver')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 120%; ">
        <div class="main-body">
            <div class="card" style="border-radius: 15px; background-color: #FAF9F6">
                <div class="card-body">
                    <form action="{{ route('submitPlanner') }}" method="post" id="formSubmit">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">No. Order</label>
                                    <input type="text" name="no_order" class="form-control"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" required autofocus
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="">No. Mobil</label>
                                    <select class="form-control" id="selectNoMobil" name="no_mobil"
                                        onchange="searchJenisArmada(this.value)" required>
                                        <option value="" selected disabled>SILAHKAN PILIH</option>
                                        @foreach ($armada as $item)
                                            <option value="{{ $item->no_mobil }}">{{ $item->no_mobil }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Type Armada</label>
                                    <input type="hidden" name="jenis_armada" id="jenisArmadavalue" required>
                                    <select class="form-control" name="" disabled id="selectJenisArmada" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Customer</label>
                                    <input type="text" name="nama_customer" class="form-control"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" required
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Lokasi Muat</label>
                                    <input type="text" name="" class="form-control lokasiMuat"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" autocomplete="off">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Lokasi Muat <a class="btn btn-sm btn-dark"
                                                        onclick="removeLokasiMuat()"><i class="fas fa-refresh"></i></a></th>
                                            </tr>
                                        </thead>
                                        <tbody id="lokasiMuatValue">
                                        </tbody>
                                    </table>
                                    <input type="hidden" required name="lokasi_muat" class="form-control lokasiMuatValue">
                                </div>
                                <div class="form-group">
                                    <label for="">Lokasi Bongkar</label>
                                    <input type="text" name="" class="form-control lokasiBongkar"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" autocomplete="off">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Lokasi Bongkar <a class="btn btn-sm btn-dark"
                                                        onclick="removeLokasiBongkar()"><i class="fas fa-refresh"></i></a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="lokasiBongkarValue">
                                        </tbody>
                                        <input type="hidden" name="lokasi_bongkar" required
                                            class="form-control lokasiBongkarValue">
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Revenue</label>
                                    <input type="text" name="revenue" class="form-control revenueValue"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" required
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Cost.</label>
                                    <input type="text" name="cost" class="form-control costValue"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" required
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Driver</label>
                                    <select class="form-control" name="driver" id="driverSelect" style="width: 100%;">
                                        <option value="" selected disabled>SILAHKAN PILIH DRIVER</option>
                                        @foreach ($driver as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-info btn-lg"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
        <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="List Job Today"
            data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-dark btn-hover-info" href="#">
                <i class="fas fa-briefcase"></i>
            </a>
        </li>
        <li class="nav-item mb-2" title="Dashboard Monitoring" data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-info"
                href="{{ url('MonitoringCheckpoint/dashboard') }}" target="_blank">
                <i class="fas fa-television"></i>
            </a>
        </li>
        <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Database Perjalanan"
            data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-primary btn-hover-primary"
                href="{{ url('MonitoringCheckpoint/planner/databasePerjalanan') }}">
                <i class="fas fa-database"></i>
            </a>
        </li>
    </ul>

    <div id="kt_demo_panel" class="offcanvas offcanvas-right p-10">
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
            <h4 class="font-weight-bold m-0">
                List Job Today
            </h4>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_demo_panel_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="offcanvas-content">
            <div class="offcanvas-wrapper mb-5 scroll-pull">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
                            @foreach ($job as $item)
                                <div class="card">
                                    <div class="card-header" id="headingOne3">
                                        <div class="card-title" data-toggle="collapse"
                                            data-target="#collapse_{{ $item->job_no }}">
                                            {{ $item->driver_name->name ?? '-' }}
                                            <a href="javascript:void(0)" onclick="deleteJob('{{ $item->token }}')"
                                                class="btn btn-sm btn-danger ml-4"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </div>
                                    <div id="collapse_{{ $item->job_no }}" class="collapse "
                                        data-parent="#accordionExample3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <ul>
                                                        <li>Job No: {{ $item->job_no }}</li>
                                                        <li>Customer: {{ $item->nama_customer }}</li>
                                                        <li>No Order: {{ $item->no_order }}</li>
                                                        <li>No Mobil: {{ $item->no_mobil }}</li>
                                                        <li>Armada: {{ $item->jenis_armada }}</li>
                                                        <li>Muat:
                                                            @foreach ($item->detail->whereNotNull('lokasi_muat') as $value)
                                                                <ul style="list-style-type:none;">
                                                                    <li>
                                                                        {{ $value->lokasi_muat }}
                                                                    </li>
                                                                </ul>
                                                            @endforeach
                                                        </li>
                                                        <li>Bongkar:
                                                            @foreach ($item->detail->whereNotNull('lokasi_bongkar') as $val)
                                                                <ul style="list-style-type:none;">
                                                                    <li>
                                                                        {{ $val->lokasi_bongkar }}
                                                                    </li>
                                                                </ul>
                                                            @endforeach
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $('#selectNoMobil').select2();

        function deleteJob(token) {
            Swal.fire({
                title: "Apakah anda yakin untuk menghapus?",
                icon: "warning",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                denyButtonText: `Batal`
            }).then((result) => {
                if (result.isConfirmed) {
                    doDeleteJob(token)
                } else if (result.isDenied) {
                    return false;
                }
            });
        }

        function doDeleteJob(token) {
            $.ajax({
                type: "GET",
                url: "{{ url('MonitoringCheckpoint/planner/deleteJob') }}/" + token,
                dataType: "json",
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        text: "Data berhasil di hapus!",
                    });
                    location.reload();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        }

        function searchJenisArmada(no_mobil) {
            $.ajax({
                type: "GET",
                url: "{{ url('MonitoringCheckpoint/planner/searchJenisArmada') }}/" + no_mobil,
                dataType: "json",
                success: function(response) {
                    $('#selectJenisArmada').html("");
                    $('#jenisArmadavalue').html("");
                    $('#selectJenisArmada').append(
                        `<option value="${response.data.armada}">${response.data.armada}</option>`
                    );
                    $('#jenisArmadavalue').val(response.data.armada);
                },
                error: function(response) {
                    console.log(response);
                }
            })
        }
        $('.revenueValue').on('keyup', function(e) {
            $('.revenueValue').val(formatRupiah(this.value, 'Rp. '));
        });

        $('.costValue').on('keyup', function(e) {
            $('.costValue').val(formatRupiah(this.value, 'Rp. '));
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
        }
        $('#driverSelect').select2();

        $(document).on("keypress", 'form', function(e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        window.onload = function() {
            window.sessionStorage.clear();
        }

        $(".lokasiMuat").keyup(function(event) {
            if (event.keyCode === 13) {
                var array = JSON.parse(sessionStorage.getItem('lokasiMuat')) || []
                var value = $(event.target).val();
                array.push(value)
                sessionStorage.setItem('lokasiMuat', JSON.stringify(array))
                $(".lokasiMuat").val("")
                appendLokasiMuat(array)
                $('.lokasiMuatValue').val(array);
            }
        });

        function appendLokasiMuat(array) {
            $('#lokasiMuatValue').html("")
            for (var i = 0; i < array.length; i++) {
                $('#lokasiMuatValue').append(`
                    <tr>
                        <td>${array[i]}</td>
                    </tr>`);
            }
        }

        function removeLokasiMuat(value) {
            sessionStorage.removeItem('lokasiMuat');
            $('#lokasiMuatValue').html("")
        }

        $(".lokasiBongkar").keyup(function(event) {
            if (event.keyCode === 13) {
                var array_bongkar = JSON.parse(sessionStorage.getItem('lokasiBongkar')) || []
                var value = $(event.target).val();
                array_bongkar.push(value)
                sessionStorage.setItem('lokasiBongkar', JSON.stringify(array_bongkar))
                $(".lokasiBongkar").val("")
                appendLokasiBongkar(array_bongkar)
                $('.lokasiBongkarValue').val(array_bongkar);
            }
        });

        function appendLokasiBongkar(array_bongkar) {
            $('#lokasiBongkarValue').html("")
            for (var i = 0; i < array_bongkar.length; i++) {
                $('#lokasiBongkarValue').append(`
                    <tr>
                        <td>${array_bongkar[i]}</td>
                    </tr>`);
            }
        }

        function removeLokasiBongkar(value) {
            sessionStorage.removeItem('lokasiBongkar');
            $('#lokasiBongkarValue').html("")
        }

        $('#formSubmit').on('submit', function(e) {
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
                    if (response.message == 'required') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Terdapat inputan yang kosong..',
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data berhasil di simpan..',
                        });
                        location.reload();
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        });
    </script>
@endpush
