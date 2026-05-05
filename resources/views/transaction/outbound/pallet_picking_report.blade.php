<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{ asset('images/logos.png') }}" />
        </div>
        <table class="table-template">
            <thead>
                <tr>
                    <td>
                        <div class="header-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="title">
                            <h3 class="title-header">
                                {{ 'Pallet Picking Report' }}
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table-header-kerangka">
                                <tr>
                                    <td>
                                        <table class="table-header">
                                            <tr>
                                                <td>Principal Name</td>
                                                <td>:</td>
                                                <td>{{ $getName }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Number</td>
                                                <td>:</td>
                                                <td>{{ $header->job_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Date</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tujuan</td>
                                                <td>:</td>
                                                <td>{{ Str::Upper($despatch->tujuan) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header">
                                            <tr>
                                                <td>Vehicle Type</td>
                                                <td>:</td>
                                                <td>{{ Str::Upper($despatch->size) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Vehicle No</td>
                                                <td>:</td>
                                                <td>{{ Str::Upper($despatch->vehicle_no) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Container No</td>
                                                <td>:</td>
                                                <td>{{ $despatch->container_no == null ? '-' : Str::Upper($despatch->container_no) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Job Confirmed</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->confirmed_date)->format('d-m-Y H:i') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NO.</th>
                                        <th>LOCATION</th>
                                        <th>SKU</th>
                                        <th>BATCH NO</th>
                                        <th>STOCK</th>
                                        <th>PICKINGAN</th>
                                        <th>SISA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $stock = 0;
                                        $pickingan = 0;
                                        $sisa = 0;
                                    @endphp
                                    @foreach ($data as $key => $val)
                                        @php
                                            $stock += $val['stockAwal'];
                                            $pickingan += $val['yangDiAmbil'];
                                            $sisa += $val['stockAkhir'];
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td> {{ $val['location_code'] }}</td>
                                            <td> {{ $val['product_code'] }}</td>
                                            <td> {{ $val['lot_no'] }}</td>
                                            <td>{{ $val['stockAwal'] }}</td>
                                            <td>{{ $val['yangDiAmbil'] }}</td>
                                            <td>{{ $val['stockAkhir'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-right">TOTAL</td>
                                        <td class="text-center">{{ $stock }}</td>
                                        <td class="text-center">{{ $pickingan }}</td>
                                        <td class="text-center">{{ $sisa }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        document.title = "{{ 'Pallet Picking Report' }}"
        // window.print()
    </script>
</body>

</html>
