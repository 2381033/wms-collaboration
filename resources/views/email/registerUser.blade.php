<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding:30px 0;">
        <tr>
            <td>
                <table align="center" cellpadding="0" cellspacing="0" width="600"
                    style="background:#ffffff; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:30px;">
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="color:#2c3e50; margin:0;">🎉 Registrasi Berhasil!</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="color:#444; font-size:14px; line-height:22px; padding-bottom:20px;">
                            Halo <strong>{{ $data['name'] }}</strong>,<br><br>
                            Pendaftaran akun Anda telah berhasil. Berikut detail akun Anda:
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="100%" cellpadding="10" cellspacing="0"
                                style="background:#f9fafc; border:1px solid #e1e4e8; border-radius:8px; margin-bottom:25px;">
                                <tr>
                                    <td style="font-size:14px; color:#333;">
                                        <strong>User ID:</strong> {{ $data['username'] }}<br>
                                        <strong>Password:</strong> {{ $data['password'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding-bottom:30px;">
                            <a href="https://mkt-wms.samudera.id/mkt/login"
                                style="display:inline-block; background:#007bff; color:#ffffff; text-decoration:none; 
                                      padding:12px 24px; border-radius:6px; font-size:15px; font-weight:bold;">
                                🔑 Login ke Sistem
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="color:#444; font-size:13px; line-height:20px;">
                            Terima kasih telah bergabung bersama kami.<br>
                            Salam hangat,<br>
                            <strong>PT Masaji Kargosentra Tama</strong>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top:25px; font-size:12px; color:#777; border-top:1px solid #eee;">
                            📩 Jika mengalami kendala login atau membutuhkan bantuan, silakan hubungi tim IT kami
                            di:<br>
                            <a href="mailto:it.mkt@samudera.id" style="color:#007bff;">it.mkt@samudera.id</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
