@extends('layouts.new.base')
@section('title', 'MKT - Online Cost Checking')
@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .card-custom {
            display: block;
            top: 0px;
            position: relative;
            background-color: #f2f8f9;
            border-radius: 4px;
            padding: 32px 24px;
            margin: 12px;
            text-decoration: none;
            z-index: 0;
            overflow: hidden;
            border: 1px solid #f2f8f9;

            &:hover {
                transition: all 0.2s ease-out;
                box-shadow: 0px 4px 8px rgba(38, 38, 38, 0.2);
                top: -4px;
                border: 1px solid #cccccc;
                background-color: orange;
            }

            &:before {
                content: "";
                position: absolute;
                z-index: -1;
                top: -16px;
                right: -16px;
                background: #00838d;
                border-radius: 32px;
                transform: scale(2);
                transform-origin: 50% 50%;
                transition: transform 0.15s ease-out;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-header">
                    <div class="card-tittle">
                        <h5>Online Cost Checking</h2>
                    </div>
                </div>
                <form action="{{ 'traceHarga' }}" id="traceHarga" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 mb-4">
                                <label for="">MoT <span class="text-danger">*</span> </label>
                                <select class="form-control selectMOT" name="mot" onchange="selectMOT(this.value)"
                                    required name="mot" style="width: 100%;">
                                    <option value="" disabled selected>Choose..</option>
                                    @foreach ($master->groupBy('mot') as $mot => $val)
                                        <option value="{{ $mot }}">{{ $mot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3 mb-4">
                                <label for="">PROD. TYPE <span class="text-danger">*</span></label>
                                <select class="form-control selectProd" name="product_type"
                                    onchange="selectProd(this.value)" required style="width: 100%;">
                                    <option value="" disabled selected>Choose..</option>
                                    @foreach ($master->groupBy('product_type') as $prod => $val)
                                        <option value="{{ $prod }}">{{ $prod }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3 mb-4">
                                <label for="">SERVICE <span class="text-danger">*</span></label>
                                <select class="form-control selectService" onchange="selectService(this.value)"
                                    name="service" required style="width: 100%;">
                                    <option value="" disabled selected>Choose..</option>
                                    {{-- @foreach ($master->groupBy('service') as $service => $val)
                                        <option value="{{ $service }}">{{ $service }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-sm-2 mb-4">
                                <label for="">VEHICLE </label>
                                <select class="form-control selectVehicle" name="vehicle_type" style="width: 100%;">
                                    <option value="" disabled selected>Choose..</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-block btn-dark mt-4" onclick="lockButton()"
                                    style="border-radius: 10px; height: 60px;"><i class="fas fa-check-circle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="destination hide">
                            <div class="appendContent">

                            </div>
                        </div>
                        <hr>
                        <div class="row contentResult mt-4 hide">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $('.selectService').select2();
        $('.selectProd').select2();
        $('.selectMOT').select2();
        $('.selectVehicle').select2();

        function lockButton() {
            var mot = $('.selectMOT').val();
            var product = $('.selectProd').val();
            var service = $('.selectService').val();
            if (mot == null || product == null || service == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select option!',
                })
            } else {
                $('.appendContent').html('')
                $('.contentResult').html('')
                $('.destination').removeClass('hide')
                $('.appendContent').append(`
                  <div class="row">
                    <div class="col-sm-2 mt-4 mb-2">
                        <label for="">ORIGIN</label>
                        <select class="form-control selectOrigin" onchange="selectOrigin(this.value)"
                            name="origin" required style="width: 100%;">
                            <option value="" selected disabled>Choose..</option>
                            @foreach ($master->where('origin', '!=', '-')->groupBy('origin') as $key => $val)
                                <option value="{{ $key }}">{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 mt-4 mb-2">
                        <label for="">CITY</label>
                        <select class="form-control selectKotaKab" onchange="selectKotaKab(this.value)"
                            name="kota_kab" required style="width: 100%;">
                            <option value="" disabled selected>Choose..</option>
                        </select>
                    </div>
                    <div class="col-sm-2 mt-4 mb-2">
                        <label for="">DESTINATION</label>
                        <select class="form-control selectDestination" onchange="selectDestination(this.value)"
                            name="destination" required style="width: 100%;">
                            <option value="" disabled selected>Choose..</option>
                        </select>
                    </div>
                    <div class="col-sm-3 mt-4">
                        <label for="">EST. SHIP DATE</label>
                        <input type="date" class="form-control" name="shipment_date" autocomplete="off"
                            placeholder="Date.." required>
                    </div>
                    <input type="text" class="form-control" id="weightVal" name="weight" value="1"
                        hidden>
                    <input type="text" class="form-control" id="cbmVal" name="cbm" value="1"
                        hidden>
                    <div class="appendOpsional">

                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-block btn-info mt-4"
                            style="border-radius: 10px; height: 60px;"><i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
                `)
                $('.selectOrigin').select2();
                $('.selectDestination').select2();
                $('.selectKotaKab').select2();
            }
        }

        function searchVehicle(val) {
            $('.selectVehicle').html("");
            $('.selectVehicle').append(`<option value="" disabled selected>Chosee..</option>`);
            if (val == 'FTL' || val == 'FCL') {
                $('.selectVehicle').append(`
                    @foreach ($master->where('vehicle_type', '!=', '-')->groupBy('vehicle_type') as $vehicle => $val)
                        <option value="{{ $vehicle }}">{{ $vehicle }}</option>
                    @endforeach
                `);
            }
        }

        function selectOrigin(val) {
            var mot = $('.selectMOT').val();
            var prod = $('.selectProd').val();
            var service = $('.selectService').val();
            var vehicle_type = $('.selectVehicle').val();
            $.ajax({
                url: "{{ url('vm-price/getKotaKab') }}/" + val + '/' + mot + '/' + prod + '/' + service + '/' +
                    vehicle_type,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('.selectKotaKab').html("");
                    $(".selectKotaKab").append('<option value="" disabled selected>Choose..</option>');
                    $.each(data, function(key, value) {
                        $(".selectKotaKab").append('<option value="' + key + '">' + key + '</option>');
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function selectKotaKab(val) {
            var origin = $('.selectOrigin').val();
            var mot = $('.selectMOT').val();
            var prod = $('.selectProd').val();
            var service = $('.selectService').val();
            var vehicle_type = $('.selectVehicle').val();
            $.ajax({
                url: "{{ url('vm-price/getSelectDestination') }}/" + origin + '/' + val + '/' + mot + '/' + prod +
                    '/' + service + '/' + vehicle_type,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('.selectDestination').html("");
                    $(".selectDestination").append('<option value="" disabled selected>Choose..</option>');
                    $.each(data, function(key, value) {
                        $(".selectDestination").append('<option value="' + key + '">' + key +
                            '</option>');
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function selectMOT(value) {
            resetForm();
            $('.selectVehicle').append(`<option value="" disabled selected>Chosee..</option>`);
        }

        function selectProd(value) {
            resetForm();
            var mot = $('.selectMOT').val();
            $.ajax({
                url: "{{ url('vm-price/getSelectService') }}/" + mot + '/' + value,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $(".selectService").html("");
                    $(".selectService").append('<option value="" disabled selected>Choose..</option>');
                    $.each(data, function(key, value) {
                        $(".selectService").append('<option value="' + key + '">' + key + '</option>');
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

        function resetForm() {
            $('.appendContent').html("");
            $('.selectOrigin').val('');
            $('.selectKotaKab').val('');
            $('.selectDestination').val('');
        }

        function selectService(value) {
            $.ajax({
                url: "{{ url('vm-price/getSelectVehicle') }}/" + value,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $(".selectVehicle").html("");
                    $(".selectVehicle").append('<option value="" disabled selected>Choose..</option>');
                    $.each(data, function(key, value) {
                        $(".selectVehicle").append('<option value="' + key + '">' + key + '</option>');
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: data,
                    })
                }
            });
        }

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
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        $('#traceHarga').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                data: $('#traceHarga').serialize(),
                url: "{{ url('vm-price/traceHarga') }}",
                type: "POST",
                dataType: 'json',
                success: function(res) {
                    $('.contentResult').show();
                    $('.appendOpsional').html("");
                    if (res.data.params.kg_flag == 'Yes') {
                        $('.appendOpsional').append(`
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label for="">Weight(Kg)</label>
                                    <input type="text" name="kg" class="form-control" onchange="setupWeight(this.value)" placeholder="" aria-describedby="helpId">
                                <small id="helpId" class="text-muted">Opsional(Gunakan simbol titik untuk angka pecahan)</small>
                            </div>
                        </div>`);
                    } else {
                        $('#weightVal').val(1)
                    }
                    if (res.data.params.cbm_flag == 'Yes') {
                        $('.appendOpsional').append(`
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label for="">CBM</label>
                                    <input type="text" name="cbm" class="form-control" onchange="setupCBM(this.value)"  placeholder="" aria-describedby="helpId">
                                <small id="helpId" class="text-muted">Opsional(Gunakan simbol titik untuk angka pecahan)</small>
                            </div>
                        </div>`);
                    } else {
                        $('#cbmVal').val(1)
                    }
                    $('.contentResult').html("");
                    if (res.data.data.length > 0) {
                        var weight = $('#weightVal').val()
                        var cbm = $('#cbmVal').val()
                        $.each(res.data.data, function(key, value) {
                            if (value.flag_expired == true) {
                                var color = 'danger';
                                var rate = `<div class="alert alert-custom alert-danger" role="alert">
                                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                                    <div class="alert-text">Expired, Silahkan Hub VM untuk melakukan update harga!</div>
                                            </div>`;
                                var cost = ``;
                            } else {
                                var color = 'success';
                                var serviceValue = $(".selectService").val();
                                if (serviceValue == 'FTL' || serviceValue == 'FCL') {
                                    var rate =
                                        `<h3> Rate: Rp. <b>${formatRupiah(value.price)}</b></h3>`;
                                    var min_charge = ``
                                    var total_cost = ``
                                } else {
                                    var rate =
                                        `<h3> Rate: Rp. <b>${formatRupiah(value.price)}</b></h3>`;
                                    var min_charge =
                                        `<label><b>Min. Charge: ${value.min_charge}Kg</b></label>`
                                    var total_cost =
                                        `<h3> Total Cost: Rp. <b>${formatRupiahNew(Math.round(value.price * value.min_charge))}</b> </h3>`
                                }
                                if (weight > 1) {
                                    if (weight > value.min_charge) {
                                        var rate =
                                            `<h3> Rate: Rp. <b>${formatRupiah(value.price)} x ${weight}Kg</b></h3>`;
                                        var total_cost =
                                            `<h3> Total Cost: Rp. <b>${formatRupiahNew(Math.round(value.price * weight))}</b> </h3>`
                                    } else {
                                        var rate =
                                            `<h3> Rate: Rp. <b>${formatRupiah(value.price)} x ${weight}Kg</b></h3>`;
                                        var total_cost =
                                            `<h3> Total Cost: Rp. <b>${formatRupiahNew(Math.round(value.price * value.min_charge))}</b> </h3>`
                                    }
                                }
                                if (cbm > 1) {
                                    var total_cost = value.price * cbm;
                                    var rate =
                                        `<h3> Rate: Rp. <b>${formatRupiah(value.price)} x ${cbm}Cbm</b></h3>`;
                                    var total_cost =
                                        `<h3> Total Cost: Rp. <b>${formatRupiahNew(Math.round(value.price * cbm))}</b> </h3>`
                                }
                                // var totalCost =  value.price;
                                // var rate = `<h3> Rate: Rp. <b>${formatRupiah(value.price)}</b></h3>`;
                                // var cost = `<h3> Total Cost: Rp. <b>${formatRupiahNew(Math.round(totalCost))}</b> </h3>`;
                            }
                            $('.contentResult').append(`
                                <div class="col-sm-12">
                                    <div class="card card-custom card-stretch gutter-b shadow-lg" style="border-radius: 20px;">
                                        <div class="card-body pt-2">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="d-flex align-items-center">
                                                        <span class="bullet bullet-bar bg-${color} align-self-stretch"></span>
                                                        <span class="flex-shrink-0 m-0 mx-4"> 
                                                            <span class="label label-dot label-${color}" style="zoom: 180%;">
                                                            </span>
                                                        </span>
                                                        <div class="d-flex flex-column flex-grow-1 mt-2">
                                                            <a class="text-dark text-hover-primary font-weight-bold font-size-lg mb-1">
                                                                <h6>
                                                                    Vendor: ${value.vendor} 
                                                                </h6> 
                                                                ${min_charge}
                                                            </a>
                                                            <span class="text-dark font-weight-bold">${rate}</span>
                                                            <span class="text-dark font-weight-bold">${total_cost}</span>
                                                        </div>
                                                        <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="Quick actions">
                                                            <button class="btn font-weight-bold btn-lg btn-dark mr-2"> <span class="fa fa-trophy text-warning"></span> RANK ${key+1} </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `);
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data not found!'
                        });
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

        function setupWeight(val) {
            $('#weightVal').val(val);
        }

        function setupCBM(val) {
            $('#cbmVal').val(val);
        }

        function formatRupiahNew(value) {
            var number_string = value.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }
    </script>
@endpush
