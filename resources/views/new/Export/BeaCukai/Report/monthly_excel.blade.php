<table class="table">
    <thead>
        <tr class="text-center">
            <th style="vertical-align : left;text-align:left;" colspan="2">PEB</th>
            <th style="vertical-align : left;text-align:left;" colspan="2">NPE</th>
            <th style="vertical-align : left;text-align:left;" colspan="2">PKBE</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Eksportir</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Forwarder</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Receiving Date</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Asal Barang</th>
            <th style="vertical-align : left;text-align:left;" colspan="2">Jenis Barang</th>
            <th colspan="2" style="vertical-align : left;text-align:left;">Kemasan
            </th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Valuta
            </th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Nilai
                Barang</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">No. Peti
                Kemas</th>
            <th rowspan="2" style="vertical-align : left;text-align:left;">Negara
                Tujuan</th>
        </tr>
        <tr class="text-center">
            <th style="vertical-align : left;text-align:left;">Nomor</th>
            <th style="vertical-align : left;text-align:left;">Tanggal</th>
            <th style="vertical-align : left;text-align:left;">Nomor</th>
            <th style="vertical-align : left;text-align:left;">Tanggal</th>
            <th style="vertical-align : left;text-align:left;">Nomor</th>
            <th style="vertical-align : left;text-align:left;">Tanggal</th>
            <th style="vertical-align : left;text-align:left;">Jumlah</th>
            <th style="vertical-align : left;text-align:left;">Satuan</th>
            <th style="vertical-align : left;text-align:left;">Jumlah</th>
            <th style="vertical-align : left;text-align:left;">Satuan</th>
        </tr>
    </thead>
    <tbody id="tbody">
        @foreach ($data as $key => $v)
            <tr>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->peb_no }}</td>
                <td style="vertical-align : left;text-align:left;">
                    {{ \Carbon\Carbon::parse($v->peb_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->npe_no }}
                </td>
                <td style="vertical-align : left;text-align:left;">
                    {{ \Carbon\Carbon::parse($v->npe_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->pkbe_no }}</td>
                <td style="vertical-align : left;text-align:left;">
                    {{ \Carbon\Carbon::parse($v->pkbe_date)->format('d-m-Y') }}
                </td>
                <td style="vertical-align : left;text-align:left;">{{ $v->eksportir }}</td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->forwarder_name == null ? '-' : $v->forwarder_name  }}
                </td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->receiving_date == null ? '-' : \Carbon\Carbon::parse($v->receiving_date)->format('d-m-Y')  }}
                </td>
                <td style="vertical-align : left;text-align:left;">{{ $v->asal_barang == null ? '-' : $v->asal_barang  }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->jumlah_jenis_barang }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->satuan_jenis_barang }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->jumlah_kemasan }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->satuan_kemasan }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->valuta }}</td>
                <td style="vertical-align : left;text-align:left;">{{ $v->nilai_barang }}</td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->no_peti_kemas }}
                </td>
                <td style="vertical-align : left;text-align:left;">
                    {{ $v->negara_tujuan }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
