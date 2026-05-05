@extends('layouts.new.base')
@section('title', 'MKT - Detail Perjalanan')
@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="float-left">
                                <a href="javascript:history.back()" class="btn mb-2 btn-md btn-dark"><i
                                        class="fas fa-arrow-left"></i> Back</a>
                                <a href="{{ url('MonitoringCheckpoint/planner/downloadFotoPerjalanan/' . $header->token) }}"
                                    class="btn mb-2 btn-md btn-info"><i class="fas fa-download"></i> Download Foto
                                    Perjalanan</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">No. Order</label>
                                <input type="text" name="no_order" class="form-control" placeholder="Silahkan di isi.."
                                    aria-describedby="helpId" required autofocus autocomplete="off" disabled
                                    value="{{ $header->no_order }}">
                            </div>
                            <div class="form-group">
                                <label for="">No. Mobil</label>
                                <input type="text" name="no_order" class="form-control" placeholder="Silahkan di isi.."
                                    aria-describedby="helpId" required autofocus autocomplete="off" disabled
                                    value="{{ $header->no_mobil }}">
                            </div>
                            <div class="form-group">
                                <label for="">Type Armada</label>
                                <input type="text" name="no_order" class="form-control" placeholder="Silahkan di isi.."
                                    aria-describedby="helpId" required autofocus autocomplete="off" disabled
                                    value="{{ $header->jenis_armada }}">
                            </div>
                            <div class="form-group">
                                <label for="">Nama Customer</label>
                                <input type="text" name="nama_customer" class="form-control"
                                    placeholder="Silahkan di isi.." aria-describedby="helpId" required autocomplete="off"
                                    disabled value="{{ $header->nama_customer }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Lokasi Muat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail->whereNotNull('lokasi_muat') as $item)
                                            <tr>
                                                <td>{{ $item->lokasi_muat }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                Lokasi Bongkar
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail->whereNotNull('lokasi_bongkar') as $item)
                                            <tr>
                                                <td>{{ $item->lokasi_bongkar }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <label for="">Driver</label>
                                <input type="text" name="nama_customer" class="form-control"
                                    placeholder="Silahkan di isi.." aria-describedby="helpId" required autocomplete="off"
                                    disabled value="{{ DB::table('users')->where('id', $header->driver)->value('name') }}">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Revenue</th>
                                            <th>Cost.</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($revenue as $item)
                                            <tr class="text-center">
                                                <td>{{ number_format($item->revenue, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->cost, 0, ',', '.') }}</td>
                                                <td>{{ $item->remarks == null ? '-' : $item->remarks }}</td>
                                            </tr>
                                        @endforeach
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
    <script src="{{ url('/assets/new/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript"></script>
@endpush
