@extends('layouts.main')

@section('title')
    Report - Tracking Carton
@endsection
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #0d6efd;
        border-radius: 0.375rem;
        padding: 4px;
    }
</style>

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Report - Tracking Carton</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Report - Tracking Carton</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form action="{{ url('warehouse/tracking-carton/search') }}" method="post" id="form-search">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="branch_id">Branch Name</label>
                            <select name="branch_id" id="branch_id" class="custom-select">
                                @foreach (Auth::user()->branch as $item)
                                    <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Principal Name</label>
                            <select class="custom-select" id="principal_id" name="principal_id">
                                @foreach (Auth::user()->principal as $item)
                                    <option value="{{ $item->id }}">{{ $item->principal_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Search By</label>
                            <select class="custom-select" onchange="searchBy(this.value)" name="type">
                                <option value="" selected disabled>Choose..</option>
                                <option value="SKU">SKU</option>
                                <option value="CARTON">CARTON ID</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="append">

                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-info btn-md mt-4 btnSearch" style="display: none"> <i
                                class="fas fa-search"></i>
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        function searchBy(val) {
            $('.btnSearch').css('display', '');
            $('.append').html(""); // Kosongkan elemen
            if (val == 'CARTON') {
                $('.append').append(`
            <label for="tagInput" class="form-label">Carton ID</label>
            <select id="tagInput" class="form-control" multiple="multiple" name="carton_id[]" style="width: 100%"></select>
        `);
                $('#tagInput').select2({
                    tags: true,
                    placeholder: 'Type Here...',
                    tokenSeparators: [',', ' '], // Enter, comma, or space triggers new tag
                    allowClear: true
                });

            } else {
                $('.append').append(`
            <div class="form-group">
                <label for="sku">Product Name To</label>
                <select id="sku" name="product_code[]" class="form-control" multiple="multiple" style="width: 100%"></select>
            </div>
        `);

                $('#sku').select2({
                    placeholder: 'Search for SKU...',
                    minimumInputLength: 1,
                    ajax: {
                        url: "{{ route('stock-report.getProduct') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                _token: CSRF_TOKEN,
                                principal_id: $('#principal_id').val(),
                                group_code_from: $('#group_code_from').val(),
                                group_code_to: $('#group_code_to').val(),
                                brand_code_from: $('#brand_code_from').val(),
                                brand_code_to: $('#brand_code_to').val(),
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.product_code,
                                        text: item.product_name + " (" + item.product_code + ")"
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }
        }

        $(document).ready(function() {});
    </script>
@endpush
