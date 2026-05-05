@extends('layouts.new.base')
@section('title', 'MKT - OB Export')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        #modal-images .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            grid-gap: 8px;
            padding: 5px;
        }

        #modal-images .gallery img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            transition: .3s;
        }

        #modal-images .gallery img:hover {
            transform: scale(1.05);
        }

        .dropdown-menu {
            z-index: 1055 !important;
        }

        .card {
            overflow: visible !important;
        }

        .card-header {
            overflow: visible !important;
        }
    </style>
@endpush

@section('content')
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right mb-4">
                                @if ($header->confirmed_flag == 'Confirmed')
                                    <a href="javascript:void(0)" onclick="tallySheet('{{ $header->id }}')"
                                        class="btn btn-md btn-info" style="border-radius: 15px;"><i
                                            class="fas fa-file-pdf"></i> Tally Sheet</a>
                                @endif
                                @if ($header->confirmed_flag == 'Open')
                                    @if (!is_null($header->checker_confirmed_at))
                                        <a href="javascript:void(0)" onclick="confirmationJob('{{ $header->job_no }}')"
                                            class="btn btn-md btn-success" style="border-radius: 15px;"><i
                                                class="fas fa-check-circle"></i> Confirm Job</a>
                                    @endif
                                @endif
                                <a href="{{ url('/home') }}" class="btn btn-md btn-dark" style="border-radius: 15px;"><i
                                        class="fas fa-home"></i> Home</a>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-line mb-5">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#JobHeader">
                                        <span class="nav-icon"><i class="flaticon-information"></i></span>
                                        <span class="nav-text">Job Header</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#jobDetail">
                                        <span class="nav-icon"><i class="flaticon-list-3"></i></span>
                                        <span class="nav-text">Job Detail</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="JobHeader" role="tabpanel"
                                    aria-labelledby="JobHeader">
                                    <form action="{{ route('export.ob.store') }}" method="post" id="PostForm">
                                        @csrf
                                        <div class="card-body p-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>PEB Number</label>
                                                        <input type="text" class="form-control" id="peb_number"
                                                            placeholder="Silahkan isi" value="{{ $header->peb_no }}"
                                                            name="peb_number" disabled autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>AJU Number</label>
                                                        <input type="text" class="form-control" id="aju_number"
                                                            placeholder="Silahkan isi" value="{{ $header->aju_no }}"
                                                            name="aju_number" disabled autocomplete="off" />
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Forwarder</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="forwarder" value="{{ $header->forwarder_name }}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Qty</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="qty" value="{{ $detail->sum('quantity') }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Shipper</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="shipper" value="{{ $header->shipper_name }}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Total Pallet</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="totalPallet" disabled value="{{ $detail->count() }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>vehcile No</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="vehicle_no" disabled value="{{ $header->vehicle_no }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>PIC Penerima</label>
                                                        <input type="text" class="form-control" name="pic"
                                                            placeholder="Silahkan isi.." autocomplete="off" disabled
                                                            value="{{ $header->pic_name }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Destination</label>
                                                        <input type="text" class="form-control" name="destination"
                                                            placeholder="Silahkan isi.." autocomplete="off" disabled
                                                            value="{{ $header->destination }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="">Remarks</label>
                                                        <textarea class="form-control" name="remarks" id="" rows="3" disabled>{{ $header->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade show" id="jobDetail" role="tabpanel"
                                    aria-labelledby="jobDetail">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="card card-custom gutter-b">
                                                <div class="card-header border-0">
                                                    <h3 class="card-title font-weight-bolder text-dark">Stapel</h3>
                                                    <div class="card-toolbar">
                                                        <div class="dropdown dropdown-inline">
                                                            @if (is_null($header->stapel_confirmed_at))
                                                                <div class="form-group">
                                                                    <select class="form-control" name=""
                                                                        id="staple_select"
                                                                        onchange="chooseStapel(this.value)">
                                                                        <option value="" disabled selected>Choose
                                                                        </option>
                                                                        @foreach ($stapel as $item)
                                                                            <option value="{{ $item->username }}">
                                                                                {{ $item->username }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-2">
                                                    <div class="d-flex align-items-center mb-10">
                                                        <div class="symbol symbol-40 symbol-light-success mr-5">
                                                            <span class="symbol-label">
                                                                <img src="{{ asset('assets/new/media/svg/avatars/009-boy-4.svg') }}"
                                                                    class="h-90 align-self-end" alt="">
                                                            </span>
                                                        </div>
                                                        <div class="d-flex flex-column flex-grow-1 font-weight-bold">
                                                            <a href="#"
                                                                class="text-dark text-hover-primary mb-1 font-size-md">{{ is_null($header->stapel_name) ? '-' : Str::Upper($header->stapel_name) }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card card-custom gutter-b">
                                                <div class="card-header border-0">
                                                    <h3 class="card-title font-weight-bolder text-dark">Checker</h3>
                                                    <div class="card-toolbar">
                                                        @if (is_null($header->checker_confirmed_at) && !is_null($header->stapel_confirmed_at))
                                                            <div class="form-group">
                                                                <select class="form-control" name=""
                                                                    id="staple_select"
                                                                    onchange="chooseChecker(this.value)">
                                                                    <option value="" disabled selected>Choose
                                                                    </option>
                                                                    @foreach ($checker as $item)
                                                                        <option value="{{ $item->username }}">
                                                                            {{ $item->username }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-body pt-2">
                                                    <div class="d-flex align-items-center mb-10">
                                                        <div class="symbol symbol-40 symbol-light-success mr-5">
                                                            <span class="symbol-label">
                                                                <img src="{{ asset('assets/new/media/svg/avatars/007-boy-2.svg') }}"
                                                                    class="h-90 align-self-end" alt="">
                                                            </span>
                                                            <a href="#"
                                                                class="text-dark text-hover-primary mb-1 font-size-md text-center">{{ is_null($header->checker_name) ? '-' : Str::Upper($header->checker_name) }}</a>
                                                        </div>
                                                        @if ($header->confirmed_flag == 'Open')
                                                            <div class="d-flex flex-column flex-grow-1 font-weight-bold">
                                                                <a href="javascript:void(0)" onclick="showImages()"
                                                                    class="badge badge-pill badge-dark"> <i
                                                                        class="fas fa-images text-white"></i> Show
                                                                    Images</a>
                                                                <hr>
                                                                @if (!is_null($header->checker_confirmed_at))
                                                                    <a href="javascript:void(0)"
                                                                        onclick="returnToChecker('{{ $header->id }}')"
                                                                        class="badge badge-pill badge-danger"> <i
                                                                            class="fas fa-reply text-white"></i> Return to
                                                                        Checker</a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="detailTable">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width: 5%;">No</th>
                                                            <th>PEB No</th>
                                                            <th>Qty</th>
                                                            <th>Pallet ID</th>
                                                            <th>Location Code</th>
                                                            <th>PO No</th>
                                                            <th>Weight (Kg)</th>
                                                            <th>Volume (m3)</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($detail as $key => $item)
                                                            @php
                                                                if (is_null($item->stapel_scan_pallet_at)) {
                                                                    $status = 'Menunggu Stapel';
                                                                } elseif (
                                                                    !is_null($item->stapel_scan_pallet_at) &&
                                                                    is_null($item->checker_scan_pallet_at)
                                                                ) {
                                                                    $status = 'Menunggu Checker';
                                                                } else {
                                                                    $status = 'Completed';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td>{{ $item->peb_no }}</td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>{{ $item->pallet_id }}</td>
                                                                <td>{{ $item->location_code }}</td>
                                                                <td>{{ explode('-', $item->serial_no)[0] }}</td>
                                                                <td>{{ $item->weight }}</td>
                                                                <td>{{ $item->cbm }}</td>
                                                                <td>{{ $status }}</td>
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

    <div class="modal fade" role="dialog" id="modal-images" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-body">
                        <div class="gallery"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        function showImages() {
            var job_no = '{{ $header->job_no }}';
            $.ajax({
                url: "{{ url('export/ob/showImages') }}/" + job_no,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let container = $('#modal-images .modal-body .gallery');
                    container.empty();
                    $.each(response.data, function(index, item) {
                        var url =
                            `{{ asset('public/foto/warehouse-export/ob-cargo/') }}/${item.file}`;
                        container.append(`
                            <img src="${url}" data-id="${item.id}" style="cursor:pointer;max-width:130px;margin:5px;border-radius:6px;"
                                onclick="deleteImage(${item.id})">
                        `);
                    });
                    $('#modal-images').modal('show');
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }

        function deleteImage(id) {
            if (!confirm('Yakin hapus foto ini?')) return;
            $.ajax({
                url: "{{ url('/export/inbound/deleteImage') }}/" + id,
                type: "GET",
                success: function(res) {
                    showImages();
                },
                error: function() {
                    alert("Gagal hapus");
                }
            })
        }

        $(function() {
            $("#peb_number").autocomplete({
                source: "{{ route('autocomplete.peb') }}",
                minLength: 2,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    loadLedgerDetail({
                        peb: ui.item.value
                    });
                }
            });

            $("#aju_number").autocomplete({
                source: "{{ route('autocomplete.aju') }}",
                minLength: 2,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    loadLedgerDetail({
                        aju: ui.item.value
                    });
                }
            });
        });

        function loadLedgerDetail(params) {
            $.ajax({
                url: "{{ url('export/ob/getDetail') }}",
                data: params,
                success: function(res) {
                    if (res.success) {
                        $("#forwarder").val(res.forwarder);
                        $("#shipper").val(res.shipper);
                        $("#qty").val(res.qty);
                        $("#totalPallet").val(res.total_pallet);
                        $("#vgm").val(res.vgm);
                    } else {
                        $("#forwarder, #shipper, #qty").val('');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data ledger');
                }
            });
        }

        function chooseStapel(username) {
            let userConfirmed = confirm("Are you sure you want to choose Stapel: " + username + "?");
            if (userConfirmed) {
                window.location.href = "{{ url('export/ob/chooseStapel/' . $header->id) }}/" + username;
            } else {
                return false;
            }
        }

        function chooseChecker(username) {
            let userConfirmed = confirm("Are you sure you want to choose Checker: " + username + "?");
            if (userConfirmed) {
                window.location.href = "{{ url('export/ob/chooseChecker/' . $header->id) }}/" + username;
            } else {
                return false;
            }
        }

        function confirmationJob(job_no) {
            let userConfirmed = confirm("Are you sure you want to confirm job?");
            if (userConfirmed) {
                window.location.href = "{{ url('export/ob/confirmationJob') }}/" + job_no;
            } else {
                return false;
            }
        }

        function tallySheet(job_id) {
            let url = "{{ url('export/ob/tally_sheet') }}/" + job_id
            window.open(url, '_blank')
        }

        function returnToChecker() {
            confirmation = confirm("Are you sure want to return this job to checker?");
            if (confirmation) {
                executeBacktoChecker();
            } else {
                return false;
            }

        }

        function executeBacktoChecker() {
            var job_id = '{{ $header->id }}';
            $.ajax({
                url: "{{ url('export/ob/backtoChecker') }}/" + job_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    alert('Job has been returned to checker successfully.');
                    location.reload();
                },
                error: function() {
                    alert('Internal Server Error, Please refresh page and try again..');
                }
            });
        }
    </script>
@endpush
