@extends('layouts.new.base')
@section('title', 'MKT - Generate Pallet ID')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <form action="{{url('/inventory/generatePalletID/postGenerate')}}" method="post" id="postGenerate">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right">
                                <a href="javascript:void(0)" class="btn btn-lg btn-dark mb-3 add hide"><i class="fas fa-plus-circle"></i> Add</a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <select class="form-control" id="typeGenerate" name="type_generate" style="width: 100%;" required>
                                    <option value="" disabled selected>TYPE</option>
                                        <option value="baru">INBOUND BARU</option>
                                        <option value="lama">BARANG LAMA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                  <div class="row appendKonten">
                   
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-lg btn-info btnsave hide"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $('#postGenerate').on('submit', function(){
            $('.btnsave').attr('disabled', true);
        });

        $('#typeGenerate').on('change', function(){
            var type = $(this).val();
            sessionStorage.setItem('type', type);
            $('.appendKonten').html("");
            $('.btnsave').removeClass('hide');
            $('.add').removeClass('hide');
            $.ajax({
                url: "{{url('inventory/generatePalletID/typeGenerate')}}/" + type,
                type: 'GET',
                dataType : 'json',
                success: function(response){
                    $('.appendKonten').append(`
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th scope="row" colspan="3" class="text-center">
                                    <label for="customFieldName">MAPPING PALLET ID PER BIN LOCATION</label>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <th>SKU</th>
                                <th>QTY</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tr class="text-center">
                            <td>
                                <div class="form-group">
                                    <select class="form-control" id="selectProductCode" name="product_code[]" style="width: 100%;">
                                        <option value="" disabled selected>PILIH SKU</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="qty[]" id="" required placeholder="QTY.." autocomplete="off">
                            </td>
                            <td>
                                <a class="btn btn-md btn-danger deleted"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="3" class="text-center">
                                    <div class="form-group">
                                        <select id="my-select" class="form-control selectLocation" name="location_code" required>
                                            <option value="" selected disabled>LOCATION</option>
                                            @foreach ($location as $item)
                                            <option value="{{$item->location_code}}">{{$item->location_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>`);
                    $.each(response.data, function(key, value){
                        $('#selectProductCode').append(`<option value="${value.product_code},${value.id}">${value.product_code} -> ${value.location_code} | <b>QTY : ${value.qtya} CTN</b></option>`)
                    });
                $('#selectProductCode').select2();
                $('.selectLocation').select2();
                },
                error: function(response){

                }
            })
        });
     
        $(".add").click(function(){
            var type = sessionStorage.getItem('type', type);
            $.ajax({
                url: "{{url('inventory/generatePalletID/typeGenerate')}}/" + type,
                type: 'GET',
                dataType : 'json',
                success: function(response){
                    $("#table").append(`
                                <tr class="text-center">
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control selectProductCode" name="product_code[]" style="width: 100%;">
                                                <option value="" disabled selected>PILIH SKU</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="qty[]" id="" required placeholder="QTY.." autocomplete="off">
                                    </td>
                                    <td>
                                        <a class="btn btn-md btn-danger deleted"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>`);
                    $.each(response.data, function(key, value){
                        $('.selectProductCode').append(`<option value="${value.product_code},${value.id}">${value.product_code} -> ${value.location_code} | <b>QTY : ${value.qtya} CTN</b></option>`)
                    });
                    $('.selectProductCode').select2();
                },
                error: function(response){

                }
            })
         
        });

        
        $("#table").on('click','.deleted',function(){
            $(this).parent().parent().remove();
        });

        $('#selectProductCode').select2();
    </script>
@endpush
