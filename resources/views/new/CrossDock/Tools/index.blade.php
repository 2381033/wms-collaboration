@extends('layouts.new.base')
@section('title', 'MKT - Tools Administrator')
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
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right mb-4">
                                <a href="{{ url('crossDock') }}" class="btn btn-md btn-dark" style="border-radius: 15px;"><i
                                        class="flaticon2-arrow-2"></i>
                                    Dashboard</a>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-line mb-5">
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#inbound" onclick="getListInbound()">
                                        <span class="nav-icon"><i class="las la-tasks"></i></span>
                                        <span class="nav-text">Inbound</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show" id="inbound" role="tabpanel" aria-labelledby="inbound">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="inboundTable">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>#</th>
                                                            <th>Job No</th>
                                                            <th>Branch</th>
                                                            <th>Warehouse</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

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

    <div id="editCustomer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('updateCustomer') }}" method="post" id="updateCustomer"
                                autocomplete="off">
                                @csrf
                                <input type="hidden" id="idCustomerValue" name="id_customer" value="">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required id="branchValueCust">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-input">Customer Name</label>
                                            <input class="form-control" type="text" name="customer" required
                                                placeholder="Type here.." id="customerValue">
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-md btn-info"><i class="fas fa-save"></i>
                                        Update
                                    </button>
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
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        function getListInbound() {
            sessionStorage.setItem('type', 'inbound')
            tableInbound();
        }

        function tableInbound() {
            var jobType = sessionStorage.getItem('type');
            $('#inboundTable').DataTable({
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                "paging": false,
                processing: true,
                serverSide: true,
                destroy: true,
                scrollx: true,
                "ordering": false,
                ajax: {
                    url: "{{ url('crossDock/tools/getListInbound') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data) {
                            return `<a href="javascript:void(0)" onclick="editHeader(${data.id})" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> Header</a>
                            <a href="javascript:void(0)" onclick="editCargoInbound('${data.id}')" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i> Cargo</a>
                            <a href="javascript:void(0)" onclick="showJobInbound('${data.id}')" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Show</i></a>
                            `
                        }
                    },
                    {
                        data: 'job_no',
                        name: 'job_no',
                    },
                    {
                        data: 'branch',
                        name: 'branch',
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse',
                    }, {
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data) {
                            if (data.confiremd_flag == 'confirmed') {
                                return `<span class="badge badge-pill badge-primary">Confirmed</span>`
                            } else {
                                return `<span class="badge badge-pill badge-danger">Open</span>`
                            }
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        function openJobInbound(id) {
            window.open("{{ url('crossDock/inbound/showJobFrontend') }}/" + id)
        }

        function deleteJobInbound(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('crossDock/masterData/warehouse/delete') }}/" + id,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
                                tableWarehouse();
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'error',
                                title: data,
                            })
                        }
                    });
                } else if (result.isDenied) {
                    return false;
                }
            })
        }


        $('#formWarehouse').on('submit', function(e) {
            e.preventDefault();
            var data = $('#formWarehouse').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'duplicate') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Warehouse..',
                        });
                    } else {
                        $('#addWarehouse').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        tableWarehouse();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });

        $('#updateWarehouse').on('submit', function(e) {
            e.preventDefault();
            var data = $('#updateWarehouse').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'duplicate') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Warehouse..',
                        });
                    } else {
                        $('#editWarehouse').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        tableWarehouse();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });



        function tableCustomer() {
            $('#tableCustomer').DataTable({
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                "paging": false,
                processing: true,
                serverSide: true,
                destroy: true,
                scrollx: true,
                "ordering": false,
                ajax: {
                    url: "{{ route('getListCustomer') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        name: 'number',
                        sortable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1
                        }
                    },
                    {
                        data: 'branch',
                        name: 'branch',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data) {
                            return `
                            <a href="javascript:void(0)" onclick="deleteCustomer(${data.id})" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a> 
                            <a href="javascript:void(0)" onclick="editCustomer(${data.id})" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>`
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        function deleteCustomer(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                denyButtonText: `Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('crossDock/masterData/customer/delete') }}/" + id,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
                                tableCustomer();
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'error',
                                title: data,
                            })
                        }
                    });
                } else if (result.isDenied) {
                    return false;
                }
            })
        }


        $('#formCustomer').on('submit', function(e) {
            e.preventDefault();
            var data = $('#formCustomer').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'duplicate') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Customer..',
                        });
                    } else {
                        $('#addCustomer').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
                        tableCustomer();
                    }
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        });
    </script>
@endpush
