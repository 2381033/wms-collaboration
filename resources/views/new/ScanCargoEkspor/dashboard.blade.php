@extends('layouts.new.base')
@section('title', 'MKT - Scan Cargo')
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
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card card-custom card-stretch gutter-b bg-light-info p-0"
                                style="border-radius: 25px;" onclick="menu('scan')">
                                <div class="card-body d-flex align-items-center py-0 mt-8">
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span
                                            class="card-title font-weight-bolder text-dark font-size-h5 mb-2 text-hover-danger">Start
                                            Scanning
                                            Cargo</span>
                                        <span class="font-weight-bold text-dark font-size-xl">Let's start scanning your
                                            cargo now!</span>
                                    </div>
                                    <img src="{{ asset('images/scan.png') }}" alt="" class="align-self-end h-120px">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card card-custom card-stretch gutter-b bg-light-primary p-0"
                                style="border-radius: 25px;" onclick="menu('jobDetails')">
                                <div class="card-body d-flex align-items-center py-0 mt-8">
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span
                                            class="card-title font-weight-bolder text-dark font-size-h5 mb-2 text-hover-danger">Job
                                            Details</span>
                                        <span class="font-weight-bold text-dark font-size-xl">Let's see job detail!</span>
                                    </div>
                                    <img src="{{ asset('images/reporting.png') }}" alt=""
                                        class="align-self-end h-120px">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card konten hide" style="outline: solid; black; border-radius: 15px;"
                                id="konten">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="float-right">
                                                <a href="#" class="btn btn-sm btn-dark" onclick="goHome()"><i
                                                        class="fas fa-home"></i>Dashboard</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="appendContent">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="scanpo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="my-modal-title">Job Details</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <center>
                                        <div id="qr-reader" style="width: 350px;"></div>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/qrcode/html5-qrcode.min.js"></script>
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 10,
            qrbox: 250,
            // rememberLastUsedCamera: true
        });
        html5QrcodeScanner.render(onScanSuccess);

        function onScanSuccess(barcode, decodedResult) {
            html5QrcodeScanner.pause();
            $('.POValue').val(barcode);
            $('#scanpo').modal('hide');
        }

        function scanPO() {
            $('#scanpo').modal('show');
        }

        function menu(menu) {
            sessionStorage.setItem('menu', menu);
            $('.card-custom').toggle('fast')
            $('.' + menu).toggle('fast');
            $('.konten').toggle('fast')
            $('.konten').removeClass('hide')
            if (menu == 'scan') {
                $('.appendContent').html('')
                startScanning();
            } else {
                $('.appendContent').html('')
                jobDetails();
            }
        }

        function jobDetails() {
            $('.appendContent').append(`<div class="row">
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required
                                                value="{{ date('Y-m-01') }}" id="startDate">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" required
                                                value="{{ date('Y-m-t') }}" id="endDate">
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
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tableList">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Job No</th>
                                                            <th>PO No</th>
                                                            <th>Qty</th>
                                                            <th>Remarks</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>`)
        }

        function goHome() {
            $('.konten').toggle('fast')
            $('.card-custom').toggle('fast')
            $('.konten').addClass('hide')
        }

        function startScanning() {
            $('.appendContent').append(`
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{ route('storeHeader') }}" method="post" id="formStoreHeader">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-sm-6">
                                    <label>No. PO</label> <a href="javascript:void(0)" class="btn btn-sm btn-primary mb-2" onclick="scanPO()"><i class="fas fa-camera"></i> Scan</a> 
                                    <input type="text" name="po_no" class="form-control POValue" required readonly
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-sm-6 mt-4">
                                    <label>Qty</label>
                                    <input type="number" name="qty" class="form-control qtyValue" required placeholder="Type here.." 
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="form-group">
                                        <label for="">Remarks</label>
                                        <textarea class="form-control" name="remarks" id="" rows="3" placeholder="Type Here.."></textarea>
                                        <span class="text-muted"> *Opsional</span>
                                    </div>
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-lg btn-info"><i class="fas fa-save"></i> Submit</button>
                            </div>
                        </form>
                    </div>
                </div>`)
            $('#formStoreHeader').on('submit', function(e) {
                e.preventDefault();
                var no_po = $('.POValue').val();
                var qtyValue = $('.qtyValue').val();
                if (no_po == "" || qtyValue == "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please enter PO or Qty',
                    })
                } else {
                    $.ajax({
                        data: $('#formStoreHeader').serialize(),
                        url: "{{ url('export/ScanCargoEkspor/storeHeader') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {
                            location.href = "{{ url('export/ScanCargoEkspor/encryptJob') }}/" + response
                                .data;
                        },
                        error: function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: error,
                            })
                        }
                    });
                }
            })
        }

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
                    url: "{{ url('export/ScanCargoEkspor/getListJobTable') }}/" + startDate + "/" + endDate + "/" +
                        statusJob,
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.confirmed_flag == 'No') {
                                var url =
                                    `{{ url('export/ScanCargoEkspor/encryptJob') }}` + '/' + data.job_no
                                var href = `window.open('${url}')`;
                                var job_no =
                                    `<a href="javascript:void(0)" onclick="${href}">${data.job_no}</a>`
                            } else {
                                var url =
                                    `{{ url('export/ScanCargoEkspor/exportExcel') }}` + '/' + data.job_no
                                var href = `window.open('${url}')`;
                                var job_no =
                                    `<a href="javascript:void(0)" class="btn btn-sm text-white" style="background-color: #28A745;" onclick="${href}"><i class="fas fa-file-excel text-white"> </i> ${data.job_no}</a>`
                            }
                            return job_no;
                        },
                    },
                    {
                        data: 'po_no',
                        name: 'po_no'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            return `${data.qty} CTN`;
                        },
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.remarks == null) {
                                var remarks = '-';
                            } else {
                                var remarks = `${data.remarks}`;
                            }
                            return remarks;
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
    </script>
@endpush
