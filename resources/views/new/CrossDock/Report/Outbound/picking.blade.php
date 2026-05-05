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
                                                <td>Job No</td>
                                                <td>:</td>
                                                <td>{{ $data->first()->job_no }}
                                            </tr>
                                            <tr>
                                                <td>Warehouse</td>
                                                <td>:</td>
                                                <td>{{ $warehouse->where('id', $data->first()->id_warehouse)->first()->name }}
                                            </tr>
                                            <tr>
                                                <td>Picking Time</td>
                                                <td>:</td>
                                                <td>{{ $data->first()->picking_at ? formatTanggalWaktuIndonesia2($data->first()->picking_at) : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="center">No</th>
                                        <th class="center">Description</th>
                                        <th class="center">Cargo ID</th>
                                        <th class="center">SKU</th>
                                        <th class="center">QTY</th>
                                        <th class="center">Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->where('picking_flag', 'Yes') as $item)
                                        <tr>
                                            <td class="center">{{ $loop->iteration }}</td>
                                            <td class="center">{{ $item->stock->description }}</td>
                                            <td class="center">{{ $item->stock->id_cargo }}</td>
                                            <td class="center">{{ $item->stock->sku }}</td>
                                            <td class="center">{{ $item->qty }}</td>
                                            <td class="center">{{ $item->stock->location_code }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="center">End Of Report</td>
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
