@extends('layouts.main')

@section('title')
    Fleet Checklist
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Fleet Checklist</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Fleet Checklist</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Branch Name</label>
                        <select class="custom-select" id="branch_filter" name="branch_filter">
                            @foreach (Auth::user()->branch as $item)
                                <option value="{{$item->id}}">{{$item->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New</span></a>
                    </div>    
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Job No</th>
                                    <th>Job Date</th>
                                    <th>Job Type</th>
                                    <th>Vehicle No</th>     
                                    <th>Driver Name</th>
                                    <th>Container No</th> 
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
        
        load_data();

        function load_data(site = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('fleet-checklist.index') }}",                    
                    type : "GET",                    
                    data : { branch_id: $('#branch_filter').val() } 
                },
                columns : [
                    { data:'job_no', name:'job_no' },
                    { data:'job_date', name:'job_date' },
                    { data:'job_type', name:'job_type' },
                    { data:'vehicle_no', name:'vehicle_no' },
                    { data:'driver_name', name:'driver_name' },
                    { data:'container_no', name:'container_no' },
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }  

        $('#refresh').click(function () {
            var branch_filter = $('#branch_filter').val();
            
            if (branch_filter != '' && branch_filter > 0) {
                $('#table_list').DataTable().destroy();
                load_data();
            } else {
                swal({
                    icon: "error",
                    text: "Branch name cannot be empty."                     
                });
            }                
        });
    });
</script>
@endpush