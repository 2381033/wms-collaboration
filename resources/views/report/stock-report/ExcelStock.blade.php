<table>
    <thead>
        @if ($report_type == 'summary')
            @if ($principal->multi_level == 'Yes')
                <tr>
                    <th style="border: 3px solid;">SKU No</th>
                    <th style="border: 3px solid;">SKU Name</th>
                    <th style="border: 3px solid;">Conversi Qty</th>
                    <th style="border: 3px solid;">1st SOH</th>
                    <th style="border: 3px solid;">2nd SOH</th>
                    <th style="border: 3px solid;">Quantum</th>
                    <th style="border: 3px solid;">1st SOB</th>
                    <th style="border: 3px solid;">2nd SOB</th>
                    <th style="border: 3px solid;">3rd SOB</th>
                    <th style="border: 3px solid;">Quantum</th>
                    <th style="border: 3px solid;">1st SOA</th>
                    <th style="border: 3px solid;">2nd SOA</th>
                    <th style="border: 3px solid;">Quantum</th>
                    <th style="border: 3px solid;">1st Unit</th>
                    <th style="border: 3px solid;">2nd Unit</th>
                    <th style="border: 3px solid;"> Quantum</th>
                </tr>
            @else
                <tr>
                    <th style="border: 3px solid;">SKU No</th>
                    <th style="border: 3px solid;">SKU Name</th>
                    <th style="border: 3px solid;">SOH</th>
                    <th style="border: 3px solid;">SOB</th>
                    <th style="border: 3px solid;">SOA</th>
                    <th style="border: 3px solid;">Unit</th>
                </tr>
            @endif
        @else
            @if ($principal->multi_level == 'Yes')
                <tr>
                    <th>Job No</th>
                    <th>Container No</th>
                    <th>Job Date</th>
                    <th>SKU No</th>
                    <th>SKU Name</th>
                    <th>Conversi Qty</th>
                    <th>Batch No</th>
                    <th>Mfg Date</th>
                    <th>Exp Date</th>
                    <th>Site Name</th>
                    <th>Area Name</th>
                    <th>Location</th>
                    <th>1st SOH</th>
                    <th>2nd SOH</th>
                    <th>Quantum</th>
                    <th>1st SOB</th>
                    <th>2nd SOB</th>
                    <th>Quantum</th>
                    <th>1st SOA</th>
                    <th>2nd SOA</th>
                    <th>Quantum</th>
                    <th>1st Unit</th>
                    <th>2nd Unit</th>
                    <th>Quantum</th>
                    <th>Freeze</th>
                    <th>Gross Weight</th>
                    <th>Volume</th>
                    <th>Status</th>
                </tr>
            @else
                <tr>
                    <th>Job No</th>
                    <th>Job Date</th>
                    <th>SKU No</th>
                    <th>SKU Name</th>
                    <th>Batch No</th>
                    <th>Mfg Date</th>
                    <th>Exp Date</th>
                    <th>Site Name</th>
                    <th>Area Name</th>
                    <th>Location</th>
                    <th>SOH</th>
                    <th>SOB</th>
                    <th>SOA</th>
                    <th>Unit</th>
                    <th>Freeze</th>
                    <th>Gross Weight</th>
                    <th>Volume</th>
                    <th>Status</th>
                    <th>CARTON ID</th>
                </tr>
            @endif
        @endif
    </thead>
    <tbody>
        @if ($report_type == 'summary')
            @if ($principal->multi_level == 'Yes')
                @foreach ($list as $key => $value)
                    <tr>
                        <td>{{ $value['product_code'] }}</td>
                        <td>{{ $value['product_name'] }}</td>
                        <td>{{ $value['conversi'] }}</td>
                        <td>{{ $value['pqtys'] }}</td>
                        <td>{{ $value['mqtys'] }}</td>
                        <td>{{ $value['quantum_soh'] }}</td>
                        <td>{{ $value['pqtyp'] }}</td>
                        <td>{{ $value['mqtyp'] }}</td>
                        <td>{{ $value['quantum_sob'] }}</td>
                        <td>{{ $value['pqtya'] }}</td>
                        <td>{{ $value['mqtya'] }}</td>
                        <td>{{ $value['quantum_soa'] }}</td>
                        <td>{{ $value['puom'] }}</td>
                        <td>{{ $value['muom'] }}</td>
                        <td>{{ $value['buom'] }}</td>
                        <td>{{ $value['quantum_unit'] }}</td>
                    </tr>
                @endforeach
            @else
                @foreach ($list as $key => $value)
                    <tr>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['product_code'] }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['product_name'] }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['pqtys'] }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['pqtyp'] }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['pqtya'] }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $value['puom'] }}</td>
                    </tr>
                @endforeach
                @foreach ($stockKosong as $item)
                    <tr>
                        <td style="border: 2px solid; text-align: left; ">{{ $item->product_code }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $item->product_name }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ '0' }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ '0' }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ '0' }}</td>
                        <td style="border: 2px solid; text-align: left; ">{{ $item->puom }}</td>
                    </tr>
                @endforeach
            @endif
        @else
            @if ($principal->multi_level == 'Yes')
                @foreach ($list as $value)
                    <tr>
                        <td>{{ $value['job_no'] }}</td>
                        <td>{{ $value['container_no'] }}</td>
                        <td>{{ $value['job_date'] }}</td>
                        <td>{{ $value['product_code'] }}</td>
                        <td>{{ $value['product_name'] }}</td>
                        <td>{{ $value['conversi'] }}</td>
                        <td>{{ $value['lot_no'] }}</td>
                        <td>{{ $value['mfg_date'] }}</td>
                        <td>{{ $value['exp_date'] }}</td>
                        <td>{{ $value['site_name'] }}</td>
                        <td>{{ $value['area_name'] }}</td>
                        <td>{{ $value['location_code'] }}</td>
                        <td>{{ $value['pqtys'] }}</td>
                        <td>{{ $value['mqtys'] }}</td>
                        <td>{{ $value['quantum_soh'] }}</td>
                        <td>{{ $value['pqtyp'] }}</td>
                        <td>{{ $value['mqtyp'] }}</td>
                        <td>{{ $value['quantum_sob'] }}</td>
                        <td>{{ $value['pqtya'] }}</td>
                        <td>{{ $value['mqtya'] }}</td>
                        <td>{{ $value['quantum_soa'] }}</td>
                        <td>{{ $value['puom'] }}</td>
                        <td>{{ $value['muom'] }}</td>
                        <td>{{ $value['quantum_unit'] }}</td>
                        <td>{{ $value['freeze_flag'] }}</td>
                        <td>{{ $value['gross_weight'] }}</td>
                        <td>{{ $value['volume'] }}</td>
                        <td>{{ $value['status_code'] }}</td>
                    </tr>
                @endforeach
            @else
                @foreach ($list as $value)
                    <tr>
                        <td>{{ $value['job_no'] }}</td>
                        <td>{{ $value['job_date'] }}</td>
                        <td>{{ $value['product_code'] }}</td>
                        <td>{{ $value['product_name'] }}</td>
                        <td>{{ $value['lot_no'] }}</td>
                        <td>{{ $value['mfg_date'] }}</td>
                        <td>{{ $value['exp_date'] }}</td>
                        <td>{{ $value['site_name'] }}</td>
                        <td>{{ $value['area_name'] }}</td>
                        <td>{{ $value['location_code'] }}</td>
                        <td>{{ $value['pqtys'] }}</td>
                        <td>{{ $value['pqtyp'] }}</td>
                        <td>{{ $value['pqtya'] }}</td>
                        <td>{{ $value['puom'] }}</td>
                        <td>{{ $value['freeze_flag'] }}</td>
                        <td>{{ $value['gross_weight'] }}</td>
                        <td>{{ $value['volume'] }}</td>
                        <td>{{ $value['status_code'] }}</td>
                        <td>{{ $value['ean_code'] }}</td>
                    </tr>
                @endforeach
            @endif
        @endif
    </tbody>
    <thead>
</table>
