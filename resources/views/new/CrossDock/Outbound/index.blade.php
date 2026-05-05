@extends('layouts.new.base')
@section('title', 'MKT - Outbound')
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
                                    <a class="nav-link active" data-toggle="tab" href="#JobHeader">
                                        <span class="nav-icon"><i class="flaticon-information"></i></span>
                                        <span class="nav-text">Job Header</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="JobHeader" role="tabpanel"
                                    aria-labelledby="JobHeader">
                                    <form action="{{ route('storeHeaderOutbound') }}" method="post" id="PostForm"
                                        autocomplete="off">
                                        @csrf
                                        <input type="hidden" name="id_header" value="{{ $job_no }}" />
                                        <div class="card-body p-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Job No:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="job_no" readonly
                                                            value="{{ $job_no }}" />
                                                        <span class="form-text text-muted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Date Out:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" readonly
                                                            value="{{ formatTanggalIndonesia2($now) }}" />
                                                        <span class="form-text text-muted"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="my-select">Branch</label>
                                                        <select class="form-control" name="id_branch" required>
                                                            <option value="" selected disabled>Silahkan Pilih
                                                            </option>
                                                            @foreach ($branch as $item)
                                                                <option value="{{ $item->id }}">{{ $item->branch_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="my-select">Warehouse</label>
                                                        <select class="form-control" name="id_warehouse" required>
                                                            <option value="" selected disabled>Silahkan Pilih
                                                            </option>
                                                            @foreach ($warehouse as $item)
                                                                <option value="{{ $item->id }}">{{ $item->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="my-select">Customer</label>
                                                        <select class="form-control" name="id_customer" id="customerSelect"
                                                            style="width: 100%;" required>
                                                            <option value="" selected disabled>Silahkan Pilih
                                                            </option>
                                                            @foreach ($customer as $item)
                                                                <option value="{{ $item->id }}">{{ $item->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>PO Number:</label>
                                                        <input type="text" class="form-control form-control" required
                                                            placeholder="Silahkan isi.." name="po_no" value="" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>DO Number:</label>
                                                        <input type="text" class="form-control form-control" required
                                                            placeholder="Silahkan isi.." name="do_no" value="" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea class="form-control mt-2" id="" rows="2" name="description" placeholder="Remarks.."
                                                            autocomplete="off"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-info btn-lg mr-2"
                                                    id="submitHeader"><i class="flaticon2-checkmark"></i> Save</button>
                                            </div>
                                        </div>
                                    </form>
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
    <script type="text/javascript">
        $('#customerSelect').select2();

        function submit() {
            $('#PostForm').submit();
        }

        $('#submitHeader').on('click', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            Swal.fire({
                title: 'Do you want to save the changes?',
                icon: 'info',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save',
                denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    submit();
                } else if (result.isDenied) {
                    return false;
                }
            })
        });
    </script>
@endpush
