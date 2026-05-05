@extends("layouts.main")

@section("title")
    Stock Adjustment
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Adjustment</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Stock Adjustment</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="job_no">Job Number</label>
                        <input type="hidden" id="adjustment_id" name="adjustment_id" @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                        <input type="text" id="job_no" name="job_no" @isset($job_view->adjust_no) value="{{$job_view->adjust_no}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="job_date">Job Date</label>
                        <input type="text" id="job_date" name="job_date" @isset($job_view->adjust_date) value="{{\Carbon\Carbon::parse($job_view->adjust_date)->format('d-m-Y')}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-4">   
                    <div class="form-group">
                        <label for="type_name">Adjust Type</label>
                        <input type="text" id="type_name" name="type_name" @isset($job_view->type_name) value="{{$job_view->type_name}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-4">   
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" @isset($job_view->description) value="{{$job_view->description}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
            </div>
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md-12">
                    <div class="btn-group">
                        <a class="btn btn-success btn-sm" id="btn-upload"><i class="fas fa-upload"></i> <span>Upload File</span></a>
                        <a class="btn btn-warning btn-sm" onclick="processConfirm()"><i class="fas fa-play"></i> <span>Proccess</span></a>
                    </div>
                </div>
                <div class="col-md-12 mt-3">                        
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
                                    <th rowspan="2">Principal Name</th>
                                    <th rowspan="2">Product Name</th>
                                    <th rowspan="2">Batch No</th>           
                                    <th rowspan="2">Site Name</th>
                                    <th rowspan="2">Area Name</th>
                                    <th rowspan="2">Location</th>
                                    <th rowspan="2">Adjust Type</th>
                                    <th colspan="3">Adjustment Qty</th>
                                    <th colspan="3">Unit Of Measure</th>
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

@section("modal")
<div class="modal fade" tabindex="-1" role="dialog" id="modal-upload">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-upload" class="form-horizontal form-bordered" method="POST">
                <input type="hidden" id="adjust_id" name="adjust_id" @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="file" id="filename" name="filename" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" id="btn-save" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Upload</button>
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    $(document).ready(function() { 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var CSRF_TOKEN = $("meta[name='csrf-token']").attr("content");
        
        load_data();

        function load_data() {
            $("#confirm_table").DataTable().destroy();     
            $("#confirm_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                paging: false,
                info: false,
                searching: false,
                ajax : {
                    url : "{{ route('adjustment-autorization.detail') }}",
                    type : "GET",
                    data : { adjust_id: $("#adjustment_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'principal_name', name:'principal_name'},
                    { data:'product_name', name:'product_name'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'job_type', name:'job_type'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }

        $("#btn-upload").click(function () {
            $('#modal-upload').modal({
                backdrop: 'static', 
                keyboard: false,
                show: true
            }); 
        });   

        if ($("#form-upload").length > 0) {
            $("#form-upload").validate({
                submitHandler: function (form) {
                    let myForm = document.getElementById("form-upload");
                    let formData = new FormData(myForm);
                    formData.append("filename", $('#filename')[0].files[0]);
                    
                    $.ajax({
                        data: formData,
                        url: "{{ route('adjustment-autorization.upload') }}", 
                        type: "POST", 
                        dataType: 'json',
                        enctype: 'multipart/form-data',
                        processData: false, 
                        contentType: false,
                        cache: false,
                        success: function (data) {                          
                            if($.isEmptyObject(data.error)){                  
                                $('#form-upload').trigger("reset");                                

                                swal({
                                    icon: "success",
                                    text: data.success                     
                                });

                                location.reload();
                            } else {
                                var pesan = "<div class='text-left alert alert-danger'>";
                                for (var i = 0; i < data.error.length; i++) {                                            
                                    pesan += data.error[i]+'</br>'; 
                                }
                                pesan += '</div>';
                                
                                const wrapper = document.createElement('div');        
                                wrapper.innerHTML = pesan;
                                swal({
                                    icon: "error",
                                    content: wrapper                     
                                });
                            } 
                        },
                        error: function (data) {
                            console.log(data);
                            swal({
                                icon: "error",
                                content: data.error                     
                            });
                        }
                    });
                }
            })
        }

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
    });

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
            url: "{{ route('adjustment-autorization.submit') }}",
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
                        text: data.success
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