<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>
<style type="text/css">
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }
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
                            @if ($report_type == 'detail')
                                <table class="table">
                                    <thead>
                                        <tr class="center">
                                            <th rowspan="2">Warehouse</th>
                                            <th rowspan="2">Job No</th>
                                            <th rowspan="2">Customer</th>
                                            <th rowspan="2">Description</th>
                                            <th rowspan="2">Cargo ID</th>
                                            <th rowspan="2">Date IN</th>
                                            <th colspan="3">Dimesions</th>
                                            <th rowspan="2">Weight Per Unit (Kg)</th>
                                            <th rowspan="2">Vol. Per Unit (Cbm)</th>
                                            <th rowspan="2" style="background-color: antiquewhite">SOH</th>
                                            <th rowspan="2" style="background-color: antiquewhite">SOB</th>
                                            <th rowspan="2" style="background-color: antiquewhite">SOA</th>
                                            <th rowspan="2" style="background-color: antiquewhite">UOM</th>
                                            <th rowspan="2" style="background-color: antiquewhite">Weight Total (Kg)
                                            </th>
                                            <th rowspan="2" style="background-color: antiquewhite">Vol. Total (Cbm)
                                            </th>
                                        </tr>
                                        <tr class="center">
                                            <th>P</th>
                                            <th>L</th>
                                            <th>T</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->where('on_hand', '>', 0) as $item)
                                            <tr class="center">
                                                <td>{{ $item->warehouse }}</td>
                                                <td>{{ $item->job_no }}</td>
                                                <td>{{ $item->customer }}</td>
                                                <td>{{ $item->inbound_remark == null ? '-' : $item->inbound_remark }}
                                                </td>
                                                <td>{{ $item->id_cargo }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                                </td>
                                                <td>{{ $item->p }}</td>
                                                <td>{{ $item->l }}</td>
                                                <td>{{ $item->t }}</td>
                                                <td>{{ $item->w }}</td>
                                                <td>{{ number_format($item->cbm_per_unit, 3, '.', '') }}</td>
                                                <td>{{ $item->on_hand }}</td>
                                                <td>{{ $item->on_booking }}</td>
                                                <td>{{ $item->on_actual }}</td>
                                                <td>{{ $item->unit }}</td>
                                                <td>{{ number_format($item->on_hand * $item->w, 0, '.', '') }}</td>
                                                <td>{{ number_format($item->on_hand * $item->cbm_per_unit, 2, '.', '') }}
                                                </td>
                                            </tr>
                                            @php
                                                $weight_total[] = $item->on_hand * $item->w;
                                                $cbm_total[] = $item->on_hand * $item->cbm_per_unit;
                                            @endphp
                                        @endforeach
                                        <tr style="border: 1px; solid; background-color: bisque" class="center">
                                            <th colspan="11" class="center">SUMMARY</th>
                                            <th>{{ array_sum($data->pluck('on_hand')->toArray()) }}
                                            <th>{{ array_sum($data->pluck('on_booking')->toArray()) }}
                                            <th>{{ array_sum($data->pluck('on_actual')->toArray()) }}</th>
                                            <th></th>
                                            <th>{{ number_format(array_sum($weight_total), 0, '.', '') }}</th>
                                            <th>{{ number_format(array_sum($cbm_total), 2, '.', '') }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center" rowspan="2">Warehouse</th>
                                            <th class="center" rowspan="2">Customer</th>
                                            <th class="center" rowspan="2">SOH</th>
                                            <th class="center" rowspan="2">SOB</th>
                                            <th class="center" rowspan="2">SOA</th>
                                            <th class="center" rowspan="2">Weight Total (Kg)</th>
                                            <th class="center" rowspan="2">Vol. Total (Cbm)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($summary as $key => $value)
                                            <tr>
                                                <td class="center">{{ $value->warehouse ?? '-' }}</td>
                                                <td class="center">{{ $value->customer ?? '-' }}</td>
                                                <td class="center">{{ $value->on_hand ?? '-' }}</td>
                                                <td class="center">{{ $value->on_booking ?? '-' }}</td>
                                                <td class="center">{{ $value->on_actual ?? '-' }}</td>
                                                <td class="center">{{ number_format($value->w_sum, 0, '.', '') }}</td>
                                                <td class="center">{{ number_format($value->total_cbm, 2, '.', '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        document.title = "{{ $tittle }}"
        // window.print()
    </script>
</body>

</html>
