@extends('layouts.new.base')
@section('title', 'MKT - Monitoring Checkpoint')
@push('styles')
    <style>
        .modal-content {
            background-color: transparent !important;
            border: 0px !important
        }

        .modal-header {
            border: 0px !important
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body pt-4">
                    <div class="row">
                        <div class="col-sm-7" style="zoom: 90%;">
                            <div class="navi navi-bold navi-hover navi-active navi-link-rounded"
                                style="height:500px;
                            overflow-y: scroll;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="9" class="text-center" id="appendProgress">

                                            </th>
                                        </tr>
                                        <tr class="text-center">
                                            <th><label style="margin-bottom: 30px;">No.</label></th>
                                            <th><label style="margin-bottom: 30px;">No Order</label></th>
                                            <th><label style="margin-bottom: 30px;">No Mobil</label></th>
                                            <th>Menuju <p>Lokasi Muat</p>
                                            </th>
                                            <th>Lokasi <p>Muat</p>
                                            </th>
                                            <th>Lokasi <p>Bongkar</p>
                                            </th>
                                            <th>Selesai <p>Order</p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="timeline timeline-2">
                                <div class="timeline-bar"></div>
                                <div class="appendHeader">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalFoto" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <center>
                                <div id="image"></div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
        <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Export To Excel"
            data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="#">
                <i class="far fa-file-excel"></i>
            </a>
        </li>
    </ul>

    <div id="kt_demo_panel" class="offcanvas offcanvas-right p-10">
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
            <h4 class="font-weight-bold m-0">
                Export To Excel
            </h4>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_demo_panel_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="offcanvas-content">
            <div class="offcanvas-wrapper mb-5 scroll-pull">
                <div class="row">
                    <form action="{{ url('MonitoringCheckpoint/dashboard/export') }}" method="post" id="exportExcel">
                        <div class="col-sm-12">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Start </label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="la la-calendar"></i></span></div>
                                        <input type="date" name="startDate" class="form-control"
                                            value="{{ $start }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">End </label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="la la-calendar"></i></span></div>
                                        <input type="date" name="endDate" class="form-control"
                                            value="{{ $end }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">No. Mobil</label>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <select class="form-control" id="selectMobil" style="width: 100%" name="no_mobil"
                                            required>
                                            <option value="ALL">ALL</option>
                                            @foreach ($mobil as $item)
                                                <option value="{{ $item->no_mobil }}">{{ $item->no_mobil }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-block btn-dark btnExport"><i
                                    class="fas fa-file-excel"></i> Export
                                Now!
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        $('#selectMobil').select2();
        updateDisplay();
        $(document).ready(function() {
            setInterval(function() {
                updateDisplay()
            }, 300000);
        });

        $('#exportExcel').on('submit', function() {
            // $('.btnExport').attr('disabled', true);
        });

        function updateDisplay() {
            $.ajax({
                url: "{{ url('MonitoringCheckpoint/dashboard/updateDisplay') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#tableBody').html("")
                    $('#appendProgress').html("")
                    $.each(data.allJob, function(key, value) {
                        var statusJob = value.status_job;
                        var confirmFlag = value.confirmed_flag;
                        var startJob = '';
                        var gateInLokasiMuat = '';
                        var gateInLokasiBongkar = '';
                        var gateOutLokasiBongkar = '';
                        var green =
                            `<a class="btn btn-sm btn-success rounded-circle text-center"><i class=""></a>`;
                        var red =
                            `<a class="btn btn-sm btn-primary rounded-circle text-center"><i class="fa-md text-center"></i></a>`;
                        if (statusJob == 'to_loc_muat') {
                            startJob = green;
                            gateInLokasiMuat = red;
                            gateInLokasiBongkar = red;
                            gateOutLokasiBongkar = red;
                        } else if (statusJob == 'gate_in_loc_muat') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = red;
                            gateOutLokasiBongkar = red;
                        } else if (statusJob == 'gate_out_loc_muat') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = red;
                            gateOutLokasiBongkar = red;
                        } else if (statusJob == 'gate_in_loc_bongkar') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = green;
                            gateOutLokasiBongkar = red;
                        } else if (statusJob == 'gate_out_loc_bongkar') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = green;
                            gateOutLokasiBongkar = red;
                        } else if (confirmFlag == 'Yes') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = green;
                            gateOutLokasiBongkar = green;
                        } else if (statusJob == 'to_garage') {
                            startJob = green;
                            gateInLokasiMuat = green;
                            gateInLokasiBongkar = green;
                            gateOutLokasiBongkar = red;
                        } else {
                            startJob = red;
                            gateInLokasiMuat = red;
                            gateInLokasiBongkar = red;
                            gateOutLokasiBongkar = red;
                        }
                        $('#tableBody').append(`
                            <tr class="text-center">
                                <td scope="row">${key+1}</td>
                                <td>
                                    <a href="#" onclick="getDisplay('${value.token}')">
                                        ${value.no_order}
                                    </a>
                                </td>
                                <td>${value.no_mobil}</td>
                                <td>${startJob}</td>
                                <td>${gateInLokasiMuat}</td>
                                <td>${gateInLokasiBongkar}</td>
                                <td>${gateOutLokasiBongkar}</td>
                            </tr>`);
                    });
                    if (data.onProgress > 0) {
                        $('#appendProgress').append(`
                                <h2>{{ $onProgress }} ORDER SEDANG BERJALAN <img
                                src="{{ asset('images/to-bongkar.gif') }}" alt=""
                                class="align-self-end" style="height: 60px; width: auto;"></h2>`)
                    } else {
                        $('#appendProgress').append(`<h2>Belum ada aktifitas</h2>`)
                    }

                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong, Please Refresh Page!',
                    });
                }
            });
        }

        function getDisplay(id) {
            $.ajax({
                url: "{{ url('MonitoringCheckpoint/dashboard/getDisplay') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    var header = response.header;
                    if (header.remarks_perjalanan == null) {
                        var notes = '';
                    } else {
                        var notes = header.remarks_perjalanan;
                    }
                    if (header.file_surat_jalan == null) {
                        var surat_jalan = '';
                    } else {
                        var surat_jalan =
                            `<a href="javascript:void(0)" class="btn btn-outline-dark btn-sm mt-2 mb-2" onclick="showFoto('surat_jalan', '${header.file_surat_jalan}')"> <i class="flaticon2-document"></i> Surat Jalan</a>`
                    }
                    $('.appendHeader').html("")
                    $('.appendHeader').append(`
                            <div class="col-sm-12 p-2 mt-4 mb-4" style="outline: solid; outline-width: 1px; border-radius: 18px;">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="d-flex flex-column flex-grow-1 font-weight-bold">
                                            <a href="#"
                                                class="text-dark text-hover-primary mb-1 font-size-md">${response.driver.name}</a>
                                            <span class="text-muted">${header.no_mobil} - ${header.jenis_armada}</span>
                                            <h5 class="text-dark" id="digital_clock_${header.id}"></h5>
                                            <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" onclick="getDisplay('${header.token}')">
                                                <i class="flaticon2-refresh"></i>  
                                            </a>
                                            ${surat_jalan}
                                            ${notes}
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="appendTimeline"></div>
                                    </div>
                    `)
                    if (header.start_job != null) {
                        var to_loc_muat = `<div class="timeline-item">
                                                <div class="timeline-badge bg-danger"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                        START JOB <span class="label label-inline label-dark font-weight-bolder" onclick="showFoto('garage','${header.foto_km}')"><i class="fas fa-image text-white"></i></span>
                                                    </span>
                                                    <span class="text-danger text-right">${(header.start_job)}</span>
                                                </div>
                                            </div>`
                    } else {
                        var to_loc_muat = ''
                    }
                    $('.appendTimeline').append(`${to_loc_muat}`)
                    $.each(response.detail, function(index, value) {
                        if (value.gate_in_loc_muat != null) {
                            var gate_in_loc_muat = `<div class="timeline-item">
                                                <div class="timeline-badge bg-danger"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                        GATE IN LOKASI MUAT <b>(${value.lokasi_muat})</b> <span class="label label-inline label-dark font-weight-bolder" onclick="showFoto('muat','${value.file_gate_in_loc_muat}')"><i class="fas fa-image text-white"></i></span>
                                                    </span>
                                                    <span class="text-danger text-right">${(value.f_gatein_loc_muat)}</span>
                                                </div>
                                            </div>`
                        } else {
                            var gate_in_loc_muat = ''
                        }
                        if (value.gate_out_loc_muat != null) {
                            var gate_out_loc_muat = `<div class="timeline-item">
                                                <div class="timeline-badge bg-danger"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                        GATE OUT LOKASI MUAT <b>(${value.lokasi_muat})</b> <span class="label label-inline label-dark font-weight-bolder" onclick="showFoto('muat','${value.file_gate_out_loc_muat}')"><i class="fas fa-image text-white"></i></span>
                                                    </span>
                                                    <span class="text-danger text-right">${(value.f_gateout_loc_muat)}</span>
                                                </div>
                                            </div>`
                        } else {
                            var gate_out_loc_muat = ''
                        }
                        if (value.gate_in_loc_bongkar != null) {
                            var gate_in_loc_bongkar = `<div class="timeline-item">
                                                <div class="timeline-badge bg-danger"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                        GATE IN LOKASI BONGKAR <b>(${value.lokasi_bongkar})</b> <span class="label label-inline label-dark font-weight-bolder" onclick="showFoto('bongkar','${value.file_gate_in_loc_bongkar}')"><i class="fas fa-image text-white"></i></span>
                                                    </span>
                                                    <span class="text-danger text-right">${(value.f_gatein_loc_bongkar)}</span>
                                                </div>
                                            </div>`
                        } else {
                            var gate_in_loc_bongkar = ''
                        }
                        if (value.gate_out_loc_bongkar != null) {
                            var gate_out_loc_bongkar = `<div class="timeline-item">
                                                <div class="timeline-badge bg-danger"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                        GATE OUT LOKASI BONGKAR <b>(${value.lokasi_bongkar})</b> <span class="label label-inline label-dark font-weight-bolder" onclick="showFoto('bongkar','${value.file_gate_out_loc_bongkar}')"><i class="fas fa-image text-white"></i></span>
                                                    </span>
                                                    <span class="text-danger text-right">${(value.f_gateout_loc_bongkar)}</span>
                                                </div>
                                            </div>`
                        } else {
                            var gate_out_loc_bongkar = ''
                        }
                        $('.appendTimeline').append(`
                            ${gate_in_loc_muat}
                            ${gate_out_loc_muat}
                            ${gate_in_loc_bongkar}
                            ${gate_out_loc_bongkar}
                        `)
                    });
                    if (header.back_to_garage == 'Yes') {
                        var start_back_to_garage = `<div class="timeline-item">
                                                <div class="timeline-badge bg-dark"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                       PERJALANAN KEMBALI KE GARASI
                                                    </span>
                                                    <span class="text-danger text-right">${(header.f_start_back_to_garage)}</span>
                                                </div>
                                            </div>`
                    } else {
                        var start_back_to_garage = '';
                    }
                    if (header.finish_back_to_garage != null) {
                        var finish_back_to_garage = `<div class="timeline-item">
                                                <div class="timeline-badge bg-dark"></div>
                                                <div
                                                    class="timeline-content d-flex align-items-center justify-content-between">
                                                    <span class="mr-3">
                                                       TIBA DI GARASI
                                                    </span>
                                                    <span class="text-danger text-right">${(header.f_finish_back_to_garage)}</span>
                                                </div>
                                            </div>`
                    } else {
                        var finish_back_to_garage = '';
                    }
                    $('.appendTimeline').append(`
                        ${start_back_to_garage}
                        ${finish_back_to_garage}
                    `)
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong, Please Refresh Page!',
                    });
                }
            });
        }

        function showFoto(type, foto) {
            $('#modalFoto').modal('show');
            if (type == 'surat_jalan') {
                var file = `{{ asset('public/foto/checkpoint-driver/surat-jalan/${foto}') }}`;
            } else {
                var file = `{{ asset('public/foto/checkpoint-driver/lokasi-${type}/${foto}') }}`;
            }
            $('#image').html("");
            $('#image').append(`<img id="image"
                src="${file}"
                width="400px">
            `);
        }

        function updateOrder() {

        }
    </script>
@endpush
