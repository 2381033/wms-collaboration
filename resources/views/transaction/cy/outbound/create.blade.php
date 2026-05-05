@extends("layouts.main")

@section("title")
    CY Outbound
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Outbound</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>CY Outbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">
                    <form id="form-outbound" name="form-outbound" method="post">
                        @csrf
                    </form>  
                    <form id="form-job" method="POST">
                        @csrf
                        <input type="hidden" id="outbound_id" name="outbound_id" @isset($header->id) value="{{$header->id}}" @endisset>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-md-12 text-right">                    
                                    <div class="btn-group">
                                        <a href="{{url("/cy/outbound/create/0")}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                                        @if (isset($header->id))
                                            @if ($header->invoice_flag == "No")
                                                @if ($header->confirmed_flag == "Open") 
                                                    <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>                                                                                                         
                                                    
                                                    @empty($header->invoice_no)
                                                        <button type="button" onclick="confirmJob();" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Submit</span></button>
                                                    @endempty 
                                                @endif
                                            @else 
                                                @if ($header->confirmed_flag == "Open") 
                                                    <button type="button" onclick="confirmJob();" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Submit</span></button>
                                                @endif
                                            @endif                                                                                        
                                        @else 
                                            <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="job_no">Job Number</label>
                                        <input type="text" id="job_no" name="job_no" @isset($header->job_no) value="{{$header->job_no}}" @endisset class="form-control" readonly>
                                    </div>
                                </div> 
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="job_date">Job Date</label>
                                        <input type="text" id="job_date" name="job_date" @isset($header->job_date) value="{{\Carbon\Carbon::parse($header->job_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div>                                 
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">    
                                    <div class="form-group">
                                        <label>Company Name</label>
                                        <input type="hidden" id="forwarder_id" name="forwarder_id" @isset($header->forwarder_id) value="{{$header->forwarder_id}}" @endisset>
                                        <input type="hidden" id="free_storage" name="free_storage" @isset($header->free_storage) value="{{$header->free_storage}}" @endisset>
                                        <input type="hidden" id="storage" name="storage" @isset($header->storage) value="{{$header->storage}}" @endisset>
                                        <input type="text" id="forwarder_name" name="forwarder_name" class="form-control" @isset($header->forwarder_name) value="{{$header->forwarder_name}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div> 
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Driver Name</label>
                                        <input type="text" autocomplete="off" id="driver_name" name="driver_name" class="form-control" @isset($header->driver_name) value="{{$header->driver_name}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>                     
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Vehicle No</label>
                                        <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" autocomplete="off" @isset($header->vehicle_no) value="{{$header->vehicle_no}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>        
                            </div>
                            <div class="row">             
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Container No</label>
                                        <input type="hidden" id="serial_id" name="serial_id" @isset($header->serial_id) value="{{$header->serial_id}}" @endisset>
                                        <input type="text" id="container_no" name="container_no" class="form-control" autocomplete="off" @isset($header->container_no) value="{{$header->container_no}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>               
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Received Date</label>
                                        <input type="text" id="received_date" name="received_date" @isset($header->received_date) value="{{\Carbon\Carbon::parse($header->received_date)->format("d/m/Y")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div>            
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Dispatch Date</label>
                                        <input type="text" id="dispatch_date" name="dispatch_date" @isset($header->dispatch_date) value="{{\Carbon\Carbon::parse($header->dispatch_date)->format("d/m/Y")}}" @endisset class="form-control" @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>        
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="leadtime">Lead Time</label>
                                        <input type="text" id="leadtime" name="leadtime" @isset($header->leadtime) value="{{$header->leadtime}}" @endisset class="form-control" readonly>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">        
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>LOLO Amount</label>
                                        <input type="text" id="lolo_amount" name="lolo_amount" @isset($header->lolo_amount) value="{{number_format($header->lolo_amount, 0, ",", "")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div>            
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Storage Amount</label>
                                        <input type="text" id="storage_amount" name="storage_amount" @isset($header->storage_amount) value="{{number_format($header->storage_amount, 0, ",", "")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div>        
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="total_amount">Total Amount</label>
                                        <input type="text" id="total_amount" name="total_amount" @isset($header->total_amount) value="{{number_format($header->total_amount, 0, ",", "")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div>    
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="payment_date">Payment Date</label>
                                        <input type="text" id="payment_date" name="payment_date" @isset($header->payment_date) value="{{\Carbon\Carbon::parse($header->payment_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </form>
                </div>           
            </div>
        </div>
    </section> 
@endsection

@section("modal")
@endsection

@push("scripts")
<script> 
    $(function() {
        $('#dispatch_date').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        });
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $( "#forwarder_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('export.getForwarderStock')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        branch_id: $("#branch_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {              
                $('#forwarder_id').val(ui.item.forwarder_id);  
                $('#forwarder_name').val(ui.item.forwarder_name);  
                $('#storage').val(ui.item.storage_amount);  
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.forwarder_name + "</div>" )
                .appendTo( ul );
        }; 

        $( "#container_no" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cy.getContainer')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        forwarder_id : $("#forwarder_id").val(),
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

        if ($("#form-job").length > 0) {
            $("#form-job").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $("#form-job").serialize(), 
                        url: "{{ route('cy-outbound.store') }}",
                        type: "POST",
                        dataType: "json",
                        success: function (data) {                   
                            if($.isEmptyObject(data.error)){
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });

                                window.open(data.success, "_top");                                
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

        $('#btn-email').click(function () {
            var booking_id = $('#booking_id').val();
            var requestUrl = "{{url('cy/booking/email')}}/" + booking_id;
            
            $.ajax({
                url: requestUrl,
                type: 'get',          
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (data) {
                    $("#loader").hide();
                    
                    if($.isEmptyObject(data.error)){
                        swal({
                            icon: "success",
                            text: "Data Successfully Deleted."                     
                        });
                    } else {
                        swal({
                            icon: "error",
                            text: data.error                   
                        });
                    }
                },
                error: function (data) {
                    console.log(data);
                    $("#loader").hide();
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            })
        });
        
        $("#dispatch_date").on("change", function() {
            generate_leadtime();
        });          

        function generate_leadtime() {          
            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'IDR',
            });  

            var storage = $('#storage').val();
            var job_date = $("#received_date").val();
            var newDate = job_date.split("/").reverse().join("/");
            var received_date = new Date(newDate);
            var free_storage = parseFloat($("#free_storage").val());
            var lolo_amount = parseFloat($("#lolo_amount").val());
            
            var now_date = $("#dispatch_date").val();

            if ( now_date == null || now_date == "" ) {
                now_date = job_date;
            }
            
            var newDate = now_date.split("/").reverse().join("/");
            var dispatch_date = new Date(newDate);

            if ( dispatch_date < received_date ) {
                $("#leadtime").val(0);
                $("#storage_amount").val(0);
                $("#total_amount").val(lolo_amount);
                return;
            }

            var days = Math.floor(( Date.parse(dispatch_date) - Date.parse(received_date) ) / 86400000); 
            var leadtime = 0;

            if ( days < free_storage) {
                leadtime = 0;
            } else {
                leadtime = days - free_storage;
            }

            var storage_amount = leadtime * storage;
            var total_amount = parseFloat(lolo_amount) + parseFloat(storage_amount);

            $("#leadtime").val(leadtime);
            // $("#storage_amount").val(formatter.format(storage_amount));
            $("#storage_amount").val(storage_amount);
            $("#total_amount").val(total_amount);
        }
    });

    function confirmJob() {
        var outbound_id = $("#outbound_id").val();
        
        $('#form-outbound').trigger("reset");

        $('.hidden-outbound').remove();

        $('#form-outbound').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'outbound_id')
                .attr('class', 'hidden-outbound')
                .val(outbound_id)
        );

        $.ajax({
            data: $("#form-outbound").serialize(), 
            url: "{{ route('cy-outbound.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {                   
                if($.isEmptyObject(data.error)){
                    swal({
                        icon: "success",
                        text: "Data Successfully Saved."                    
                    });
                    
                    window.location.reload();
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
</script>
@endpush