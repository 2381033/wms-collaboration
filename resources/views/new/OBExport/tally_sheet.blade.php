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
                                <p>CARGO REDELIVEERY EXPORT (DETAIL)</p>
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
                                                <td>{{ $data['header']->shipper_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL QTY</td>
                                                <td>:</td>
                                                <td>{{ $data['detail']->sum('quantity') }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VOLUME (ACTUAL)</td>
                                                <td>:</td>
                                                <td>{{ $data['cbm_total'] . ' CBM' }}</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL VGM </td>
                                                <td>:</td>
                                                <td>{{ $data['vgm_total'] == 0 ? '-' : $data['vgm_total'] . ' KG' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>DESTINATION</td>
                                                <td>:</td>
                                                <td>{{ $data['header']->destination }}</td>
                                            </tr>
                                            <tr>
                                                <td>PIC NAME</td>
                                                <td>:</td>
                                                <td>
                                                    {{ Str::Upper($data['header']->pic_name) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table-header" style="margin-left: 2%">
                                            <tr>
                                                <td>PRINCIPAL</td>
                                                <td>:</td>
                                                <td>{{ Str::Upper($data['header']->forwarder_name) }}</td>
                                            </tr>
                                            <tr>
                                                <td>TRUCK NO</td>
                                                <td>:</td>
                                                <td>{{ $data['header']->vehicle_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>DATE IN</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($data['header']->created_at)->format('d-m-Y H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UNLOADING START</td>
                                                <td>:</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data['detail']->first()->created_at)->format('d-m-Y H:i') ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UNLOADING FINISH</td>
                                                <td>:</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data['header']->updated_at)->format('d-m-Y H:i') ?? '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BUYER</td>
                                                <td>:</td>
                                                <td>
                                                    {{ Str::Upper($data['header']->consignee_name) }}
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
                                        <th rowspan="3">PALLET ID</th>
                                        <th rowspan="3">MARKING & PO</th>
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
                                    @foreach ($data['detail']->groupBy('pallet_id') as $palletId => $items)
                                        @foreach ($items->groupBy(function ($item) {
        return $item->serial_no_formatted;
    }) as $poNumber => $groupedByPo)
                                            @php
                                                $pebNo = $data['header']->peb_no == 0 ? '-' : $data['header']->peb_no;
                                                $rowspan = $groupedByPo->count();
                                            @endphp
                                            @foreach ($groupedByPo as $_key => $val_)
                                                <tr>
                                                    @if ($_key == 0)
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ $pebNo }}</td>
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ $groupedByPo[0]->pallet_id }}</td>
                                                        <td class="center" rowspan="{{ $rowspan }}">
                                                            {{ strtoupper($poNumber) }}</td>
                                                    @endif
                                                    <td class="center">{{ $val_->quantity }}</td>
                                                    <td class="center">{{ '-' }}</td>
                                                    <td class="center">{{ '-' }}</td>
                                                    <td class="center">{{ '-' }}</td>
                                                    <td class="center">{{ $val_->weight == 0 ? '-' : $val_->weight }}
                                                    </td>
                                                    <td class="center">
                                                        {{ number_format($val_->cbm, 3, '.', '') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="right" colspan="4">TOTAL</th>
                                        <th class="center">{{ $data['qtyTotal'] }}</th>
                                        <th></th>
                                        <th></th>
                                        <th class="center">{{ $data['vgm_total'] }}
                                        <th class="center">{{ number_format($data['cbm_total'], 3, '.', '') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="card" style="border-style: solid; border-width: thin; margin-top: 20px; ">
                                <h5 style="text-align: center">PHOTO CARGO RECEIVING</h5>
                                @foreach ($data['foto_cargo'] as $val)
                                    <img src="{{ asset('foto/warehouse-export/ob-cargo/' . $val->file) }}"
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
                            <td class="center">PIC NAME</td>
                            <td class="center">CHECKER NAME</td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="text-align: center">
                                <b>{{ Str::Upper($data['header']->remarks) }}</b>
                            </td>
                            <td class="center">
                            </td>
                            <td class="center">
                            </td>
                        </tr>
                        <tr>
                            <td class="center">{{ Str::Upper($data['header']->pic_name) }}</td>
                            <td class="center">{{ Str::Upper($data['header']->checker_name) }}</td>
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
