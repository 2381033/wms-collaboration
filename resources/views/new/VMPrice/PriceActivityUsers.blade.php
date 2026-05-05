@extends('layouts.new.base')
@section('title', 'MKT - Activity Users')
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
    <div class="container">
        <div class="main-body">
            <div class="card">
                <div class="card-header">
                    <div class="card-tittle">
                        <h5>Activity List Today</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Tools</th>
                                    <th>User</th>
                                    <th>Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->groupBy('user_id') as $key => $item)
                                    <tr class="text-center">
                                        <td scope="row">{{ $loop->iteration }}</td>
                                        <td scope="row">
                                            <a href="#" onclick="detailActivity('{{ $key }}')"
                                                class="btn btn-dark btn-md">
                                                Show <i class="fas fa-eye ml-2"></i>
                                            </a>
                                        </td>
                                        <td>{{ DB::table('users')->Where('id', $key)->value('name') }}</td>
                                        <td> <span class="badge badge-primary">{{ $data->where('user_id', $key)->count() }}
                                                Activity</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body" style="overflow-y: scroll; height: 550px;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="timeline timeline-justified timeline-4">
                                    <div class="timeline-bar"></div>
                                    <div class="timeline-items">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function detailActivity(user_id) {
            $.ajax({
                url: "{{ url('vm-price/detailActivity') }}/" + user_id,
                type: "GET",
                dataType: 'json',
                success: function(data) {
                    $('#modal-detail').modal('show')
                    $('.timeline-items').html("")
                    $('.timeline-content').html("")
                    $.each(data, function(key, value) {
                        $('.timeline-items').append(`
                                <div class="timeline-item">
                                    <div class="timeline-badge">
                                        <div class="bg-info"></div>
                                    </div>
                                    <div class="timeline-label">
                                        <span class="text-primary font-weight-bold">${value.time}</span>
                                    </div>
                                    <div class="timeline-content">
                                        <table class="table table-bordereed" id="tableModal">
                                            <tr>
                                                <th>SHIP. DATE</th>
                                                <th>ORIGIN</th>
                                                <th>DESTINATION</th>
                                                <th>MoT</th>
                                                <th>PROD. TYPE</th>
                                                <th>SERVICE</th>
                                                <th>VEHICLE TYPE</th>
                                            </tr>
                                            <tbody>
                                                <tr>
                                                    <td>${value.ship_date}</td>
                                                    <td>${value.origin}</td>
                                                    <td>${value.destination}</td>
                                                    <td>${value.mot}</td>
                                                    <td>${value.product_type}</td>
                                                    <td>${value.service}</td>
                                                    <td>${value.vehicle_type}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`)
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

        function selectService(value) {
            var origin = $('.selectOrigin').val();
            var dest = $('.selectDestination').val();
            var mot = $('.selectMOT').val();
            var prod = $('.selectProd').val();
            var service = $('.selectService').val();
            $.ajax({
                url: "{{ url('vm-price/getSelectVehicle') }}/" + origin + '/' + dest + '/' + mot + '/' + prod +
                    '/' + value,
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
    </script>
@endpush
