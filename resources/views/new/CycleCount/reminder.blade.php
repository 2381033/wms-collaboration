@extends('layouts.new.base')
@section('title', 'MKT - Cycle Count Reminder')
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
    <div class="container" style="zoom: 120%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select class="form-control" name="branch_id" id="selectBranch" required>
                                    <option value="" selected disabled>Choose Branch</option>
                                    @foreach ($branch as $item)
                                        <option value="{{ $item->branch_id }}">{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select class="form-control" name="principal_id[]" id="selectPrincipal" required
                                    multiple="multiple" style="width: 100%">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script type="text/javascript">
        $('#selectPrincipal').select2();
        $('#selectBranch').on('change', function() {
            var branch_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{ url('inventory/cycleCount/getPrincipal') }}/" + branch_id,
                success: function(data) {
                    $('#selectPrincipal').html('');
                    $.each(data, function(key, value) {
                        $('#selectPrincipal').append(
                            `<option value="${value.principal_id}">${value.principal_name}</option>`
                        );
                    });
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error',
                    });
                }
            });
        });

        function searchData() {
            var job_no = sessionStorage.getItem('job_no');
            var location = sessionStorage.getItem('location');
            $.ajax({
                type: "GET",
                url: "{{ url('inventory/cycleCount/getListData') }}/" + job_no + '/' + location,
                success: function(data) {
                    if (data.message == "not_found") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data not found',
                        });
                        $('.appendList').html('');
                        $('#selectLocation').html('');
                    } else {
                        appendKonten(data)
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error',
                    });
                }
            });
        }

        $('#form-post').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var product_code_value = $('.selectSKU').val();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.data == 'ok') {
                        if (product_code_value == 'ALL') {
                            search();
                        } else {
                            cariMaterial(product_code_value);
                        }
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Internal Server Error..',
                    })
                }
            });
        })

        $('#postStokTransfer').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        searchData();
                        $('#stokTransfer').modal('hide');
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: error,
                    })
                }
            });
        })

        function appendKonten(array) {
            $('.appendList').html('');
            $('#selectLocation').html('');
            $('#selectLocation').append(`<option value="" selected disabled>Choose</option>`);
            $.each(array.location, function(key, value) {
                $('#selectLocation').append(
                    `<option value="${value}">${value}</option>`);
            });
            $('#selectLocation').append(`<option value="All">ALL LOCATION</option>`);
            $.each(array.data, function(key, value) {
                $('.appendList').append(`<div class="card card-custom bg-light-warning mb-4" style="outline: solid; border-radius: 15px;">
                                            <div class="card-header ribbon ribbon-right">
                                                <div class="ribbon-target bg-info" style="top: 10px; right: -5px; zoom:150%;"><a href="javascript:void(0)" onclick="klikOK(${value.id})" class="text-white">OK
                                                    </a>
                                                </div>
                                                <h1 class="card-title">${value.product_code} | ${value.product_name} </h1>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-sm-8">
                                                                <h1>${value.qtya} ${value.puom} </h1>
                                                                <hr>
                                                                <h5 class="mt-2">
                                                                    <b>Location : ${value.location_code} </b> 
                                                                    <a class="btn btn-md btn-danger mb-3 ml-4" onclick="variance('${value.id}')"><i class="fas fa-info-circle"></i> Variance</a>
                                                                        </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>`)
            });
        }

        function variance(id) {
            Swal.fire({
                title: 'Variance Reason',
                input: 'textarea',
                inputLabel: 'Remarks Variance',
                inputPlaceholder: 'Input here...',
                inputAttributes: {
                    'aria-label': 'Remarks Variance'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Remarks wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let remarks = result.value;
                    submitVariance(id, remarks);
                }
            });
        }

        function submitVariance(id, remarks) {
            $.ajax({
                url: "{{ url('inventory/cycleCount/submitVariance') }}",
                type: 'POST',
                data: {
                    id: id,
                    remarks: remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Variance berhasil disimpan'
                    });
                    searchData();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan variance'
                    });
                }
            });
        }
    </script>
@endpush
