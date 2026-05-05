@extends('layouts.main')

@section('title')
    Issue - Reason
@endsection

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Issue - Reason</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Issue - Reason</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row info-wrap">
                <div class="col-md 12">
                    <form id="form-job" method="POST">
                        @csrf
                        <input type="hidden" id="id" name="id"
                            @isset($view->id) value="{{ $view->id }}" @endisset>
                        <div class="container mt-3">
                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <div class="btn-group">
                                        <a href="{{ url('/issue-reason/create/0') }}" class="btn btn-primary btn-sm"><i
                                                class="fas fa-plus"></i> <span>Add New Job</span></a>
                                        @if (!isset($view))
                                            <button type="submit" id="btn-save-job" class="btn btn-success btn-sm"><i
                                                    class="fas fa-save"></i> <span>Save</span></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="job_no">Job Number</label>
                                        <input type="text" id="Job_no" name="job_no"
                                            @isset($view->job_no) value="{{ $view->job_no }}" @endisset
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="job_date">Job Date</label>
                                        <input type="text" id="job_date" name="job_date"
                                            @isset($view->job_date) value="{{ \Carbon\Carbon::parse($view->job_date)->format('d-m-Y') }}" @endisset
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="principal_id">Principal Name</label>
                                        <select name="principal_id" id="principal_id" class="custom-select">
                                            @foreach (Auth::user()->principal as $item)
                                                <option value="{{ $item->id }}"
                                                    @isset($view->principal_id) @if ($item->id == $view->principal_id) selected @endif @endisset>
                                                    {{ $item->principal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="order_no">Order No</label>
                                        <input type="hidden" id="outbound_id" name="outbound_id"
                                            @isset($view->outbound_id) value="{{ $view->outbound_id }}" @endisset>
                                        <input type="text" id="order_no" name="order_no"
                                            @isset($view->order_no) value="{{ $view->order_no }}" @endisset
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" id="customer_name"
                                            @isset($view->customer_name) value="{{ $view->customer_name }}" @endisset
                                            class="form-control disabled">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" id="description"
                                            @isset($view->description) value="{{ $view->description }}" @endisset
                                            class="form-control disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="rate">
                                        <input type="radio" id="star5" name="rate" value="5"
                                            @isset($view->rating) @if ($view->rating <= '5') checked @endif @endisset />
                                        <label for="star5" title="Excellent">5 stars</label>
                                        <input type="radio" id="star4" name="rate" value="4"
                                            @isset($view->rating) @if ($view->rating <= '4') checked @endif @endisset />
                                        <label for="star4" title="Good">4 stars</label>
                                        <input type="radio" id="star3" name="rate" value="3"
                                            @isset($view->rating) @if ($view->rating <= '3') checked @endif @endisset />
                                        <label for="star3" title="Fair">3 stars</label>
                                        <input type="radio" id="star2" name="rate" value="2"
                                            @isset($view->rating) @if ($view->rating <= '2') checked @endif @endisset />
                                        <label for="star2" title="Poor">2 stars</label>
                                        <input type="radio" id="star1" name="rate" value="1"
                                            @isset($view->rating) @if ($view->rating <= '1') checked @endif @endisset />
                                        <label for="star1" title="Bad">1 stars</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" id="issue_id" name="issue_id"
                                            @isset($view->issue_id) value="{{ $view->issue_id }}" @endisset>

                                        <button type="button" onclick="setIssue(1)"
                                            class="btn btn-success mt-1 col-2 @isset($view->issue_id) @if ($view->issue_id == 1) disabled @endif @endisset">Quality</button>
                                        <button type="button" onclick="setIssue(2)"
                                            class="btn btn-success mt-1 col-2 @isset($view->issue_id) @if ($view->issue_id == 2) disabled @endif @endisset">Quantity</button>
                                        <button type="button" onclick="setIssue(3)"
                                            class="btn btn-success mt-1 col-2 @isset($view->issue_id) @if ($view->issue_id == 3) disabled @endif @endisset">Punctuality</button>
                                        <button type="button" onclick="setIssue(4)"
                                            class="btn btn-success mt-1 col-2 @isset($view->issue_id) @if ($view->issue_id == 4) disabled @endif @endisset">Driver
                                            Attitude</button>
                                        <button type="button" onclick="setIssue(5)"
                                            class="btn btn-success mt-1 col-2 @isset($view->issue_id) @if ($view->issue_id == 5) disabled @endif @endisset">Tell
                                            us more</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <input type="text" id="notes" name="notes" class="form-control"
                                            @isset($view->notes) value="{{ $view->notes }}" @endisset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
@endsection

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        .rate {
            float: left;
            height: 46px;
            padding: 0 10px;
        }

        .rate:not(:checked)>input {
            position: absolute;
            top: -9999px;
        }

        .rate:not(:checked)>label {
            float: right;
            width: 1em;
            overflow: hidden;
            white-space: nowrap;
            cursor: pointer;
            font-size: 30px;
            color: #ccc;
        }

        .rate:not(:checked)>label:before {
            content: '★ ';
        }

        .rate>input:checked~label {
            color: #ffc700;
        }

        .rate:not(:checked)>label:hover,
        .rate:not(:checked)>label:hover~label {
            color: #deb217;
        }

        .rate>input:checked+label:hover,
        .rate>input:checked+label:hover~label,
        .rate>input:checked~label:hover,
        .rate>input:checked~label:hover~label,
        .rate>label:hover~input:checked~label {
            color: #c59b08;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            var d = new Date();
            d.setDate(d.getDate());
            $('#eta').datepicker({
                todayBtn: "linked",
                language: "it",
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            }).datepicker("setDate", d);
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#order_no").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('outbound.getOutboundOrderIssue') }}",
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
                        $('#outbound_id').val(ui.item.outbound_id);
                        $('#order_no').val(ui.item.order_no);
                        $('#customer_name').val(ui.item.customer_name);
                        $('#description').val(ui.item.description);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>Customer Name: " + item.customer_name + "<br/>Order No: " + item.order_no +
                            "<br/>Description: " + item.description + "</div>")
                        .appendTo(ul);
                };

            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('issue-reason.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                            },
                            success: function(data) {
                                $("#loader").hide();
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });

                                    window.open(data.success, '_top');
                                } else {
                                    var pesan =
                                    "<div class='text-left alert alert-danger'>";
                                    for (var i = 0; i < data.error.length; i++) {
                                        pesan += data.error[i] + '</br>';
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
                            error: function(data) {
                                console.log('Error:', data);
                                $("#loader").hide();
                            }
                        });
                    }
                })
            }
        });

        function setIssue(issue) {
            $('#issue_id').val(issue);
        }
    </script>
@endpush
