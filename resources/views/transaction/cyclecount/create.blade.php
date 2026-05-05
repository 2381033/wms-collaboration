@extends("layouts.main")

@section("title")
    Cycle Count
@endsection

@section("content")
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Cycle Count</h2>
                <ol>
                    <li><a href="{{route("home")}}">Home</a></li>
                    <li>Cycle Count</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="cyclecount_no">Job Number</label>
                        <input type="text" id="cyclecount_no" name="cyclecount_no" @isset($job_view->cyclecount_no) value="{{$job_view->cyclecount_no}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="cyclecount_date">Job Date</label>
                        <input type="text" id="cyclecount_date" name="cyclecount_date" @isset($job_view->cyclecount_date) value="{{\Carbon\Carbon::parse($job_view->cyclecount_date)->format("d-m-Y")}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">                    
                    <div class="btn-group">
                        <a href="{{url("/inventory/cycle-count/create/0")}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                        &nbsp;&nbsp;
                        <a href="#" class="btn btn-info btn-sm"><i class="fas fa-folder-open"></i> <span>Open Job</span></a>
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab" aria-controls="job" aria-selected="true">
                            <i class="fas fa-box"></i> Entry</a>
                        </li>
                        @if (isset($job_view->id) && !empty($job_view->id))
                        <li class="nav-item">
                            <a class="nav-link" id="entry-link" data-toggle="tab" href="#entry-tab" role="tab" aria-controls="entry" aria-selected="false">
                            <i class="fas fa-box"></i> Entry Actual</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="release-link" data-toggle="tab" href="#release-tab" role="tab" aria-controls="release" aria-selected="false">
                            <i class="fas fa-box"></i> Release Stock</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="confirm-link" data-toggle="tab" href="#confirm-tab" role="tab" aria-controls="confirm" aria-selected="false">
                            <i class="fas fa-box"></i> Confirm Investigate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="review-link" data-toggle="tab" href="#review-tab" role="tab" aria-controls="review" aria-selected="false">
                            <i class="fas fa-box"></i> Review</a>
                        </li>
                        @endif 
                    </ul>
                    <div class="tab-content" id="replenishTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel" aria-labelledby="job-tab5"> 
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="cycle_id" name="cycle_id" @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Principal Name</label>
                                                <select class="custom-select" id="principal_id" name="principal_id" @isset($job_view->id) disabled @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->principal as $item)
                                                        <option value="{{$item->id}}" @if(isset($job_view->principal_id) && !empty($job_view->principal_id)) @if ($item->id == $job_view->principal_id) selected @endif @endif>{{$item->principal_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description" name="description" class="form-control" @isset($job_view->description) value="{{$job_view->description}}" @endisset @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Group Name From</label>
                                                <input type="hidden" id="group_id_from" name="group_id_from" @isset($job_view->group_id_from) value="{{$job_view->group_id_from}}" @endisset>
                                                <input type="hidden" id="group_code_from" name="group_code_from" @isset($job_view->group_code_from) value="{{$job_view->group_code_from}}" @endisset>
                                                <input type="text" autocomplete="off" id="group_name_from" name="group_name_from" class="form-control" @isset($job_view->group_name_from) value="{{$job_view->group_name_from}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Group Name To</label>
                                                <input type="hidden" id="group_id_to" name="group_id_to" @isset($job_view->group_id_to) value="{{$job_view->group_id_to}}" @endisset>
                                                <input type="hidden" id="group_code_to" name="group_code_to" @isset($job_view->group_code_to) value="{{$job_view->group_code_to}}" @endisset>
                                                <input type="text" autocomplete="off" id="group_name_to" name="group_name_to" class="form-control" @isset($job_view->group_name_to) value="{{$job_view->group_name_to}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Brand Name From</label>
                                                <input type="hidden" id="brand_id_from" name="brand_id_from" @isset($job_view->brand_id_from) value="{{$job_view->brand_id_from}}" @endisset>
                                                <input type="hidden" id="brand_code_from" name="brand_code_from" @isset($job_view->brand_code_from) value="{{$job_view->brand_code_from}}" @endisset>
                                                <input type="text" autocomplete="off" id="brand_name_from" name="brand_name_from" class="form-control" @isset($job_view->brand_name_from) value="{{$job_view->brand_name_from}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Brand Name To</label>
                                                <input type="hidden" id="brand_id_to" name="brand_id_to" @isset($job_view->brand_id_to) value="{{$job_view->brand_id_to}}" @endisset>
                                                <input type="hidden" id="brand_code_to" name="brand_code_to" @isset($job_view->brand_code_to) value="{{$job_view->brand_code_to}}" @endisset>
                                                <input type="text" autocomplete="off" id="brand_name_to" name="brand_name_to" class="form-control" @isset($job_view->brand_name_to) value="{{$job_view->brand_name_to}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name From</label>
                                                <input type="hidden" id="product_id_from" name="product_id_from" @isset($job_view->product_id_from) value="{{$job_view->product_id_from}}" @endisset>
                                                <input type="hidden" id="product_code_from" name="product_code_from" @isset($job_view->product_code_from) value="{{$job_view->product_code_from}}" @endisset>
                                                <input type="text" autocomplete="off" id="product_name_from" name="product_name_from" class="form-control" @isset($job_view->product_name_from) value="{{$job_view->product_name_from}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name To</label>
                                                <input type="hidden" id="product_id_to" name="product_id_to" @isset($job_view->product_id_to) value="{{$job_view->product_id_to}}" @endisset>
                                                <input type="hidden" id="product_code_to" name="product_code_to" @isset($job_view->product_code_to) value="{{$job_view->product_code_to}}" @endisset>
                                                <input type="text" autocomplete="off" id="product_name_to" name="product_name_to" class="form-control" @isset($job_view->product_name_to) value="{{$job_view->product_name_to}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">   
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Site</label>
                                                <input type="hidden" id="site_id" name="site_id" @isset($job_view->site_id) value="{{$job_view->site_id}}" @endisset>
                                                <input type="text" autocomplete="off" id="site_name" name="site_name" class="form-control" @isset($job_view->site_name) value="{{$job_view->site_name}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div> 
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Area</label>
                                                <input type="hidden" id="area_id" name="area_id" @isset($job_view->area_id) value="{{$job_view->area_id}}" @endisset>
                                                <input type="text" autocomplete="off" id="area_name" name="area_name" class="form-control" @isset($job_view->area_name) value="{{$job_view->area_name}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>                     
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Location From</label>
                                                <input type="hidden" id="location_id_from" name="location_id_from" @isset($job_view->location_id_from) value="{{$job_view->location_id_from}}" @endisset>
                                                <input type="text" id="location_code_from" name="location_code_from" class="form-control" autocomplete="off" @isset($job_view->location_code_from) value="{{$job_view->location_code_from}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>                   
                                        <div class="col-md-3">    
                                            <div class="form-group">
                                                <label>Location To</label>
                                                <input type="hidden" id="location_id_to" name="location_id_to" @isset($job_view->location_id_to) value="{{$job_view->location_id_to}}" @endisset>
                                                <input type="text" id="location_code_to" name="location_code_to" class="form-control" autocomplete="off" @isset($job_view->location_code_to) value="{{$job_view->location_code_to}}" @endisset @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>         
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">                                                    
                                                @if (!isset($job_view->id))
                                                    <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                                @else 
                                                    <a id="blank-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Blank Form</span></a>
                                                    <a id="book-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Book Form</span></a>
                                                @endif
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="entry-tab" role="tabpanel" aria-labelledby="entry-tab5">             
                            <form id="form-entry" name="form-entry" method="post">
                                @csrf                                           
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                                @if ($job_view->confirmed_flag == 'No')
                                                    <button type="submit" class="btn btn-success btn-sm" id="btn-update"><i class="fas fa-save"></i> <span>Update</span></button>
                                                @endif
                                            @else
                                                <button type="submit" class="btn btn-success btn-sm" id="btn-update"><i class="fas fa-save"></i> <span>Update</span></button>
                                            @endif                                                       
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">                                          
                                        <div class="table-responsive">
                                            <table id="entry_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                                <thead class="text-center">
                                                    <tr>   
                                                        <th rowspan="2">Product Name</th>
                                                        <th rowspan="2">Batch No</th>           
                                                        <th rowspan="2">Site Name</th>
                                                        <th rowspan="2">Area Name</th>
                                                        <th rowspan="2">Location</th>
                                                        <th colspan="6">Actual Quantity</th>
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
                            </form> 
                        </div>
                        <div class="tab-pane fade show" id="release-tab" role="tabpanel" aria-labelledby="release-tab5">                         
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processRelease()" id="process-release"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                        <a id="release-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Release Report</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">              
                                    <form id="form-release" name="form-release" method="post">
                                        @csrf
                                    </form>                                                      
                                    <div class="table-responsive">
                                        <table id="release_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th rowspan="2">
                                                        <input type="checkbox" required="required" class="release-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Actual Quantity</th>
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
                                        <a class="btn btn-warning btn-sm" onclick="processConfirm()" id="process-confirm"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                        <a id="confirm-print" @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Confirmation Report</span></a>
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
                                                    <th colspan="6">Book Quantity</th>
                                                    <th colspan="6">Actual Quantity</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>   
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
                        <div class="tab-pane fade show" id="review-tab" role="tabpanel" aria-labelledby="review-tab5">                         
                            <div class="row mt-3">
                                <div class="col-md-12">                                          
                                    <div class="table-responsive">
                                        <table id="review_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                            <thead class="text-center">
                                                <tr>   
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

        $("#entry-link").click( function(e) {
            e.preventDefault(); 
            load_entry();
        });

        $("#release-link").click( function(e) {
            e.preventDefault(); 
            load_release();
        });

        $("#confirm-link").click( function(e) {
            e.preventDefault(); 
            load_confirm();
        });

        $("#review-link").click( function(e) {
            e.preventDefault(); 
            load_review();
        });

        load_data();

        function load_data() {
            link_id = $('.nav-tabs .active').attr('id');
            if (link_id == 'entry-link') {
                load_entry();
            } else if (link_id == 'release-link') {
                load_release();
            } else if (link_id == 'confirm-link') {
                load_confirm();
            } else if (link_id == 'review-link') {
                load_review();
            }
        }
        
        function load_entry() {
            $("#entry_table").DataTable().destroy();   
            $("#entry_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('cycle-detail.index') }}",
                    type : "GET",
                    data : { cycle_id: $("#cycle_id").val() } 
                },
                columns : [
                    { data:'product_name', name:'product_name'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'actual_pqty', name:'pqty'},
                    { data:'puom', name:'puom'},
                    { data:'actual_mqty', name:'mqty'},
                    { data:'muom', name:'muom'},
                    { data:'actual_bqty', name:'bqty'},
                    { data:'buom', name:'buom'}
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }
        
        function load_release() {
            $("#release_table").DataTable().destroy();   
            $("#release_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('cycle-release.index') }}",
                    type : "GET",
                    data : { cycle_id: $("#cycle_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false },
                    { data:'product_name', name:'product_name'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'actual_pqty', name:'pqty'},
                    { data:'puom', name:'puom'},
                    { data:'actual_mqty', name:'mqty'},
                    { data:'muom', name:'muom'},
                    { data:'actual_bqty', name:'bqty'},
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
                    url : "{{ route('cycle-confirm.index') }}",
                    type : "GET",
                    data : { cycle_id: $("#cycle_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false },
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
                    { data:'actual_pqty', name:'actual_pqty'},
                    { data:'puom', name:'puom'},
                    { data:'actual_mqty', name:'actual_mqty'},
                    { data:'muom', name:'muom'},
                    { data:'actual_bqty', name:'actual_bqty'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }
        
        function load_review() {
            $("#review_table").DataTable().destroy();   
            $("#review_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('cycle-review.index') }}",
                    type : "GET",
                    data : { cycle_id: $("#cycle_id").val() } 
                },
                columns : [
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

        $( "#group_name_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product-group.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#group_id_from").val(ui.item.group_id);
                $("#group_code_from").val(ui.item.group_code);
                $("#group_name_from").val(ui.item.group_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.group_code + ", Name : " + item.group_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#group_name_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product-group.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#group_id_to").val(ui.item.group_id);
                $("#group_code_to").val(ui.item.group_code);
                $("#group_name_to").val(ui.item.group_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.group_code + ", Name : " + item.group_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#brand_name_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product-brand.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#brand_id_from").val(ui.item.brand_id);
                $("#brand_code_from").val(ui.item.brand_code);
                $("#brand_name_from").val(ui.item.brand_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.brand_code + ", Name : " + item.brand_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#brand_name_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product-brand.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#brand_id_to").val(ui.item.brand_id);
                $("#brand_code_to").val(ui.item.brand_code);
                $("#brand_name_to").val(ui.item.brand_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.brand_code + ", Name : " + item.brand_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#product_name_from" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#product_id_from").val(ui.item.product_id);
                $("#product_code_from").val(ui.item.product_code);
                $("#product_name_from").val(ui.item.product_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.product_code + ", Name : " + item.product_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#product_name_to" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-product.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#product_id_to").val(ui.item.product_id);
                $("#product_code_to").val(ui.item.product_code);
                $("#product_name_to").val(ui.item.product_name);
                return false;
            }
        })        
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( "<div>Code : " + item.product_code + ", Name : " + item.product_name + "</div>" )
                .appendTo( ul );
        };  

        $( "#site_name" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('cycle-site.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        product_code_from: $("#product_code_from").val(),
                        product_code_to: $("#product_code_to").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#site_id").val(ui.item.site_id);
                $("#site_name").val(ui.item.site_name);
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
                    url:"{{route('cycle-siteArea.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        product_code_from: $("#product_code_from").val(),
                        product_code_to: $("#product_code_to").val(),
                        site_id: $("#site_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#area_id").val(ui.item.area_id);
                $("#area_name").val(ui.item.area_name);
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
                    url:"{{route('cycle-location.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        product_code_from: $("#product_code_from").val(),
                        product_code_to: $("#product_code_to").val(),
                        site_id: $("#site_id").val(),
                        area_id: $("#area_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#location_id_from").val(ui.item.location_id);
                $("#location_code_from").val(ui.item.location_code);
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
                    url:"{{route('cycle-location.auto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
                        principal_id: $("#principal_id").val(),
                        group_code_from: $("#group_code_from").val(),
                        group_code_to: $("#group_code_to").val(),
                        brand_code_from: $("#brand_code_from").val(),
                        brand_code_to: $("#brand_code_to").val(),
                        product_code_from: $("#product_code_from").val(),
                        product_code_to: $("#product_code_to").val(),
                        site_id: $("#site_id").val(),
                        area_id: $("#area_id").val(),
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {   
                $("#location_id_to").val(ui.item.location_id);
                $("#location_code_to").val(ui.item.location_code);
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
                        url: "{{ route('cycle-job.store') }}",
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
            
        if ($("#form-entry").length > 0) {
            $("#form-entry").validate({
                submitHandler: function (form) {
                    var actionType = $('#btn-update').val();
                    $('#btn-update').html('Sending..');
                    
                    $.ajax({
                        data: $('#form-entry').serialize(),
                        url: "{{ route('cycle-detail.store') }}", 
                        type: "POST", 
                        dataType: 'json',
                        success: function (data) { 
                            if($.isEmptyObject(data.error)){
                                $('#form-entry').trigger("reset");
                                $('#btn-update').html('Update');
                                var oTable = $('#entry_table').dataTable();
                                oTable.fnDraw(false);

                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });   
                            } else {
                                swal({
                                    icon: "error",
                                    text: data.error                     
                                });
                            }
                        },
                        error: function (data) { 
                            console.log('Error:', data);
                            $('#btn-update').html('Update');
                        }
                    });
                }
            })
        }

        $("#release_table").on("click", ".release-check", function() {
            if (this.checked == true) {                    
                $(".release-check-all").prop("checked", true);
            } else {                    
                $(".release-check-all").prop("checked", false);
            }
        });

        $("#release_table").on("click", ".release-check-all", function() {
            $(".release-check").prop("checked", this.checked);
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

        $('body').on('click', '#blank-print', function () {
            var data_id = $('#cycle_id').val();

            window.open("{{url('/inventory/cycle-count/report/blank/')}}" + "/" + data_id, "CycleCountReport","width=800,height=600");
        });

        $('body').on('click', '#book-print', function () {
            var data_id = $('#cycle_id').val();

            window.open("{{url('/inventory/cycle-count/report/book/')}}" + "/" + data_id, "CycleCountReport","width=800,height=600");
        });

        $('body').on('click', '#release-print', function () {
            var data_id = $('#cycle_id').val();

            window.open("{{url('/inventory/cycle-count/report/release/')}}" + "/" + data_id, "CycleCountReport","width=800,height=600");
        });

        $('body').on('click', '#confirm-print', function () {
            var data_id = $('#cycle_id').val();

            window.open("{{url('/inventory/cycle-count/report/invest/')}}" + "/" + data_id, "CycleCountReport","width=800,height=600");
        });
    });

    function processRelease() {
        var oTable = $("#release_table").dataTable();
        $("#form-release").trigger("reset");

        $(".hidden-release").remove();
        oTable.$("input[type='checkbox']").each(function(){
            if(this.checked){
                $("#form-release").append(
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-release")
                        .val(this.value)
                );
            }
        });  
        
        $("#btn-process-release").html("Sending..");

        $.ajax({
            data: $("#form-release").serialize(), 
            url: "{{ route('cycle-release.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if($.isEmptyObject(data.error)){
                    $("#form-release").trigger("reset");
                    $("#btn-process-release").html("Process"); 
                    var oTable = $("#release_table").dataTable();
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
                $("#btn-process-release").html("Process");
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
            url: "{{ route('cycle-confirm.submit') }}",
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