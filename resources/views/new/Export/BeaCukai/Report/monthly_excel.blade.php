<table class="table">
    <thead>
        <tr class="text-center">
            <th style="vertical-align : left;text-align:left; border: solid black" colspan="2">PEB</th>
            <th style="vertical-align : left;text-align:left; border: solid black" colspan="2">NPE</th>
            <th style="vertical-align : left;text-align:left; border: solid black" colspan="2">PKBE</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Eksportir</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Forwarder</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Receiving Date</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Asal Barang</th>
            <th style="vertical-align : left;text-align:left; border: solid black" colspan="2">Jenis Barang</th>
            <th colspan="2" style="vertical-align : left;text-align:left; border: solid black">Kemasan
            </th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Valuta
            </th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Nilai
                Barang</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">No. Peti
                Kemas</th>
            <th rowspan="2" style="vertical-align : left;text-align:left; border: solid black">Negara
                Tujuan</th>
        </tr>
        <tr class="text-center">
            <th style="vertical-align : left;text-align:left; border: solid black">Nomor</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tanggal</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Nomor</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tanggal</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Nomor</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Tanggal</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Jumlah</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Satuan</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Jumlah</th>
            <th style="vertical-align : left;text-align:left; border: solid black">Satuan</th>
        </tr>
    </thead>
    <tbody id="tbody">
        @foreach ($data as $key => $v)
            <tr>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->peb_no }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ \Carbon\Carbon::parse($v->peb_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->npe_no }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ \Carbon\Carbon::parse($v->npe_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->pkbe_no }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ \Carbon\Carbon::parse($v->pkbe_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->eksportir }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->forwarder_name == null ? '-' : $v->forwarder_name  }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->receiving_date == null ? '-' : \Carbon\Carbon::parse($v->receiving_date)->format('d-m-Y')  }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->asal_barang == null ? '-' : $v->asal_barang  }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->jumlah_jenis_barang }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->satuan_jenis_barang }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->jumlah_kemasan }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->satuan_kemasan }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->valuta }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">{{ $v->nilai_barang }}</td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->no_peti_kemas }}
                </td>
                <td style="vertical-align : left;text-align:left; border: solid black">
                    {{ $v->negara_tujuan }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
