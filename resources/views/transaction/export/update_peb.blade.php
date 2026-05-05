@extends('layouts.main')

@section('title')
    Export - Update PEB NO
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Export - Update Peb No</li>
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
                            <thead class="text-center">
                                <tr>
                                    <th>Job Number</th>
                                    <th>Job Date</th>
                                    <th>Forwarder Name</th>
                                    <th>Shipper Name</th>
                                    <th>Consignee Name</th>
                                    <th>PEB No</th>
                                    <th>AJU No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ url('export/updatePeb') }}" method="post" id="updatePeb">
                        @csrf
                        <input type="hidden" name="id" required class="idValue">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="">No. PEB Old</label>
                                        <input type="text" name="" disabled id="orderNo" class="form-control"
                                            placeholder="" aria-describedby="helpId" value="0">
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="">No. PEB New</label>
                                        <input type="text" name="peb_no" id="peb_new" class="form-control"
                                            aria-describedby="helpId" required autocomplete="off"
                                            placeholder="Silahkan Di isi..">
                                    </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-md btn-info btnUpdatePEB"><i
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

    </section>
@endsection

@section('modal')
@endsection

@push('scripts')
    <script>
        load_data();

        function load_data() {
            $('#table_list').DataTable().destroy();
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('export/getListUpdateNoPeb') }}",
                    type: "GET",
                },
                columns: [{
                        data: 'job_no',
                        name: 'job_no'
                    },
                    {
                        data: 'job_date',
                        name: 'job_date'
                    },
                    {
                        data: 'forwarder_name',
                        name: 'forwarder_name'
                    },
                    {
                        data: 'shipper_name',
                        name: 'shipper_name'
                    },
                    {
                        data: 'consignee_name',
                        name: 'consignee_name'
                    },
                    {
                        data: 'peb_no',
                        name: 'peb_no'
                    },
                    {
                        data: 'aju_no',
                        name: 'aju_no'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            return `<a class="btn btn-dark btn-sm" href="#" onclick="editpeb('${data.id}')"> Edit PEB</a>`;
                        },
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
        }

        function editpeb(id) {
            $('.idValue').val(id);
            $('#modal').modal('show');
        }

        $('#updatePeb').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $('.btnUpdatePEB').attr('disabled', true);
            var validate = $('#peb_new').val();
            if (validate == '' && validate == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'PEB No. Wajib di isi..',
                })
            } else {
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $('.btnUpdatePEB').attr('disabled', false);
                        if (response.message == 'success') {
                            swal({
                                icon: 'success',
                                text: 'PEB Berhasil di ubah!',
                            });
                            $('#modal').modal('hide')
                            load_data();
                        }
                    },
                    error: function(error) {
                        $('.btnUpdatePEB').attr('disabled', false);
                        swal({
                            icon: "error",
                            text: 'Internal Server Error..',
                        });
                    }
                });
            }
        })
    </script>
@endpush
