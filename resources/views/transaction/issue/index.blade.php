@extends('layouts.main')

@section('title')
Issue - Reason
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Issue - Reason</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Issue - Reason</li>
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
                        {{-- @can('gate-access', 'issue/list') --}}
                            <a href="{{url('/issue-reason/create/0')}}" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                        {{-- @endcan --}}
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
                                    <th>Job Number</th>
                                    <th>Job Date</th>
                                    <th>Ref No</th>
                                    <th>Customer Name</th>
                                    <th>Rating</th>
                                    <th>Class Name</th>
                                    <th>Notes</th>
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

@push('styles')
<style>
    *{
        margin: 0;
        padding: 0;
    }
    .rate {
        float: left;
        height: 46px;
        padding: 0 10px;
    }
    .rate:not(:checked) > input {
        position:absolute;
        top:-9999px;
    }
    .rate:not(:checked) > label {
        float:right;
        width:1em;
        overflow:hidden;
        white-space:nowrap;
        cursor:pointer;
        font-size:30px;
        color:#ccc;
    }
    .rate:not(:checked) > label:before {
        content: '★ ';
    }
    .rate > input:checked ~ label {
        color: #ffc700;    
    }
    .rate:not(:checked) > label:hover,
    .rate:not(:checked) > label:hover ~ label {
        color: #deb217;  
    }
    .rate > input:checked + label:hover,
    .rate > input:checked + label:hover ~ label,
    .rate > input:checked ~ label:hover,
    .rate > input:checked ~ label:hover ~ label,
    .rate > label:hover ~ input:checked ~ label {
        color: #c59b08;
    }
</style>
@endpush

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
                    url : "{{ route('issue-reason.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: $('#principal_id').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                    } 
                },
                columns : [
                    { data:'job_no', name:'job_no' },
                    { data:'job_date', name:'job_date' },
                    { data:'order_no', name:'order_no' },
                    { data:'customer_name', name:'customer_name' },
                    { data:'rating', name:'rating' },
                    { data:'class_name', name:'class_name' },
                    { data:'notes', name:'notes'}
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }
    });
</script>
@endpush