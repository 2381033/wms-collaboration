<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/despatch.css') }}">
</head>

<body>
    <div class="page">
        <div class="container">
            <div class="row">
                <div class="column">
                    <img alt="image" class="logo" src="{{ asset('images/logos.png') }}" style="width: 300px;" />
                </div>
                <div class="column">
                    <h4 style="float: right;">PT. Masaji Kargosentra Tama</h4>
                </div>
            </div>
            <div class="row">
                <div class="title">
                    Delivery Order
                </div>
            </div>
            <div class="row">
                <div class="column left">
                    <table>
                        <tr>
                            <td colspan="2">On Behalf Of:</td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    {{ $customer->where('id', $v_despatch->id_customer)->first()->name }}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">Delivered To:</td>
                        </tr>
                        <tr>
                            <td>{{ $despatch->store_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ $despatch->store_address }}</td>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b><u>Detail Vehicle</u></b></td>
                        </tr>
                        <tr>
                            <td>
                                <b>Container No : </b>{{ $despatch->container_no }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Vehicle No : </b>{{ $despatch->vehicle_no }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Driver Name : </b>{{ $despatch->driver_name }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="column right">
                    <table>
                        <tr>
                            <td>DO Number</td>
                            <td>:</td>
                            <td> {{ $v_despatch->do_no }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($v_despatch->do_no, 'C39') }}"
                                    alt="barcode" style="width: 350px; height: 50px;" /><br>
                            </td>
                        </tr>
                        <tr>
                            <td>Requested Delivery Date</td>
                            <td>:</td>
                            <td>
                                {{ formatTanggalIndonesia2($despatch->etd) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Customer Ref.</td>
                            <td>:</td>
                            <td>
                                {{ $despatch->ref_number }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($despatch->ref_number, 'C39') }}"
                                    alt="barcode" style="width: 350px; height: 50px;" /><br>
                            </td>
                        </tr>
                        <tr>
                            <td>Customer Name</td>
                            <td>:</td>
                            <td>
                                {{ $despatch->store_name }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th rowspan="3" class="center">No</th>
                            <th rowspan="3" class="center">ID Cargo</th>
                            <th rowspan="3" class="center">Description</th>
                            <th colspan="3">Shipped Quantity</th>
                        </tr>
                        <tr>
                            <th>Qty</th>
                            {{-- <th>Unit</th> --}}
                            <th>Weight Total(Kg)</th>
                            <th>Vol. Total(Cbm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupBy as $key => $value)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td class="center">{{ $key }}</td>
                                <td class="center">
                                    {{ $data->where('id_cargo', $key)->first()->stock->description ?? '-' }}
                                </td>
                                <td class="center">
                                    {{ array_sum($data->where('id_cargo', $key)->pluck('qty')->toArray()) }}
                                </td>
                                <td class="center">{{ number_format(array_sum($w_sum[$key]), 0, '.', '') }}</td>
                                <td class="center">{{ number_format(array_sum($cbm_sum[$key]), 2, '.', '') }}</td>
                            </tr>
                            @php
                                $qty_sum[] = array_sum(
                                    $data
                                        ->where('id_cargo', $key)
                                        ->pluck('qty')
                                        ->toArray(),
                                );
                                $w_sum[] = array_sum($w_sum[$key]);
                                $vol_sum[] = array_sum($cbm_sum[$key]);
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="3" class="center"><b>TOTAL</b></td>
                            <td class="center">{{ array_sum($qty_sum) }}</td>
                            <td class="center">{{ array_sum($w_sum) }}</td>
                            <td class="center">{{ number_format(array_sum($vol_sum), 2, '.', '') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="rowspace">
                    <table class="table">
                        <tr>
                            <td class="sign">Driver Name:</td>
                            <td class="sign">Tally Name:</td>
                            <td class="sign">Warehouse:</td>
                            <td class="sign">Security:</td>
                            <td class="sign">Received Name:</td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td>Date:</td>
                            <td>Date:</td>
                            <td>Date:</td>
                            <td>Date:</td>
                        </tr>
                        <tr>
                            <td>Signature:<br><br><br><br></td>
                            <td>Signature:<br><br><br><br></td>
                            <td>Signature:<br><br><br><br></td>
                            <td>Signature:<br><br><br><br></td>
                            <td>Signature:<br><br><br><br></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
