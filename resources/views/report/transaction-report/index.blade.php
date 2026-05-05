@extends('layouts.main')

@section('title')
    Transaction Report
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Transaction Report</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Transaction Report</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-filter" name="form-filter" action="{{ route('transaction-report.report') }}" method="post"
                onsubmit="target_popup(this)">
                @csrf
                <div class="row info-wrap" data-aos="fade-up">
                    <div class="col-md-3">
                        <fieldset>
                            <legend>Report Group On</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" value="product" checked>
                                <label class="form-check-label" for="product">Product</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" value="product-lot">
                                <label class="form-check-label" for="product-lot">Product + Batch No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" value="product-doc">
                                <label class="form-check-label" for="product-doc">Product + Document Ref</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="GroupOn" value="product-site">
                                <label class="form-check-label" for="product-site">Site + Location</label>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Job Type</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jobType" value="All" checked>
                                <label class="form-check-label" for="All">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jobType" value="Inbound">
                                <label class="form-check-label" for="Inbound">Inbound</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jobType" value="Outbound">
                                <label class="form-check-label" for="Outbound">Outbound</label>
                            </div>
                            {{-- <div class="form-check">
                                <input class="form-check-input" type="radio" name="jobType" value="Moves">
                                <label class="form-check-label" for="Moves">Transfer</label>
                            </div> --}}
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jobType" value="Adjustments">
                                <label class="form-check-label" for="Adjustments">Adjustments</label>
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="branch_id">Branch Name</label>
                                        <select name="branch_id" id="branch_id" class="custom-select">
                                            @foreach (Auth::user()->branch as $item)
                                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_from">Date From</label>
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' id="date_from" name="date_from"
                                                class="form-control" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="text" id="date_to" name="date_to" class="form-control"
                                            data-provide="datepicker" data-date-format="dd/mm/yyyy">
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
                                        <label for="batch_from">Batch No From</label>
                                        <input type="text" id="batch_from" name="batch_from" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch_to">Batch No To</label>
                                        <input type="text" id="batch_to" name="batch_to" class="form-control">
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
            var today = getFirstDate(),
                lastDay = getLastDate();

            $('#date_from').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", today);

            $('#date_to').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", lastDay);
        });

        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#product_name_from").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('product.getProduct') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
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
                            url: "{{ route('product.getProduct') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
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
                            $("#area_id").append('<option value="' + value.area_id +
                                '">' + value.area_name + '</option>');
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
        });

        function downloadExcel() {
            var GroupOn = $('input[name="GroupOn"]:checked').val();
            var jobType = $('input[name="jobType"]:checked').val();
            var query = {
                GroupOn: GroupOn,
                jobType: jobType,
                branch_id: $('#branch_id').val(),
                principal_id: $('#principal_id').val(),
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                product_from: $('#product_code_from').val(),
                product_to: $('#product_code_to').val(),
                batch_from: $('#batch_from').val(),
                batch_to: $('#batch_to').val(),
                site_id: $('#site_id').val(),
                area_id: $('#area_id').val(),
                location_code_from: $('#location_code_from').val(),
                location_code_to: $('#location_code_to').val()
            }

            var url = "{{ URL::to('warehouse/transaction-report/export') }}?" + $.param(query)

            window.open(url, '_blank');
        }

        function target_popup(form) {
            window.open('', 'TransactionReport', 'width=800,height=600,resizeable,scrollbars');
            form.target = 'TransactionReport';
        }
    </script>
@endpush
