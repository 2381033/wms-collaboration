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
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="center" rowspan="2">ID Cargo</th>
                                        <th class="center" rowspan="2">Job Date</th>
                                        <th class="center" rowspan="2">Description</th>
                                        <th class="center" rowspan="2">Type Job</th>
                                        <th class="center" rowspan="2">Qty</th>
                                        <th class="center" rowspan="2">Balance</th>
                                        <th class="center" rowspan="2">Uom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach ($grouping as $key => $value)
                                        @foreach ($value as $_key => $v)
                                            <tr>
                                                @php
                                                    if ($_key == 0) {
                                                        $total = $v->qty;
                                                    } else {
                                                        if ($v->type_job == 'in') {
                                                            $total += $v->qty;
                                                        } else {
                                                            $total -= $v->qty;
                                                        }
                                                    }
                                                @endphp
                                                @if ($_key == 0)
                                                    <td class="center" rowspan="{{ $value->count() }}">
                                                        {{ $value[0]->id_cargo }}</td>
                                                @endif
                                                <td class="center">
                                                    {{ \Carbon\Carbon::parse($v->created_at)->format('d-m-Y') }}</td>
                                                <td class="center">{{ $v->description == null ? '-' : $v->description }}
                                                </td>
                                                <td class="center">{{ $v->type_job == 'in' ? 'Inbound' : 'Outbound' }}
                                                </td>
                                                <td class="center">{{ $v->qty }}</td>
                                                <td class="center">{{ ABS($total) }}</td>
                                                <td class="center">{{ $v->unit }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    {{-- <tr>
                                        <td colspan="5">End Of Report</td>
                                    </tr> --}}
                                </tbody>
                            </table>
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
