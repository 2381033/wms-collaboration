@extends("layouts.main")

@section("title")
    CY Payment
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>CY Payment</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>CY Payment</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap">  
                <div class="col-md 12">
                    <form id="form-payment" name="form-payment" method="post">
                        @csrf
                    </form>  
                    <form id="form-job" method="POST">
                        @csrf
                        <input type="hidden" id="payment_id" name="payment_id" @isset($header->id) value="{{$header->id}}" @endisset>
                        <div class="container mt-3">
                            <div class="row">
                                <div class="col-md-12 text-right">                    
                                    <div class="btn-group">
                                        <a href="{{url("/cy/payment/create/0")}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                                        @if (isset($header->id))
                                            @if ($header->confirmed_flag == "Open")
                                                <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>                                                    
                                                <button type="button" onclick="confirmJob();" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Submit</span></button>
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
                                        <input type="text" id="forwarder_name" name="forwarder_name" class="form-control" @isset($header->forwarder_name) value="{{$header->forwarder_name}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div> 
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Payment Amount</label>
                                        <input type="text" id="payment_amount" name="payment_amount" class="form-control" autocomplete="off" @isset($header->payment_amount) value="{{number_format($header->payment_amount, 0, ",", ".")}}" @endisset @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset readonly>
                                    </div>
                                </div>           
                                <div class="col-md-3">    
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="text" id="payment_date" name="payment_date" @isset($header->payment_date) value="{{\Carbon\Carbon::parse($header->payment_date)->format("d/m/Y")}}" @endisset class="form-control" @isset($header->id) @if ($header->confirmed_flag !== "Open") disabled @endif @endisset>
                                    </div>
                                </div>        
                            </div>
                        </div>
                    </form>
                </div>           
            </div>
            @isset($header)
                <div class="row info-wrap mt-2">
                    <div class="col-md-12 mb-2">          
                        @if ($header->confirmed_flag == "Open")
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> <span>Add Entry</span></button>
                            <button type="button" onclick="savePayment('detail');" class="btn btn-success btn-sm" id="btn-save-add"><i class="fas fa-save"></i> <span>Update</span></button>
                        @endif                        
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table-detail" class="table table-striped table-bordered table-sm" style="width:100%;" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Invoice Amount</th>
                                        <th>Payment Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </section> 
@endsection

