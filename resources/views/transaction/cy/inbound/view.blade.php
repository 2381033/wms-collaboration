@extends("layouts.main")

@section("title")
    CY Inbound
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Inbound</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>CY Inbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">                    
                    <div class="container mt-3">
                        <form id="form-inbound" name="form-inbound" method="post">
                            @csrf
                        </form>  
                        <div class="row">
                            <div class="col-md-12 text-right">                    
                                <div class="btn-group">
                                    @if (isset($header->id) && $header->confirmed_flag == "Open")
                                        <button type="button" onclick="confirmJob();" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Submit</span></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">   
                                <div class="form-group">
                                    <label for="booking_no">Job Number</label>
                                    <input type="hidden" id="inbound_id" name="inbound_id" @isset($header->id) value="{{$header->id}}" @endisset>
                                    <input type="text" id="job_no" @isset($header->job_no) value="{{$header->job_no}}" @endisset class="form-control" readonly>
                                </div>
                            </div> 
                            <div class="col-md-3">   
                                <div class="form-group">
                                    <label for="booking_date">Job Date</label>
                                    <input type="text" id="job_date" @isset($header->job_date) value="{{\Carbon\Carbon::parse($header->job_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
                                </div>
                            </div> 
                            <div class="col-md-6">    
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input type="text" autocomplete="off" id="forwarder_id" class="form-control" @isset($header->forwarder_name) value="{{$header->forwarder_name}}" @endisset readonly>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-6">    
                                <div class="form-group">
                                    <label>Reference No</label>
                                    <input type="text" autocomplete="off" id="reference_no" class="form-control" @isset($header->reference_no) value="{{$header->reference_no}}" @endisset readonly>
                                </div>
                            </div> 
                            <div class="col-md-4">    
                                <div class="form-group">
                                    <label>Driver Name</label>
                                    <input type="text" autocomplete="off" id="driver_name" class="form-control" @isset($header->driver_name) value="{{$header->driver_name}}" @endisset readonly>
                                </div>
                            </div>                     
                            <div class="col-md-2">    
                                <div class="form-group">
                                    <label>Vehicle No</label>
                                    <input type="text" id="vehicle_no" class="form-control" autocomplete="off" @isset($header->vehicle_no) value="{{$header->vehicle_no}}" @endisset readonly>
                                </div>
                            </div>        
                        </div>
                        <div class="row">   
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Container Status</label>
                                    <input type="text" id="container_status" class="form-control" autocomplete="off" @isset($header->container_status) value="{{$header->container_status}}" @endisset readonly>
                                </div>
                            </div>     
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Container Size</label>
                                    <input type="text" id="size_id" class="form-control" autocomplete="off" @isset($header->size_name) value="{{$header->size_name}}" @endisset readonly>                                        
                                </div>
                            </div>    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Container Type</label>
                                    <input type="text" id="type_id" class="form-control" autocomplete="off" @isset($header->type_name) value="{{$header->type_name}}" @endisset readonly>
                                </div>
                            </div>    
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Container No</label>   
                                    <input type="text" name="container_no" value="{{$header->container_no}}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">   
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Invoice Type</label>
                                    <input type="text" id="invoice_type" class="form-control" autocomplete="off" @isset($header->invoice_name) value="{{$header->invoice_name}}" @endisset readonly>
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>           
            </div>
        </div>
    </section> 
@endsection

@section("modal")
@endsection

@push("scripts")
<script> 
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    });

    function confirmJob() {
        var inbound_id = $("#inbound_id").val();

        $('#form-inbound').trigger("reset");

        $('.hidden-inbound').remove();

        $('#form-inbound').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'inbound_id')
                .attr('class', 'hidden-inbound')
                .val(inbound_id)
        );

        $.ajax({
            data: $("#form-inbound").serialize(), 
            url: "{{ route('cy-inbound.store') }}",
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