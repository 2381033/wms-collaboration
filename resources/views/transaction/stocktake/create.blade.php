@extends('layouts.main')

@section('title')
    Stock Take
@endsection
<style>
    .custom-card {
        border-radius: 16px;
        background: linear-gradient(to bottom right, #f9f9ff, #f1f5fb);
        transition: box-shadow 0.3s ease-in-out;
    }

    .custom-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .custom-card .card-title {
        font-size: 1.1rem;
        color: #333;
    }

    input.form-control-sm {
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .btn-mathcing {
        border-radius: 6px;
        transition: background-color 0.2s;
    }
</style>

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Stock Take</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Stock Take</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container-fluid">
            <div class="row" data-aos="fade-up">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="stocktake_no">Job Number</label>
                        <input type="text" id="stocktake_no" name="stocktake_no"
                            @isset($job_view->stocktake_no) value="{{ $job_view->stocktake_no }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="stocktake_date">Job Date</label>
                        <input type="text" id="stocktake_date" name="stocktake_date"
                            @isset($job_view->stocktake_date) value="{{ \Carbon\Carbon::parse($job_view->stocktake_date)->format('d-m-Y') }}" @endisset
                            class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3" data-aos="fade-up">
                <div class="col-md-12">
                    <div class="btn-group">
                        <a href="{{ url('/inventory/stock-take/create/0') }}" class="btn btn-primary btn-sm"><i
                                class="fas fa-plus"></i> <span>Add New Job</span></a>
                        &nbsp;&nbsp;
                    </div>
                </div>
            </div>
            <div class="row info-wrap" data-aos="fade-up">
                <div class="col-md 12">
                    <ul class="nav nav-tabs" id="inbound-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="job-link" data-toggle="tab" href="#job-tab" role="tab"
                                aria-controls="job" aria-selected="true">
                                <i class="fas fa-box"></i> Entry</a>
                        </li>
                        @if (isset($job_view->id) && !empty($job_view->id))
                            @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                @if ($job_view->confirmed_flag == 'No')
                                    <li class="nav-item">
                                        <a class="nav-link" id="entry-link" data-toggle="tab" href="#entry-tab"
                                            role="tab" aria-controls="entry" aria-selected="false">
                                            <i class="fas fa-box"></i> Entry Actual</a>
                                @endif
                            @endif
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="release-link" data-toggle="tab" href="#release-tab" role="tab"
                                    aria-controls="release" aria-selected="false">
                                    <i class="fas fa-box"></i> Release Stock</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content" id="replenishTab">
                        <div class="tab-pane fade show active" id="job-tab" role="tabpanel" aria-labelledby="job-tab5">
                            <form id="form-job" method="POST">
                                @csrf
                                <input type="hidden" id="take_id" name="take_id"
                                    @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                <div class="container mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Principal Name</label>
                                                <select class="custom-select" id="principal_id"
                                                    onchange="selectPrincipal(this.value)" name="principal_id"
                                                    @isset($job_view->id) disabled @endisset>
                                                    <option value="">.:Select:.</option>
                                                    @foreach (Auth::user()->principal as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if (isset($job_view->principal_id) && !empty($job_view->principal_id)) @if ($item->id == $job_view->principal_id) selected @endif
                                                            @endif>{{ $item->principal_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Freeze Blok</label>
                                                @isset($job_view->id)
                                                    <input type="text" value="{{ $job_view->block }}" class="form-control"
                                                        disabled>
                                                @else
                                                    <select class="form-control" id="blok" name="block[]" multiple
                                                        required>
                                                    </select>
                                                @endisset
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" autocomplete="off" id="description" name="description"
                                                    class="form-control"
                                                    @isset($job_view->description) value="{{ $job_view->description }}" @endisset
                                                    @isset($job_view->id) disabled @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="btn-group">
                                                @if (!isset($job_view->id))
                                                    <button type="submit" id="btn-save-job"
                                                        class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                                        <span>Save</span></button>
                                                @else
                                                    <a id="blank-print"
                                                        @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                        class="btn btn-info btn-sm"><i class="fas fa-print"></i>
                                                        <span>Blank Form</span></a>
                                                    <a id="book-print"
                                                        @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                                        class="btn btn-info btn-sm"><i class="fas fa-print"></i>
                                                        <span>Book Form</span></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="entry-tab" role="tabpanel" aria-labelledby="entry-tab5">
                            <form id="form-entry" name="form-entry" method="post">
                                @csrf
                                <div class="row mt-3">
                                    <div class="col-md-12 mb-4">
                                        <input class="mb-4" type="text" id="search_entry" placeholder="Search...">
                                        <div id="entry_cards"></div>
                                        <div id="pagination" class="mt-3"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="release-tab" role="tabpanel" aria-labelledby="release-tab5">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                            @if ($job_view->confirmed_flag == 'No')
                                                <a class="btn btn-warning btn-sm" onclick="processRelease()"
                                                    id="process-release"><i class="fas fa-play"></i>
                                                    <span>Proccess</span></a>
                                            @endif
                                        @endif
                                        <a id="release-print"
                                            @if (isset($job_view->id) && !empty($job_view->id)) enabled @else disabled @endif
                                            class="btn btn-info btn-sm"><i class="fas fa-print"></i> <span>Release
                                                Report</span></a>
                                        @if (isset($job_view->confirmed_flag) && !empty($job_view->confirmed_flag))
                                            @if ($job_view->confirmed_flag == 'No')
                                                <a id="confirm-job" class="btn btn-success btn-sm"><i
                                                        class="fas fa-check-circle text-white"></i>
                                                    <span class="text-white">
                                                        Confirm
                                                        Job</span></a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <form id="form-release" name="form-release" method="post">
                                        <input type="hidden" id="take_release" name="take_release"
                                            @isset($job_view->id) value="{{ $job_view->id }}" @endisset>
                                        @csrf
                                    </form>
                                    <div class="table-responsive">
                                        <table id="release_table" class="table table-bordered table-sm"
                                            style="width:100%">
                                            <thead class="text-center">
                                                <tr>
                                                    <th rowspan="2">
                                                        <input type="checkbox" required="required"
                                                            class="release-check-all">
                                                    </th>
                                                    <th rowspan="2">Product Code</th>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Batch No</th>
                                                    <th rowspan="2">Site Name</th>
                                                    <th rowspan="2">Area Name</th>
                                                    <th rowspan="2">Location</th>
                                                    <th colspan="6">Actual Quantity</th>
                                                    <th colspan="3">Actual Batch</th>
                                                </tr>
                                                <tr>
                                                    <th>1st</th>
                                                    <th>Unit</th>
                                                    <th>2nd</th>
                                                    <th>Unit</th>
                                                    <th>3rd</th>
                                                    <th>Unit</th>
                                                    <th>Batch</th>
                                                    <th>Mfg Date</th>
                                                    <th>Exp Date</th>
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

@section('modal')
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let perPage = 10;
        let currentSearch = '';
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            // Setup event listener
            $("#entry-link").click(function(e) {
                e.preventDefault();
                load_entry(page = 1, search = '');
            });

            $("#release-link").click(function(e) {
                e.preventDefault();
                load_release();
            });

            $("#confirm-link").click(function(e) {
                e.preventDefault();
                load_confirm();
            });

            load_data();
            let originalEntryData = [];

            function load_data() {
                let link_id = $('.nav-tabs .active').attr('id');
                if (link_id == 'entry-link') {
                    load_entry(page = 1, search = '');
                } else if (link_id == 'release-link') {
                    load_release();
                } else if (link_id == 'confirm-link') {
                    load_confirm();
                }
            }

            function load_entry(page = 1, search = '') {
                console.log($("#take_id").val());
                $.ajax({
                    url: "{{ route('take-detail.index') }}",
                    type: "GET",
                    data: {
                        take_id: $("#take_id").val(),
                        page: page,
                        per_page: perPage,
                        search: search
                    },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            renderEntryCards(response.data);
                            $('#pagination').remove();
                        } else {
                            $('#entry_cards').html(
                                `<div class="alert alert-info">Tidak ada data ditemukan.</div>`);
                            $('#pagination').empty();
                        }
                    },
                    error: function() {
                        $('#entry_cards').html(
                            `<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>`
                        );
                        $('#pagination').empty();
                    }
                });
            }

            function renderEntryCards(data) {
                let container = $('#entry_cards');
                container.empty();

                data.forEach(function(item) {
                    let card = `
                    <form class="entry-form" data-id="${item.id}">
                        <div class="card custom-card mb-4 shadow-sm border-0">
                            <div class="card-body p-4 position-relative">
                                <h5 class="card-title fw-semibold text-dark mb-3">${item.product_code}</h5>
                                <div class="row text-muted small mb-3">
                                    <div class="col-md-6 mb-2"><strong>Name:</strong> ${item.product_name ?? '-'}</div>
                                    ${item.lot_no ? `<div class="col-md-6 mb-2"><strong>Batch No:</strong> ${item.lot_no}</div>` : ''}
                                    <div class="col-md-6 mb-2"><strong>Location:</strong> ${item.location_code ?? '-'}</div>
                                    <div class="col-md-6 mb-2"><strong>Site:</strong> ${item.site_name ?? '-'}</div>
                                    <div class="col-md-6 mb-2"><strong>Area:</strong> ${item.area_name ?? '-'}</div>
                                    <div class="col-md-6 mb-2 text-danger"><strong>SOH:</strong> ${item.qty} ${item.puom}</div>
                                    <div class="col-md-6 mb-2 text-danger"><strong>SOA:</strong> ${item.soa} ${item.puom}</div>
                                    <div class="col-md-6 mb-2 text-danger"><strong>SOB:</strong> ${item.sob} ${item.puom}</div>
                                </div>

                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label"><strong>Qty Actual (${item.puom})</strong></label>
                                        <input type="number" 
                                            class="form-control form-control-sm qty-actual-input" 
                                            name="actual_pqty" 
                                            value="0" 
                                            placeholder="Isi qty actual...">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label"><strong>Notes</strong></label>
                                        <input type="text" 
                                            class="form-control form-control-sm note-input" 
                                            name="note"
                                            value="${item.note ?? ''}" 
                                            placeholder="Type here...">
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-success btn-mathcing" data-id="${item.id}"> <i class="fas fa-check-circle"></i> Mathcing </a>
                                    <button type='submit' class="btn btn-sm btn-dark">
                                        <i class="fas fa-save"></i> Update
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    `;
                    container.append(card);
                });

                $('.entry-form').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const id = form.data('id');
                    const qty = form.find('input[name="actual_pqty"]').val();
                    const note = form.find('input[name="note"]').val();

                    $.ajax({
                        url: "{{ url('inventory/stock-take/updateList') }}/" + id,
                        type: "POST", // atau POST sesuai kebutuhan
                        data: {
                            _token: '{{ csrf_token() }}',
                            actual_pqty: qty,
                            id: id,
                            note: note
                        },
                        success: function(response) {
                            swal({
                                icon: "success",
                                text: "Data Successfully updated."
                            });
                            load_entry();
                        },
                        error: function(xhr) {
                            alert('Gagal mengupdate data');
                        }
                    });
                });
            }

            $('#search_entry').on('input', function() {
                currentSearch = $(this).val();
                currentPage = 1;
                load_entry(currentPage, currentSearch);
            });

            $('body').on('click', '.btn-mathcing', function() {
                const id = $(this).data('id');
                mathcing(id)
            });

            function deleteEntry(id) {
                $.ajax({
                    url: `/take-detail/${id}`,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        alert('Data berhasil dihapus');
                        load_entry();
                    },
                    error: function() {
                        alert('Gagal menghapus data');
                    }
                });
            }

            $("#form-job").validate({
                submitHandler: function(form) {
                    var actionType = $("#btn-save-job").val();
                    $("#btn-save-job").html("Sending..");
                    $("#btn-save-job").attr("disabled", true);

                    $.ajax({
                        data: $("#form-job").serialize(),
                        url: "{{ route('take-job.store') }}",
                        type: "POST",
                        dataType: "json",
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
                                $("#btn-save-job").attr("disabled", false);
                                swal({
                                    icon: "success",
                                    text: "Data Successfully Saved."
                                });

                                window.open(data.success, "_top");
                            } else {
                                $("#btn-save-job").attr("disabled", false);
                                var pesan =
                                    "<div class='text-left alert alert-danger'>";
                                for (var i = 0; i < data.error.length; i++) {
                                    pesan += data.error[i] + "</br>";
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
                        error: function(data) {
                            console.log("Error:", data);
                            $("#btn-save-job").html("Save");
                            $("#btn-save-job").attr("disabled", false);
                        }
                    });
                }
            })

            function mathcing(id) {
                swal({
                        title: "Are you sure?",
                        icon: "info",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ url('inventory/stock-take/matchingList') }}/" + id,
                                type: "GET",
                                dataType: "json",
                                success: function(data) {
                                    if ($.isEmptyObject(data.error)) {
                                        swal({
                                            icon: "success",
                                            text: "Data Successfully Saved."
                                        });
                                        let val = $('#search_entry').val();
                                        load_entry(page = 1, search = val);
                                    }
                                },
                                error: function(data) {
                                    console.log("Error:", data);
                                }
                            });
                        } else {
                            return false;
                        }
                    });
            }

            function confirmJob(id) {
                swal({
                        title: "Are you sure for confirm job?",
                        icon: "info",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ url('inventory/stock-take/confirmJob') }}/" + id,
                                type: "GET",
                                dataType: "json",
                                success: function(data) {
                                    location.reload();
                                },
                                error: function(data) {
                                    console.log("Error:", data);
                                }
                            });
                        } else {
                            return false;
                        }
                    });
            }

            function load_release() {
                $("#release_table").DataTable().destroy();
                $("#release_table").DataTable({
                    "dom": "<'toolbar'>frtip",
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: {
                        url: "{{ route('take-release.index') }}",
                        type: "GET",
                        data: {
                            take_id: $("#take_release").val()
                        }
                    },
                    columns: [{
                            data: 'check',
                            name: 'check',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'product_code',
                            name: 'product_code'
                        },
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'lot_no',
                            name: 'lot_no'
                        },
                        {
                            data: 'site_name',
                            name: 'site_name'
                        },
                        {
                            data: 'area_name',
                            name: 'area_name'
                        },
                        {
                            data: 'location_code',
                            name: 'location_code'
                        },
                        {
                            data: 'actual_pqty',
                            name: 'pqty'
                        },
                        {
                            data: 'puom',
                            name: 'puom'
                        },
                        {
                            data: 'actual_mqty',
                            name: 'mqty'
                        },
                        {
                            data: 'muom',
                            name: 'muom'
                        },
                        {
                            data: 'actual_bqty',
                            name: 'bqty'
                        },
                        {
                            data: 'buom',
                            name: 'buom'
                        },
                        {
                            data: 'actual_lot_no',
                            name: 'actual_lot_no'
                        },
                        {
                            data: 'actual_mfg_date',
                            name: 'actual_mfg_date'
                        },
                        {
                            data: 'actual_exp_date',
                            name: 'actual_exp_date'
                        },
                    ],
                    order: [
                        [0, "asc"]
                    ]
                });
            }

            // Checkbox handlers
            $("#release_table").on("click", ".release-check", function() {
                $(".release-check-all").prop("checked", $(".release-check:checked").length === $(
                    ".release-check").length);
            });

            $("#release_table").on("click", ".release-check-all", function() {
                $(".release-check").prop("checked", this.checked);
            });

            $("#confirm_table").on("click", ".confirm-check", function() {
                $(".confirm-check-all").prop("checked", $(".confirm-check:checked").length === $(
                    ".confirm-check").length);
            });

            $("#confirm_table").on("click", ".confirm-check-all", function() {
                $(".confirm-check").prop("checked", this.checked);
            });

            // Cetak
            $('body').on('click', '#blank-print', function() {
                window.open("{{ url('/inventory/stock-take/report/blank/') }}/" + $('#take_id').val(),
                    "CycleCountReport", "width=800,height=600");
            });

            $('body').on('click', '#book-print', function() {
                window.open("{{ url('/inventory/stock-take/report/book/') }}/" + $('#take_id').val(),
                    "CycleCountReport", "width=800,height=600");
            });

            $('body').on('click', '#release-print', function() {
                window.open("{{ url('/inventory/stock-take/report/release/') }}/" + $('#take_id').val(),
                    "CycleCountReport", "width=800,height=600");
            });

            $('body').on('click', '#confirm-print', function() {
                window.open("{{ url('/inventory/stock-take/report/adjust/') }}/" + $('#take_id').val(),
                    "CycleCountReport", "width=800,height=600");
            });

            $('body').on('click', '#confirm-job', function() {
                var id = $('#take_id').val();
                confirmJob(id);
            });
        });
        $('#blok').select2({
            placeholder: "Pilih Blok",
            width: '100%'
        });


        function selectPrincipal(principal_id) {
            $.ajax({
                url: "{{ url('inventory/stock-take/getBlok') }}/" + principal_id,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    $('#blok').empty();
                    $('#blok').append('<option value="">.:Select:.</option>');
                    $.each(res.data, function(key, value) {
                        $('#blok').append('<option value="' + value.block + '">' + value.block +
                            '</option>');
                    });
                },
                error: function(data) {
                    console.log("Error:", data);
                }
            });
        }

        function processRelease() {
            var release_ids = [];

            // Ambil ID yang diceklis
            $("#release_table input[type='checkbox']:checked").each(function() {
                release_ids.push($(this).val());
            });

            if (release_ids.length === 0) {
                swal({
                    icon: "warning",
                    text: "Pilih minimal satu item terlebih dahulu."
                });
                return;
            }

            // Buat form dinamis
            var form = $('<form>', {
                method: 'POST',
                action: '{{ route('take-release.submit') }}',
                target: '_blank'
            });

            // Tambahkan CSRF token
            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));

            // Tambahkan input untuk release_id[]
            release_ids.forEach(function(id) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'release_id[]',
                    value: id
                }));
            });

            // Tambahkan form ke body dan submit ke tab baru
            $('body').append(form);
            form[0].submit();
            form.remove();
        }
    </script>
@endpush
