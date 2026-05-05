@extends('layouts.main')

@section('title')
    Picking Report
@endsection

@section('content')  
<section id="breadcrumbs" class="breadcrumbs">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Picking Report</h2>
            <ol>
                <li><a href="{{route('home')}}">Home</a></li>
                <li>Picking Report</li>
            </ol>
        </div>
    </div>
</section>
<section id="contact" class="contact">
    <div class="container">
        <form id="form-filter" name="form-filter" action="{{ route('picking-report.report') }}" method="get"
        onsubmit="target_popup(this)">
        @csrf
        <div class="row info-wrap" data-aos="fade-up">
            <div class="row" style="width: 100%">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="principal_id">Principal Name</label>
                        <select name="principal_id" id="principal_id" class="custom-select form-control">
                            @foreach (Auth::user()->principal as $item)
                                <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="jobName">Job Name</label>
                        <select name="jobName" id="jobName" class="custom-select form-control">
                            <option value="EXP">Outbound</option>
                            {{-- <option value="3">Transfer</option> --}}
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="row" style="width: 100%">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="periode_start">Period Start</label>
                        <input type="texte" id="periode_start" name="periode_start" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="periode_end">Period End</label>
                        <input type="text" id="periode_end" name="periode_end" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fileType">File Type</label><br />
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="fileType" id="pdf"
                                value="pdf" checked>
                            <label class="form-check-label" for="pdf">PDF</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="fileType" id="excel"
                                value="excel">
                            <label class="form-check-label" for="excel">Excel</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <br />
                    <button type="button" onclick="downloadData();" class="btn btn-sm form-control"
                        style="background-color: #36577d"><i class="fas fa-search" style="color: whitesmoke"></i>
                    </button>
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
            var d = new Date();
            d.setDate(d.getDate() - 30);
            $('#periode_start').datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true
            });

            $('#periode_end').datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true
            });

            $("#periode_start").datepicker("option", "dateFormat", 'dd/mm/yy');
            $("#periode_end").datepicker("option", "dateFormat", 'dd/mm/yy');
        });
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        });
        function downloadData() {
            var fileType = $('input[name="fileType"]:checked').val();
            var query = {
                // _token: $('meta[name="csrf-token"]').attr('content'),
                principal_id: $('#principal_id').val(),
                job_name: $('#job_name').val(),
                periode_start: $('#periode_start').val(),
                periode_end: $('#periode_end').val(),
                fileType: fileType
            }
            let dataForm = $('#form-filter').serialize();

            if (fileType == 'pdf') {
                $('#form-filter').submit();
            } else if (fileType == 'excel') {
                var url = "{{ URL::to('warehouse/picking-report/export') }}?" + dataForm
                window.open(url, '_blank');
                // var url = "{{ URL::to('kpi/distribution-center/export') }}?" + $.param(query)
                // window.open(url, '_blank');
                window.open('', 'PickingReport', 'width=800,height=600,resizeable,scrollbars');
                form.target = 'PickingReport';
            }
        }
        function target_popup(form) {
            window.open('', 'PickingReport', 'width=800,height=600,resizeable,scrollbars');
            form.target = 'PickingReport';
        }
</script>
@endpush

