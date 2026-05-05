@extends('layouts.main')

@section('title')
    Location
@endsection

@section('content')    
<!-- ======= Breadcrumbs ======= -->
<section id="breadcrumbs" class="breadcrumbs">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Location</h2>
            <ol>
                <li><a href="{{route('home')}}">Home</a></li>
                <li>Location</li>
            </ol>
        </div>
    </div>
</section><!-- End Breadcrumbs -->

<section id="about-us" class="about-us">
    <div class="container">
        <form action="{{route('dashboard-location.refresh')}}" class="form-bordered" method="POST">
            @csrf
            <div class="row info-wrap">
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Site Name</label>
                        <select class="custom-select" id="site_id" name="site_id">
                            @foreach ($site_list as $item)
                                <option value="{{$item->id}}" @isset ($site_id) @if ($site_id == $item->id) selected @endif @endisset>{{$item->site_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-6">    
                    <div class="form-group">
                        <label>Area Name</label>
                        <select class="custom-select" id="area_id" name="area_id">
                            @foreach ($area_list as $item)
                                <option value="{{$item->id}}" @isset ($area_id) @if ($area_id == $item->id) selected @endif @endisset>{{$item->area_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-12">
                    <div class="btn-group center">
                        <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-refresh"></i> Refresh</button>
                        {{-- <a id="icr-print" class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Show</span></a> --}}
                    </div>                
                </div>
            </div>
        </form>

        <div class="row mt-md-2">
            <div class="col-md-12">
                <div class="dashboard">
                    <div class="cellEmpty">Empty</div>
                    <div class="cellMixed">Mixed</div>
                    <div class="cellReserved">Reserved</div>
                    <div class="cellFull">Full</div>                    
                    <div class="cellBad">Bad</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">                       
                <div class="container-box">
                    @foreach ($aisle_list as $aisle)
                        <div class="row-box">
                            <div class="column-box col-100 center-vertical">
                                Aisle {{$aisle->location_aisle}}
                            </div>
                        </div>
                        <div class="row-box">
                            <div class="column-box col-100">
                                @php
                                    $level_count = $location_list->where("location_aisle", $aisle->location_aisle)->groupBy("location_level")->count();   
                                @endphp
                                
                                @for ($i = 1; $i <= $level_count; $i++)
                                    @php
                                        $list = $location_list->where("location_aisle", $aisle->location_aisle)->where("location_level", $i);                            
                                    @endphp
                                    
                                    <div class="dashboard">
                                        @foreach ($list as $item)
                                            <div class="pageBox">
                                                <div class="cell{{$item->status_name}}" @if ($item->status_name !== "Empty") onclick="showDetail('{{$item->id}}');" @endif>
                                                    {{$item->location_code}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-break"></div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('modal')
<div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Stock Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mt-md">
                        <div class="table-responsive">
                            <table id="table-detail" class="table table-bordered table-striped mb-none table-sm" >
                                <thead>
                                    <tr>
                                        <th class="center">Principal Name</th>
                                        <th class="center">SKU Code</th>
                                        <th class="center">SKU Name</th>
                                        <th class="center">Batch No</th>
                                        <th class="center">Mfg Date</th>
                                        <th class="center">Exp Date</th>
                                        <th class="center">1st Qty</th>
                                        <th class="center">1st Unit</th>
                                        <th class="center">2nd Qty</th>
                                        <th class="center">2nd Unit</th>
                                        <th class="center">3rd Qty</th>
                                        <th class="center">3rd Unit</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() { 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        
        $('#site_id').on('change', function() {
            var site_id = this.value;
            $("#area_id").html('');
            $.ajax({
                url:"{{route('location.area')}}",
                type: "GET",
                data: {
                    site_id: site_id,
                    _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $.each(result.area_list,function(key,value){
                        $("#area_id").append('<option value="'+value.id+'">'+value.area_name+'</option>');
                    });
                }
            });
        }); 
    });
      
    function showDetail(id) {
        var requestUrl = "{{url('dashboard/location/detail')}}/" + id;

        $("#table-detail").DataTable().destroy();
        $("#table-detail").DataTable({
            processing : true,
            serverSide : true,
            searching: false,
            paging : false,
            info : false,
            responsive: true,              
            ajax : {
                url : requestUrl,
                type : "GET",
            },
            columns : [
                { data:"principal_name", name:"principal_name"},
                { data:"product_code", name:"product_code"},
                { data:"product_name", name:"product_name"},
                { data:"lot_no", name:"lot_no"},
                { data:"mfg_date", name:"mfg_date"},
                { data:"exp_date", name:"exp_date"},
                { data:"pqty", name:"pqty"},
                { data:"puom", name:"puom"},
                { data:"mqty", name:"mqty"},
                { data:"muom", name:"muom"},
                { data:"bqty", name:"bqty"},
                { data:"buom", name:"buom"}
            ],
            order : [
                [0, "asc"]
            ]
        });     

        $('#modal-detail').modal(); 
    }
</script>
    
@endpush

@push('styles')
    <link href="{{ asset('assets/css/location.css') }}" rel="stylesheet"> 
@endpush