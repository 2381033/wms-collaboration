<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('assets/css/despatch.css') }}">
</head>
<style type="text/css">
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

    input[type=text],
    select {
        width: 90%;
        padding: 5px;
        display: inline-block;
        border: 1px transparent;
        border-radius: 4px;
        box-sizing: border-box;
    }

    @media print {
        .no-print {
            visibility: hidden !important;
        }
    }

    .wrap {
        width: 5px;
        word-wrap: break-word;
        !important;
        font-size: 10px;
    }
</style>

<body>
    <form action="{{ route('addRemarksDespatch') }}" method="post" id="formDespatch">
        @csrf
        <div class="page">
            <div class="container">
                <div class="row">
                    <div class="column">
                        <img alt="image" class="logo" src="{{ asset('images/logos.png') }}"
                            style="width: 300px;" />
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
                                <td>&nbsp;&nbsp;</td>
                                <td>{{ $view_data->principal_name }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->prin_address1 }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->prin_address2 }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->prin_address3 }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Delivered To:</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->store_name }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->store_address1 }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->store_address2 }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ $view_data->store_address3 }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Detail Vehicle:</b></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <b>Vehicle No : </b>{{ $view_data->vehicle_no }}
                                    <br>
                                    <b>Seal No : </b>{{ $view_data->seal_no }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="column right">
                        <table>
                            <tr>
                                <td>DO Number</td>
                                <td>:</td>
                                <td>
                                    {{ $view_data->do_no }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($view_data->do_no, 'C39') }}"
                                        alt="barcode" /><br>
                                </td>
                            </tr>
                            <tr>
                                <td>Requested Delivery Date</td>
                                <td>:</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($view_data->etd)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Target Date</td>
                                <td>:</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($order_data->due_date)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Customer Ref.</td>
                                <td>:</td>
                                <td>
                                    {{ $view_data->reference_no }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    @isset($view_data->reference_no)
                                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($view_data->do_no, 'C39') }}"
                                            alt="barcode" /><br>
                                    @endisset
                                </td>
                            </tr>
                            <tr>
                                <td>Customer Name</td>
                                <td>:</td>
                                <td>
                                    {{ $view_data->customer_name }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    @if ($view_data->multi_level == 'Yes')
                        <table class="table">
                            <thead>
                                <tr>
                                    <th rowspan="3" class="center">No</th>
                                    <th rowspan="3" class="center">Product</th>
                                    <th rowspan="3" class="center">Description</th>
                                    <th rowspan="3" class="center">Batch No</th>
                                    <th rowspan="3" class="center">Conversi Qty</th>
                                    <th colspan="6">Shipped Quantity</th>
                                    <th rowspan="3" class="center">Quantum</th>
                                </tr>
                                <tr>
                                    <th colspan="2">1st</th>
                                    <th colspan="2">2nd</th>
                                    <th colspan="2">3rd</th>
                                    {{-- <th rowspan="2">Weight</th>
                                <th rowspan="2">Volume</th> --}}
                                </tr>
                                <tr>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                    $pqty = 0;
                                    $mqty = 0;
                                    $bqty = 0;
                                    $total_pqty = 0;
                                    $total_mqty = 0;
                                    $total_bqty = 0;
                                    $total_quantum = 0;
                                    $weight = 0;
                                    $volume = 0;
                                @endphp
                                @foreach ($detail_list as $item)
                                    @php
                                        $pqty = ($item->qty - ($item->qty % $item->uppp)) / $item->uppp;
                                        $mqty =
                                            (($item->qty % $item->uppp) - (($item->qty % $item->uppp) % $item->muppp)) /
                                            $item->muppp;
                                        $bqty = ($item->qty % $item->uppp) % $item->muppp;
                                    @endphp
                                    <tr>
                                        <td class="center">{{ $i }}</td>
                                        <td class="center">{{ $item->product_code }}</td>
                                        <td class="center">{{ $item->product_name }}</td>
                                        <td class="center">{{ $item->lot_no }}</td>
                                        <td class="center">{{ $item->muppp }}</td>
                                        <td class="right">{{ number_format($pqty, 0, ',', '.') }}</td>
                                        <td class="center">{{ $item->puom }}</td>
                                        <td class="right">{{ number_format($mqty, 0, ',', '.') }}</td>
                                        <td class="center">{{ $item->muom }}</td>
                                        <td class="right">{{ number_format($bqty, 0, ',', '.') }}</td>
                                        <td class="center">{{ $item->buom }}</td>
                                        <td class="right">
                                            {{ $pqty * $item->muppp + $item->mqty }}
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                        $total_pqty += $pqty;
                                        $total_mqty += $mqty;
                                        $total_bqty += $bqty;
                                        $total_quantum += $pqty * $item->muppp + $item->mqty;
                                        $weight += $item->qty * $item->gross_weight;
                                        $volume += $item->qty * $item->volume;
                                    @endphp
                                @endforeach
                                <tr class="blod-text">
                                    <td colspan="5" class="right">Total : </td>
                                    <td class="center" colspan="2">{{ number_format($total_pqty, 0, ',', '.') }}
                                    </td>
                                    <td class="center" colspan="2">{{ number_format($total_mqty, 0, ',', '.') }}
                                    </td>
                                    <td class="center" colspan="2">{{ number_format($total_bqty, 0, ',', '.') }}
                                    </td>
                                    <td class="right" colspan="2"> {{ number_format($total_quantum, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th rowspan="3" class="center">No</th>
                                    <th rowspan="3" class="center">Product</th>
                                    <th rowspan="3" class="center">Description</th>
                                    <th colspan="4">Shipped Quantity</th>
                                </tr>
                                <tr>
                                    <th colspan="2">1st</th>
                                    <th rowspan="2" class="center">Remarks</th>
                                </tr>
                                <tr>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                    $pqty = 0;
                                    $mqty = 0;
                                    $bqty = 0;
                                    $total_pqty = 0;
                                    $total_mqty = 0;
                                    $total_bqty = 0;
                                    $total_quantum = 0;
                                    $weight = 0;
                                    $volume = 0;
                                @endphp
                                @foreach ($detail_list as $item)
                                    @php
                                        $pqty = ($item->qty - ($item->qty % $item->uppp)) / $item->uppp;
                                        $mqty =
                                            (($item->qty % $item->uppp) - (($item->qty % $item->uppp) % $item->muppp)) /
                                            $item->muppp;
                                        $bqty = ($item->qty % $item->uppp) % $item->muppp;
                                    @endphp
                                    <tr>
                                        <td class="center">{{ $i }}</td>
                                        <td class="center">{{ $item->product_code }}
                                            <input type="hidden" class="noprint" name="id[]"
                                                value="{{ $item->id }}" autocomplete="off">
                                        </td>
                                        <td class="center">
                                            <div class="center"
                                                style="width: 100%; word-wrap: break-word; white-space: initial; text-align: center; !important">
                                                {{ $item->product_name }}
                                            </div>
                                        </td>
                                        <td class="center">{{ number_format($pqty, 0, ',', '.') }}</td>
                                        <td class="center">{{ $item->puom }}</td>
                                        <td class="center">
                                            <div class="center"
                                                style="margin-top: 15px; width: 100%; word-wrap; break-word; white-space: initial; text-align: center; !important">
                                                {{ $item->remarks }}
                                            </div>
                                            @if (is_null($item->remarks))
                                                <input type="text" class="no-print" name="remarks[]"
                                                    placeholder="..." autocomplete="off" value="">
                                            @else
                                                <hr class="no-print">
                                                <input type="text" class="no-print" name="remarks[]"
                                                    placeholder="..." autocomplete="off"
                                                    value="{{ $item->remarks }}">
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                        $total_pqty += $pqty;
                                        $total_mqty += $mqty;
                                        $total_bqty += $bqty;
                                        $total_quantum += $item->pqty * $item->muppp + $item->mqty;
                                        $weight += $item->qty * $item->gross_weight;
                                        $volume += $item->qty * $item->volume;
                                    @endphp
                                @endforeach
                                <tr class="blod-text">
                                    <td colspan="3" class="right">Total : </td>
                                    <td class="center" colspan="3">{{ number_format($total_pqty, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
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
        <button type="submit" class="float no-print" onclick="addRemarks()">
            <i class="fas fa-save fa-lg my-float"></i>
            <span class="tooltiptext">Save Remarks</span>
        </button>
    </form>
</body>
<script type="text/javascript">
    function addRemarks() {
        $('#formDespatch').on('submit', function() {
            $('.float').attr('disabled', true);
        });
    }
</script>

</html>
