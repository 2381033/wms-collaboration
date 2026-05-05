@extends('layouts.main')

@section('title')
    CY Outbound
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Outbound</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>CY Outbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-2">    
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="text" id="date_from" name="date_from" class="form-control">
                    </div>
                </div> 
                <div class="col-md-2">    
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="text" id="date_to" name="date_to" class="form-control">
                    </div>
                </div> 
                <div class="col-md-2">    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="custom-select" id="status_code" name="status_code">
                            <option value="Open">Open</option>
                            <option value="Cancel">Cancel</option>
                            <option value="Confirmed">Confirmed</option>
                        </select>
                    </div>
                </div>                                 
                <div class="col-md-6"> 
                    <div class="form-group">
                        <label for="branch_id">Branch Name</label>
                        <select name="branch_id" id="branch_id" class="custom-select">
                            @foreach (Auth::user()->branch as $item)
                                <option value="{{$item->id}}" @isset($header->job_date) @if( $item->id == $header->branch_id ) selected @endif @endisset>{{$item->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>            
            </div>
            <div class="row">                
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="{{url('/cy/outbound/create/0')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                    </div>    
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md-12">            
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{session('success')}}
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Job No</th>
                                    <th>Job Date</th>
                                    <th>Company Name</th>
                                    <th>Driver Name</th>
                                    <th>Vehicle No</th>
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
    $( function() {
        var today = getFirstDate(),
            lastDay = getLastDate();

        $('#date_from').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        }).datepicker("setDate", today);

        $('#date_to').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        }).datepicker("setDate", lastDay);
    } );

    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        load_data();

        $('#refresh').click(function () {
            $('#table_list').DataTable().destroy();
            load_data();
        });

        function load_data(forwarder = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('cy-outbound.index') }}",
                    type : "GET",
                    data : { 
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        status_code: $('#status_code').val(),
                        branch_id: $('#branch_id').val()
                    } 
                },
                columns : [
                    { data:'job_no', name:'job_no' },
                    { data:'job_date', name:'job_date' },
                    { data:'forwarder_name', name:'forwarder_name' },
                    { data:'driver_name', name:'driver_name' },
                    { data:'vehicle_no', name:'vehicle_no'},
                    { data:'container_no', name:'container_no'},
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }
    });
</script>
@endpush