@section("modal")
<div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-detail" name="form-detail" method="post">
                            @csrf
                        </form>  
                        <div class="table-responsive">
                            <table id="table-list" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Invoice Amount</th>
                                        <th>Payment Amount</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" onclick="savePayment('list');" class="btn btn-primary btn-sm" id="btn-save-add"><i class="fas fa-save"></i> <span>Save</span></button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script> 
    $(function() {
        $('#payment_date').datepicker({
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


        load_detail();

        function load_detail() {       
            var dataId = $('#payment_id').val();

            $('#table-detail').DataTable().destroy();         
            $('#table-detail').DataTable({
                "dom": '<"wrapper"flipt>',
                processing : true,
                serverSide : true,
                paging : false,
                searching: false,
                info: false,
                destroy: true,
                ajax : {
                    url : "{{ route('cy-payment-detail.index') }}",
                    type : "GET",
                    data : { 
                        payment_id: dataId
                    } 
                },
                columns : [              
                    { data: 'invoice_no', name: 'invoice_no'},
                    { data: 'invoice_amount', name: 'invoice_amount'},
                    { data: 'payment_amount', name: 'payment_amount'},   
                    { data: 'action', name: 'action', searchable: false, orderable: false },             
                ],
                order : [
                    [0, 'asc']
                ]
            });
        }        

        $( "#forwarder_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('export.getForwarderInvoice')}}",
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

        if ($("#form-job").length > 0) {
            $("#form-job").validate({
                submitHandler: function (form) {
                    $.ajax({
                        data: $("#form-job").serialize(), 
                        url: "{{ route('cy-payment.store') }}",
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
            
        $("#btn-add").click(function () {
            var payment_id = $('#payment_id').val();
            var forwarder_id = $('#forwarder_id').val();

            $('#table-list').DataTable().destroy();         
            $('#table-list').DataTable({
                "dom": '<"wrapper"flipt>',
                processing : true,
                serverSide : true,
                paging : false,
                destroy: true,
                ajax : {
                    url : "{{ route('cy-payment-detail.invoice') }}",
                    type : "GET",
                    data : { 
                        payment_id: payment_id,
                        forwarder_id: forwarder_id
                    } 
                },
                columns : [              
                    { data: 'job_no', name: 'job_no'},
                    { data: 'job_date', name: 'job_date'},
                    { data: 'invoice_amount', name: 'invoice_amount'},
                    { data: 'payment_amount', name: 'payment_amount'},
                ],
                order : [
                    [0, 'asc']
                ]
            });

            $("#modal-detail").modal({
                backdrop: "static", 
                keyboard: false,
                show: true
            }); 
        });  

        $(document).on('click', '.delete', function () {
            dataId = $(this).attr('id');
            $('#modal-konfirmasi').modal('show');
        });
        
        $('#btn-delete').click(function () {
            var requestUrl = "";
            var requestData = {};

            requestUrl = "{{route('cy-payment-detail.destroy')}}"; 
            requestData = {
                "_token": "{{ csrf_token() }}",
                "id" : dataId
            };

            $.ajax({
                url: requestUrl,
                type: 'delete',
                data: requestData,                
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (data) {
                    $("#loader").hide();
                    setTimeout(function () {
                        $('#modal-konfirmasi').modal('hide');

                        var oTable = "";
                        oTable = $('#table-detail').dataTable();

                        oTable.fnDraw(false);
                        
                        location.reload();       
                    });
                    
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
                    $("#loader").hide();
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            })
        });
    });

    function savePayment(type) {
        if (type == "detail") {
            var oTable = $('#table-detail').dataTable();
        } else {
            var oTable = $('#table-list').dataTable();
        }

        $('.hidden-detail').remove();        

        oTable.$('input[type="hidden"]').each(function(){
            if(this.value){
                $('#form-detail').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-detail')
                        .val(this.value)
                );
            }
        });  

        var amount = 0;
        oTable.$('input[type="text"]').each(function(){
            if(this.value){
                $('#form-detail').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .attr('class', 'hidden-detail')
                        .val(this.value)
                );

                amount = amount + parseFloat(this.value);
            }
        });  

        if ( amount == 0 ) {
            swal({
                icon: "error",
                text: "Payment amount must be required."                     
            });
            return false;
        }

        $('#form-detail').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', "payment_id")
                .attr('class', 'hidden-detail')
                .val($("#payment_id").val())
        );

        $.ajax({
            data: $('#form-detail').serialize(), 
            url: "{{ route('cy-payment-detail.store') }}",
            type: "POST",
            dataType: 'json',
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $('#form-detail').trigger("reset");        
                if($.isEmptyObject(data.error)){
                    var oTable = $('#table-detail').dataTable();
                    oTable.fnDraw(false);

                    swal({
                        icon: "success",
                        text: "Data was processed successfully."                     
                    });

                    window.location.reload();
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
            }
        });
    }

    function confirmJob() {
        var payment_id = $("#payment_id").val();
        var invoice_id = $("#invoice_id").val();
        
        $('#form-payment').trigger("reset");

        $('.hidden-payment').remove();

        $('#form-payment').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'payment_id')
                .attr('class', 'hidden-payment')
                .val(payment_id)
        );

        $('#form-payment').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'invoice_id')
                .attr('class', 'hidden-payment')
                .val(invoice_id)
        );        

        $.ajax({
            data: $("#form-payment").serialize(), 
            url: "{{ route('cy-payment.submit') }}",
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