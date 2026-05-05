@extends('layouts.main')

@section('title')
    Fleet CheckList
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Fleet CheckList</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Fleet CheckList</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form method="POST" id="form-job">
                @csrf
                <div class="card">
                    <div class="card-header">Header</div>
                    <div class="card-body">                        
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="job_no">Job No</label>
                                    <input type="hidden" id="id" name="id" value="{{$header->id}}" >
                                    <input type="text" class="form-control" value="{{$header->job_no}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="job_date">Job Date</label>
                                    <input type="text" class="form-control" value="{{\Carbon\Carbon::parse($header->job_date)->format('d-m-Y')}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="job_type">Job Type</label>
                                    <input type="text" class="form-control" value="{{$header->job_type}}" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name</label>
                                    <input type="text" class="form-control" name="vendor_name" value="{{$header->vendor_name}}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">                    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="size_id">Container Size</label>
                                    <select name="size_id" id="size_id" class="custom-select">
                                        <option value="">Selected...</option>
                                        @foreach ($size_list as $item)
                                            <option value="{{$item->id}}" @if ($item->id == $header->size_id) selected @endif>{{$item->size_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                   
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_id">Container Type</label>
                                    <select name="type_id" id="type_id" class="custom-select">
                                        <option value="">Selected...</option>
                                        @foreach ($type_list as $item)
                                            <option value="{{$item->id}}" @if ($item->id == $header->type_id) selected @endif>{{$item->type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>   
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="container_no">Container No</label>
                                    <input type="text" class="form-control" name="container_no" value="{{$header->container_no}}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">      
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="driver_id">Driver Name</label>
                                    <select name="driver_id" id="driver_id" class="custom-select">
                                        <option value="">Selected...</option>
                                        @foreach ($driver_list as $item)
                                            <option value="{{$item->id}}" @if ($item->id == $header->driver_id) selected @endif>{{$item->driver_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone_no">Phone No</label>
                                    <input type="text" class="form-control" name="phone_no" value="{{$header->phone_no}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_no">Vehicle No</label>
                                    <select name="vehicle_no" id="vehicle_no" class="custom-select">
                                        <option value="">Select...</option>
                                        @foreach ($vehicle_list as $item)
                                            <option value="{{$item->vehicle_no}}" @if ($item->vehicle_no == $header->vehicle_no) selected @endif>{{$item->vehicle_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="seal_no">Seal No</label>
                                    <input type="text" class="form-control" name="seal_no" value="{{$header->seal_no}}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">   
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="km_start">KM Start</label>
                                    <input type="text" class="form-control" name="km_start" value="{{$header->km_start}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="km_end">KM End</label>
                                    <input type="text" class="form-control" name="km_end" value="{{$header->km_end}}" />
                                </div>
                            </div>                 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <input type="text" class="form-control" name="remarks_header" value="{{$header->remarks}}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">Detail</div>
                    <div class="card-body">          
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-responsive table-sm">
                                        <thead class="text-center">
                                            <tr>
                                                <th>
                                                    No
                                                </th>
                                                <th>
                                                    Item Name
                                                </th>
                                                <th>
                                                    Hasil Pemeriksaan
                                                </th>
                                                <th>
                                                    Catatan / Tindakan
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $group_name = "";
                                            $i = 1;
                                            $row = 0;
                                        @endphp
                                        @foreach ($detail as $item)
                                            @if ($group_name !== $item->group_name)
                                                @php
                                                    $i = 1;
                                                @endphp
                                                <tr>
                                                    <td colspan="4"><b>{{$item->group_name}}</b></td>
                                                </tr>       
                                            @endif                       
                                            <tr>                                    
                                                @php
                                                    $id = $row;
                                                @endphp
                                                <td class="text-center">
                                                    <input type="hidden" name="item_id[]" value="{{$item->id}}">
                                                    {{$i}}
                                                </td>
                                                <td>{{$item->item_name}}</td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="resultFlag[{{$id}}]" id="yes" value="Yes" @if ($item->action_flag == null) checked @else {{ $item->results_flag == 'Yes' ? 'checked' : '' }} @endif>
                                                        <label class="form-check-label" for="yes">Ya</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="resultFlag[{{$id}}]" id="no" value="No" {{ $item->results_flag == 'No' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="no">Tidak</label>
                                                    </div>
                                                </td>
            
                                                @if ($item->item_type == "Remarks")
                                                    <td>
                                                        <input type="hidden" class="form-control" name="actionFlag[{{$id}}]">
                                                        <input type="text" class="form-control" name="remarks[]" value="{{$item->remarks}}">
                                                    </td>
                                                @else 
                                                    <td>                                                    
                                                        <input type="hidden" class="form-control" name="remarks[]">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="proper" value="Proper" @if ($item->action_flag == null) checked @else {{ $item->action_flag == 'Proper' ? 'checked' : '' }} @endif>
                                                            <label class="form-check-label" for="proper">Layak</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="less" value="Less" {{ $item->action_flag == 'Less' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="less">Tolak</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="alert" value="Alert" {{ $item->action_flag == 'Alert' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="less">Peringatan</label>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                            @php
                                                $group_name = $item->group_name;
                                                $i++;
                                                $row++;
                                            @endphp
                                            @endforeach
                                        </tbody>
                                    </table>  
                                </div>                      
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Submit</button>
                                    @isset($header->vehicle_no)
                                        <a id="report-print" class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Print</span></a>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section> 
@endsection

@section('modal')
    
@endsection

@push('scripts')
<script>
    $('body').on('click', '#report-print', function () {
        var data_id = $('#id').val();
        
        window.open("{{url('/fleet-checklist/report/')}}" + "/" + data_id, 'InboundReport','width=800,height=600')
    });   

    if ($("#form-job").length > 0) {
        $("#form-job").validate({
            submitHandler: function (form) {
                $.ajax({
                    data: $('#form-job').serialize(), 
                    url: "{{ route('fleet-checklist.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function () {
                        $("#loader").show();
                    },
                    success: function (data) {                   
                        $("#loader").hide();
                        if($.isEmptyObject(data.error)){
                            swal({
                                icon: "success",
                                text: "Data Successfully Saved."                    
                            });

                            window.open(data.success, '_top');
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
                        console.log('Error:', data);
                        $("#loader").hide();
                    }
                });
            }
        })
    }
</script>
@endpush