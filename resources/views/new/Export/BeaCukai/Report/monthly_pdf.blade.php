<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
</head>
<style type="text/css">
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }
    }
</style>

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
                            <h5 class="title-header">
                                LAPORAN BULANAN KONSOLIDATOR
                                <p></p>
                                PT MASAJI KARGOSENTRA TAMA
                                <p></p>
                                {{ 'Periode ' . \Carbon\Carbon::parse($start)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($end)->format('d-m-Y') }}
                            </h5>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th colspan="2">PEB</th>
                                        <th colspan="2">NPE</th>
                                        <th colspan="2">PKBE</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Eksportir</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Forwarder</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Receiving Date</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Asal Barang
                                        </th>
                                        <th colspan="2">Jenis Barang</th>
                                        <th colspan="2" style="vertical-align : middle;text-align:center;">Kemasan
                                        </th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Valuta
                                        </th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Nilai
                                            Barang</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">No. Peti
                                            Kemas</th>
                                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Negara
                                            Tujuan</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    @foreach ($data as $key => $v)
                                        <tr class="text-center">
                                            <td class="center">
                                                {{ $v->peb_no }}</td>
                                            <td class="center">
                                                {{ \Carbon\Carbon::parse($v->peb_date)->format('d-m-Y') }}
                                            </td>
                                            <td class="center">
                                                {{ $v->npe_no }}
                                            </td>
                                            <td class="center">
                                                {{ \Carbon\Carbon::parse($v->npe_date)->format('d-m-Y') }}
                                            </td>
                                            <td class="center">
                                                {{ $v->pkbe_no }}</td>
                                            <td class="center">
                                                {{ \Carbon\Carbon::parse($v->pkbe_date)->format('d-m-Y') }}
                                            </td>
                                            <td class="center">{{ $v->eksportir }}</td>
                                            <td class="center">
                                                {{ $v->forwarder_name == null ? '-' : $v->forwarder_name  }}
                                            </td>
                                            <td class="center">
                                                {{ $v->receiving_date == null ? '-' : \Carbon\Carbon::parse($v->receiving_date)->format('d-m-Y')  }}
                                            </td>
                                            <td>{{ $v->asal_barang == null ? '-' : $v->asal_barang  }}</td>
                                            <td>{{ $v->jumlah_jenis_barang }}</td>
                                            <td>{{ $v->satuan_jenis_barang }}</td>
                                            <td>{{ $v->jumlah_kemasan }}</td>
                                            <td>{{ $v->satuan_kemasan }}</td>
                                            <td>{{ $v->valuta }}</td>
                                            <td>{{ $v->nilai_barang }}</td>
                                            <td class="center">
                                                {{ $v->no_peti_kemas }}
                                            </td>
                                            <td class="center">
                                                {{ $v->negara_tujuan }}
                                            </td>
                                            {{-- <td>{{ $v->no_peti_kemas }}</td> --}}
                                            {{-- <td>{{ $v->negara_tujuan }}</td> --}}
                                            {{-- <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->peb_date)->format('d-m-Y') }}</td>
                                            <td>{{ $item->npe_no }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->npe_date)->format('d-m-Y') }}</td>
                                            <td>{{ $item->pkbe_no }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->pkbe_date)->format('d-m-Y') }}</td>
                                            <td>{{ $item->eksportir }}</td>
                                            <td>{{ $item->jumlah_jenis_barang }}</td>
                                            <td>{{ $item->satuan_jenis_barang }}</td>
                                            <td>{{ $item->jumlah_kemasan }}</td>
                                            <td>{{ $item->satuan_kemasan }}</td>
                                            <td>{{ $item->valuta }}</td>
                                            <td>{{ $item->nilai_barang }}</td>
                                            <td>{{ $item->no_peti_kemas }}</td>
                                            <td>{{ $item->negara_tujuan }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
    <script>
        // window.print()
    </script>
</body>

</html>
