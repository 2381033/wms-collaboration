@extends('layouts.main')

@section('title')
    Delivery Report
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Delivery Report</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Delivery Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{route('dispatch.print')}}" method="post" onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-3">
                        <div class="form-group text-center">                 
                            <button type="submit" id="tombol-print" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> <span>Print</span></button>
                            <button type="button" onclick="downloadExcel();" class="btn btn-success btn-sm"><i class="fas fa-download"></i> <span>Download</span></button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <fieldset> 
                            <legend>Filter By</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="principal_id">Principal Name</label>
                                        <select name="principal_id" id="principal_id" class="custom-select">
                                            @foreach (Auth::user()->principal as $item)
                                                <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_from">Date From</label>
                                        <input type="text" id="date_from" name="date_from" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="text" id="date_to" name="date_to" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_code_from">Customer Name From</label>
                                        <select name="customer_code_from" id="customer_code_from" class="custom-select">
                                            <option value=""></option>
                                            @foreach ($customer_list as $item)
                                                <option value="{{$item->customer_code}}">{{$item->customer_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_code_to">Customer Name To</label>
                                        <select name="customer_code_to" id="customer_code_to" class="custom-select">
                                            <option value=""></option>
                                            @foreach ($customer_list as $item)
                                                <option value="{{$item->customer_code}}">{{$item->customer_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="store_code_from">Store Name From</label>
                                        <select name="store_code_from" id="store_code_from" class="custom-select">
                                            <option value=""></option>
                                            @foreach ($store_list as $item)
                                                <option value="{{$item->store_code}}">{{$item->store_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="store_code_to">Store Name To</label>
                                        <select name="store_code_to" id="store_code_to" class="custom-select">
                                            <option value=""></option>
                                            @foreach ($store_list as $item)
                                                <option value="{{$item->store_code}}">{{$item->store_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </section> 
@endsection

@section('modal')
@endsection

@push('scripts')
<script>        
    $(function() {
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
    });

    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                
        $('#principal_id').on('change', function() {
            var principal_id = this.value;
            $("#customer_code_from").html('');
            $("#customer_code_to").html('');
            $("#store_code_from").html('');
            $("#store_code_to").html('');
            $.ajax({
                url:"{{route('dispatch.list')}}",
                type: "GET",
                data: {
                    principal_id: principal_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#customer_code_from').html('<option value="">.:Select:.</option>'); 
                    $.each(result.customer_list,function(key,value){
                        $("#customer_code_from").append('<option value="'+value.customer_code+'">'+value.customer_name+'</option>');
                    });
                    
                    $('#customer_code_to').html('<option value="">.:Select:.</option>'); 
                    $.each(result.customer_list,function(key,value){
                        $("#customer_code_to").append('<option value="'+value.customer_code+'">'+value.customer_name+'</option>');
                    });

                    $('#store_code_from').html('<option value="">.:Select:.</option>'); 
                    $.each(result.store_list,function(key,value){
                        $("#store_code_from").append('<option value="'+value.store_code+'">'+value.store_name+'</option>');
                    });
                    
                    $('#store_code_to').html('<option value="">.:Select:.</option>'); 
                    $.each(result.store_list,function(key,value){
                        $("#store_code_to").append('<option value="'+value.store_code+'">'+value.store_name+'</option>');
                    });
                }
            });
        });    
    });

    function downloadExcel() {
        var query = {
            principal_id: $('#principal_id').val(),
            customer_code_from: $('#customer_code_from').val(),
            customer_code_to: $('#customer_code_to').val(),
            store_code_from: $('#store_code_from').val(),
            store_code_to: $('#store_code_to').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
        }
        
        var url = "{{URL::to('report/dispatch/export')}}?" + $.param(query)
        
        window.open(url, '_blank');
    }
    
    function target_popup(form) {
        window.open('', 'StockReport', 'width=800,height=600,resizeable,scrollbars');
        form.target = 'StockReport';
    }
</script>
@endpush