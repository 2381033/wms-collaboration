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
    <style>
        @media print {
            .noprint {
                visibility: hidden;
            }
        }
    </style>
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
                        <div class="title">Pallet ID</div>
                    </div>
                </div>
                <div class="row">
                    <div class="column col-100 center">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->qrcode, 'QRCODE') }}"
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
                                <td class="text-bold">{{ $principal }}</td>
                            </tr>
                            <tr>
                                <td>Job No</td>
                                <td class="text-bold">{{ $item->master_job->job_no }}</td>
                            </tr>
                            <tr>
                                <td>Job Date</td>
                                <td>{{ \Carbon\Carbon::parse($item->master_job->job_date)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td>SKU No</td>
                                <td class="text-bold">{{ $item->product_code }}</td>
                            </tr>
                            <tr>
                                <td>SKU Name</td>
                                <td class="text-bold">{{ $item->master_product->product_name }}</td>
                            </tr>
                            @if (!is_null($item->master_detail->mfg_date))
                                <tr>
                                    <td>Mfg Date</td>
                                    <td>{{ \Carbon\Carbon::parse($item->master_detail->mfg_date)->format('d-m-Y') }}
                                    </td>
                                </tr>
                            @endif
                            @if (!is_null($item->master_detail->exp_date))
                                <tr>
                                    <td>Expiry Date</td>
                                    <td>{{ \Carbon\Carbon::parse($item->master_detail->exp_date)->format('d-m-Y') }}
                                    </td>
                                </tr>
                            @endif
                            @if (!is_null($item->master_detail->lot_no))
                                <tr>
                                    <td>Batch No</td>
                                    <td class="text-bold">{{ $item->master_detail->lot_no }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Stock Status</td>
                                <td class="text-bold">{{ $item->master_detail->product_status }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="noprint" style="background-color: yellow; text-align: center">
                    <h2>Preview Only</h2>
                    <h4>Location: {{ $item->location_code }}</h4>
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
