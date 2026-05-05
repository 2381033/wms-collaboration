@extends('layouts.main')

@section('title')
Handling Report
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Handling Report</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Handling Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{route('handling-report.report')}}" method="post" onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-3">
                        <fieldset> 
                            <legend>Report Group On</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="summary" value="summary" checked>
                                <label class="form-check-label" for="summary">Summary</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="detail" value="detail">
                                <label class="form-check-label" for="detail">Detail</label>
                            </div>
                        </fieldset>
                        <br/>
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

    function downloadExcel() {
        var query = {
            principal_id: $('#principal_id').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
        }

        var url = "{{URL::to('warehouse/handling-report/export')}}?" + $.param(query)
        
        window.open(url, '_blank');
    }

    function target_popup(form) {
        window.open('', 'HandlingReport', 'width=800,height=600,resizeable,scrollbars');
        form.target = 'HandlingReport';
    }
</script>
@endpush