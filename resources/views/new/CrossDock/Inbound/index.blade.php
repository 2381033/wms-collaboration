@extends('layouts.new.base')
@section('title', 'MKT - Inbound')
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
                                {{-- <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#CargoDetail">
                                        <span class="nav-icon"><i class="flaticon2-open-box"></i></span>
                                        <span class="nav-text">Cargo Detail</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#MappingPallet">
                                        <span class="nav-icon"><i class="flaticon-clipboard"></i></span>
                                        <span class="nav-text">Mapping Pallet</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Confirmation">
                                        <span class="nav-icon"><i class="flaticon2-checkmark"></i></span>
                                        <span class="nav-text">Confirmation</span>
                                    </a>
                                </li> --}}
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="JobHeader" role="tabpanel"
                                    aria-labelledby="JobHeader">
                                    <form action="{{ route('storeHeader') }}" method="post" id="PostForm">
                                        @csrf
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
                                                        <label>Date In:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            placeholder="Enter full name" name="date_in" readonly
                                                            value="{{ formatTanggalIndonesia2($now) }}" />
                                                        <span class="form-text text-muted"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
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
                                                    <div class="form-group">
                                                        <textarea class="form-control mt-2" name="remarks" id="" rows="2" name="description"
                                                            placeholder="Remarks" autocomplete="off"></textarea>
                                                    </div>
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
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>Vehicle Number</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="vehicle_number"
                                                                    required autocomplete="off" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-select">Vehicle Type</label>
                                                                <select class="form-control" name="vehicle" id="my-select"
                                                                    required>
                                                                    <option value="" selected disabled>Silahkan
                                                                        Pilih
                                                                    </option>
                                                                    @foreach ($vehicle as $item)
                                                                        <option value="{{ $item->name }}">
                                                                            {{ $item->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Driver Name</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="driver_name" required
                                                                    autocomplete="off" />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>Container Number</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="container_number"
                                                                    autocomplete="off" required />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="my-select">Size</label>
                                                                <select class="form-control" name="size"
                                                                    id="my-select" required>
                                                                    <option value="" selected disabled>Silahkan
                                                                        Pilih
                                                                    </option>
                                                                    @foreach ($vehicleSize as $item)
                                                                        <option value="{{ $item->name }}">
                                                                            {{ $item->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Transporter Name</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Silahkan isi" name="transporter_name"
                                                                    autocomplete="off" required />
                                                            </div>
                                                        </div>
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
                                {{-- <div class="tab-pane fade" id="CargoDetail" role="tabpanel"
                                    aria-labelledby="CargoDetail">
                                    Tab content 2
                                </div>
                                <div class="tab-pane fade" id="MappingPallet" role="tabpanel"
                                    aria-labelledby="MappingPallet">
                                    Tab
                                    content 4
                                </div>
                                <div class="tab-pane fade" id="Confirmation" role="tabpanel"
                                    aria-labelledby="Confirmation">
                                    Tab content 5
                                </div> --}}
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
