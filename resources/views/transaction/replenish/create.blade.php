@extends("layouts.main")

@section("title")
    Stock Replenishment
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Replenishment</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>Stock Replenishment</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="replenish_no">Job Number</label>
                        <input type="text" id="replenish_no" name="replenish_no" @isset($job_view->replenish_no) value="{{$job_view->replenish_no}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="replenish_date">Job Date</label>
                        <input type="text" id="replenish_date" name="replenish_date" @isset($job_view->replenish_date) value="{{\Carbon\Carbon::parse($job_view->replenish_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">                    
                    <div class="btn-group">
                        <a href="{{url("/inventory/stock-replenish/create/0")}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                        &nbsp;&nbsp;
                        <a href="#" class="btn btn-info btn-sm"><i class="fas fa-folder-open"></i> <span>Open Job</span></a>
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="replenish-link" data-toggle="tab" href="#replenish-tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-box"></i> Replenishment</a>
                        </li>
                        @if (isset($job_view->id) && !empty($job_view->id))
                        <li class="nav-item">
                            <a class="nav-link" id="cancel-link" data-toggle="tab" href="#cancel-tab" role="tab" aria-controls="cancel" aria-selected="false">
                            <i class="fas fa-box"></i> Cancel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="confirm-link" data-toggle="tab" href="#confirm-tab" role="tab" aria-controls="confirm" aria-selected="false">
                            <i class="fas fa-box"></i> Confirmation</a>
                        </li>
                        @endif 
                    </ul>
                    <div class="tab-content" id="replenishTab">
                        <div class="tab-pane fade show active" id="replenish-tab" role="tabpanel" aria-labelledby="home-tab5"> 
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="replenish_id" name="replenish_id" @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Principal Name</label>
                                                <select class="custom-select" id="principal_id" name="principal_id" @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->principal as $item)
                                                        <option value="{{$item->id}}" @if(isset($job_view->principal_id) && !empty($job_view->principal_id)) @if ($item->id == $job_view->principal_id) selected @endif @endif>{{$item->principal_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name From</label>
                                                <input type="hidden" id="product_id_from" name="product_id_from" @isset($job_view->product_id_from) value="{{$job_view->product_id_from}}" @endisset>
                                                <input type="hidden" id="product_code_from" name="product_code_from" @isset($job_view->product_code_from) value="{{$job_view->product_code_from}}" @endisset>
                                                <input type="text" autocomplete="off" id="product_name_from" name="product_name_from" class="form-control" @isset($job_view->product_name_from) value="{{$job_view->product_name_from}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name To</label>
                                                <input type="hidden" id="product_id_to" name="product_id_to" @isset($job_view->product_id_to) value="{{$job_view->product_id_to}}" @endisset>
                                                <input type="hidden" id="product_code_to" name="product_code_to" @isset($job_view->product_code_to) value="{{$job_view->product_code_to}}" @endisset>
                                                <input type="text" autocomplete="off" id="product_name_to" name="product_name_to" class="form-control" @isset($job_view->product_name_to) value="{{$job_view->product_name_to}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">   
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Site</label>
                                                <input type="hidden" id="site_id" name="site_id" @isset($job_view->site_id) value="{{$job_view->site_id}}" @endisset>
                                                <input type="text" autocomplete="off" id="site_name" name="site_name" class="form-control" @isset($job_view->site_name) value="{{$job_view->site_name}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div> 
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Area</label>
                                                <input type="hidden" id="area_id" name="area_id" @isset($job_view->area_id) value="{{$job_view->area_id}}" @endisset>
                                                <input type="text" autocomplete="off" id="area_name" name="area_name" class="form-control" @isset($job_view->area_name) value="{{$job_view->area_name}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div>                     
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Location From</label>
                                                <input type="hidden" id="location_id_from" name="location_id_from" @isset($job_view->location_id_from) value="{{$job_view->location_id_from}}" @endisset>
                                                <input type="text" id="location_code_from" name="location_code_from" class="form-control" autocomplete="off" @isset($job_view->location_code_from) value="{{$job_view->location_code_from}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div>                   
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Location To</label>
                                                <input type="hidden" id="location_id_to" name="location_id_to" @isset($job_view->location_id_to) value="{{$job_view->location_id_to}}" @endisset>
                                                <input type="text" id="location_code_to" name="location_code_to" class="form-control" autocomplete="off" @isset($job_view->location_code_to) value="{{$job_view->location_code_to}}" @endisset @isset($job_view->id) @if ($job_view->allocated_flag == "Yes") disabled @endif @endisset>
                                            </div>
                                        </div>         
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">                                                    
                                                @if (isset($job_view->id))
                                                    @if ($job_view->allocated_flag == "No")
                                                        <button type="button" onclick="retrieveLocation();" class="btn btn-primary btn-sm"><i class="fas fa-sync"></i> <span>Retrieve</span></button>
                                                        <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>                                                    
                                                    @endif                                                    
                                                @else 
                                                    <button type="button" onclick="retrieveLocation();" class="btn btn-primary btn-sm"><i class="fas fa-sync"></i> <span>Retrieve</span></button>
                                                    <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                                @endif
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="row mt-3">
                                    <div class="col-md-12">                                          
                                        <div class="table-responsive">
                                            <table id="entry_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                                <thead class="text-center">
                                                    <tr>   
                                                        <th>
                                                            <input type="checkbox" class="entry-check-all">
                                                        </th>
                                                        <th>Product Name</th>       
                                                        <th>Site Name</th>
                                                        <th>Area Name</th>
                                                        <th>Location</th>
                                                        <th>Unit Of Measure</th>
                                                        <th>Reorder Level</th>
                                                        <th>Reorder Qty</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="cancel-tab" role="tabpanel" aria-labelledby="cancel-tab5">                         
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processCancel()" id="btn-process-cancel"><i class="fas fa-play"></i> <span>Proccess</span></a>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">              
                                    <form id="form-cancel" name="form-cancel" method="post">
                                        @csrf
                                    </form>                                                      
                                    <div class="table-responsive">
                                        <table id="cancel_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th rowspan="2">
                                                        <input type="checkbox" required="required" class="cancel-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Quantity</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>                                                   
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="tab-pane fade show" id="confirm-tab" role="tabpanel" aria-labelledby="confirm-tab5">                         
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processConfirm()" id="btn-process-confirm"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                        <a id="pick-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Pick Report</span></a>
                                        <a id="put-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Put-away Report</span></a>
                                        <a id="pickput-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Combined Pick and Put-away Report</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">              
                                    <form id="form-confirm" name="form-confirm" method="post">
                                        @csrf
                                    </form>                                                      
                                    <div class="table-responsive">
                                        <table id="confirm_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th rowspan="2">
                                                        <input type="checkbox" required="required" class="confirm-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Quantity</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>                                                     
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
        
        $("#cancel-link").click( function(e) {
            e.preventDefault(); 
            load_cancel();
        });

        $("#confirm-link").click( function(e) {
            e.preventDefault(); 
            load_confirm();
        });

        load_data();

        function load_data() {
            link_id = $(".nav-tabs .active").attr("id");
            if (link_id == "cancel-link") {
                load_cancel();
            } else if (link_id == "confirm-link") {
                load_confirm();
            }
        }
        
        function load_cancel() {
            $("#cancel_table").DataTable().destroy();   
            $("#cancel_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('replenish-cancel.index') }}",
                    type : "GET",
                    data : { replenish_id: $("#replenish_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'product_name', name:'product_name'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'puom', name:'puom'},
                    { data:'mqty', name:'mqty'},
                    { data:'muom', name:'muom'},
                    { data:'bqty', name:'bqty'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }
        
        function load_confirm() {
            $("#confirm_table").DataTable().destroy();   
            $("#confirm_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('replenish-confirm.index') }}",
                    type : "GET",
                    data : { replenish_id: $("#replenish_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'product_name', name:'product_name'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'puom', name:'puom'},
                    { data:'mqty', name:'mqty'},
                    { data:'muom', name:'muom'},
                    { data:'bqty', name:'bqty'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }

        $( "#product_name_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-product.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#product_name_from').val(ui.item.product_name);
                $('#product_code_from').val(ui.item.product_code);
                $('#product_id_from').val(ui.item.product_id);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.product_name + "</div>" )
                .appendTo( ul );
        };  
        
        $( "#product_name_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-product.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#product_name_to').val(ui.item.product_name);
                $('#product_code_to').val(ui.item.product_code);
                $('#product_id_to').val(ui.item.product_id);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.product_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#site_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-site.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_from: $('#product_code_from').val(),
                        product_to: $('#product_code_to').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#site_id').val(ui.item.site_id);
                $('#site_name').val(ui.item.site_name);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.site_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#area_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-area.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_from: $('#product_code_from').val(),
                        product_to: $('#product_code_to').val(),
                        site_id: $('#site_id').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#area_id').val(ui.item.area_id);
                $('#area_name').val(ui.item.area_name);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.area_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#location_code_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-location.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_from: $('#product_code_from').val(),
                        product_to: $('#product_code_to').val(),
                        site_id: $('#site_id').val(),
                        area_id: $('#area_id').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#location_code_from').val(ui.item.location_code);
                $('#location_id_from').val(ui.item.location_id);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.location_code + "</div>" )
                .appendTo( ul );
        };  

        $( "#location_code_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('replenish-location.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_from: $('#product_code_from').val(),
                        product_to: $('#product_code_to').val(),
                        site_id: $('#site_id').val(),
                        area_id: $('#area_id').val(),
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#location_code_to').val(ui.item.location_code);
                $('#location_id_to').val(ui.item.location_id);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>" + item.location_code + "</div>" )
                .appendTo( ul );
        };
        
        if ($("#form-job").length > 0) {
            $("#form-job").validate({
                submitHandler: function (form) {
                    var actionType = $("#btn-save-job").val();
                    $("#btn-save-job").html("Sending..");

                    $.ajax({
                        data: $("#form-job").serialize(), 
                        url: "{{ route('replenish-job.store') }}",
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
                                $("#btn-save-job").html("Save");
                            } 
                        },
                        error: function (data) {
                            console.log("Error:", data);
                            $("#btn-save-job").html("Save");
                        }
                    });
                }
            })
        }

        $('#entry_table').on('click', '.entry-check', function() {
            if (this.checked == true) {                 
                $('.entry-check-all').prop('checked', true);
            } else {                    
                $('.entry-check-all').prop('checked', false);
            }
        });

        $('#entry_table').on('click', '.entry-check-all', function() {
            $('.entry-check').prop('checked', this.checked);
        });  

        $("#cancel_table").on("click", ".cancel-check", function() {
            if (this.checked == true) {                    
                $(".cancel-check-all").prop("checked", true);
            } else {                    
                $(".cancel-check-all").prop("checked", false);
            }
        });

        $("#cancel_table").on("click", ".cancel-check-all", function() {
            $(".cancel-check").prop("checked", this.checked);
        });     

        $("#confirm_table").on("click", ".confirm-check", function() {
            if (this.checked == true) {                    
                $(".confirm-check-all").prop("checked", true);
            } else {                    
                $(".confirm-check-all").prop("checked", false);
            }
        });

        $("#confirm_table").on("click", ".confirm-check-all", function() {
            $(".confirm-check").prop("checked", this.checked);
        });    
        
        $("body").on("click", "#pickput-print", function () {
            var data_id = $("#replenish_id").val();

            window.open("{{url('/inventory/stock-replenish/report/pickput/')}}" + "/" + data_id, "ReplenishReport","width=800,height=600")
        }); 

        $("body").on("click", "#pick-print", function () {
            var data_id = $("#replenish_id").val();

            window.open("{{url('/inventory/stock-replenish/report/pick/')}}" + "/" + data_id, "ReplenishReport","width=800,height=600")
        }); 

        $("body").on("click", "#put-print", function () {
            var data_id = $("#replenish_id").val();

            window.open("{{url('/inventory/stock-replenish/report/put/')}}" + "/" + data_id, "ReplenishReport","width=800,height=600")
        });
    });

    function retrieveLocation() {
        $('#entry_table').DataTable().destroy();
        $('#entry_table').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            paging : false,
            info : false,
            ajax : {
                url : "{{ route('replenish-location.index') }}",
                type : "GET",
                data : { 
                    principal_id: $('#principal_id').val(),
                    product_from: $('#product_code_from').val(),
                    product_to: $('#product_code_to').val(),
                    site_id: $('#site_id').val(),
                    area_id: $('#area_id').val(),
                    location_from: $('#location_code_from').val(),
                    location_to: $('#location_code_to').val()
                } 
            },
            columns : [
                { data:'check', name:'check', searchable: false, orderable: false },
                { data:'product_name', name:'product_name'},
                { data:'site_name', name:'site_name'},
                { data:'area_name', name:'area_name'},
                { data:'location_code', name:'location_code'},
                { data:'puom', name:'puom'},
                { data:'reorder_level', name:'reorder_level'},
                { data:'reorder_qty', name:'reorder_qty'}
            ],
            order : [
                [0, 'asc']
            ]
        });
    }    

    function processCancel() {
        var oTable = $("#cancel_table").dataTable();
        $("#form-cancel").trigger("reset");

        $(".hidden-cancel").remove();
        oTable.$("input[type='checkbox']").each(function(){
            if(this.checked){
                $("#form-cancel").append(
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-cancel")
                        .val(this.value)
                );
            }
        });  
        
        $("#btn-process-cancel").html("Sending..");

        $.ajax({
            data: $("#form-cancel").serialize(), 
            url: "{{ route('replenish-cancel.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if($.isEmptyObject(data.error)){
                    $("#form-cancel").trigger("reset");
                    $("#btn-process-cancel").html("Process"); 
                    var oTable = $("#cancel_table").dataTable();
                    oTable.fnDraw(false);

                    swal({
                        icon: "success",
                        text: "Data was processed successfully."                     
                    });
                } else {
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            },
            error: function (data) {
                $("#btn-process-cancel").html("Process");
            }
        });
    }

    function processConfirm() {
        var oTable = $("#confirm_table").dataTable();
        $("#form-confirm").trigger("reset");

        $(".hidden-confirm").remove();
        oTable.$("input[type='checkbox']").each(function(){
            if(this.checked){
                $("#form-confirm").append(
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-confirm")
                        .val(this.value)
                );
            }
        });  
        
        $("#btn-process-confirm").html("Sending..");

        $.ajax({
            data: $("#form-confirm").serialize(), 
            url: "{{ route('replenish-confirm.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if($.isEmptyObject(data.error)){
                    $("#form-confirm").trigger("reset");
                    $("#btn-process-confirm").html("Process"); 
                    var oTable = $("#confirm_table").dataTable();
                    oTable.fnDraw(false);

                    swal({
                        icon: "success",
                        text: "Data was processed successfully."                     
                    });
                } else {
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            },
            error: function (data) {
                $("#btn-process-confirm").html("Process");
            }
        });
    }
</script>
@endpush