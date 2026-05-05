@extends('layouts.new.base')
@section('title', 'MKT - Master Data')
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
                                    <a class="nav-link" data-toggle="tab" href="#warehouse" onclick="getWarehouse()">
                                        <span class="nav-icon"><i class="las la-warehouse"></i></span>
                                        <span class="nav-text">Warehouse</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#customer" onclick="getCustomer()">
                                        <span class="nav-icon"><i class="flaticon-network"></i></span>
                                        <span class="nav-text">Customer</span>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#location">
                                        <span class="nav-icon"><i class="flaticon-information"></i></span>
                                        <span class="nav-text">Location</span>
                                    </a>
                                </li> --}}
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#mapping">
                                        <span class="nav-icon"><i class="flaticon-user-settings"></i></span>
                                        <span class="nav-text">Mapping Site</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show " id="warehouse" role="tabpanel" aria-labelledby="warehouse">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-left">
                                                <a href="#addWarehouse" data-toggle="modal" class="btn btn-lg btn-info"><i
                                                        class="fas fa-add"></i>
                                                    Add</a>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tableWarehouse">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No</th>
                                                            <th>Branch</th>
                                                            <th>Warehouse</th>
                                                            <th>Capacity</th>
                                                            <th>#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show " id="customer" role="tabpanel" aria-labelledby="customer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-left">
                                                <a href="#addCustomer" data-toggle="modal" class="btn btn-lg btn-info"><i
                                                        class="fas fa-add"></i>
                                                    Add</a>
                                                <a href="#importCustomer" data-toggle="modal"
                                                    class="btn btn-lg btn-success"><i class="fa fa-file-excel"></i>
                                                    Import</a>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tableCustomer">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No</th>
                                                            <th>Branch</th>
                                                            <th>Customer</th>
                                                            <th>#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active " id="mapping" role="tabpanel"
                                    aria-labelledby="mapping">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-left">
                                                <a href="#addMapping" data-toggle="modal" class="btn btn-lg btn-info"><i
                                                        class="fas fa-add"></i>
                                                    Add</a>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered mt-4" id="tableMapping">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>No</th>
                                                            <th>Name</th>
                                                            <th>Username</th>
                                                            <th>Site</th>
                                                            <th>#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data_mapping as $key => $value)
                                                            <tr class="text-center">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $value['name'] }}</td>
                                                                <td>{{ $value['username'] }}</td>
                                                                <td>{{ implode(', ', $value['warehouse']) }}</td>
                                                                <td>
                                                                    <a href="#"
                                                                        onclick="deleteMapping('{{ $key }}')"
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="addWarehouse" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('addWarehouse') }}" method="post" id="formWarehouse"
                                autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select id="my-select" class="form-control" name="id_branch" required>
                                                <option value="" selected disabled>Choose</option>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-input">Warehouse</label>
                                            <input class="form-control" type="text" name="warehouse" required
                                                placeholder="Type here..">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="my-input">Capacity</label>
                                            <input class="form-control" type="number" name="capacity" required
                                                placeholder="Type here..">
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-md btn-info"><i class="fas fa-save"></i>
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editWarehouse" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('updateWarehouse') }}" method="post" id="updateWarehouse"
                                autocomplete="off">
                                @csrf
                                <input type="hidden" id="idWarehouseValue" name="id_warehouse" value="">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select class="form-control" name="id_branch" required id="branchValue">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-input">Warehouse</label>
                                            <input class="form-control" type="text" name="warehouse" required
                                                placeholder="Type here.." id="warehouseValue">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="my-input">Capacity</label>
                                            <input class="form-control" type="number" id="capacityValue"
                                                name="capacity" required placeholder="Type here..">
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

    <div id="addCustomer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('addCustomer') }}" method="post" id="formCustomer"
                                autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-select">Branch</label>
                                            <select id="my-select" class="form-control" name="id_branch" required>
                                                <option value="" selected disabled>Choose</option>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="my-input">Customer Name</label>
                                            <input class="form-control" type="text" name="customer" required
                                                placeholder="Type here..">
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-md btn-info"><i class="fas fa-save"></i>
                                        Save
                                    </button>
                                </div>
                            </form>
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

    <div id="importCustomer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('importCustomer') }}" method="post" id="importCustomer"
                                autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <a href="{{ url('assets/excel/template-customer.xlsx') }}"
                                            class="btn btn-md btn-dark"><i class="las la-download"></i> Download
                                            Template</a>
                                        <br>
                                        <div class="input-group mt-4">
                                            <input class="form-control" type="file" name="excel" placeholder=""
                                                aria-label="Recipient's text" aria-describedby="my-addon" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="my-addon">File</span>
                                            </div>
                                        </div>
                                        <div class="float-right mt-4">
                                            <button type="submit" class="btn btn-md btn-success btn-upload"><i
                                                    class="las la-upload"></i> Upload
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

    <div id="addMapping" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" data-bs-keyboard="false"
            data-bs-backdrop="static">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('addMapping') }}" method="post" id="formMapping"
                                autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="my-select">Users</label>
                                            <select class="form-control" name="id_user" id="selectUser"
                                                onchange="choseeUser(this.value)" required style="width: 100%;">
                                                <option value="" selected disabled>Choose..</option>
                                                @foreach ($users as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <h4 class="text-center"><b>ACCESS SITE</b></h4>
                                        <hr>
                                        <div class="siteAccess">

                                        </div>
                                    </div>
                                    <div class="col-sm-2">

                                    </div>
                                    <div class="col-sm-5">
                                        <h4 class="text-center"><b>UNACCESS SITE</b></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="unsiteAccess">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="hide btn btn-md btn-info btn-mapping"><i
                                            class="fas fa-save"></i>
                                        Submit
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
        $('#selectUser').select2();

        function choseeUser(id) {
            $.ajax({
                url: "{{ url('crossDock/masterData/mapping/getListMapping') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('.siteAccess').html("")
                    $.each(data.access, function(index, value) {
                        $('.siteAccess').append(`
                            <div class="form-group mt-2">
                                <div class="checkbox-inline">
                                    <label class="checkbox">
                                        <input type="checkbox" value="${value.id}" checked="checked" name="id_warehouse[]"/>
                                        <span></span>
                                        ${value.name}
                                    </label>
                                </div>
                            </div>`)
                    });

                    $('.unsiteAccess').html("")
                    $.each(data.unaccess, function(index, value) {
                        $('.unsiteAccess').append(`
                            <div class="form-group mt-2">
                                <div class="checkbox-inline">
                                    <label class="checkbox">
                                        <input type="checkbox" value="${value.id}" name="id_warehouse[]"/>
                                        <span></span>
                                        ${value.name}
                                    </label>
                                </div>
                            </div>`)
                    });
                    $('.btn-mapping').removeClass('hide');
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function getWarehouse() {
            tableWarehouse();
        }

        function tableWarehouse() {
            $('#tableWarehouse').DataTable({
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
                    url: "{{ route('getListWarehouse') }}",
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
                        data: 'capacity',
                        name: 'capacity'
                    }, {
                        data: null,
                        name: null,
                        sortable: false,
                        render: function(data) {
                            return `
                            <a href="javascript:void(0)" onclick="deleteWarehouse(${data.id})" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a> 
                            <a href="javascript:void(0)" onclick="editWarehouse(${data.id})" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>`
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        function deleteWarehouse(id) {
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

        function editWarehouse(id) {
            $.ajax({
                url: "{{ url('crossDock/masterData/warehouse/edit') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $('#editWarehouse').modal('show');
                        $('#idWarehouseValue').val(id);
                        $('#warehouseValue').val(data.name);
                        $('#capacityValue').val(data.capacity);
                        $('#branchValue').html('')
                        $('#branchValue').append(`
                            <option value="${data.id_branch}" selected>${data.branch_name}</option>
                            @foreach ($branch as $item)
                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                            @endforeach
                        `)
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

        function getCustomer() {
            tableCustomer();
        }

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

        function editCustomer(id) {
            $.ajax({
                url: "{{ url('crossDock/masterData/customer/edit') }}/" + id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $('#editCustomer').modal('show');
                        $('#idCustomerValue').val(id);
                        $('#customerValue').val(data.name);
                        $('#branchValueCust').html('')
                        $('#branchValueCust').append(`
                            <option value="${data.id_branch}" selected>${data.branch_name}</option>
                            @foreach ($branch as $item)
                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                            @endforeach
                        `)
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

        $('#updateCustomer').on('submit', function(e) {
            e.preventDefault();
            var data = $('#updateCustomer').serialize();
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
                        $('#editCustomer').modal('hide');
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

        $('#importCustomer').on('submit', function() {
            $('.btn-upload').hide();
        });

        $('#formMapping').on('submit', function(e) {
            e.preventDefault();
            var data = $('#formMapping').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if (data.message == 'null') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Please Choose Site..',
                        });
                    } else {
                        $('#addMapping').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Data was processed successfully.',
                        });
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
        });

        function deleteMapping(id) {
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
                        url: "{{ url('crossDock/masterData/mapping/delete') }}/" + id,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
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
                } else if (result.isDenied) {
                    return false;
                }
            })
        }
    </script>
@endpush
