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
                                            {{-- <tr>
                                                <td>Job No</td>
                                                <td>:</td>
                                                <td>{{ $data->first()->job_no }}
                                            </tr> --}}
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
                                        <th>Qty</th>
                                        <th>Location</th>
                                        <th>Vol. (Cbm)</th>
                                        <th>Weight (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $v)
                                        <tr>
                                            <td class="center">{{ $v->id_cargo }}</td>
                                            <td class="center">{{ $v->description }}</td>
                                            <td class="center">{{ $v->qty . ' ' . $v->unit }}</td>
                                            <td class="center">{{ $v->batch->location_code }}</td>
                                            <td class="center">
                                                {{ number_format(($v->p * $v->l * $v->t * $v->qty) / 1000000, 2, '.', '') }}
                                            </td>
                                            <td class="center">{{ $v->w }}</td>
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
