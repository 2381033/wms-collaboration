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
                                        <th class="center" rowspan="2">No</th>
                                        <th class="center" rowspan="2">Date</th>
                                        <th class="center" rowspan="2">Inbound (Cbm)</th>
                                        <th class="center" rowspan="2">Outbound (Cbm)</th>
                                        <th class="center" rowspan="2">Stock (Cbm)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $stock = 0;
                                    @endphp
                                    @foreach ($list as $key => $item)
                                        @php
                                            $cbm_in[$key] = array_sum($item['in']->pluck('cbm_per_unit')->toArray()) ?? 0;
                                            $cbm_out[$key] = array_sum($item['out']->pluck('cbm_per_unit')->toArray()) ?? 0;
                                            $stock += $cbm_in[$key] - $cbm_out[$key];
                                        @endphp
                                        <tr>
                                            <td class="center">{{ $loop->iteration }}</td>
                                            <td class="center">{{ \Carbon\carbon::parse($item[$key])->format('d-m-Y') }}
                                            </td>
                                            <td class="center">{{ number_format($cbm_in[$key], 2, ',', '.') }}</td>
                                            <td class="center">{{ number_format($cbm_out[$key], 2, ',', '.') }}</td>
                                            <td class="center">{{ number_format($stock, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="center">End Of Report</td>
                                    </tr>
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
