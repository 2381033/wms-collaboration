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
                                {{ $tittle }}
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
                                                <td>Customer</td>
                                                <td>:</td>
                                                <td>{{ $customer }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Job No</td>
                                                <td>:</td>
                                                <td>{{ $data->first()->job_no }}
                                            </tr>
                                            <tr>
                                                <td>Warehouse</td>
                                                <td>:</td>
                                                <td>{{ $warehouse }}
                                            </tr>
                                            <tr>
                                                <td>Date IN</td>
                                                <td>:</td>
                                                <td>{{ formatTanggalIndonesia2($data->first()->created_at) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cargo ID</th>
                                        <th>Description</th>
                                        <th>Pallet</th>
                                        {{-- <th>SKU</th> --}}
                                        <th>Qty</th>
                                        <th>Location</th>
                                        <th>Vol. (Cbm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupBy as $key => $value)
                                        @foreach ($value as $_key => $v)
                                            <tr>
                                                @if ($_key == 0)
                                                    <td class="center" rowspan="{{ $value->count() }}">
                                                        {{ $value[0]->id_cargo }}</td>
                                                    <td class="center" rowspan="{{ $value->count() }}">
                                                        {{ $value[0]->description }}
                                                    </td>
                                                @endif
                                                <td class="center">{{ $v->pallet_ke }}</td>
                                                {{-- <td class="center">{{ $v->sku }}</td> --}}
                                                <td class="center">{{ $v->qty_pallet . ' ' . $v->unit }}</td>
                                                <td class="center">{{ $v->location_code }}</td>
                                                <td class="center">
                                                    {{ number_format(($v->p * $v->l * $v->t * $v->qty_pallet) / 1000000, 2, '.', '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="center">End Of Report</td>
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
        document.title = "{{ $tittle }}"
        // window.print()
    </script>
</body>

</html>
