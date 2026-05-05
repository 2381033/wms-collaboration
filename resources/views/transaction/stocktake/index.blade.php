@extends('layouts.main')

@section('title')
    Stock Take
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Take</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Stock Take</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Principal Name</label>
                        <select class="custom-select" id="principal_id" name="principal_id">
                            @foreach (Auth::user()->principal as $item)
                                <option value="{{$item->id}}">{{$item->principal_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
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
            </div>
            <div class="row">                
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Refresh</button>
                        <a href="{{url('/inventory/stock-take/create/0')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
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
                            <thead class="text-center">
                                <tr>
                                    <th>Job Number</th>
                                    <th>Job Date</th>
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
            var principal_id = $('#principal_id').val();
            
            if (principal_id != '' && principal_id > 0) {
                $('#table_list').DataTable().destroy();
                load_data();
            } else {
                swal({
                    icon: "error",
                    text: "Principal name cannot be empty."                     
                });
            }                
        });

        function load_data(principal = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('take-job.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: $('#principal_id').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val()
                    } 
                },
                columns : [
                    { data:'stocktake_no', name:'stocktake_no'},
                    { data:'stocktake_date', name:'stocktake_date'},
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