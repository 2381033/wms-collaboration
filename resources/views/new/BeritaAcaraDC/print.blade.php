<!DOCTYPE html>
<html>

<head>
    <title>Berita Acara</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .page {
            width: 210mm;
            margin: auto;
            padding: 10mm;
        }


        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            width: 200px;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            flex: 1;
        }

        .doc-info {
            border: 1px solid #000;
            font-size: 11px;
            padding: 5px;
            width: 200px;
        }

        .doc-info table td {
            padding: 2px;
        }

        hr {
            border: 1px solid #000;
        }

        table.main {
            width: 100%;
        }

        table.main td {
            padding: 3px;
            vertical-align: top;
        }

        .label {
            width: 150px;
        }

        .foto img {
            width: 400px;
            margin-top: 10px;
        }

        .ttd-box {
            margin-top: 20px;
            border: 1px solid #000;
        }

        .ttd-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-box td {
            border: 1px solid #000;
            text-align: center;
            padding: 10px;
        }

        .ttd-name {
            height: 60px;
            vertical-align: bottom;
        }

        .footer {
            font-size: 12px;
        }

        .top-left {
            position: absolute;
            top: 5px;
            left: 10px;
            font-size: 12px;
        }

        table {
            border-collapse: collapse !important;
        }

        table,
        th,
        td {
            border: 0.5pt solid #000 !important;
        }

        @media print {

            table,
            th,
            td {
                border: 1px solid #000 !important;
            }
        }

        @page {
            margin: 20mm;
        }

        @media print {
            body {
                counter-reset: page;
            }

            .footer {
                position: fixed;
                bottom: 10px;
                right: 20px;
            }

            .footer:after {
                content: "Page " counter(page);
            }
        }


        @media print {
            .ttd-box {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }


        @media print {
            img {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" width="150">
            </div>

            <div class="title">BERITA ACARA</div>

            <div class="doc-info">
                <table>
                    <tr>
                        <td>No Dok.</td>
                        <td>: FRM-OPS-WHS-001</td>
                    </tr>
                    <tr>
                        <td>Rev</td>
                        <td>: 00</td>
                    </tr>
                    <tr>
                        <td>Tgl Efektif</td>
                        <td>: 14-04-2026</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <table class="main">
            <tr>
                <td class="label">Task ID</td>
                <td>: {{ $ba->no_doc }}</td>
            </tr>
            <tr>
                <td>Tanggal Temuan</td>
                <td>: {{ date('d-m-Y', strtotime($ba->tanggal_temuan)) }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: {{ $ba->kategori }}</td>
            </tr>
            <tr>
                <td>Sub Kategori</td>
                <td>: {{ $ba->sub_kategori }}</td>
            </tr>
            <tr>
                <td>Tanda Tangan Pihak Kedua</td>
                <td>: {{ $ba->ttd_pihak2 == 'Yes' ? 'Ya' : 'Tidak' }}</td>
            </tr>
            <tr>
                <td>Tempat Kejadian</td>
                <td>: {{ $ba->tempat_kejadian }}</td>
            </tr>
            <tr>
                <td>Kronologis</td>
                <td>:
                    {{ $ba->kronologis }}
                </td>
            </tr>
            <tr>
                <td>Tindakan segera yang telah di lakukan</td>
                <td>:
                    {{ $ba->solusi }}
                </td>
            </tr>

            <tr>
                <td>Dokumentasi</td>
                <td class="foto">
                    @foreach ($files as $file)
                        <img src="{{ asset('public/' . $file->file_path) }}"
                            style="width:250px; height:200px; object-fit:cover; margin:5px;">
                    @endforeach
                </td>
            </tr>
        </table>

        <br>
        <b>Created By</b> : {{ $ba->created_by }} <br>
        <b>Print Date</b> : {{ now() }}

        <div class="ttd-box">
            <table>
                <tr>
                    <td colspan="4"><b>PIHAK PERTAMA</b></td>
                </tr>
                @if ($ba->kategori == 'Kerusakan Product' || $ba->kategori == 'Kerusakan Property')
                    <tr>
                        <td>DIBUAT</td>
                        <td>SAKSI</td>
                        <td>DIPERIKSA</td>
                        <td>MENGETAHUI</td>
                    </tr>
                @else
                    <tr>
                        <td>DIBUAT</td>
                        <td>QC</td>
                        <td>DICHECK</td>
                        <td>MENGETAHUI</td>
                    </tr>
                @endif
                <tr class="ttd-name">
                    <td>{{ $ba->created_by }}
                        <br>
                        ({{ 'Pelapor' }})
                    </td>
                    <td>{{ $ba->qc }}
                        <br>
                        ({{ $ba->posisi_qc }})
                    </td>
                    <td>{{ $ba->mengetahui }}
                        <br>
                        ({{ $ba->posisi_mengetahui }})
                    </td>
                    <td>{{ $ba->pj }}
                        <br>
                        ({{ $ba->posisi_pj }})
                    </td>
                </tr>
            </table>
        </div>

        @if ($ba->ttd_pihak2 == 'Yes')
            <div class="ttd-box">
                <table>
                    <tr>
                        <td colspan="2"><b>PIHAK KEDUA</b></td>
                    </tr>
                    <tr>
                        <td>DISETUJUI</td>
                        <td>MENGETAHUI</td>
                    </tr>
                    <tr class="ttd-name">
                        <td>( .............. )</td>
                        <td>( .............. )</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
</body>
<script>
    window.onload = function() {
        window.print();
    }
</script>

</html>
