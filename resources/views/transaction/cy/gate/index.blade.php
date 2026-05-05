@extends('layouts.main')

@section('title')
    CY Gate
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Gate</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>CY Gate</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-3">  
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Transaction Type</label>
                                <div class="form-input">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="transactionType" id="trx_inbound" value="inbound" checked>
                                        <label class="form-check-label" for="inbound">Inbound</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="transactionType" id="trx_outbound" value="outbound">
                                        <label class="form-check-label" for="outbound">Outbound</label>
                                    </div>
                                </div>
                            </div>    
                        </div>                    
                    </div> 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gate Type</label>
                                <div class="form-input">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gateType" id="gate_in" value="in" checked>
                                        <label class="form-check-label" for="in">Gate In</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gateType" id="gate_out" value="out">
                                        <label class="form-check-label" for="out">Gate Out</label>
                                    </div>
                                </div>
                            </div>    
                        </div>                    
                    </div>
                </div>
                <div class="col-md-9">  
                    <div id="inbound" class="inbound" style="display:show">
                        <div class="container">                            
                            <div class="row mb-md-3">
                                <div class="col-md-2">  
                                    <label>Booking No</label>
                                </div>      
                                <div class="col-md-6">
                                    <input type="text" id="book_no" name="book_no" class="form-control">
                                </div>
                                <div class="col-md-3">  
                                    <button type="button" onclick="submitData();" class="btn btn-primary btn-block btn-sm">Search</button>
                                </div>
                            </div>
                            <div id="inbound-form" class="inbound" style="display:none">                                
                                <form id="form-job" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Company Name</label>
                                                <input type="hidden" id="booking_no" name="booking_no">
                                                <input type="hidden" id="forwarder_id" name="forwarder_id">
                                                <input type="text" id="forwarder_name" name="forwarder_name" class="form-control" readonly>
                                            </div>
                                        </div> 
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Reference No</label>
                                                <input type="text" id="reference_no" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">   
                                        <div class="col-md-4">    
                                            <div class="form-group">
                                                <label>Driver Name</label>
                                                <input type="text" autocomplete="off" id="driver_name" name="driver_name" class="form-control">
                                            </div>
                                        </div>                     
                                        <div class="col-md-4">    
                                            <div class="form-group">
                                                <label>Vehicle No</label>
                                                <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" autocomplete="off">
                                            </div>
                                        </div>        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Container Status</label>
                                                <select id="container_status" name="container_status" class="form-control">
                                                    <option value=""></option>
                                                    <option value="Empty">Empty</option>
                                                    <option value="Full">Full</option>
                                                </select>
                                            </div>
                                        </div>     
                                    </div>
                                    <div class="row">    
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Container Size</label>
                                                <select id="size_id" name="size_id" class="form-control">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>     
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Container Type</label>
                                                <select id="type_id" name="type_id" class="form-control">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>    
                                        <div class="col-md-4">    
                                            <div class="form-group">
                                                <label>Invoice Type</label>
                                                <select id="invoice_type" name="invoice_type" class="form-control" readonly>
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>        
                                    </div>
                                    <div class="row">   
                                        <div class="col-md-4">    
                                            <div class="form-group">
                                                <label>Container No</label>
                                                <input type="text" id="container_no" name="container_no" class="form-control" autocomplete="off" @isset($header->container_no) value="{{$header->container_no}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                            </div>
                                        </div>    
                                        <div class="col-md-2">
                                            <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>     
                    <div id="outbound" class="outbound" style="display:none">
                        <div class="container">
                            <div id="outbound-create" style="display:show">
                                <form id="form-outbound" method="POST">
                                    @csrf
                                    <input type="hidden" id="gate_type" name="gate_type">
                                    <div class="row">                                        
                                        <div class="col-md-6"> 
                                            <div class="form-group">
                                                <label for="branch_id">Branch Name</label>
                                                <select name="branch_id" id="branch_id" class="custom-select">
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{$item->id}}" @isset($header->job_date) @if( $item->id == $header->branch_id ) selected @endif @endisset>{{$item->branch_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>                       
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Vehicle No</label>
                                                <input type="text" id="vehicle_no_out" name="vehicle_no_out" class="form-control" autocomplete="off">
                                            </div>
                                        </div>   
                                    </div>
                                    <div class="row">    
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Driver Name</label>
                                                <input type="text" autocomplete="off" id="driver_name_out" name="driver_name_out" class="form-control">
                                            </div>
                                        </div>    
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Container No</label>
                                                <input type="text" id="container_no_out" name="container_no_out" class="form-control" autocomplete="off" @isset($header->container_no) value="{{$header->container_no}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                            </div>
                                        </div>    
                                    </div>      
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button type="submit" id="btn-save-outbound" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div id="outbound-list" style="display:none">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Vehicle No</th>
                                                        <th>Driver Name</th>
                                                        <th>Container No</th>
                                                        <th>Gate In</th>
                                                        <th>Gate Out</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        $(document).on('click', 'input[name="transactionType"]', function() {
            if ( $(this).val() == "outbound") {
                $('#outbound').show();
                $('#inbound').hide();                
                $('#inbound-form').hide();   
            } else {
                $('#inbound').show();
                $('#outbound').hide();                
                $('#inbound-form').hide();   
            }            
            $("#form-job").trigger("reset");
        });

        $(document).on('click', 'input[name="gateType"]', function() {
            $("#form-job").trigger("reset");
            
            var transaction = document.getElementsByName('transactionType');
            var transactionType = "";

            for (var i = 0, length = transaction.length; i < length; i++) {
                if (transaction[i].checked) {
                    transactionType = transaction[i].value;
                }
            }
            
            if ( transactionType == "outbound" ) {
                if ( $(this).val() == "in") {
                    $("#outbound-create").show();
                    $("#outbound-list").hide();
                } else {
                    $("#outbound-create").hide();
                    $("#outbound-list").show();
                    load_outbound();
                }
            }
        });

        if ($("#form-job").length > 0) {
            $("#form-job").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $("#form-job").serialize(), 
                        url: "{{ route('cy-gate.inboundGateIn') }}",
                        type: "POST",
                        dataType: "json",
                        success: function (data) {                   
                            if($.isEmptyObject(data.error)){
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });                             
                            } else {
                                var pesan = "<div class='text-left alert alert-danger'>";
                                for (var i = 0; i < data.error.length; i++) {                                            
                                    pesan += data.error[i]+"</br>"; 
                                }
                                pesan += "</div>";
                                
                                const wrapper = document.createElement("div");        
                                wrapper.innerHTML = pesan;
                                swal({
                                    icon: "error",
                                    content: wrapper                     
                                });
                            } 
                        },
                        error: function (data) {
                            console.log("Error:", data);
                        }
                    });
                }
            })
        }

        if ($("#form-outbound").length > 0) {
            $("#form-outbound").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $("#form-outbound").serialize(), 
                        url: "{{ route('cy-gate.outboundGateIn') }}",
                        type: "POST",
                        dataType: "json",
                        success: function (data) {                   
                            if($.isEmptyObject(data.error)){
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });                             
                            } else {
                                var pesan = "<div class='text-left alert alert-danger'>";
                                for (var i = 0; i < data.error.length; i++) {                                            
                                    pesan += data.error[i]+"</br>"; 
                                }
                                pesan += "</div>";
                                
                                const wrapper = document.createElement("div");        
                                wrapper.innerHTML = pesan;
                                swal({
                                    icon: "error",
                                    content: wrapper                     
                                });
                            } 
                        },
                        error: function (data) {
                            console.log("Error:", data);
                        }
                    });
                }
            })
        }

        $('body').on('click', '.gate-out', function () {
            var data_id = $(this).data('id');

            $.ajax({
                data: {                    
                    "id": data_id,
                    "_token": CSRF_TOKEN
                },
                url: "{{ route('cy-gate.outboundGateOut') }}",
                type: 'POST',
                dataType: 'json',
                success: function (data) { 
                    swal({
                        icon: "success",
                        text: "Data Successfully Saved."                    
                    }); 
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });

    function inboundGateOut(book_no) {
        $.ajax({
            data: {
                "booking_no": book_no,
                "_token": CSRF_TOKEN
            }, 
            url: "{{ route('cy-gate.inboundGateOut') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {                   
                if($.isEmptyObject(data.error)){
                    swal({
                        icon: "success",
                        text: "Data Successfully Saved."                    
                    });                             
                } else {
                    var pesan = "<div class='text-left alert alert-danger'>";
                    for (var i = 0; i < data.error.length; i++) {                                            
                        pesan += data.error[i]+"</br>"; 
                    }
                    pesan += "</div>";
                    
                    const wrapper = document.createElement("div");        
                    wrapper.innerHTML = pesan;
                    swal({
                        icon: "error",
                        content: wrapper                     
                    });
                } 
            },
            error: function (data) {
                console.log("Error:", data);
            }
        });
    }

    function submitData() {
        var book = $("#book_no").val();
        var transaction = document.getElementsByName('transactionType');
        var gate = document.getElementsByName('gateType');
        var transactionType = "";
        var gateType = "";

        for (var i = 0, length = transaction.length; i < length; i++) {
            if (transaction[i].checked) {
                transactionType = transaction[i].value;
            }
        }

        for (var i = 0, length = gate.length; i < length; i++) {
            if (gate[i].checked) {
                gateType = gate[i].value;
            }
        }
        
        if ( transactionType == "inbound" ) {            
            if ( gateType == "in" ) {
                load_data(book);
            } else {                    
                inboundGateOut(book);
            }
        } 
    };

    function load_data(book) {
        $.ajax({
            url: "{{ url('cy/gate-in') }}/" + book,                   
            type: 'get',
            dataType: 'json',
            beforeSend: function () {
                $("#loader").show();
                $('#inbound').hide();
            },
            success: function (result) {     
                $("#loader").hide();
                // var x = confirm("Are you sure you want to process?");
                // if (x) {
                    if($.isEmptyObject(result.error)){                                               
                        $('#inbound').show();                                            
                        $('#inbound-form').show();

                        $.each(result.invoice_list,function(key,value){
                            $("#invoice_type").append('<option value="'+value.id+'">'+value.type_name+'</option>');
                        });

                        $.each(result.size_list,function(key,value){
                            $("#size_id").append('<option value="'+value.id+'">'+value.size_name+'</option>');
                        });
                        
                        $.each(result.type_list,function(key,value){
                            $("#type_id").append('<option value="'+value.id+'">'+value.type_name+'</option>');
                        });

                        var job_date = "";
                        
                        if (result.header.job_date !== null ) {
                            job_date = getFormatDate(result.header.job_date);
                        }

                        $("#job_no").val(result.header.job_no);
                        $("#job_date").val(job_date);
                        $("#booking_no").val(result.header.booking_no);
                        $("#forwarder_id").val(result.header.forwarder_id);
                        $("#forwarder_name").val(result.header.forwarder_name);
                        $("#reference_no").val(result.header.reference_no);
                        $("#invoice_type").val(result.header.invoice_type);
                        $("#vehicle_no").val(result.header.vehicle_no);
                        $("#driver_name").val(result.header.driver_name);
                        $("#size_id").val(result.header.size_id);
                        $("#type_id").val(result.header.type_id);
                        $("#container_status").val(result.header.container_status);
                        $("#container_no").val(result.header.container_no);
                    } else {
                        var pesan = "<div class='text-left alert alert-danger'>";
                        for (var i = 0; i < result.error.length; i++) {                                            
                            pesan += result.error[i]+'</br>'; 
                        }
                        pesan += '</div>';
                        
                        const wrapper = document.createElement('div');        
                        wrapper.innerHTML = pesan;
                        swal({
                            icon: "error",
                            content: wrapper                     
                        });
                    }
                // }
            }
        });
    }

    function load_outbound(forwarder = '') {
        $('#table_list').DataTable().destroy();
        $('#table_list').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            paging: false,
            ajax : {
                url : "{{ route('cy-gate.outboundList') }}",
                type : "GET",
                data : { 
                } 
            },
            columns : [
                { data:'vehicle_no', name:'vehicle_no' },
                { data:'driver_name', name:'driver_name' },
                { data:'container_no', name:'container_no' },
                { data:'gate_in', name:'gate_in' },
                { data:'gate_out', name:'gate_out' },
                { data:'action', name:'action' },
            ],
            order : [
                [0, 'asc']
            ]
        });
    }
</script>
@endpush