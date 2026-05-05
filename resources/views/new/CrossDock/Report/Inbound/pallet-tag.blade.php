<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>

    <link rel="stylesheet" href="{{ asset('assets/css/pallet_one.css') }}">
</head>

<body>
    @foreach ($data as $item)
        <div class="page">
            <div class="container">
                <div class="row">
                    <div class="column col-30">
                        <img alt="image" src="{{ asset('images/logos.png') }}" alt="" height="50pt">
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <div class="title">Pallet Tag</div>
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->qrcode, 'QRCODE') }}"
                            alt="barcode" style="width: 200px;" />
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center text-bold">
                        {{ $item->sku }}
                    </div>
                </div>
                <div class="row">
                    <div class="column col-break center">

                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <table class="table">
                            <tr>
                                <td>Customer Name</td>
                                <td class="text-bold">
                                    {{ $customer }}
                                </td>
                            </tr>
                            <tr>
                                <td>Job No</td>
                                <td class="text-bold">{{ $item->job_no }}</td>
                            </tr>
                            <tr>
                                <td>Date IN</td>
                                <td>{{ formatTanggalIndonesia2($item->created_at) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>SKU</td>
                                <td class="text-bold">{{ $item->sku }}</td>
                            </tr> --}}
                            <tr>
                                <td>QTY</td>
                                <td class="text-bold">{{ $item->qty_pallet }}&nbsp; of
                                    &nbsp;{{ $item->qty }}</td>
                            </tr>
                            <tr>
                                <td>UOM</td>
                                <td class="text-bold">{{ $item->unit }}</td>
                            </tr>
                            <tr>
                                <td>ID Cargo</td>
                                <td class="text-bold">{{ $item->id_cargo }}</td>
                            </tr>
                            <tr>
                                <td>Pallet</td>
                                <td class="text-bold">{{ $item->pallet_ke }}&nbsp; of
                                    &nbsp;{{ $data->where('id_cargo', $item->id_cargo)->count() }}</td>
                            </tr>
                            <tr>
                                <td>Warehouse</td>
                                <td class="text-bold">
                                    {{ $warehouse }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="column col-break center">
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        Page {{ $loop->iteration }} / {{ $loop->count }} </div>
                </div>
            </div>
        </div>
    @endforeach
    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
