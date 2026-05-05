@extends('layouts.main')

@section('title')
    Stock Report
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Report</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Stock Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{ route('stock-report.report') }}" method="post"
                onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-3">
                        <fieldset>
                            <legend>Report Type</legend>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="reportType" id="summary"
                                    value="summary" checked>
                                <label class="form-check-label" for="summary">Summary</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="reportType" id="detail"
                                    value="detail">
                                <label class="form-check-label" for="detail">Detail</label>
                            </div>
                        </fieldset>
                        <br />
                        <fieldset>
                            <legend>Report Group On</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="product" value="product"
                                    checked>
                                <label class="form-check-label" for="product">Product</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="product-lot"
                                    value="product-lot">
                                <label class="form-check-label" for="product-lot">Product + Batch No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="product-exp"
                                    value="product-exp">
                                <label class="form-check-label" for="product-exp">Product + Mfg/Exp Date</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="product-doc"
                                    value="product-doc">
                                <label class="form-check-label" for="product-doc">Product + Document Ref</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" id="site-loc"
                                    value="site-loc">
                                <label class="form-check-label" for="site-loc">Site + Location</label>
                            </div>
                        </fieldset>
                        <br />
                        <fieldset>
                            <legend>Sort Order</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortOrder" id="asc"
                                    value="asc" checked>
                                <label class="form-check-label" for="asc">Ascending</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortOrder" id="desc"
                                    value="desc">
                                <label class="form-check-label" for="desc">Descending</label>
                            </div>
                        </fieldset>
                        <br />
                        <div class="form-group text-center">
                            <button type="submit" id="tombol-print" class="btn btn-primary btn-sm"><i
                                    class="fas fa-print"></i> <span>Print</span></button>
                            <button type="button" onclick="downloadExcel();" class="btn btn-success btn-sm"><i
                                    class="fas fa-download"></i> <span>Download</span></button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <fieldset>
                            <legend>Filter By</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="branch_id">Branch Name</label>
                                        <select name="branch_id" id="branch_id" class="custom-select">
                                            @foreach (Auth::user()->branch as $item)
                                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="principal_id">Principal Name</label>
                                        <select name="principal_id" id="principal_id" class="custom-select">
                                            @foreach (Auth::user()->principal as $item)
                                                <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_code_from">Group Name From</label>
                                        <select name="group_code_from" id="group_code_from" class="custom-select">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_code_to">Group Name To</label>
                                        <select name="group_code_to" id="group_code_to" class="custom-select">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_code_from">Brand Name From</label>
                                        <select name="brand_code_from" id="brand_code_from" class="custom-select">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_code_to">Brand Name To</label>
                                        <select name="brand_code_to" id="brand_code_to" class="custom-select">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name_from">Product Name From</label>
                                        <input type="hidden" id="product_code_from" name="product_code_from">
                                        <input type="text" id="product_name_from" name="product_name_from"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name_to">Product Name To</label>
                                        <input type="hidden" id="product_code_to" name="product_code_to">
                                        <input type="text" id="product_name_to" name="product_name_to"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="site_id">Site Name</label>
                                        <select name="site_id" id="site_id" class="custom-select">
                                            <option value="">.:Select:.</option>
                                            @foreach (Auth::user()->site as $item)
                                                <option value="{{ $item->id }}">{{ $item->site_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="area_id">Area Name</label>
                                        <select name="area_id" id="area_id" class="custom-select">
                                            <option value="">.:Select:.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location_code_from">Location From</label>
                                        <input type="text" id="location_code_from" name="location_code_from"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location_code_to">Location From</label>
                                        <input type="text" id="location_code_to" name="location_code_to"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="exp_date_from">Expiry Date From</label>
                                        <input type="text" id="exp_date_from" name="exp_date_from"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="exp_date_to">Expiry Date To</label>
                                        <input type="text" id="exp_date_to" name="exp_date_to" class="form-control">
                                    </div>
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
        $(function() {
            var d = new Date();
            d.setDate(d.getDate() - 30);
            $('#exp_date_from').datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true
            });

            $('#exp_date_to').datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true
            });

            $("#exp_date_from").datepicker("option", "dateFormat", 'dd/mm/yy');
            $("#exp_date_to").datepicker("option", "dateFormat", 'dd/mm/yy');
        });
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $('#principal_id').on('change', function() {
                var principal_id = this.value;
                $("#group_code_from").html('');
                $("#group_code_to").html('');
                $.ajax({
                    url: "{{ route('stock-report.getProductGroup') }}",
                    type: "GET",
                    data: {
                        principal_id: principal_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#group_code_from').html('<option value="">.:Select:.</option>');
                        $.each(result.group_list, function(key, value) {
                            $("#group_code_from").append('<option value="' + value
                                .group_code + '">' + value.group_name + '</option>');
                        });

                        $('#group_code_to').html('<option value="">.:Select:.</option>');
                        $.each(result.group_list, function(key, value) {
                            $("#group_code_to").append('<option value="' + value
                                .group_code + '">' + value.group_name + '</option>');
                        });
                    }
                });
            });

            $('#group_code_from').on('change', function() {
                $("#brand_code_from").html('');
                $("#brand_code_to").html('');
                $.ajax({
                    url: "{{ route('stock-report.getProductBrand') }}",
                    type: "GET",
                    data: {
                        principal_id: $('#principal_id').val(),
                        group_code_from: $('#group_code_from').val(),
                        group_code_to: $('#group_code_to').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#brand_code_from').html('<option value="">.:Select:.</option>');
                        $.each(result.brand_list, function(key, value) {
                            $("#brand_code_from").append('<option value="' + value
                                .brand_code + '">' + value.brand_name + '</option>');
                        });

                        $('#brand_code_to').html('<option value="">.:Select:.</option>');
                        $.each(result.brand_list, function(key, value) {
                            $("#brand_code_to").append('<option value="' + value
                                .brand_code + '">' + value.brand_name + '</option>');
                        });
                    }
                });
            });

            $('#group_code_to').on('change', function() {
                $("#brand_code_from").html('');
                $("#brand_code_to").html('');
                $.ajax({
                    url: "{{ route('stock-report.getProductBrand') }}",
                    type: "GET",
                    data: {
                        principal_id: $('#principal_id').val(),
                        group_code_from: $('#group_code_from').val(),
                        group_code_to: $('#group_code_to').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#brand_code_from').html('<option value="">.:Select:.</option>');
                        $.each(result.brand_list, function(key, value) {
                            $("#brand_code_from").append('<option value="' + value
                                .brand_code + '">' + value.brand_name + '</option>');
                        });

                        $('#brand_code_to').html('<option value="">.:Select:.</option>');
                        $.each(result.brand_list, function(key, value) {
                            $("#brand_code_to").append('<option value="' + value
                                .brand_code + '">' + value.brand_name + '</option>');
                        });
                    }
                });
            });

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
                                group_code_from: $('#group_code_from').val(),
                                group_code_to: $('#group_code_to').val(),
                                brand_code_from: $('#brand_code_from').val(),
                                brand_code_to: $('#brand_code_to').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#product_name_from').val(ui.item.product_name);
                        $('#product_code_from').val(ui.item.product_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.product_name + " (" + item.product_code + ")</div>")
                        .appendTo(ul);
                };

            $("#product_name_to").autocomplete({
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
                                group_code_from: $('#group_code_from').val(),
                                group_code_to: $('#group_code_to').val(),
                                brand_code_from: $('#brand_code_from').val(),
                                brand_code_to: $('#brand_code_to').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#product_name_to').val(ui.item.product_name);
                        $('#product_code_to').val(ui.item.product_code);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.product_name + " (" + item.product_code + ")</div>")
                        .appendTo(ul);
                };

            $('#site_id').on('change', function() {
                var site_id = this.value;
                $("#area_id").html('');
                $.ajax({
                    url: "{{ route('stock-report.getArea') }}",
                    type: "GET",
                    data: {
                        site_id: site_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#area_id').html('<option value="">.:Select:.</option>');
                        $.each(result.area_list, function(key, value) {
                            $("#area_id").append('<option value="' + value.id + '">' +
                                value.area_name + '</option>');
                        });
                    }
                });
            });

            $("#location_code_from").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock-report.getLocation') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                site_id: $('#site_id').val(),
                                area_id: $('#area_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#location_code_from').val(ui.item.location_code);
                        $('#site_id').val(ui.item.site_id).trigger("change");
                        $('#area_id').val(ui.item.area_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Site : " + item.site_name + ", Area : " + item.area_name +
                            "<br>Location : <b>" + item.location_code + "</b></div>")
                        .appendTo(ul);
                };

            $("#location_code_to").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('stock-report.getLocation') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                site_id: $('#site_id').val(),
                                area_id: $('#area_id').val(),
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#location_code_to').val(ui.item.location_code);
                        $('#site_id').val(ui.item.site_id).trigger("change");
                        $('#area_id').val(ui.item.area_id);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Site : " + item.site_name + ", Area : " + item.area_name +
                            "<br>Location : <b>" + item.location_code + "</b></div>")
                        .appendTo(ul);
                };
        });

        function downloadExcel() {
            var reportType = $('input[name="reportType"]:checked').val();
            var query = {
                reportType: reportType,
                branch_id: $('#branch_id').val(),
                principal_id: $('#principal_id').val(),
                group_code_from: $('#group_code_from').val(),
                group_code_to: $('#group_code_to').val(),
                brand_code_from: $('#brand_code_from').val(),
                brand_code_to: $('#brand_code_to').val(),
                product_from: $('#product_code_from').val(),
                product_to: $('#product_code_to').val(),
                site_id: $('#site_id').val(),
                area_id: $('#area_id').val(),
                location_code_from: $('#location_code_from').val(),
                location_code_to: $('#location_code_to').val(),
                exp_date_from: $('#exp_date_from').val(),
                exp_date_to: $('#exp_date_to').val(),
            }

            var url = "{{ URL::to('warehouse/stock-report/export') }}?" + $.param(query)

            window.open(url, '_blank');
        }

        function target_popup(form) {
            window.open('', 'StockReport', 'width=800,height=600,resizeable,scrollbars');
            form.target = 'StockReport';
        }
    </script>
@endpush
