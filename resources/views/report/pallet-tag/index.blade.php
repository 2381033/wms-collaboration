@extends('layouts.main')

@section('title')
    Pallet Tag
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Pallet Tag</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Pallet Tag</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{ route('pallet-tag.print') }}" method="post"
                onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-12">
                        <fieldset>
                            <legend>Filter By</legend>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="principal_id">Principal Name <span class="text-danger">*</span></label>
                                        <select name="principal_id" id="principal_id" class="custom-select">
                                            @foreach (Auth::user()->principal as $item)
                                                <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="principal_id">Site Name <span class="text-danger">*</span></label>
                                        <select name="site_id" id="site_id" class="custom-select">
                                            <option value="" disabled selected>Silahkan Pilih</option>
                                            @foreach (Auth::user()->site as $item)
                                                <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_code">Product Code <span class="text-danger">*</span> </label>
                                        <input type="hidden" id="product_code_from" name="product_code_from">
                                        <input type="text" id="product_name_from" name="product_code"
                                            class="form-control" placeholder="silahkan isi.." required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_name_to">Batch No</label>
                                        <input type="text" id="product_name_to" name="lot_no" class="form-control"
                                            placeholder="Opsional..">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location_code_from">Location Code <span
                                                class="text-danger">*</span></label>
                                        <select name="location_code" id="location_code" class="custom-select"
                                            style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <button type="submit" id="tombol-print" class="btn btn-primary btn-lg"><i
                                            class="fas fa-print"></i> <span>Print</span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
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
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#location_code").select2();
            $("#location_code").attr('disabled', true);
            $("#product_name_from").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock-report.getProduct') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                group_code_from: "",
                                group_code_to: "",
                                brand_code_from: "",
                                brand_code_to: "",
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#product_name_from').val(ui.item.product_code + " - " + ui.item.product_name);
                        $('#product_code_from').val(ui.item.product_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.product_name + " (" + item.product_code + ")</div>")
                        .appendTo(ul);
                };

            $('#site_id').on('change', function() {
                $("#location_code").attr('disabled', false);
                var site_id = this.value;
                $("#location_code").html('');
                $.ajax({
                    url: "{{ url('report/pallet-tag/getLocation') }}/" + site_id,
                    type: "GET",
                    dataType: 'json',
                    success: function(response) {
                        $('#location_code').html(
                            '<option value="" disabled selected>Silahkan Pilih</option>');
                        $.each(response.data, function(key, value) {
                            $("#location_code").append('<option value="' + value.id +
                                '">' +
                                value.location_code + '</option>');
                        });
                    }
                });
            });
        });

        function target_popup(form) {
            window.open('', 'PalletTagReport', 'width=800,height=600,resizeable,scrollbars');
            form.target = 'PalletTagReport';
        }
    </script>
@endpush
