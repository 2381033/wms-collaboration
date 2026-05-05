@extends('layouts.new.base')
@section('title', 'MKT - Report Penagihan')
@push('styles')
    <link href="{{ url('/') }}assets/new/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />
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
    <div class="container-fluid">
        <div class="main-body">
            <div class="card" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="zoom: 110%;">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center text-dark"
                                                style="background-color: #CBCFFF">
                                                <h2>{{ date('M') }}</h2>
                                            </th>
                                        </tr>
                                        <tr class="text-center">
                                            <th style="background-color: #003166" class="text-white">NO</th>
                                            <th style="background-color: #003166" class="text-white">INCOMING</th>
                                            <th style="background-color: #003166" class="text-white">OUTGOING</th>
                                            <th style="background-color: #003166" class="text-white">BALANCE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < $jumlah_hari; $i++)
                                            <tr class="text-center">
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ number_format($incoming[$i][0]->cbm ?? 0, 2, '.', '') }}</td>
                                                <td>{{ number_format($outgoing[$i][0]->cbm ?? 0, 2, '.', '') }}</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ url('/') }}/assets/new/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#postPrice').on('submit', function() {
            $('.btnsave').attr('disabled', true);
        });

        $('#table').DataTable();
    </script>
@endpush
