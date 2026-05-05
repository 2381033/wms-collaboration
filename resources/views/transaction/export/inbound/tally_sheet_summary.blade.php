<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
</head>

<style>
    .grid {
        display: grid;
        width: 114rem;
        grid-gap: 3rem;
        grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
    }

    @media (max-width: 60em) {
        .grid {
            grid-gap: 3rem;
        }
    }

    .grid .card {
        display: flex;
        flex-direction: column;
        background-color: #fff;
        border-radius: 0.4rem;
        overflow: hidden;
        box-shadow: 0 3rem 6rem rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: 0.2s;
    }
</style>

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
                                TALLY SHEET
                                <p>RECEIVING EXPORT (DETAIL)</p>
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
                                                <td>SHIPPER</td>
                                                <td>:</td>
                                                <td>{{ $header->shipper_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL QTY</td>
                                                <td>:</td>
                                                <td>{{ $detail->sum('quantity') . ' ' . $detail[0]->unit }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VOLUME (ACTUAL)</td>
                                                <td>:</td>
                                                <td>{{ $cbm_total . ' CBM' }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VGM </td>
                                                <td>:</td>
                                                <td>{{ $vgm_total == 0 ? '-' : $vgm_total . ' KG' }}</td>
                                            </tr>
                                            <tr>
                                                <td>DESTINATION</td>
                                                <td>:</td>
                                                <td>{{ $header->destination }}</td>
                                            </tr>
                                            <tr>
                                                <td>FINAL DESTINATION</td>
                                                <td>:</td>
                                                <td>
                                                    {{ Str::Upper($header->final_destination) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header" style="margin-left: 2%">
                                            <tr>
                                                <td>PRINCIPAL</td>
                                                <td>:</td>
                                                <td>{{ Str::Upper($header->forwarder_name) }}</td>
                                            </tr>
                                            <tr>
                                                <td>TRUCK NO</td>
                                                <td>:</td>
                                                <td>{{ $header->vehicle_no }}
                                                    ({{ DB::table('ex_gate_in_cargo')->where('vehicle_number', $header->vehicle_no)->value('vehicle_type') }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>DATE IN</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->created_at)->format('d-m-Y H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UNLOADING START</td>
                                                <td>:</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($detail->first()->created_at)->format('d-m-Y H:i') ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UNLOADING FINISH</td>
                                                <td>:</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($header->checker_confirmed_flag)->format('d-m-Y H:i') ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BUYER</td>
                                                <td>:</td>
                                                <td>
                                                    {{ Str::Upper($header->consignee_name) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th rowspan="3">PEB NO.</th>
                                        <th rowspan="3">MARKING & PO</th>
                                        <th rowspan="3">PKGS</th>
                                        <th rowspan="3">PALLET ID</th>
                                        <th rowspan="3">QTY CARGO</th>
                                        <th colspan="5">MEASUREMENT</th>
                                    </tr>
                                    <tr>
                                        <th>L</th>
                                        <th>W</th>
                                        <th>H</th>
                                        <th>VGM</th>
                                        <th>CBM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail->groupBy('pallet_id') as $palletId => $items)
                                        @foreach ($items->groupBy(function ($item) {
        return explode('-', $item->serial_no)[0];
    }) as $poNumber => $groupedByPo)
                                            @php
                                                $pebNo = $header->peb_no == 0 ? '-' : $header->peb_no;
                                                $rowspan = $groupedByPo->count(); // Hitung jumlah baris untuk peb_no
                                            @endphp

                                            @foreach ($groupedByPo as $_key => $val_)
                                                <tr>
                                                    @if ($_key == 0)
                                                        <!-- Merowspan peb_no jika ada nilai yang sama -->
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ $pebNo }}
                                                        </td>
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ strtoupper($poNumber) }}
                                                        </td>
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ $groupedByPo[0]->unit }}
                                                        </td>
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ $groupedByPo[0]->pallet_id }}
                                                        </td>
                                                    @endif
                                                    <td class="center">{{ $val_->quantity }}</td>
                                                    <td class="center">{{ $val_->length }}</td>
                                                    <td class="center">{{ $val_->width }}</td>
                                                    <td class="center">{{ $val_->height }}</td>
                                                    <td class="center">{{ $val_->weight == 0 ? '-' : $val_->weight }}
                                                    </td>
                                                    <td class="center">
                                                        {{ number_format((($val_->length * $val_->width * $val_->height) / 1000000) * $val_->quantity, 3, '.', '') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="right" colspan="4">TOTAL</th>
                                        <th class="center">{{ $qtyTotal }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="center">{{ $vgm_total }}
                                        <th class="center">{{ number_format($cbm_total, 3, '.', '') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="card" style="border-style: solid; border-width: thin; margin-top: 20px; ">
                                <h5 style="text-align: center">PHOTO CARGO RECEIVING</h5>
                                @foreach ($foto_cargo as $val)
                                    <img src="{{ asset('public/foto/warehouse-export/inbound-cargo/' . $val->file) }}"
                                        style="width: 150px; border-radius: 20px; padding: 10px; justify-content: center; align-items: center; text-align: center;">
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <table class="table" style="margin-top: 5px;">
                        <tr>
                            <td class="center">REMARKS</td>
                            <td class="center">DRIVER NAME</td>
                            <td class="center">CHECKER NAME</td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="text-align: center"><b>{{ Str::Upper($header->remarks) }}</b>
                            </td>
                            <td class="center">
                                {{-- <img src="{{ asset('/public/foto/warehouse-export/signature/' . $header->ttd_driver) }}"
                                    style="width: 150px; border-radius: 20px; padding: 10px; justify-content: center; align-self: center"> --}}
                            </td>
                            <td class="center">
                                {{-- <img src="{{ asset('/public/foto/warehouse-export/signature/' . $header->ttd_checker) }}"
                                    style="width: 150px; border-radius: 20px; padding: 10px; justify-content: center; align-self: center"> --}}
                            </td>
                        </tr>
                        <tr>
                            <td class="center">{{ $driver->driver_name }}</td>
                            <td class="center">{{ Str::Upper($header->pic_name) }}</td>
                        </tr>
                    </table>
                </tr>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
</body>

</html>
