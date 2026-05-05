@extends('layouts.main')

@section('title')
    Pending Report
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Pending Report</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Pending Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{route('pending-report.print')}}" method="post" onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-12">
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
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' id="date_from" name="date_from" class="form-control" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="text" id="date_to" name="date_to" class="form-control" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 text-center">
                                    <button type="submit" id="tombol-print" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> <span>Print</span></button>
                                    <button type="button" onclick="downloadExcel();" class="btn btn-success btn-sm"><i class="fas fa-download"></i> <span>Download</span></button>
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
    });
    
    function target_popup(form) {
        window.open('', 'PendingReport', 'width=800,height=600,resizeable,scrollbars');
        form.target = 'PendingReport';
    }

    function downloadExcel() {
        var query = {
            principal_id: $('#principal_id').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
        }
        
        var url = "{{URL::to('warehouse/report/pending/export')}}?" + $.param(query)
        
        window.open(url, '_blank');
    }
</script>
@endpush