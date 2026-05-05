@extends('layouts.new.base')
@section('title', 'MKT - Cycle Count')
@push('styles')
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
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <form action="{{ url('inventory/cycleCount/storeJob') }}" method="post" id="form-post">
                        @csrf
                        <input type="hidden" id="typeValue" name="type">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <select class="form-control" id="selectPrincipal" name="principal_id" required>
                                        <option value="" disabled selected>Principal</option>
                                        @foreach ($principal as $item)
                                            <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="my-input">Job No</label>
                                    <input id="my-input" class="form-control" type="text" name="job_no"
                                        value="{{ $job_no }}" readonly>
                                </div> --}}
                                <div class="form-group">
                                    <select class="form-control" id="selectSite" name="site_id" onchange="siteSelect()"
                                        required>
                                        <option value="" disabled selected>Site</option>
                                        @foreach ($site as $item)
                                            <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="type" onchange="selectType(this.value)" required>
                                        <option value="" disabled selected>Type Cycle Count</option>
                                        <option value="sku">Per SKU</option>
                                        <option value="blok">Per Location</option>
                                    </select>
                                </div>
                                <div class="contentSelect">

                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" required name="description" rows="3" placeholder="DESCRIPTION"></textarea>
                                </div>
                                <div class="float-right">
                                    <a href="#upload-excel" data-toggle="modal"
                                        class="btn btn-lg mb-4 btnExcel hide text-white" style="background-color: green">
                                        <i class="fas fa-file-excel text-white"></i> Upload Excel SKU
                                    </a>
                                    <a href="#upload-excel-location" data-toggle="modal"
                                        class="btn btn-lg mb-4 btnExcelLocation hide text-white"
                                        style="background-color: green">
                                        <i class="fas fa-file-excel text-white"></i> Upload Excel Location
                                    </a>
                                    <button type="submit" class="btn btn-lg btn-info mb-4 btnSave">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="6" class="text-center">LIST LOCATION CYCLE COUNT
                                                    {{ formatTanggalIndonesia2(date('Y-m-d')) }}
                                                </th>
                                            </tr>
                                            <tr class="text-center">
                                                <th>NO</th>
                                                <th>JOB NO</th>
                                                <th>SITE AREA</th>
                                                <th>LOCATION/SKU</th>
                                                <th>DESCRIPTION</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($list_today->groupBy('job_no') as $key => $value)
                                                @foreach ($value as $item)
                                                    <tr class="text-center">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->job_no }}</td>
                                                        <td>{{ $item->site_name }}</td>
                                                        <td
                                                            style="word-wrap: break-word;
                                                        word-break: break-all;  
                                                        white-space: normal !important;
                                                        text-align: justify;">
                                                            @if ($item->type == 'sku')
                                                                {{ implode(', ', array_unique($detail[$key]->pluck('product_code')->toArray())) }}
                                                            @else
                                                                {{ implode(', ', array_unique($detail[$key]->pluck('location_code')->toArray())) }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->description }}</td>
                                                        <td>
                                                            <a href="#" onclick="deleteJob('{{ $item->job_no }}')"
                                                                class="btn btn-danger btn-sm"><i
                                                                    class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editJob" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title" id="my-modal-title">Form Edit</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('updateJob') }}" method="post" id="updateJob">
                                @csrf
                                <input class="form-control" type="text" name="job_no" value="" id="jobNoValue"
                                    hidden>
                                <input class="form-control" type="text" name="site_id" value="" id="siteIdValue"
                                    hidden>
                                <input class="form-control" type="text" name="type" value=""
                                    id="typeValueJob" hidden>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="appendContent">

                                        </div>
                                        <div class="form-group">
                                            <label for="">Description</label>
                                            <input class="form-control" type="text" name="description"
                                                id="descriptionValue" placeholder="Description" required>
                                        </div>
                                        <div class="float-right ">
                                            <button type="submit" class="btn btn-lg btn-info mt-4" id="btnUpdate"><i
                                                    class="fas fa-save"></i>
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="upload-excel" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title" id="my-modal-title">Form Upload Excel</h5>
                            <a href="#" onclick="downloadTemplate()" class="btn btn-lg mb-4 btnTemplate text-white"
                                style="background-color: green">
                                <i class="fas fa-download text-white"></i> Download Template
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('inventory/cycleCount/import') }}" enctype="multipart/form-data"
                                method="post" id="formExcel">
                                @csrf
                                <input class="form-control siteParams" type="text" name="site_id" value=""
                                    id="" hidden>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="file" name="excel" required id=""
                                                class="form-control" placeholder="" aria-describedby="helpId">
                                        </div>
                                        <div class="float-right ">
                                            <button type="submit" class="btn btn-lg btn-info mt-4" id="btnImport"><i
                                                    class="fas fa-save"></i>
                                                Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="upload-excel-location" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="modal-header">
                            <h5 class="modal-title" id="my-modal-title">Form Upload Excel Location</h5>
                            <a href="#" onclick="downloadTemplateLocation()"
                                class="btn btn-lg mb-4 btnTemplate text-white" style="background-color: green">
                                <i class="fas fa-download text-white"></i> Download Template Location
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('inventory/cycleCount/importByLocation') }}"
                                enctype="multipart/form-data" method="post" id="formExcelLocation">
                                @csrf
                                <input class="form-control siteParams" type="text" name="site_id" value=""
                                    id="" hidden>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="file" name="excel" required id=""
                                                class="form-control" placeholder="" aria-describedby="helpId">
                                        </div>
                                        <div class="float-right ">
                                            <button type="submit" class="btn btn-lg btn-info mt-4"
                                                id="btnImportLocation"><i class="fas fa-save"></i>
                                                Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function siteSelect() {
            $('.contentSelect').html("")
            $('#selectResult').html("")
        }

        function downloadTemplate() {
            var site = $('.siteParams').val();
            location.href = "{{ url('inventory/cycleCount/templateExport') }}/" + site + '/' + 'sku';
        }

        function downloadTemplateLocation() {
            var site = $('.siteParams').val();
            location.href = "{{ url('inventory/cycleCount/templateExport') }}/" + site + '/' + 'location';
        }

        function select2() {
            $('#selectResult').select2({
                'placeholder': {
                    id: '', // the value of the option
                    text: 'Please Select..'
                }
            });
        }

        $('#selectPrincipal').select2({
            'placeholder': {
                text: 'Select Principal..'
            }
        });

        function selectType(val) {
            var site = $('#selectSite').val();
            if (site == "" || site == null) {
                Swal.fire({
                    title: 'Choose Site..',
                    icon: 'warning',
                    showDenyButton: true,
                });
            } else {
                $('.siteParams').val(site)
                if (val == 'sku') {
                    $('.btnExcel').removeClass('hide')
                    $('.btnExcelLocation').addClass('hide')
                } else {
                    $('.btnExcelLocation').removeClass('hide')
                    $('.btnExcel').addClass('hide')
                }
                var principalId = $('#selectPrincipal').val()
                $.ajax({
                    url: "{{ url('inventory/cycleCount/getList') }}/" + val + '/' + site + '/' + principalId,
                    method: 'GET',
                    success: function(data) {
                        $('.contentSelect').html("")
                        $('.contentSelect').append(`
                                        <div class="form-group">
                                            <select class="form-control " id="selectResult" name="values[]"
                                                required style="width: 100%;" multiple="multiple">
                                            </select>
                                        </div>`)

                        $('#selectResult').html("")
                        select2();
                        $.each(data, function(index, value) {
                            if (val == 'sku') {
                                $('#selectResult').append(`
                                <option value="${value.product_id}">${value.product_code + '-'+ value.principal_name}</option>
                            `)
                            } else {
                                $('#selectResult').append(`
                                    <option value="${value.location_code}">${value.location_code}</option>
                                `)
                            }
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        })
                    }
                });
            }
        }

        function editJob(id) {
            $.ajax({
                url: "{{ url('inventory/cycleCount/editJob') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $('.appendContent').html("");
                        $('#editJob').modal('show');
                        $('#descriptionValue').val(data.header.description);
                        $('#typeValueJob').val(data.header.type);
                        $('#jobNoValue').val(data.header.job_no);
                        $('#siteIdValue').val(data.header.site_id);
                        if (data.header.type == 'sku') {
                            $('.appendContent').append(`
                                        <div class="form-group">
                                            <label>Add SKU</label>
                                            <select class="form-control " id="selectEdit" name="values[]"
                                                style="width: 100%;" multiple="multiple">
                                            </select>
                                        </div>`);
                            $('#selectEdit').select2();
                            $.each(data.loop, function(index, value) {
                                $('#selectEdit').append(
                                    `<option value="${value.product_id}">${value.product_code}</option>`
                                )
                            });
                        } else {
                            var locme = data.myDetail
                            $('.appendContent').append(`<div class="form-group">
                                            <label>Location</label>
                                            <input class="form-control" type="text" name="location[]" value=""
                                                id="locMeValue" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Add Location</label>
                                            <select class="form-control " id="selectEdit" name="values[]"
                                                style="width: 100%;" multiple="multiple">
                                            </select>
                                        </div>`);
                            $('#selectEdit').select2();
                            $('#locMeValue').val(locme.join());
                            $.each(data.loop, function(index, value) {
                                $('#selectEdit').append(
                                    `<option value="${value}">${value}</option>`)
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.error,
                        })
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

        function deleteJob(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Konfirm',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    location.href = "{{ url('inventory/cycleCount/deleteJob') }}/" + id
                } else if (result.isDenied) {
                    return false;
                }
            })
        }

        $('#form-post').on('submit', function(e) {
            e.preventDefault();
            var data = $('#form-post').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'not_found') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock is not available at the location you selected..',
                        });
                    } else if (data.message == 'exist') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Job already exists..',
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data has been saved successfully',
                        })
                        location.reload();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        })

        $('#updateJob').on('submit', function(e) {
            e.preventDefault();
            var data = $('#updateJob').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.error,
                        })
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        })

        $('#formExcel').on('submit', function() {
            $('#btnImport').attr('disabled', true);
        });
        $('#formExcelLocation').on('submit', function() {
            $('#btnImportLocation').attr('disabled', true);
        });
    </script>
@endpush
