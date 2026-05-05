<!DOCTYPE html>
<html>

<head>
    <style>
        /* Style untuk email */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #5f6368;
            text-align: center;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        td {
            text-align: left;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-top: 30px;
        }

        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Inbound Notification</h2>
        <p>Dear Recipient,</p>
        <p>Please find the inbound job details below:</p>

        <table>
            <thead>
                <tr>
                    <th>Job No</th>
                    <th>Job Date</th>
                    <th>Description</th>
                    <th>ETA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->job_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->job_date)->format('d-m-Y') }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->eta)->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Thank you for your attention.</p>

        <div class="footer">
            <p>Best Regards,<br>Administrator<br>PT Masaji Kargasentra Tama</p>
        </div>
    </div>
</body>

</html>
