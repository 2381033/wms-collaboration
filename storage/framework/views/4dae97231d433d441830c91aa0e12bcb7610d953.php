<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stock Freeze</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding:30px 0;">
        <tr>
            <td>
                <table align="center" cellpadding="0" cellspacing="0" width="600"
                    style="background:#ffffff; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:30px;">

                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <img src="https://img.icons8.com/emoji/96/000000/warning-emoji.png" alt="Warning Icon"
                                width="60" style="margin-bottom:10px;">
                            <h2 style="color:#d9534f; margin:0;">⚠️ Peringatan Freeze Activity <?php echo e($activity); ?> -
                                <?php echo e($principal->principal_name); ?>

                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="color:#444; font-size:14px; line-height:22px; padding-bottom:20px;">
                            <?php echo nl2br(e($body_email)); ?>

                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="100%" cellpadding="15" cellspacing="0"
                                style="background:#fff3cd; border:1px solid #f5c6cb; border-radius:8px; margin-bottom:25px;">
                                <tr>
                                    <td style="font-size:14px; color:#856404;">
                                        <strong>Tanggal Berlaku:</strong> <?php echo e(date('d-m-Y H:i')); ?>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="color:#444; font-size:13px; line-height:20px;">
                            Salam hormat,<br>
                            <strong>PT Masaji Kargosentra Tama</strong>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top:25px; font-size:12px; color:#777; border-top:1px solid #eee;">
                            📩 Jika membutuhkan bantuan lebih lanjut, silakan hubungi tim IT kami di:<br>
                            <a href="mailto:it.mkt@samudera.id" style="color:#007bff;">it.mkt@samudera.id</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/email/FreezeStockDC.blade.php ENDPATH**/ ?>