@extends('layouts.main')

@section('title')
    Principal
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Principal</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Principal</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <a href="{{route('principal.create')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></a>
                    </div>    
                </div>
                <div class="col-md-12">            
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{session('success')}}
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Short Name</th>
                                    <th>Principal Name</th>
                                    <th>Status</th>     
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section> 
@endsection

@section('modal')
@endsection

@push('scripts')
<script>        
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
        $('#table_list').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            ajax : {
                url : "{{ route('principal.index') }}",
                type : "GET"
            },
            columns : [
                { data:'short_name', name:'short_name' },
                { data:'principal_name', name:'principal_name' },
                { data:'active', name:'active' },
                { data: 'action', name: 'action' }
            ],
            order : [
                [0, 'asc']
            ]
        });

        $(document).on('click', '.delete', function () {
            dataId = $(this).attr('id');
            $('#action-delete').val('principal');
            $('#modal-konfirmasi').modal('show');
        });

        $('#btn-delete').click(function () {
            var action = $('#action-delete').val();
            var requestUrl = "";

            if (action == 'principal') {
                requestUrl = "principal/" + dataId; 
            } else if (action == 'site') {
                var principal_id = $('#principal_id_site').val();
                requestUrl = "principal-site/" + principal_id + "/" + dataId; 
            } 

            $.ajax({
                url: requestUrl,
                type: 'delete',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (data) {
                    if($.isEmptyObject(data.error)){
                        swal({
                            icon: "success",
                            text: "Data Successfully Deleted."                     
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error                   
                        });
                    }

                    $('#modal-konfirmasi').modal('hide');

                    var oTable = "";
                    if (action == 'principal') {
                        oTable = $('#table_list').dataTable();
                    } else if (action == 'site') {
                        oTable = $('#site_table').dataTable();
                    } 
                        
                    oTable.fnDraw(false);
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });
    });
</script>
@endpush