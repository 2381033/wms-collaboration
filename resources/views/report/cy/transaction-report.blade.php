@extends('layouts.main')

@section('title')
CY - Transaction Report
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY - Transaction Report</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>CY - Transaction Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{route('cy-report.transaction')}}" method="post" onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="forwarder_id">Company Name</label>
                                    <select name="forwarder_id" id="forwarder_id" class="custom-select">
                                        <option value="All">All</option>
                                        @foreach ($forwarder_list as $item)
                                            <option value="{{$item->id}}">{{$item->forwarder_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type='text' id="date_from" name="date_from" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="text" id="date_to" name="date_to" class="form-control" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                </div>
                            </div>
                        </div>
                        <div class="row">  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="job_type">Job Type</label>
                                    <select name="job_type" id="job_type" class="custom-select">
                                        <option value="All">All</option>
                                        <option value="Inbound">Inbound</option>
                                        <option value="Outbound">Outbound</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">                                                  
                                <div class="form-group text-center">                 
                                    <button type="submit" id="tombol-print" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> <span>Print</span></button>
                                    <button type="button" onclick="downloadExcel();" class="btn btn-success btn-sm"><i class="fas fa-download"></i> <span>Download</span></button>
                                </div>
                            </div>
                        </div>
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
            forwarder_id: $('#forwarder_id').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            job_type: $('#job_type').val(),
        }

        var url = "{{URL::to('cy/report/transaction/export')}}?" + $.param(query)
        
        window.open(url, '_blank');
    }

    function target_popup(form) {
        window.open('', 'CYTransactionReport', 'width=800,height=600,resizeable,scrollbars');
        form.target = 'CYTransactionReport';
    }
</script>
@endpush