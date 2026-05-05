<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title></title>
    <style>
        :root {
            color-scheme: light;
            supported-color-schemes: light;
        }

        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        a {
            text-decoration: none;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        table,
        th,
        td {
            border: 1px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
        }

        .btn-td,
        .btn-a {
            transition: all 100ms ease-in;
        }

        .btn-td-primary:hover,
        .btn-a-primary:hover {
            background: #0C2340 !important;
            border-color: #0C2340 !important;
        }
    </style>
</head>

<body style="margin: 0px; background-color: white; padding: 0px !important;">
    <center role="article" aria-roledescription="email" lang="en" style="width: 100%;background-color: white;">
        <div style="max-height:0; overflow:hidden; mso-hide:all;" aria-hidden="true"> </div>
        <div style="max-width: 680px; margin: 0 auto;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                style="margin: auto;">
                <tr>
                    <td align="center" style="font-size:0; line-height: 0;background-color:#FDB913;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td
                                    style="padding:40px 30px 30px 30px;text-align:center;font-size:24px;font-weight:bold;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="background-color:#FDB913;padding:0 10px 10px;text-align:left">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td
                                    style="padding: 10px;font-family: 'Arial', sans-serif;font-size:14px;mso-line-height-rule: exactly;color:#0C2340;">
                                    <p style="margin:0;">
                                        <strong>Dear,<span style="color:#525ca3"> Spv. Warehouse
                                                Export & AO Team
                                            </span>
                                        </strong>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                @if ($data['type'] == 'partial')
                                    <td
                                        style="padding:10px; font-family: Arial, sans-serif; font-size:16px; mso-line-height-rule: exactly;line-height: 21px; color: #0C2340;text-align:left">
                                        <p style="margin:0">Checker baru saja menandai PO <b>{{ $data['po'] }}</b>
                                            sebagai pengiriman parsial
                                            <strong style="color:#525ca3">
                                                Berikut Detailnya: </strong>
                                        </p>
                                    </td>
                                @else
                                    <td
                                        style="padding:10px; font-family: Arial, sans-serif; font-size:16px; mso-line-height-rule: exactly;line-height: 21px; color: #0C2340;text-align:left">
                                        <p style="margin:0">Terdapat perbedaan receiving qty pada scan carton
                                            <strong style="color:#525ca3">
                                                Berikut Detailnya: </strong>
                                        </p>
                                    </td>
                                @endif
                            </tr>
                        </table>
                        <table border="2" style="th, td: padding: 15px;">
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">CUSTOMER</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['customer'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">SHIPPER</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['shipper'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">PO NUMBER</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['po'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">QTY BOOKING</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['booking'] }} CTN</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">QTY ACTUAL</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['actual'] }} CTN</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">CHECKER</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['checker'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; font-weight: bold">JOB DATE</td>
                                <td style="font-size: 18px; font-weight: bold">:</td>
                                <td style="font-size: 18px; font-weight: bold"> {{ $data['jobDate'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px;background-color:#FDB913;">
                        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0"
                            style="margin: auto;">
                            <tr>
                                @if ($data['type'] == 'partial')
                                    <td class="btn-td btn-td-primary"
                                        style="border-radius: 30px; background: red;text-align:center">
                                        <a class="btn-a btn-a-primary"
                                            href="https://mkt-wms.samudera.id/mkt/export/inbound/scanCtn/partial"
                                            style="border: 1px solid red; font-family: Arial,sans-serif; font-size:14px; mso-line-height-rule: exactly;line-height: 14px; text-decoration: none; padding: 10px 31px; color: #ffffff; font-weight:bold;display: block; border-radius: 30px;"><strong>
                                                Klik untuk Melihat Detail
                                            </strong>
                                        </a>
                                    </td>
                                @else
                                    <td class="btn-td btn-td-primary"
                                        style="border-radius: 30px; background: red;text-align:center">
                                        <a class="btn-a btn-a-primary"
                                            href="https://mkt-wms.samudera.id/mkt/export/inbound/scanCtn/outstanding"
                                            style="border: 1px solid red; font-family: Arial,sans-serif; font-size:14px; mso-line-height-rule: exactly;line-height: 14px; text-decoration: none; padding: 10px 31px; color: #ffffff; font-weight:bold;display: block; border-radius: 30px;"><strong>
                                                Klik untuk Melakukan Aksi
                                            </strong>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td
                        style="padding-top:0;padding-right: 20px;padding-bottom:20px;padding-left:20px; font-family:Arial, sans-serif; font-size:10px; mso-line-height-rule: exactly;line-height: 15px; color: #999999;background-color:#FDB913;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td
                                    style="font-family: Arial, sans-serif; font-size: 10px; mso-line-height-rule: exactly;line-height:15px; color: white;text-align: center">
                                    <p style="margin: 0">Thanks,</p>
                                    <p style="margin: 0">Administrator</p>
                                    <p style="margin: 0">PT. MASAJI KARGOSENTRA TAMA</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
</body>

</html>
