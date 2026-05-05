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
                        <input type="text" id="job_no" name="job_no" @isset($job_view->adjust_no) value="{{$job_view->adjust_no}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
                <div class="col-md-2">   
                    <div class="form-group">
                        <label for="job_date">Job Date</label>
                        <input type="text" id="job_date" name="job_date" @isset($job_view->adjust_date) value="{{\Carbon\Carbon::parse($job_view->adjust_date)->format('d-m-Y')}}" @endisset class="form-control" readonly>
                    </div>
                </div> 
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">                    
                    <div class="btn-group">
                        <a href="{{url('/inventory/stock-adjustment/create/0')}}"  class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <span>Add New Job</span></a>
                        &nbsp;&nbsp;
                        <a href="#" class="btn btn-info btn-sm"><i class="fas fa-folder-open"></i> <span>Open Job</span></a>
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">  
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-box"></i> Job Information</a>
                        </li>
                        @if (isset($job_view->id) && !empty($job_view->id))
                        <li class="nav-item">
                            <a class="nav-link" id="detail-link" data-toggle="tab" href="#detail-tab" role="tab" aria-controls="detail" aria-selected="false">
                            <i class="fas fa-box"></i> Entry Detail</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="process-link" data-toggle="tab" href="#process-tab" role="tab" aria-controls="process" aria-selected="false">
                            <i class="fas fa-box"></i> Proccess</a>
                        </li>
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
                    <div class="tab-content" id="transferTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel" aria-labelledby="home-tab5"> 
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="adjustment_id" name="adjustment_id" @isset($job_view->id) value="{{$job_view->id}}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Branch Name</label>
                                                <select class="custom-select" id="branch_id" name="branch_id">
                                                    @foreach (Auth::user()->branch as $item)
                                                        <option value="{{$item->id}}" @if(isset($job_view->branch_id) && !empty($job_view->branch_id)) @if ($item->id == $job_view->branch_id) selected @endif @endif>{{$item->branch_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Adjustment Type</label>
                                                <select class="custom-select" id="type_id" name="type_id" @isset($job_view->id) @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach ($type_list as $item)
                                                        <option value="{{$item->id}}" @if(isset($job_view->type_id) && !empty($job_view->type_id)) @if ($item->id == $job_view->type_id) selected @endif @endif>{{$item->type_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description" name="description" class="form-control" @isset($job_view->description) value="{{$job_view->description}}" @endisset @isset($job_view->id) @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">                                                    
                                                @if (!isset($job_view->id) && empty($job_view->id))
                                                    <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i class="fas fa-save"></i> <span>Save</span></button>
                                                @endif
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="detail-tab" role="tabpanel" aria-labelledby="detail-tab5">                            
                            <div class="container mt-3">
                                <div class="row mb-3" data-aos="fade-up">
                                    <div class="col-md-12">                    
                                        <div class="btn-group">                                             
                                            @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                                @if ($job_view->confirmed_flag == "No")
                                                    <button type="button" class="btn btn-primary btn-sm" id="btn-add-entry"><i class="fas fa-plus"></i> <span>Add Entry</span></button>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-entry"><i class="fas fa-plus"></i> <span>Add Entry</span></button>
                                            @endif
                                        </div>
                                        <a id="detail-print" @if (isset($job_detail->id) && !empty($job_detail->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Entry Report</span></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="detail_table" class="table table-striped table-bordered table-sm" style="width:100%;">
                                                <thead class="text-center">
                                                    <tr>   
                                                        <th rowspan="2">Action</th>
                                                        <th rowspan="2">Principal Name</th>
                                                        <th rowspan="2">Product Name</th>
                                                        <th rowspan="2">Adjust Type</th>
                                                        <th rowspan="2">Batch No</th>           
                                                        <th rowspan="2">Site Name</th>
                                                        <th rowspan="2">Area Name</th>
                                                        <th rowspan="2">Location</th>
                                                        <th colspan="3">Stock On Hand</th>
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
                                                        <th>Unit</th> 
                                                        <th>Unit</th> 
                                                        <th>Unit</th>                                                  
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="process-tab" role="tabpanel" aria-labelledby="process-tab5">                           
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a class="btn btn-warning btn-sm" onclick="processProcess()" id="btn-process"><i class="fas fa-play"></i> <span>Proccess</span></a>
                                        <a id="process-print" @if (isset($job_detail->id) && !empty($job_detail->id)) enabled @else disabled @endif class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Process Report</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">              
                                    <form id="form-process" name="form-process" method="post">
                                        @csrf
                                    </form>                                                      
                                    <div class="table-responsive">
                                        <table id="process_table" class="table table-striped table-bordered table-sm" style="width:100%">
                                            <thead class="text-center">
                                                <tr>   
                                                    <th rowspan="2">
                                                        <input type="checkbox" required="required" class="process-check-all">
                                                    </th>
                                                    <th rowspan="2">Principal Name</th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Adjust Type</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="3">Stock On Hand</th>
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
                                                    <th>Unit</th> 
                                                    <th>Unit</th> 
                                                    <th>Unit</th>                                                  
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div> 
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
                                                    <th rowspan="2">Principal Name</th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Adjust Type</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="3">Stock On Hand</th>
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
                                                    <th>Unit</th> 
                                                    <th>Unit</th> 
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
                                                    <th rowspan="2">Principal Name</th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Adjust Type</th>
                                                    <th rowspan="2">Batch No</th>           
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="3">Stock On Hand</th>
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
                                                    <th>Unit</th> 
                                                    <th>Unit</th> 
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-filter">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">                    
                    <div class="col-md-12">    
                        <div class="form-group">
                            <label>Principal Name</label>
                            <select class="custom-select" id="principal_filter" name="principal_filter">
                                <option value="">.:Select:.</option>
                                @foreach (Auth::user()->principal as $item)
                                    <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Stock Status</label>
                            <select name="new_stock" id="new_stock" class="form-control">
                                <option value="Exist">Exist Stock</option>
                                <option value="New">New Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                <button type="button" class="btn btn-success btn-sm" id="btn-filter"><i class="fas fa-check"></i> <span>Filter</span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-stock">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="stock_table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>   
                                        <th rowspan="2">
                                            Action
                                        </th>
                                        <th rowspan="2">SKU Code</th>
                                        <th rowspan="2">SKU Name</th>
                                        <th rowspan="2">Batch No</th>           
                                        <th rowspan="2">Site Name</th>
                                        <th rowspan="2">Area Name</th>
                                        <th rowspan="2">Location</th>
                                        <th colspan="6">Quantity Stock</th>
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
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-product">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="product_table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                                <thead class="text-center">
                                    <tr>   
                                        <th>Action</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>1st Unit</th>
                                        <th>2rd Unit</th>
                                        <th>3nd Unit</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-detail" method="post">
                <input type="hidden" id="adjust_id" name="adjust_id" @if(isset($job_view->id) && !empty($job_view->id)) value="{{$job_view->id}}" @endif>
                <input type="hidden" id="serial_id" name="serial_id">
                <input type="hidden" id="detail_id" name="detail_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Principal Name</label>
                                <input type="text" id='principal_name' name="principal_name" class="form-control" autocomplete="off" readonly>
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="hidden" id='status_flag' name="status_flag">
                                <input type="hidden" id='product_id' name="product_id">
                                <input type="text" id='product_name' name="product_name" class="form-control" autocomplete="off" readonly>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>1st Qty</label>
                                <input type="hidden" id="uppp" name="uppp">
                                <input type="hidden" id="muppp" name="muppp">
                                <input type="text" autocomplete="off" id="pqty" name="pqty" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="puom" name="puom" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>2nd Qty</label>
                                <input type="text" autocomplete="off" id="mqty" name="mqty" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="muom" name="muom" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>3rd Qty</label>
                                <input type="text" autocomplete="off" id="bqty" name="bqty" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="buom" name="buom" class="form-control" readonly>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" id='po_number' name="po_number" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Batch No</label>
                                <input type="text" id='lot_no' name="lot_no" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Manufactur Name</label>
                                <input type="hidden" id="manufactur_id" name="manufactur_id">
                                <input type="text" id='manufactur_name' name="manufactur_name" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                    </div>
                    <div class="row">   
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Site Name</label>
                                <select name="site_id" id="site_id" class="custom-select">
                                    <option value="">.:Select:.</option>
                                    @foreach (Auth::user()->site as $item)
                                        <option value="{{$item->id}}">{{$item->site_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-6">    
                            <div class="form-group">
                                <label>Area Name</label>
                                <input type="hidden" id='area_id' name="area_id">                                
                                <input type="text" id='area_name' name="area_name" class="form-control" autocomplete="off">
                            </div>
                        </div>                     
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Location</label>
                                <input type="hidden" id='location_id' name="location_id">
                                <input type="text" id='location_code' name="location_code" class="form-control" autocomplete="off">
                            </div>
                        </div>         
                    </div>
                    <div class="row">
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>1st Qty</label>
                                <input type="text" autocomplete="off" id="actual_pqty" name="actual_pqty" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="actual_puom" name="actual_puom" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>2nd Qty</label>
                                <input type="text" autocomplete="off" id="actual_mqty" name="actual_mqty" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="actual_muom" name="actual_muom" class="form-control" readonly>
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>3rd Qty</label>
                                <input type="text" autocomplete="off" id="actual_bqty" name="actual_bqty" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-2">    
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" autocomplete="off" id="actual_buom" name="actual_buom" class="form-control" readonly>
                            </div>
                        </div> 
                    </div>
                    <div class="row">                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Adjustment Type</label>
                                <select name="adjust_type" id="adjust_type" class="custom-select">
                                    <option value="Plus">Plus</option>
                                    <option value="Minus">Minus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Stock Status</label>
                                <input type="hidden" id="status_id" name="status_id">
                                <input type="text" id='status_name' name="status_name" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Mfg Date</label>
                                <input type="text" id='mfg_date' name="mfg_date" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-3">    
                            <div class="form-group">
                                <label>Exp Date</label>
                                <input type="text" id='exp_date' name="exp_date" class="form-control" autocomplete="off">
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="alert alert-danger" style="display:none">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> <span>Close</span></button>
                    <button type="submit" class="btn btn-success btn-sm" id="save_detail"><i class="fas fa-save"></i> <span>Save</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script> 
    $(function() {
        var d = new Date(); d.setDate( d.getDate());
        
        $('#mfg_date').datepicker({
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            minDate: -30,
        }).datepicker("setDate", d);

        $('#exp_date').datepicker({
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            minDate: -30,
        })

        $( "#mfg_date" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
        $( "#exp_date" ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
    });

    $(document).ready(function() { 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var CSRF_TOKEN = $("meta[name='csrf-token']").attr("content");
        
        $("#detail-link").click( function(e) {
            e.preventDefault(); 
            load_detail();
        });

        $("#process-link").click( function(e) {
            e.preventDefault(); 
            load_process();
        });

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
            if (link_id == "detail-link") {
                load_detail();
            } else if (link_id == "process-link") {
                load_process();
            } else if (link_id == "cancel-link") {
                load_cancel();
            } else if (link_id == "confirm-link") {
                load_confirm();
            }
        }

        function load_detail() {
            $("#detail_table").DataTable().destroy();     
            $("#detail_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('adjustment-detail.index') }}",
                    type : "GET",
                    data : { adjust_id: $("#adjustment_id").val() } 
                },
                columns : [
                    { data:'action', name:'action'},
                    { data:'principal_name', name:'principal_name'},
                    { data:'product_name', name:'product_name'},
                    { data:'adjust_type', name:'adjust_type'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'actual_pqty', name:'actual_pqty'},
                    { data:'actual_mqty', name:'actual_mqty'},
                    { data:'actual_bqty', name:'actual_bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }

        function load_process() {
            $("#process_table").DataTable().destroy();     
            $("#process_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('adjustment-process.index') }}",
                    type : "GET",
                    data : { adjust_id: $("#adjustment_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'principal_name', name:'principal_name'},
                    { data:'product_name', name:'product_name'},
                    { data:'adjust_type', name:'adjust_type'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'actual_pqty', name:'actual_pqty'},
                    { data:'actual_mqty', name:'actual_mqty'},
                    { data:'actual_bqty', name:'actual_bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }

        function load_cancel() {
            $("#cancel_table").DataTable().destroy();     
            $("#cancel_table").DataTable({
                "dom": "<'toolbar'>frtip",
                processing : true,
                serverSide : true,
                ajax : {
                    url : "{{ route('adjustment-cancel.index') }}",
                    type : "GET",
                    data : { adjust_id: $("#adjustment_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'principal_name', name:'principal_name'},
                    { data:'product_name', name:'product_name'},
                    { data:'adjust_type', name:'adjust_type'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'actual_pqty', name:'actual_pqty'},
                    { data:'actual_mqty', name:'actual_mqty'},
                    { data:'actual_bqty', name:'actual_bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
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
                    url : "{{ route('adjustment-confirm.index') }}",
                    type : "GET",
                    data : { adjust_id: $("#adjustment_id").val() } 
                },
                columns : [
                    { data:'check', name:'check', searchable: false, orderable: false},
                    { data:'principal_name', name:'principal_name'},
                    { data:'product_name', name:'product_name'},
                    { data:'adjust_type', name:'adjust_type'},
                    { data:'lot_no', name:'lot_no'},
                    { data:'site_name', name:'site_name'},
                    { data:'area_name', name:'area_name'},
                    { data:'location_code', name:'location_code'},
                    { data:'pqty', name:'pqty'},
                    { data:'mqty', name:'mqty'},
                    { data:'bqty', name:'bqty'},
                    { data:'actual_pqty', name:'actual_pqty'},
                    { data:'actual_mqty', name:'actual_mqty'},
                    { data:'actual_bqty', name:'actual_bqty'},
                    { data:'puom', name:'puom'},
                    { data:'muom', name:'muom'},
                    { data:'buom', name:'buom'},
                ],
                order : [
                    [0, "asc"]
                ]
            });
        }

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

        $( "#location_code" ).autocomplete({	
            minLength:0,	        
            classes: {
                "ui-autocomplete": "highlight"
            },
            source: function( request, response ) {                    
                $.ajax({
                    url:"{{route('site.getLocationAuto')}}",
                    dataType: "json",
                    data: {
                        _token: CSRF_TOKEN,
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
                $('#location_id').val(ui.item.location_id);
                $('#location_code').val(ui.item.location_code);
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
                        url: "{{ route('adjustment-job.store') }}",
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
            
        $("#btn-add-entry").click(function () {  
            $('#form-filter').trigger("reset");
            $("#modal-filter").modal({
                backdrop: "static", 
                keyboard: false,
                show: true
            }); 
        });  
            
        $("#btn-filter").click(function () {  
            var principal_id = $('#principal_filter').val();
            var new_stock = $('#new_stock').val();

            if (principal_id == "") {
                swal({
                    icon: "error",
                    text: "Principal name field is required."
                });
                return false;
            }

            if (new_stock == 'Exist') {
                load_stock(principal_id);
            } else {
                load_product(principal_id);
            }
        });  
        
        $('body').on('click', '.edit-stock', function () {
            $('#form-detail').trigger("reset");
            var data_id = $(this).data('id');

            $.ajax({
                url:"{{route('adjustment.stock-edit')}}",
                dataType: "json",
                method: "post",
                data: {
                    _token: CSRF_TOKEN,
                    id: data_id
                },
                success: function( data ) {
                    $('#modal-stock').modal('hide');

                    $('#modal-detail').modal({
                        backdrop: 'static', 
                        keyboard: false,
                        show: true
                    });

                    var mfg_date = "";
                    var exp_date = "";
                    
                    if (data.mfg_date !== null ) {
                        mfg_date = getFormatDate(data.mfg_date);
                    }

                    if (data.exp_date !== null ) {
                        exp_date = getFormatDate(data.exp_date);
                    }
                    
                    if (data.unit_level == 1) {                
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = true;
                    } else if (data.unit_level == 2) {
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = false;
                    } else {
                        document.getElementById("actual_mqty").readOnly = false;
                        document.getElementById("actual_bqty").readOnly = false;
                    }
                                        
                    document.getElementById("po_number").readOnly = true;
                    document.getElementById("manufactur_name").readOnly = true;
                    document.getElementById("status_name").readOnly = true;
                    document.getElementById("lot_no").readOnly = true;
                    document.getElementById("mfg_date").readOnly = true;
                    document.getElementById("exp_date").readOnly = true;
                    document.getElementById("site_id").readOnly = true;
                    document.getElementById("area_name").readOnly = true;
                    document.getElementById("location_code").readOnly = true;

                    document.getElementById("site_id").className = 'disabled custom-select';
                
                    $("#site_id").find("option").prop("hidden", true);

                    $('#status_flag').val('Exist');
                    $('#adjust_type').val(adjust_type);
                    $('#serial_id').val(data.id);
                    $('#detail_id').val('');
                    $('#principal_id').val(data.principal_id);
                    $('#principal_name').val(data.principal_name);
                    $('#product_id').val(data.product_id);
                    $('#product_name').val(data.product_name);
                    $('#po_number').val(data.po_number);
                    $('#manufactur_id').val(data.manufactur_id);
                    $('#manufactur_name').val(data.manufactur_name);
                    $('#status_id').val(data.status_id);
                    $('#status_name').val(data.status_name);
                    $('#pqty').val(data.pqty);
                    $('#mqty').val(data.mqty);
                    $('#bqty').val(data.bqty);
                    $('#uppp').val(data.uppp);
                    $('#muppp').val(data.muppp);
                    $('#puom').val(data.puom);
                    $('#muom').val(data.muom);
                    $('#buom').val(data.buom);
                    $('#actual_pqty').val(0);
                    $('#actual_mqty').val(0);
                    $('#actual_bqty').val(0);
                    $('#actual_puom').val(data.puom);
                    $('#actual_muom').val(data.muom);
                    $('#actual_buom').val(data.buom);
                    $('#lot_no').val(data.lot_no);
                    $('#mfg_date').val(mfg_date);
                    $('#exp_date').val(exp_date);
                    $('#site_id').val(data.site_id);
                    $('#area_id').val(data.area_id);
                    $('#area_name').val(data.area_name);
                    $('#location_code').val(data.location_code);
                }, 
                error: function(data) {
                    console.log(data);
                } 
            });
        });
        
        $('body').on('click', '.edit-product', function () {
            $('#form-detail').trigger("reset");
            var data_id = $(this).data('id');
            
            $.ajax({
                url:"{{route('adjustment.product-edit')}}",
                dataType: "json",
                method: "post",
                data: {
                    _token: CSRF_TOKEN,
                    id: data_id
                },
                success: function( data ) {
                    $('#modal-stock').modal('hide');

                    $('#modal-detail').modal({
                        backdrop: 'static', 
                        keyboard: false,
                        show: true
                    });
                    
                    if (data.unit_level == 1) {                
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = true;
                    } else if (data.unit_level == 2) {
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = false;
                    } else {
                        document.getElementById("actual_mqty").readOnly = false;
                        document.getElementById("actual_bqty").readOnly = false;
                    }
                                        
                    document.getElementById("adjust_type").readOnly = true;
                    document.getElementById("po_number").readOnly = false;
                    document.getElementById("manufactur_name").readOnly = false;
                    document.getElementById("status_name").readOnly = false;
                    document.getElementById("lot_no").readOnly = false;
                    document.getElementById("mfg_date").readOnly = false;
                    document.getElementById("exp_date").readOnly = false;
                    document.getElementById("area_name").readOnly = false;
                    document.getElementById("location_code").readOnly = false;

                    document.getElementById("site_id").className = 'custom-select';
                    $("#site_id").find("option").prop("hidden", false);

                    var adjust_type = 'Plus';

                    $('#status_flag').val('New');
                    $('#adjust_type').val(adjust_type);
                    $('#serial_id').val(0);
                    $('#detail_id').val('');
                    $('#principal_id').val(data.principal_id);
                    $('#principal_name').val(data.principal_name);
                    $('#product_id').val(data.id);
                    $('#product_name').val(data.product_name);
                    $('#po_number').val('');
                    $('#pqty').val(0);
                    $('#mqty').val(0);
                    $('#bqty').val(0);
                    $('#uppp').val(data.uppp);
                    $('#muppp').val(data.muppp);
                    $('#puom').val(data.puom);
                    $('#muom').val(data.muom);
                    $('#buom').val(data.buom);
                    $('#actual_pqty').val(0);
                    $('#actual_mqty').val(0);
                    $('#actual_bqty').val(0);
                    $('#actual_puom').val(data.puom);
                    $('#actual_muom').val(data.muom);
                    $('#actual_buom').val(data.buom);
                }, 
                error: function(data) {
                    console.log(data);
                } 
            });
        });
        
        $("body").on("click", ".edit-detail", function () {
            var data_id = $(this).data("id");

            $.ajax({
                url:"{{route('adjustment-detail.edit')}}",
                dataType: "json",
                method: "post",
                data: {
                    _token: CSRF_TOKEN,
                    id: data_id
                },
                success: function( data ) {
                    $("#modal-stock").modal("hide");

                    $("#modal-detail").modal({
                        backdrop: "static", 
                        keyboard: false,
                        show: true
                    });
                    
                    var mfg_date = "";
                    var exp_date = "";
                    
                    if (data.mfg_date !== null ) {
                        mfg_date = getFormatDate(data.mfg_date);
                    }
                    
                    if (data.exp_date !== null ) {
                        exp_date = getFormatDate(data.exp_date);
                    }
                    
                    if (data.unit_level == 1) {                
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = true;
                    } else if (data.unit_level == 2) {
                        document.getElementById("actual_mqty").readOnly = true;
                        document.getElementById("actual_bqty").readOnly = false;
                    } else {
                        document.getElementById("actual_mqty").readOnly = false;
                        document.getElementById("actual_bqty").readOnly = false;
                    }

                    if (data.status_flag == 'Exist') {
                        document.getElementById("po_number").readOnly = true;
                        document.getElementById("manufactur_name").readOnly = true;
                        document.getElementById("status_name").readOnly = true;
                        document.getElementById("lot_no").readOnly = true;
                        document.getElementById("mfg_date").readOnly = true;
                        document.getElementById("exp_date").readOnly = true;
                        document.getElementById("site_id").readOnly = true;
                        document.getElementById("area_name").readOnly = true;
                        document.getElementById("location_code").readOnly = true;
    
                        document.getElementById("site_id").className = 'disabled custom-select';
                    
                        $("#site_id").find("option").prop("hidden", true);
                    } else {
                        document.getElementById("adjust_type").readOnly = true;
                        document.getElementById("po_number").readOnly = false;
                        document.getElementById("manufactur_name").readOnly = false;
                        document.getElementById("status_name").readOnly = false;
                        document.getElementById("lot_no").readOnly = false;
                        document.getElementById("mfg_date").readOnly = false;
                        document.getElementById("exp_date").readOnly = false;
                        document.getElementById("area_name").readOnly = false;
                        document.getElementById("location_code").readOnly = false;

                        document.getElementById("site_id").className = 'custom-select';
                        $("#site_id").find("option").prop("hidden", false);
                    }

                    $('#status_flag').val(data.status_flag);
                    $('#adjust_type').val(data.adjust_type);
                    $('#serial_id').val(data.serial_id);
                    $('#detail_id').val(data.id);
                    $('#principal_id').val(data.principal_id);
                    $('#principal_name').val(data.principal_name);
                    $('#product_id').val(data.product_id);
                    $('#product_name').val(data.product_name);
                    $('#po_number').val(data.po_number);
                    $('#pqty').val(data.pqty);
                    $('#mqty').val(data.mqty);
                    $('#bqty').val(data.bqty);
                    $('#uppp').val(data.uppp);
                    $('#muppp').val(data.muppp);
                    $('#puom').val(data.puom);
                    $('#muom').val(data.muom);
                    $('#buom').val(data.buom);
                    $('#actual_pqty').val(data.actual_pqty);
                    $('#actual_mqty').val(data.actual_mqty);
                    $('#actual_bqty').val(data.actual_bqty);
                    $('#actual_puom').val(data.puom);
                    $('#actual_muom').val(data.muom);
                    $('#actual_buom').val(data.buom);
                    $('#lot_no').val(data.lot_no);
                    $('#mfg_date').val(mfg_date);
                    $('#exp_date').val(exp_date);
                    $('#site_id').val(data.site_id);
                    $('#site_name').val(data.site_name);
                    $('#area_id').val(data.area_id);
                    $('#area_name').val(data.area_name);
                    $('#location_id').val(data.location_id);
                    $('#location_code').val(data.location_code);
                }
            });
        });

        if ($("#form-detail").length > 0) {
            $("#form-detail").validate({
                submitHandler: function (form) {
                    var actionType = $('#btn-save-detail').val();
                    $('#btn-save-detail').html('Sending..');

                    $.ajax({
                        data: $('#form-detail').serialize(), 
                        url: "{{ route('adjustment-detail.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {                   
                            if($.isEmptyObject(data.error)){
                                $('#form-detail').trigger("reset");
                                $('#modal-detail').modal('hide');
                                $('#btn-save-detail').html('Save');
                                var oTable = $('#detail_table').dataTable();
                                oTable.fnDraw(false);

                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."                    
                                });                              
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
                                $('#btn-save-detail').html('Save');
                            } 
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            $('#btn-save-detail').html('Save');
                        }
                    });
                }
            })
        }

        $(document).on("click", ".delete-detail", function () {
            dataId = $(this).attr("id");
            $("#modal-konfirmasi").modal("show");
        });
        
        $("#btn-delete").click(function () {
            $.ajax({
                url: "{{route('adjustment-detail.destroy')}}",
                type: "delete",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id" : dataId
                },
                beforeSend: function () {
                    $("#btn-delete").text("Sending..");
                },
                success: function (data) {
                    $("#btn-delete").text("Delete");
                    setTimeout(function () {
                        $("#modal-konfirmasi").modal("hide");
                        var oTable = $("#detail_table").dataTable();
                        oTable.fnDraw(false);
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
                    $("#btn-delete").text("Delete");
                    swal({
                        icon: "error",
                        text: data.error                     
                    });
                }
            })
        });

        $("#process_table").on("click", ".process-check", function() {
            if (this.checked == true) {                    
                $(".process-check-all").prop("checked", true);
            } else {                    
                $(".process-check-all").prop("checked", false);
            }
        });

        $("#process_table").on("click", ".process-check-all", function() {
            $(".process-check").prop("checked", this.checked);
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
        
        $("body").on("click", "#detail-print", function () {
            var data_id = $("#adjustment_id").val();

            window.open("{{url('/inventory/stock-adjustment/report/entry/')}}" + "/" + data_id, "TransferReport","width=800,height=600")
        }); 

        $("body").on("click", "#process-print", function () {
            var data_id = $("#adjustment_id").val();

            window.open("{{url('/inventory/stock-adjustment/report/process/')}}" + "/" + data_id, "TransferReport","width=800,height=600")
        }); 
    });

    function load_stock(principal) {
        $('#modal-filter').modal('hide');

        $('#stock_table').DataTable().destroy();
        $('#stock_table').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            scrollY : "300px",
            scrollX : true,
            scrollCollapse : true,
            paging : false,
            info : false,
            ajax : {
                url : "{{ route('adjustment.stock') }}",
                type : "GET",
                data : { 
                    adjustment_id: $("#adjustment_id").val(),
                    principal_id: principal
                } 
            },
            columns : [
                { data:'action', name:'action', searchable: false, orderable: false },
                { data:'product_code', name:'product_code'},
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
                { data:'buom', name:'buom'}
            ],
            order : [
                [3, 'asc'],
                [4, 'asc'],
                [5, 'asc']
            ]
        });
        
        $('#modal-stock').modal(
            {
                backdrop: 'static', 
                keyboard: false,
                show: true
            }
        );
    }      
    
    function load_product(principal) {
        $('#modal-filter').modal('hide');

        $('#product_table').DataTable().destroy();
        $('#product_table').DataTable({
            "dom": '<"toolbar">frtip',
            processing : true,
            serverSide : true,
            scrollY : "300px",
            scrollX : true,
            scrollCollapse : true,
            paging : false,
            info : false,
            ajax : {
                url : "{{ route('adjustment.product') }}",
                type : "GET",
                data : { 
                    principal_id: principal
                } 
            },
            columns : [
                { data:'action', name:'action', searchable: false, orderable: false },
                { data:'product_code', name:'product_code'},
                { data:'product_name', name:'product_name'},
                { data:'puom', name:'puom'},
                { data:'muom', name:'muom'},
                { data:'buom', name:'buom'}
            ],
            order : [
                [1, 'asc']
            ]
        });
        
        $('#modal-product').modal(
            {
                backdrop: 'static', 
                keyboard: false,
                show: true
            }
        );
    }   

    function processProcess() {
        var oTable = $("#process_table").dataTable();
        $("#form-process").trigger("reset");

        $(".hidden-process").remove();
        oTable.$("input[type='checkbox']").each(function(){
            if(this.checked){
                $("#form-process").append(
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", this.name)
                        .attr("class", "hidden-process")
                        .val(this.value)
                );
            }
        });  
        
        $("#btn-process").html("Sending..");

        $.ajax({
            data: $("#form-process").serialize(), 
            url: "{{ route('adjustment-process.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if($.isEmptyObject(data.error)){
                    $("#form-process").trigger("reset");
                    $("#btn-process").html("Process"); 
                    var oTable = $("#process_table").dataTable();
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
                $("#btn-process").html("Process");
            }
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
            url: "{{ route('adjustment-cancel.submit') }}",
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

        
        $("#form-confirm").append(
            $("<input>")
                .attr("type", "hidden")
                .attr("name", "adjustment_id")
                .attr("class", "hidden-confirm")
                .val($("#adjustment_id").val())
        );
        
        $("#btn-process-confirm").html("Sending..");

        $.ajax({
            data: $("#form-confirm").serialize(), 
            url: "{{ route('adjustment-confirm.submit') }}",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if($.isEmptyObject(data.error)){
                    $("#form-confirm").trigger("reset");
                    $("#btn-process-confirm").html("Process"); 
                    var oTable = $("#confirm_table").dataTable();
                    oTable.fnDraw(false);

                    if($.isEmptyObject(data.message)) {                                        
                        swal({
                            icon: "success",
                            text: data.success
                        });
                    } else {
                        var pesan = "<div class='text-left alert alert-danger'>";
                        for (var i = 0; i < data.message.length; i++) {                                            
                            pesan += data.message[i]+'</br>'; 
                        }
                        pesan += '</div>';
                        
                        const wrapper = document.createElement('div');        
                        wrapper.innerHTML = pesan;
                        swal({
                            icon: "error",
                            content: wrapper                     
                        });
                    }                    
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