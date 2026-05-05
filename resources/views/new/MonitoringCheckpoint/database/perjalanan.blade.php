@extends('layouts.new.base')
@section('title', 'MKT - Database Perjalanan')
@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <input type="date" class="form-control" required value="{{ date('Y-m-01') }}" id="startDate">
                        </div>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" required value="{{ date('Y-m-t') }}" id="endDate">
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <select id="statusJob" class="form-control">
                                    <option value="No" selected>Open</option>
                                    <option value="Yes">Confirmed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <a href="#" onclick="searchData()" class="btn btn-block btn-dark"><i
                                    class="fas fa-search"></i>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <div class="float-right">
                                <a href="{{ url('MonitoringCheckpoint/planner') }}" class="btn btn-lg btn-light-info"><i
                                        class="fas fa-add"></i> Add New Job
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableList">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Job No</th>
                                            <th>No Order</th>
                                            <th>Customer</th>
                                            <th>Revenue</th>
                                            <th>Cost</th>
                                            <th>Additional Revenue/Cost</th>
                                            <th>Download Image</th>
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

    <div id="modal-revenue" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('submitAdditionalRevenueCost') }}" method="post" id="postRevenueCost">
                    @csrf
                    <input type="hidden" name="token" required class="tokenValue">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th colspan="3">No. Order : <b class="orderNo"></b></th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>REVENUE</th>
                                            <th>COST</th>
                                            <th>REMARKS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="detailRevenueCost">
                                        <tr>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Additional Revenue</label>
                                    <input type="text" name="revenue" required class="form-control revenueValue"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Additional Cost</label>
                                    <input type="text" name="cost" required class="form-control costValue"
                                        placeholder="Silahkan di isi.." aria-describedby="helpId" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="remarksValue" rows="5" placeholder="Silahkan isi.."></textarea>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info btnSubmit"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-order-no" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('updateOrderNo') }}" method="post" id="updateOrderNo">
                    @csrf
                    <input type="hidden" name="token" required class="tokenValueUpdateOrderNo">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">No. Order Old</label>
                                    <input type="text" name="" disabled id="orderNo" class="form-control"
                                        placeholder="" aria-describedby="helpId">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">No. Order New</label>
                                    <input type="text" name="no_order" id="orderNoNew" class="form-control"
                                        aria-describedby="helpId" required autocomplete="off"
                                        placeholder="Silahkan Di isi..">
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-md btn-info btnUpdateOrderNo"><i
                                            class="fas fa-save"></i>
                                        Update</button>
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
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        function searchData() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var statusJob = $('#statusJob').val();

            $('#tableList').DataTable().clear().destroy()
            $('#tableList').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ordering: false,
                paging: false,
                "columnDefs": [{
                    "className": "dt-center",
                    "targets": "_all"
                }],
                ajax: {
                    url: "{{ url('MonitoringCheckpoint/planner/getListDatabasePerjalanan') }}/" + startDate + "/" +
                        endDate + "/" + statusJob,
                    type: "GET",
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
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a href="{{ url('MonitoringCheckpoint/planner/historyPerjalanan/${data.token}') }}">${data.job_no}</a>`;
                        },
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a class="btn btn-sm btn-success" onclick="editOrderNo('${data.token}', '${data.no_order}')"><i class="fas fa-edit"></i> ${data.no_order}</a>`;
                        },
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer'
                    },
                    {
                        data: 'revenue',
                        name: 'revenue'
                    },
                    {
                        data: 'cost',
                        name: 'cost'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a class="btn btn-sm btn-info" onclick="detailRevenueCost('${data.token}')"><i class="fas fa-add"></i> </a>`;
                        },
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.confirmed_flag == 'No') {
                                var image =
                                    '-'
                            } else {
                                var image =
                                    `<a class="btn btn-sm btn-dark" href="{{ url('MonitoringCheckpoint/planner/downloadFotoPerjalanan/${data.token}') }}"><i class="fas fa-download"></i> </a>`
                            }
                            return image;
                        },
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.confirmed_flag == 'No') {
                                var status =
                                    '<span class = "badge badge-primary">Open</span>'
                            } else {
                                var status =
                                    '<span class="badge badge-success">Confirmed</span>'
                            }
                            return status;
                        },
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }

        function editOrderNo(token, no_order) {
            $('.tokenValueUpdateOrderNo').val(token)
            $('#modal-order-no').modal('show')
            $('#orderNo').val(no_order)
        }

        function detailRevenueCost(token) {
            $.ajax({
                type: "GET",
                url: "{{ url('MonitoringCheckpoint/planner/detailRevenueCost') }}/" + token,
                dataType: "json",
                success: function(response) {
                    $('.tokenValue').val(token)
                    $('#modal-revenue').modal('show');
                    $('.detailRevenueCost').html('');
                    $('.orderNo').text(response.header.no_order);
                    $.each(response.data, function(key, value) {
                        var remarks = value.remarks;
                        $('.detailRevenueCost').append(`
                              <tr class="text-center">
                                    <td>${value.formatRevenue}</td>
                                    <td>${value.formatCost}</td>
                                    <td>${remarks == null ? '-' : remarks}</td>
                              </tr>`);
                    });
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

        $('#postRevenueCost').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $('.btnSubmit').attr('disabled', true);
            var remarks = $('#remarksValue').val();
            if (remarks == '' && remarks == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks wajib di isi..',
                })
            } else {
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $('.btnSubmit').attr('disabled', false);
                        if (response.message == 'success') {
                            $('#modal-revenue').modal('hide');
                            searchData();
                        }
                    },
                    error: function(error) {
                        $('.btnSubmit').attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        })
                    }
                });
            }
        })

        $('#updateOrderNo').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $('.btnUpdateOrderNo').attr('disabled', true);
            var validate = $('#orderNoNew').val();
            if (validate == '' && validate == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No. Order wajib di isi..',
                })
            } else {
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $('.btnUpdateOrderNo').attr('disabled', false);
                        if (response.message == 'success') {
                            $('#modal-order-no').modal('hide')
                            searchData();
                        }
                    },
                    error: function(error) {
                        $('.btnUpdateOrderNo').attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Internal Server Error..',
                        })
                    }
                });
            }
        })
    </script>
@endpush
