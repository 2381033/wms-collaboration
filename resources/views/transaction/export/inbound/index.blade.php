@extends('layouts.main')

@section('title')
    Export - Inbound
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - Inbound</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Export - Inbound</li>
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
                <div class="col-md-4"> 
                    <div class="form-group">
                        <label for="branch_id">Branch Name</label>
                        <select name="branch_id" id="branch_id" class="custom-select">
                            @foreach (Auth::user()->branch as $item)
                                <option value="{{$item->id}}" @isset($header->job_date) @if( $item->id == $header->branch_id ) selected @endif @endisset>{{$item->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-2">    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="custom-select" id="status_flag" name="status_flag">
                            <option value="Open">Open</option>
                            <option value="Confirmed">Confirmed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="{{url('/export/inbound/create/0')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                    </div>    
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Job Number</th>
                                    <th>Job Date</th>
                                    <th>Forwarder Name</th> 
                                    <th>Shipper Name</th> 
                                    <th>Consignee Name</th> 
                                    <th>PEB No</th> 
                                    <th>Status</th> 
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
            weekAgo = getLastDate();

        var dateFormat = "dd/mm/yy";
        var from = $( "#date_from" )
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                }).datepicker('setDate', today);

        var to = $( "#date_to" ).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 3
            }).datepicker('setDate', weekAgo);

        $( "#date_from" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
        $( "#date_to" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
    } );
    
    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $('#refresh').click(function () {
            load_data();
        });

        load_data();

        function load_data(principal = '') {
            $('#table_list').DataTable().destroy();
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('export-inbound.index') }}",
                    type : "GET",
                    data : { 
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        branch_id: $('#branch_id').val(),
                        status_flag: $('#status_flag').val(),
                    } 
                },
                columns : [
                    { data:'job_no', name:'job_no'},
                    { data:'job_date', name:'job_date'},
                    { data:'forwarder_name', name:'forwarder_name'},
                    { data:'shipper_name', name:'shipper_name'},
                    { data:'consignee_name', name:'consignee_name'},
                    { data:'peb_no', name:'peb_no'},
                    { data:'status_flag', name:'status_flag'}
                ],
                order : [
                    [0, 'desc']
                ]
            });
        }
    });
</script>
@endpush