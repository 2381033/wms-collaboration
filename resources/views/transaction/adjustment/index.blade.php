@extends('layouts.main')

@section('title')
    Stock Adjustment
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Adjustment</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Stock Adjustment</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Branch Name</label>
                        <select class="custom-select" id="branch_id" name="branch_id">
                            @foreach (Auth::user()->branch as $item)
                                <option value="{{$item->id}}">{{$item->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="text" id="date_from" name="date_from" class="form-control">
                    </div>
                </div> 
                <div class="col-md-3">    
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="text" id="date_to" name="date_to" class="form-control">
                    </div>
                </div> 
            </div>
            <div class="row">                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="confirmed_flag" id="confirmed_flag" class="custom-select">
                            <option value="No">Open</option>
                            <option value="Yes">Confirmed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="{{url('/inventory/stock-adjustment/create/0')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
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
                                    <th>Adjustment Number</th>
                                    <th>Adjustment Date</th>
                                    <th>Adjustment Type</th>
                                    <th>Description</th>
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

        load_data();

        $('#refresh').click(function () {
            $('#table_list').DataTable().destroy();
            load_data();
        });

        function load_data() {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('adjustment-job.index') }}",
                    type : "GET",
                    data : { 
                        branch_id: $('#branch_id').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        confirmed_flag: $('#confirmed_flag').val(),
                    } 
                },
                columns : [
                    { data:'adjust_no', name:'adjust_no'},
                    { data:'adjust_date', name:'adjust_date'},
                    { data:'type_name', name:'type_name'},
                    { data:'description', name:'description'},
                    { data:'confirmed_flag', name:'confirmed_flag'}
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }
    });
</script>
@endpush