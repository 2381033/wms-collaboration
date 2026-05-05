<div class="content">
    <table class="table-header-kerangka">
        <tr>
            <td>
                <table class="table-header">
                    <tr>
                        <td>Principal Name</td>
                        <td>:</td>
                        <td>{{ $getName }}</td>
                    </tr>
                    <tr>
                        <td>Job Number</td>
                        <td>:</td>
                        <td>{{ $header->job_no }}</td>
                    </tr>
                    <tr>
                        <td>Job Date</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($header->job_date)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>Tujuan</td>
                        <td>:</td>
                        <td>{{ Str::upper($despatch->tujuan) }}</td>
                    </tr>
                </table>
                <table class="table-header">
                    <tr>
                        <td>Vehicle Type</td>
                        <td>:</td>
                        <td>{{ Str::upper($despatch->size) }}</td>
                    </tr>
                    <tr>
                        <td>Vehicle No</td>
                        <td>:</td>
                        <td>{{ Str::upper($despatch->vehicle_no) }}</td>
                    </tr>
                    <tr>
                        <td>Container No</td>
                        <td>:</td>
                        <td>{{ $despatch->container_no ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Job Confirmed</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($header->confirmed_date)->format('d-m-Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>NO.</th>
                <th>LOCATION</th>
                <th>SKU</th>
                <th>BATCH NO</th>
                <th>STOCK</th>
                <th>PICKINGAN</th>
                <th>SISA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $val)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td> {{ $val['location_code'] }}</td>
                    <td> {{ $val['product_code'] }}</td>
                    <td> {{ $val['lot_no'] }}</td>
                    <td>{{ $val['stockAwal'] }}</td>
                    <td>{{ $val['yangDiAmbil'] }}</td>
                    <td>{{ $val['stockAkhir'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
