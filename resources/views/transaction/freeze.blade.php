@extends('layouts.main')

@section('title')
    Freeze Stock & Location
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Freeze Stock & Location</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Freeze Stock & Location</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <fieldset>
                <legend>Stock Filter</legend>
                <div class="row">
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>Principal Name</label>
                            <select class="custom-select" id="principal_id" name="principal_id">
                                @foreach (Auth::user()->principal as $item)
                                    <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Product Name From</label>
                            <input type="hidden" id="product_code_from" name="product_code_from">
                            <input type="text" autocomplete="off" id="product_name_from" name="product_name_from" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Product Name To</label>
                            <input type="hidden" id="product_code_to" name="product_code_to">
                            <input type="text" autocomplete="off" id="product_name_to" name="product_name_to" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">   
                    <div class="col-md-3">    
                        <div class="form-group">
                            <label>Site</label>
                            <input type="hidden" id="site_id" name="site_id">
                            <input type="text" autocomplete="off" id="site_name" name="site_name" class="form-control">
                        </div>
                    </div> 
                    <div class="col-md-3">    
                        <div class="form-group">
                            <label>Area</label>
                            <input type="hidden" id="area_id" name="area_id">
                            <input type="text" autocomplete="off" id="area_name" name="area_name" class="form-control">
                        </div>
                    </div>                     
                    <div class="col-md-3">    
                        <div class="form-group">
                            <label>Location From</label>
                            <input type="text" id="location_from" name="location_from" class="form-control" autocomplete="off">
                        </div>
                    </div>                   
                    <div class="col-md-3">    
                        <div class="form-group">
                            <label>Location To</label>
                            <input type="text" id="location_to" name="location_to" class="form-control" autocomplete="off">
                        </div>
                    </div>         
                </div>
                <form id="form-freeze" name="form-freeze" method="post">
                    @csrf
                    <div class="row">   
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Unfreeze">Unfreeze</option>
                                    <option value="Freeze">Freeze</option>
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-9">    
                            <div class="form-group">
                                <label>Remarks</label>
                                <input type="text" autocomplete="off" id="remarks" name="remarks" class="form-control">
                            </div>
                        </div>            
                    </div>
                </form> 
            </fieldset>
            <div class="row">                
                <div class="col-md-12">            
                    <div class="btn-group mb-3">
                        <button type="button" id="refresh" name="refresh" class="btn btn-info btn-sm">Retrieve</button>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-process-picking" onclick="processFreeze();"><i class="fas fa-gear"></i> <span>Process</span></button>
                    </div>    
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">                                
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="table_list" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="2">
                                        <input type="checkbox" required="required" class="freeze-all">
                                    </th>
                                    <th rowspan="2">Serial No</th>
                                    <th rowspan="2">SKU No</th>
                                    <th rowspan="2">SKU Name</th>
                                    <th rowspan="2">Site Name</th>
                                    <th rowspan="2">Area Name</th>
                                    <th rowspan="2">Location</th>
                                    <th colspan="3">Quantity</th>
                                    <th colspan="3">Unit</th>
                                </tr>
                                <tr>
                                    <th>1st</th>
                                    <th>2nd</th>
                                    <th>3rd</th>
                                    <th>1st</th>
                                    <th>2nd</th>
                                    <th>3rd</th>
                                </tr>
                            </thead>
                        </table>
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
    $( function() {
        var today = getFirstDate(),
            weekAgo = getLastDate();

        var dateFormat = "dd/mm/yy";
        var from = $( "#date_from" )
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                }).datepicker('setDate', today);

        var to = $( "#date_to" ).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 3
            }).datepicker('setDate', weekAgo);

        $( "#date_from" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
        $( "#date_to" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
    } );

    $(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        load_data();

        $('#refresh').click(function () {
            var principal_id = $('#principal_id').val();
            
            if (principal_id != '' && principal_id > 0) {
                $('#table_list').DataTable().destroy();
                load_data();
            } else {
                swal({
                    icon: "error",
                    text: "Principal name cannot be empty."                     
                });
            }                
        });

        function load_data(principal = '') {
            $('#table_list').DataTable({
                "dom": '<"toolbar">frtip',
                processing : true,
                serverSide : true,
                paging : false,
                ajax : {
                    url : "{{ route('stock-freeze.index') }}",
                    type : "GET",
                    data : { 
                        principal_id: $('#principal_id').val(),
                        status: $('#status').val(),
                        product_from: $('#product_code_from').val(),
                        product_to: $('#product_code_to').val(),
                        site_id: $('#site_id').val(),
                        area_id: $('#area_id').val(),
                        location_from: $('#location_from').val(),
                        location_to: $('#location_to').val()
                    } 
                },
                columns : [
                    { data: 'check', name: 'check', searchable: false, orderable: false },
                    { data:'serial_no', name:'serial_no'},
                    { data:'product_code', name:'product_code'},
                    { data:'product_name', name:'product_name'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
                    { data:'buom', name:'buom'}
                ],
                order : [
                    [0, 'asc']
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
                    url:"{{route('stock.getStockProduct')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {    
                $('#product_name_from').val(ui.item.product_code + " - " + ui.item.product_name);
                $('#product_code_from').val(ui.item.product_code);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>Code : " + item.product_code + "<br> Name : " + item.product_name + "</b><br>Stock Available 1st : " + item.pqty + ' ' + item.puom + ', 2nd : ' + item.mqty + ' ' + item.muom + ', 3rd : ' + item.bqty + ' ' + item.buom + "</div>" )
                .appendTo( ul );
        }; 

        $( "#product_name_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('stock.getStockProduct')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {    
                $('#product_name_to').val(ui.item.product_code + " - " + ui.item.product_name);
                $('#product_code_to').val(ui.item.product_code);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>Code : " + item.product_code + "<br> Name : " + item.product_name + "</b><br>Stock Available 1st : " + item.pqty + ' ' + item.puom + ', 2nd : ' + item.mqty + ' ' + item.muom + ', 3rd : ' + item.bqty + ' ' + item.buom + "</div>" )
                .appendTo( ul );
        }; 

        $( "#site_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('stock.getStockSite')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        search: request.term
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
                .append( "<div><b>" + item.site_name + "</b></div>" )
                .appendTo( ul );
        };  

        $( "#area_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('site.getAreaAuto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        site_id: $('#site_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $('#site_id').val(ui.item.site_id);
                $('#site_name').val(ui.item.site_name);
                $('#area_id').val(ui.item.area_id);
                $('#area_name').val(ui.item.area_name);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>" + item.site_name + "</b><br>" + item.area_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#location_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('stock.getStockLocation')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_id: $('#product_id').val(),
                        site_id: $('#site_id').val(),
                        area_id: $('#area_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $('#site_id').val(ui.item.site_id);
                $('#site_name').val(ui.item.site_name);
                $('#area_id').val(ui.item.area_id);
                $('#area_name').val(ui.item.area_name);
                $('#location_from').val(ui.item.location_code);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>Site : " + item.site_name + ", Area : " + item.area_name + "</b><br>" + item.location_code + "</div>" )
                .appendTo( ul );
        };    

        $( "#location_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('stock.getStockLocation')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $('#principal_id').val(),
                        product_id: $('#product_id').val(),
                        site_id: $('#site_id').val(),
                        area_id: $('#area_id').val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $('#site_id').val(ui.item.site_id);
                $('#site_name').val(ui.item.site_name);
                $('#area_id').val(ui.item.area_id);
                $('#area_name').val(ui.item.area_name);
                $('#location_to').val(ui.item.location_code);
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div><b>Site : " + item.site_name + ", Area : " + item.area_name + "</b><br>" + item.location_code + "</div>" )
                .appendTo( ul );
        };  

        $('#table_list').on('click', '.freeze', function() {
            if (this.checked == true) {                    
                $('.freeze-all').prop('checked', true);
            } else {                    
                $('.freeze-all').prop('checked', false);
            }
        });

        $('#table_list').on('click', '.freeze-all', function() {
            $('.freeze').prop('checked', this.checked);
        });  
    });

    function processFreeze() {
        var remarks = $("#remarks").val();
        var jumlah = 0;

        if (remarks == '') {            
            swal({
                icon: "error",
                text: "Remarks cannot be empty."                     
            });
            return false;
        }

        var oTable = $('#table_list').dataTable();
        // $('#form-freeze').trigger("reset");

        $('.hidden-picking').remove();
        oTable.$('input[type="checkbox"]').each(function(){
            if(this.checked){
                jumlah = 1;
                $('#form-freeze').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-picking')
                        .val(this.value)
                );
            }
        });  

        if (jumlah == 0) {         
            swal({
                icon: "error",
                text: "Please select row first."                     
            });
            return false;
        }
        
        $.ajax({
            data: $('#form-freeze').serialize(), 
            url: "{{ route('stock-freeze.submit') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if($.isEmptyObject(data.error)){
                    $('#form-freeze').trigger("reset");
                    var oTable = $('#table_list').dataTable();
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
                console.log(data);
            }
        });
    }
</script>
@endpush