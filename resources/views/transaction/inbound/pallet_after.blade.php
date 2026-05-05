<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Report</title>

    <link rel="stylesheet" href="{{ asset('assets/css/pallet_one.css') }}">
</head>

<body>
    @foreach ($list_data as $item)
        {{-- @if ($item->master_product->product_name != $product_before || $item->master_detail->lot_no != $lot_before)
        @endif --}}
        <div class="page">
            <div class="container">
                <div class="row">
                    <div class="column col-30">
                        <img alt="image" src="{{ asset('images/logos.png') }}" alt="" height="50pt">
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <div class="title">Pallet ID</div>
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->master_detail->qrcode . '<' . $item->lot_no . '<' . $item->location_code ?? '-', 'QRCODE') }}"
                            alt="barcode" style="width: 200px;" />
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
                                <td>Principal Name</td>
                                <td class="text-bold">
                                    {{ DB::table('iv_principal')->where('id', $item->principal_id)->first()->principal_name }}
                                </td>
                            </tr>
                            <tr>
                                <td>Job No</td>
                                <td class="text-bold">{{ $item->job_no }}</td>
                            </tr>
                            <tr>
                                <td>Job Date</td>
                                <td>{{ \Carbon\Carbon::parse($item->job_date)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td>SKU No</td>
                                <td class="text-bold">{{ $item->product_code }}</td>
                            </tr>
                            <tr>
                                <td>SKU Name</td>
                                <td class="text-bold">{{ $item->product_name }}</td>
                            </tr>
                            @if ($item->mfg_date != null)
                                <tr>
                                    <td>Mfg Date</td>
                                    <td>{{ \Carbon\Carbon::parse($item->mfg_date)->format('d-m-Y') }}
                                    </td>
                                </tr>
                            @endif
                            @if ($item->exp_date != null)
                                <tr>
                                    <td>Expiry Date</td>
                                    <td>{{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-Y') }}
                                    </td>
                                </tr>
                            @endif
                            @if ($item->lot_no != null)
                                <tr>
                                    <td>Batch No</td>
                                    <td class="text-bold">{{ $item->lot_no }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Stock Status</td>
                                <td class="text-bold">{{ $item->product_status }}</td>
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
</body>

</html>
