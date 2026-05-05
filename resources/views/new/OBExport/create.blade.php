@extends('layouts.new.base')
@section('title', 'MKT - OB Export')
@push('styles')
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
    <div class="container" style="zoom: 110%;">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-right mb-4">
                                <a href="{{ url('home') }}" class="btn btn-md btn-dark" style="border-radius: 15px;"><i
                                        class="fas fa-home"></i> Home</a>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-line mb-5">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#JobHeader">
                                        <span class="nav-icon"><i class="flaticon-information"></i></span>
                                        <span class="nav-text">Job Header</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="JobHeader" role="tabpanel"
                                    aria-labelledby="JobHeader">
                                    <form action="{{ route('export.ob.store') }}" method="post" id="PostForm">
                                        @csrf
                                        <div class="card-body p-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>PEB Number</label>
                                                        <input type="text" class="form-control" id="peb_number"
                                                            placeholder="Silahkan isi" name="peb_number" required
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>AJU Number</label>
                                                        <input type="text" class="form-control" id="aju_number"
                                                            placeholder="Silahkan isi" name="aju_number" required
                                                            autocomplete="off" />
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Forwarder</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="forwarder" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Qty</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="qty" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Shipper</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="shipper" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Total Pallet</label>
                                                        <input type="text" class="form-control" placeholder="auto"
                                                            id="totalPallet" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">No. Mobil</label>
                                                        <select class="form-control" name="no_mobil" required
                                                            id="">
                                                            <option value="" selected disabled>Silahkan Pilih
                                                            </option>
                                                            @foreach ($mobil as $item)
                                                                <option value="{{ $item->vehicle_number }}">
                                                                    {{ $item->vehicle_number }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>PIC Penerima</label>
                                                        <input type="text" class="form-control" name="pic"
                                                            placeholder="Silahkan isi.." autocomplete="off" id=""
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Destination</label>
                                                        <input type="text" class="form-control" name="destination"
                                                            placeholder="Silahkan isi.." autocomplete="off" id=""
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="">Remarks</label>
                                                        <textarea class="form-control" name="remarks" id="" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-info btn-lg mr-2"
                                                    id="submitHeader"><i class="flaticon2-checkmark"></i>
                                                    Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $("#peb_number").autocomplete({
                source: "{{ route('autocomplete.peb') }}",
                minLength: 2,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    loadLedgerDetail({
                        peb: ui.item.value
                    });
                }
            });

            $("#aju_number").autocomplete({
                source: "{{ route('autocomplete.aju') }}",
                minLength: 2,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    loadLedgerDetail({
                        aju: ui.item.value
                    });
                }
            });
        });

        function loadLedgerDetail(params) {
            $.ajax({
                url: "{{ url('export/ob/getDetail') }}",
                data: params,
                success: function(res) {
                    if (res.success) {
                        $("#forwarder").val(res.forwarder);
                        $("#shipper").val(res.shipper);
                        $("#qty").val(res.qty);
                        $("#totalPallet").val(res.total_pallet);
                        $("#vgm").val(res.vgm);
                    } else {
                        $("#forwarder, #shipper, #qty").val('');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data ledger');
                }
            });
        }
    </script>
@endpush
