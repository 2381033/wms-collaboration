@if ($reportType == 'detail')
    <table class="table">
        <thead>
            <tr>
                <th rowspan="10"><b>{{ $tittle }}<b></th>
            </tr>
            <tr>
                <th rowspan="2">Shipper</th>
                <th rowspan="2">Receiving Date</th>
                <th rowspan="2">PO Number</th>
                <th rowspan="2">PEB Number</th>
                <th rowspan="2">Destination</th>
                <th rowspan="2">QTY</th>
                <th rowspan="2">Pallet ID</th>
                <th rowspan="2">Vol. Total (Cbm)</th>
                {{-- <th  rowspan="2">Vol. Weight (Kg)</th> --}}
                <th rowspan="2">Total Pallet</th>
                <th rowspan="2">Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ $value['shipper_name'] }}</td>
                    <td>{{ $value['receiving'] }}</td>
                    <td>{{ $value['po_number'] }}</td>
                    <td>{{ $value['peb_no'] }}</td>
                    <td>{{ $value['destination'] }}</td>
                    <td>{{ $value['quantity'] }}</td>
                    <td>{{ $value['pallet_id'] }}</td>
                    <td>{{ $value['cbm'] }}</td>
                    {{-- <td >{{ $value['weight'] }}</td> --}}
                    <td>{{ $value['total_pallet'] }}</td>
                    <td>{{ $value['location_code'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <table class="table">
        <thead>
            <tr>
                <th rowspan="2"><b>{{ $tittle }}<b></th>
            </tr>
            <tr>
                <th rowspan="2">Shipper</th>
                <th rowspan="2">QTY</th>
                <th rowspan="2">Vol. Total (Cbm)</th>
                {{-- <th  rowspan="2">Vol. Weight (Kg)</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ $value['shipper_name'] }}</td>
                    <td>{{ $value['quantity'] }}</td>
                    <td>{{ $value['cbm'] }}</td>
                    {{-- <td >{{ $value['weight'] }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
