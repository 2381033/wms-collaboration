@extends('layouts.main')

@section('title')
Export - CLP Report
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - CLP Report</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Export - CLP Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{route('export-report.clp')}}" method="post" onsubmit="target_popup(this)">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipper_id">Shipper Name</label>
                                    <select name="shipper_id" id="shipper_id" class="custom-select">
                                        <option value="All">All</option>
                                        @foreach ($shipper_list as $item)
                                            <option value="{{$item->id}}">{{$item->shipper_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="container_no">Container No</label>
                                    <input type="text" id="container_no" name="container_no" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">                                                  
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
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

    $( "#container_no" ).autocomplete({	
        minLength:0,	        
        classes: {
            "ui-autocomplete": "highlight"
        },
        source: function( request, response ) {                    
            $.ajax({
                url:"{{route('export.getContainerExport')}}",
                dataType: "json",
                data: {
                    _token: CSRF_TOKEN,
                    forwarder_id : $("#forwarder_id").val(),
                    date_from : $("#date_from").val(),
                    date_to : $("#date_to").val(),
                    search: request.term
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        select: function (event, ui) {
            $('#received_date').val(ui.item.job_date);  
            $('#container_no').val(ui.item.container_no);  
            $('#serial_id').val(ui.item.serial_id);  
            $('#lolo_amount').val(ui.item.rate_amount);  
            $('#free_storage').val(ui.item.free_storage);  
            
            return false;
        }
    })        
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
            .append( "<div>" + item.container_no + "</div>" )
            .appendTo( ul );
    }; 

    function downloadExcel() {
        var query = {
            forwarder_id: $('#forwarder_id').val(),
            shipper_id: $('#shipper_id').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            container_no: $('#container_no').val(),
        }

        var url = "{{URL::to('export/report/clp/export')}}?" + $.param(query)
        
        window.open(url, '_blank');
    }

    function target_popup(form) {
        window.open('', 'CYStockReport', 'width=800,height=600,resizeable,scrollbars');
        form.target = 'CYStockReport';
    }
</script>
@endpush