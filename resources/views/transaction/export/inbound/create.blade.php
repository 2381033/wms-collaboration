@extends('layouts.main')

@section('title')
    Export - Inbound
@endsection

<style>
    .page {
        width: 125mm;
        min-height: 148mm;
        padding: 3mm;
        margin: 5mm auto;
        border: 1px #333 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        page-break-after: always;
        page-break-inside: avoid;
    }

    .container {
        width: 100%;
        margin: 0px auto;
    }

    .row:after {
        content: "";
        display: grid;
        clear: both;
    }

    .column {
        float: left;
        padding-bottom: 5px;
    }

    .col-100 {
        width: 100%;
    }

    .center {
        text-align: center;
    }

    .hide {
        display: none;
    }

    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #0C9;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
    }

    .my-float {
        margin-top: 22px;
    }

    .float .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        top: -5%;
        right: 105%;
        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .float:hover .tooltiptext {
        visibility: visible;
    }
</style>

@section('content')
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Export - Inbound</h2>
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Export - Inbound</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <form id="form-job" method="POST">
                @csrf
                <div class="row info-wrap p-3 m-3" style="border-radius: 13px; text-shadow: 13px;">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Forwarder Name</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.."
                                        id="forwarder_name" name="forwarder_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Shipper Name</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="shipper_name" name="shipper_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Consignee Name</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="consignee_name" name="consignee_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>PEB No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="peb_no"
                                        name="peb_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>AJU No</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." id="aju_no"
                                        name="aju_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Vehicle No.</label>
                                    <select class="form-control selectVehicle" name="vehicle_no" required id="">
                                        <option value="" selected disabled>Silahkan Pilih</option>
                                        @foreach ($vehicle as $item)
                                            <option value="{{ $item->vehicle_number }}">{{ $item->vehicle_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>VGM (Kg)</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="vgm" name="vgm" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="destination" name="destination" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Final Destination</label>
                                    <input type="text" autocomplete="off" placeholder="Silahkan isi.." required
                                        id="final_destination" name="final_destination" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Branch</label>
                                    <select id="my-select" required class="form-control" name="branch_id">
                                        <option value="" disabled selected>Silahkan Pilih</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group  col-sm-3 col-md-3 col-lg-3">
                        <br />
                        <a class="btn btn-dark btn-lg add-row text-white"> <i class="fas fa-plus-circle text-white">
                            </i> Add PO
                        </a>
                    </div>
                </div>
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>PO No.</th>
                                <th>QTY</th>
                                <th>Vol. CBM</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="demorow text-center">
                                <td class="sl_no">1</td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm" name="po[]"
                                            id="inputPO" aria-describedby="helpId" autocomplete="off"
                                            placeholder="Input here.." required>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm" name="qty[]"
                                            id="" aria-describedby="helpId" autocomplete="off"
                                            placeholder="Input Here.." required>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm" name="cbm[]"
                                            id="cbmAutomate" aria-describedby="helpId" autocomplete="off"
                                            placeholder="Input Here.." required>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-danger deleterow" type="submit"> <i
                                            class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <hr>
                <div class="col-sm-12">
                    <div class="float-right mt-2">
                        <button type="submit" id="btn-save-job" class="btn btn-success btn-lg rounded-4 saveJob"><i
                                class="fas fa-save"></i>
                            <span> Save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#cbmAutomate").on("keydown", function(e) {
            if (e.key === "," || e.keyCode === 188) {
                e.preventDefault();
            }
        });

        document.getElementById('vgm').addEventListener('input', function(e) {
            let value = this.value;
            value = value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            this.value = value;
        });


        $("#cbmAutomate").on("input", function() {
            this.value = this.value.replace(/,/g, "");
        });

        $('#inputPO').on('keypress', function(e) {
            if (e.which === 45) {
                e.preventDefault();
            }
        });
        $('#inputPO').on('input', function() {
            this.value = this.value.replace(/-/g, '');
        });
        $(".add-row").click(function() {
            const $this = $("table tbody");
            const lastIndex = Number($this.find("td.sl_no:last").text());
            const incremented = lastIndex + 1;
            const rowname = $("#new_row").val();
            let markup = `<tr class="text-center"><td class='sl_no'> ${incremented} </td>`;
            markup +=
                `<td>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm" name="po[]"
                                id="poAdd" aria-describedby="helpId" placeholder="Input here.." autocomplete="off" required>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm" name="qty[]"
                                id="" aria-describedby="helpId" placeholder="Input Here.." autocomplete="off" required>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm" name="cbm[]"
                                id="cbmAdd" aria-describedby="helpId" placeholder="Input Here.." autocomplete="off" required>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger deleterow" type="submit"> <i
                                class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>`;
            $("table tbody").append(markup);
            $("#cbmAdd").on("keydown", function(e) {
                // Cek kalau tombol yang ditekan adalah koma (keyCode 188 atau e.key === ",")
                if (e.key === "," || e.keyCode === 188) {
                    e.preventDefault(); // cegah input koma
                }
            });

            // Optional: kalau ada paste koma, hapus otomatis
            $("#cbmAdd").on("input", function() {
                this.value = this.value.replace(/,/g, "");
            });

            $('#poAdd').on('keypress', function(e) {
                if (e.which === 45) {
                    e.preventDefault();
                }
            });
            $('#poAdd').on('input', function() {
                this.value = this.value.replace(/-/g, '');
            });
        });

        $("table tbody").on("click", ".deleterow", function() {
            $(this).parent().parent().remove();
            $("tr .sl_no").each(function(i) {
                // Table tr seriaal  number update.
                $(this).text(i + 1);
            });
        });

        $('.selectChecker').select2();
        $('.selectVehicle').select2();
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#forwarder_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getForwarder') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                service_name: "Export",
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#forwarder_id').val(ui.item.forwarder_id);
                        $('#forwarder_name').val(ui.item.forwarder_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.forwarder_name + "</div>")
                        .appendTo(ul);
                };

            $("#shipper_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getShipper') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#shipper_id').val(ui.item.shipper_id);
                        $('#shipper_name').val(ui.item.shipper_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.shipper_name + "</div>")
                        .appendTo(ul);
                };

            $("#consignee_name").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getConsignee') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#consignee_id').val(ui.item.consignee_id);
                        $('#consignee_name').val(ui.item.consignee_name);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.consignee_name + "</div>")
                        .appendTo(ul);
                };

            $("#destination").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getDestination') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#destination').val(ui.item.destination);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.destination + "</div>")
                        .appendTo(ul);
                };

            $("#final_destination").autocomplete({
                    minLength: 0,
                    classes: {
                        "ui-autocomplete": "highlight"
                    },
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('export.getFinalDestination') }}",
                            dataType: "json",
                            data: {
                                _token: CSRF_TOKEN,
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#final_destination').val(ui.item.final_destination);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.final_destination + "</div>")
                        .appendTo(ul);
                };



            if ($("#form-job").length > 0) {
                $("#form-job").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-job').serialize(),
                            url: "{{ route('export-inbound.store') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                $("#loader").show();
                                $('#btn-save-job').hide();
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
                                $('#btn-save-job').show();
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                $("#loader").hide();
                                $('#btn-save-job').show();
                            }
                        });
                    }
                })
            }

            if ($("#form-detail").length > 0) {
                $("#form-detail").validate({
                    submitHandler: function(form) {
                        $.ajax({
                            data: $('#form-detail').serialize(),
                            url: "{{ route('export-detail.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(data) {
                                if ($.isEmptyObject(data.error)) {
                                    swal({
                                        icon: "success",
                                        text: "Data Successfully Saved."
                                    });
                                    location.href = data.message
                                } else {
                                    swal({
                                        icon: "error",
                                        text: data.error
                                    });
                                }
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            }
        });
    </script>
@endpush
