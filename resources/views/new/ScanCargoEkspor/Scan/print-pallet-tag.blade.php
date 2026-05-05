@extends('layouts.new.base')
@section('title', 'MKT - Print Pallet Tag')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
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
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom gutter-b">
                <div class="card-body">
                    <form action="{{ url('export/ScanCargoEkspor/doPrint') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Pallet Tag <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="palletTag" name="pallet_tag"
                                        id="" aria-describedby="helpId"
                                        placeholder="Masukan 10 Digit Awal Pallet Tag" required autofocus
                                        autocomplete="off">
                                    <small id="helpId" class="form-text text-danger">*Required</small>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="">Range Start <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="start" name="start"
                                    id="" aria-describedby="helpId" placeholder="Start" autocomplete="off"
                                    required>
                            </div>
                            <div class="col-sm-2">
                                <label for="">Range End <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="end" name="end"
                                    id="" aria-describedby="helpId" placeholder="End" autocomplete="off" required>
                            </div>
                            <div class="col-sm-4">
                                <br>
                                <button type="submit" class="btn btn-outline-primary mt-2 btn-block"><i
                                        class="fas fa-print"></i>
                                    Print
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#start, #end').on('input', function() {
                let start = parseInt($('#start').val(), 10);
                let end = parseInt($('#end').val(), 10);

                // Reset validasi
                $('#start')[0].setCustomValidity('');
                $('#end')[0].setCustomValidity('');

                if (!isNaN(start) && start < 0) {
                    $('#start')[0].setCustomValidity('Nilai tidak boleh negatif');
                }

                if (!isNaN(end) && end < 0) {
                    $('#end')[0].setCustomValidity('Nilai tidak boleh negatif');
                }

                if (!isNaN(start) && !isNaN(end)) {
                    if (end < start) {
                        $('#end')[0].setCustomValidity(
                            'Range End tidak boleh lebih kecil dari Range Start');
                    }
                }
            });
            $('#palletTag').on('input', function() {
                let val = $(this).val();

                // Hapus karakter selain huruf dan angka
                val = val.replace(/[^a-zA-Z0-9]/g, '').slice(0, 10);

                $(this).val(val);
            });
        });
    </script>
@endpush
