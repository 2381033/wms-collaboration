@extends('layouts.main')

@section('title')
    Outbound
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Mapping Lokasi</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Outbound</li>
                    <li>Mapping Lokasi</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;"
                            style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>#</th>
                                    <th>Job No</th>
                                    <th>Branch</th>
                                    <th>Principal</th>
                                </tr>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="#" onclick="getListDetailMapping('{{ $item->job_no }}')"
                                                class="btn btn-md btn-dark"><i class="fas fa-eye"></i></a>
                                        </td>
                                        <td>{{ $item->job_no }}</td>
                                        <td>{{ $item->branch }}</td>
                                        <td>{{ $item->principal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('postMappingLokasi') }}" method="post" id="postMappingLokasi">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th colspan="4">JOB NO : <b class="jobNoText"></b></th>
                                        </tr>
                                        <tr class="text-center">
                                            <th rowspan="2">
                                                <input type="checkbox" class="selectAll" style="zoom: 160%;">
                                            </th>
                                            <th>SKU</th>
                                            <th>Location Code</th>
                                            <th>Qty</th>
                                        </tr>
                                    <tbody id="table_detail">

                                    </tbody>
                                    </thead>
                                </table>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-lg btn-info btnPass"><i class="fas fa-key"></i>
                                        ByPass</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
@endsection

@push('scripts')
    <script>
        $('#table_list').DataTable();

        $(".selectAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('#postMappingLokasi').on('submit', function(e) {
            e.preventDefault();
            var data = $('#postMappingLokasi').serialize();
            $.ajax({
                data: data,
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        swal({
                            icon: "success",
                            text: "Data Successfully Saved."
                        });
                        location.reload();
                    }
                },
                error: function(data) {
                    swal({
                        icon: "error",
                        text: data.error
                    });
                }
            });
            $('.btnPass').hide();
        });

        function getListDetailMapping(job_no) {
            $.ajax({
                url: "{{ url('warehouse/outbound/getListDetailMapping/') }}/" + job_no,
                type: "get",
                dataType: 'json',
                success: function(data) {
                    $('#detailModal').modal('show');
                    $('.jobNoText').text(job_no);
                    $('#table_detail').html('');
                    $.each(data, function(index, value) {
                        console.log(value);
                        $('#table_detail').append(`
                        <tr class="text-center">
                            <td>   
                                <input type="checkbox" required="required" name="id[]" value="${value.id}" style="zoom: 160%;">
                            </td>
                            <td>   
                                ${value.product_code}
                            </td>
                            <td>   
                                ${value.location_code}
                            </td>
                            <td>   
                                ${value.qty} ${value.puom}
                            </td>
                        </tr>
                        `);
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

        // $(document).ready(function() {
        //     var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        //     load_data();

        //     function load_data() {
        //         $('#table_list').DataTable({
        //             "dom": '<"toolbar">frtip',
        //             processing: true,
        //             serverSide: true,
        //             ajax: {
        //                 url: "{{ route('outbound-job.index') }}",
        //                 type: "GET",
        //                 data: {
        //                     branch_id: $('#branch_id').val(),
        //                     principal_id: $('#principal_id').val(),
        //                     date_from: $('#date_from').val(),
        //                     date_to: $('#date_to').val(),
        //                     status_code: $('#status_code').val()
        //                 }
        //             },
        //             columns: [{
        //                     data: 'job_no',
        //                     name: 'job_no'
        //                 },
        //                 {
        //                     data: 'job_date',
        //                     name: 'job_date',
        //                     sortable: false,
        //                     render: function(job_date) {
        //                         return formatTanggalIndonesia2(job_date);
        //                     }
        //                 },
        //                 {
        //                     data: 'confirmed_date',
        //                     name: 'confirmed_date',
        //                     sortable: false,
        //                     render: function(confirmed_date) {
        //                         if (confirmed_date == null) {
        //                             var confirm = '-';
        //                         } else {
        //                             var confirm = formatTanggalIndonesia2(confirmed_date);
        //                         }
        //                         return confirm;
        //                     }
        //                 },
        //                 {
        //                     data: 'description',
        //                     name: 'description'
        //                 },
        //                 {
        //                     data: 'reference_no',
        //                     name: 'reference_no'
        //                 },
        //                 {
        //                     data: 'confirmed_flag',
        //                     name: 'confirmed_flag'
        //                 }
        //             ],
        //             order: [
        //                 [0, 'asc']
        //             ]
        //         });
        //     }
        // });
    </script>
@endpush
