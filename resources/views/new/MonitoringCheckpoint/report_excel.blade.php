<table class="table">
    <thead>
        <tr class="text-center">
            <th style="vertical-align : left;text-align:left; border: solid black">No. Order</th>
            <th style="vertical-align : left;text-align:left; border: solid black">No. Mobil</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Jenis Armada</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Nama Driver</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Nama Customer</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Muat 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Muat 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Muat 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Bongkar 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Bongkar 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lokasi Bongkar 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Muat 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Muat 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Muat 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Muat 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Muat 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Muat 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Muat 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Muat 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Muat 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Bongkar 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Bongkar 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Bongkar 1</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Bongkar 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Bongkar 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Bongkar 2</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Lokasi Bongkar 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Keluar Lokasi Bongkar 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Lead Time Lokasi Bongkar 3</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tiba Di Garasi</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Revenue</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Cost</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Margin</th>
        </tr>
    </thead>
    <tbody id="tbody">
        @foreach ($data as $key => $value)
            <tr>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['no_order'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['no_mobil'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['jenis_armada'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['nama_driver'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['nama_customer'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_muat_1'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_muat_2'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_muat_3'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_bongkar_1'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_bongkar_2'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['lokasi_bongkar_3'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_muat_1'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_muat_1'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['leadtime_muat_1'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_muat_2'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_muat_2'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['leadtime_muat_2'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_muat_3'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_muat_3'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['leadtime_muat_3'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_bongkar_1'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_bongkar_1'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['leadtime_bongkar_1'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_bongkar_2'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_bongkar_2'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['leadtime_bongkar_2'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gatein_lokasi_bongkar_3'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['gateout_lokasi_bongkar_3'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $value['leadtime_bongkar_3'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['tiba_di_garasi'] }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['revenue'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['cost'] }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $value['margin'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
