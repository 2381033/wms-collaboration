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
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="center">Cargo ID</th>
                                        <th class="center">DESCRIPTION</th>
                                        <th class="center">QTY</th>
                                        <th class="center">SCAN TIME</th>
                                        <th class="center">SCAN BY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupBy as $key => $value)
                                        @foreach ($value as $_key => $v)
                                            <tr class="center">
                                                @if ($_key == 0)
                                                    <td class="center" rowspan="{{ $value->count() }}">
                                                        {{ $v->id_cargo }}
                                                    </td>
                                                @endif
                                                <td class="center">{{ $v->stock_description }}</td>
                                                <td class="center">{{ $v->qty . ' ' . $v->stock_unit }}</td>
                                                <td class="center">
                                                    {{ $v->scan_at == null ? '-' : formatTanggalWaktuIndonesia2($v->scan_at) }}
                                                </td>
                                                <td class="center">{{ $v->scan_by }}</td>
                                            </tr>
                                        @endforeach
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
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        document.title = "{{ $tittle }}"
        // window.print()
    </script>
</body>

</html>
