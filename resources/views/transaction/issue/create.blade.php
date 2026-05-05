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
        <div id="loadingOverlay"
            style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    z-index:9999;
    align-items:center;
    justify-content:center;
">
            <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="container">
            <div class="row info-wrap">
                <div class="col-md 12">
                    <div class="mb-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" id="selectAll">Select All</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="deselectAll">Deselect All</button>
                        <button type="button" class="btn btn-sm btn-success" id="saveData">Save</button>
                    </div>
                    <table id="rating_table" class="table table-striped table-bordered table-sm" style="width:100%;">
                        <thead class="text-center">
                            <tr>
                                <th>
                                    <input type="checkbox" required="required" class="confirm-check-all">
                                </th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Description</th>
                                <th>Rating</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
@endsection

@push('styles')
    <style>
        .rate {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 6px;
        }

        .rate input {
            display: none;
        }

        .rate label {
            font-size: 38px;
            cursor: pointer;
            color: #ddd;
            transition: all 0.25s ease;
            position: relative;
        }

        .rate label:before {
            content: "★";
        }

        .rate label:hover,
        .rate label:hover~label {
            color: #ffb400;
            transform: scale(1.2);
            text-shadow: 0 0 8px rgba(255, 180, 0, 0.6);
        }

        .rate input:checked~label {
            color: #ffc700;
            text-shadow: 0 0 10px rgba(255, 199, 0, 0.9);
        }

        .rate input:checked+label {
            animation: pop 0.25s ease;
        }

        @keyframes pop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.35);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        getList();

        $(document).on('change', '.rate input[type="radio"]', function() {
            let row = $(this).closest('tr');
            row.find('.row-check').prop('checked', true);
        });

        $(document).on('change', '.row-check', function() {
            if (!$(this).is(':checked')) {
                let row = $(this).closest('tr');
                row.find('.rate input[type="radio"]').prop('checked', false);
            }
        });

        $('#selectAll').on('click', function() {
            $('.row-check').prop('checked', true);
        });

        $('#deselectAll').on('click', function() {
            $('.row-check').prop('checked', false);
        });

        $(document).on('change', '.confirm-check-all', function() {
            $('.row-check').prop('checked', this.checked);
        });

        $('#saveData').on('click', function() {
            $('#loadingOverlay').css('display', 'flex');
            let data = [];
            $('.row-check:checked').each(function() {
                let id = $(this).val();
                let rating = $('input[name="rating_' + id + '"]:checked').val();
                let notes = $('input[name="notes_' + id + '"]').val();
                data.push({
                    id: id,
                    rating: rating ?? null,
                    notes: notes ?? null
                });

            });

            if (data.length === 0) {
                $('#loadingOverlay').hide();
                alert('Pilih minimal 1 data.');
                return;
            }

            $.ajax({
                url: "{{ route('issue-reason.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    items: data
                },
                success: function(response) {
                    $('#loadingOverlay').hide();
                    if (response.status === 'success') {
                        alert(response.message);
                        getList();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').hide();
                    let message = 'Terjadi kesalahan saat menyimpan.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors)
                                .flat()
                                .join('\n');
                        }
                    }
                    alert(message);
                }
            });

        });

        function getList() {
            $('#rating_table').DataTable().destroy();
            $('#rating_table').DataTable({
                "dom": '<"wrapper"flipt>',
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('issue-reason.getList') }}",
                    type: "GET",
                },
                columns: [{
                        data: 'check',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'order_no'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'rating',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notes',
                        orderable: false,
                        searchable: false
                    }
                ]

            });
        }
    </script>
@endpush
