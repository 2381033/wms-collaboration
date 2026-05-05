@extends("layouts.main")

@section("title")
    CY Booking
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Booking</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>CY Booking</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">
                    <form id="form-job" method="POST">
                        @csrf
                        <input type="hidden" id="booking_id" name="booking_id" @isset($header->id) value="{{$header->id}}" @endisset>
                        <div class="container mt-3">
                            <div class="row">                                
                                <div class="col-md-12 text-right">                    
                                    <div class="btn-group">
                                        <a href="{{url("/cy/booking/create/0")}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                                        @if (isset($header->id))
                                            @if ($header->status_flag == "Open")
                                                <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>                                                    
                                            @endif
                                            
                                            <button type="button" class="btn btn-info btn-sm" id="btn-email"><i class="fas fa-email"></i> <span>Send Email</span></button>
                                        @else 
                                            <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="booking_no">Booking Number</label>
                                        <input type="text" id="booking_no" name="booking_no" @isset($header->booking_no) value="{{$header->booking_no}}" @endisset class="form-control" readonly>
                                    </div>
                                </div> 
                                <div class="col-md-3">   
                                    <div class="form-group">
                                        <label for="booking_date">Booking Date</label>
                                        <input type="text" id="booking_date" name="booking_date" @isset($header->booking_date) value="{{\Carbon\Carbon::parse($header->booking_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
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
                                        <input type="text" id="forwarder_name" name="forwarder_name" class="form-control" @isset($header->forwarder_name) value="{{$header->forwarder_name}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div> 
                                <div class="col-md-6">    
                                    <div class="form-group">
                                        <label>Reference No</label>
                                        <input type="text" autocomplete="off" id="reference_no" name="reference_no" class="form-control" @isset($header->reference_no) value="{{$header->reference_no}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">   
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label>Driver Name</label>
                                        <input type="text" autocomplete="off" id="driver_name" name="driver_name" class="form-control" @isset($header->driver_name) value="{{$header->driver_name}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>                     
                                <div class="col-md-2">    
                                    <div class="form-group">
                                        <label>Vehicle No</label>
                                        <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" autocomplete="off" @isset($header->vehicle_no) value="{{$header->vehicle_no}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>        
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Container Status</label>
                                        <select id="container_status" name="container_status" class="form-control">
                                            <option value=""></option>
                                            <option value="Empty" @if(isset($header->container_status)) @if ('Empty' == $header->container_status) selected @endif @endif>Empty</option>
                                            <option value="Full" @if(isset($header->container_status)) @if ('Full' == $header->container_status) selected @endif @endif>Full</option>
                                        </select>
                                    </div>
                                </div>      
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Container Size</label>
                                        <select id="size_id" name="size_id" class="form-control">
                                            <option value=""></option>
                                            @foreach ($size_list as $item)
                                                <option value="{{$item->id}}" @if(isset($header->size_id)) @if ($item->id == $header->size_id) selected @endif @endif>{{$item->size_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>     
                            </div>
                            <div class="row">   
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Container Type</label>
                                        <select id="type_id" name="type_id" class="form-control">
                                            <option value=""></option>
                                            @foreach ($type_list as $item)
                                                <option value="{{$item->id}}" @if(isset($header->type_id)) @if ($item->id == $header->type_id) selected @endif @endif>{{$item->type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>    
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label>Invoice Type</label>
                                        <select id="invoice_type" name="invoice_type" class="form-control">
                                            <option value=""></option>
                                            @foreach ($invoice_list as $item)
                                                <option value="{{$item->id}}" @if(isset($header->invoice_type)) @if ($item->id == $header->invoice_type) selected @endif @endif>{{$item->type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>                     
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label>Container No</label>
                                        <input type="text" id="container_no" name="container_no" class="form-control" autocomplete="off" @isset($header->container_no) value="{{$header->container_no}}" @endisset @isset($header->id) @if ($header->status_flag !== "Open") disabled @endif @endisset>
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
        var d = new Date(); d.setDate( d.getDate() );
        $('#eta').datepicker({
            todayBtn: "linked",
            language: "it",
            autoclose: true,
            todayHighlight: true,
		    format: 'dd/mm/yyyy',
        }).datepicker("setDate", d);
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
                    url:"{{route('export.getForwarder')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        service_name: "CY Handling",
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
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.forwarder_name + "</div>" )
                .appendTo( ul );
        }; 

        if ($("#form-job").length > 0) {
            $("#form-job").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $("#form-job").serialize(), 
                        url: "{{ route('cy-booking.store') }}",
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
                            text: "Data Successfully Sending."                     
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
    });
</script>
@endpush