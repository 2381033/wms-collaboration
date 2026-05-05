<h2 style="margin-bottom:5px">Inbound Putaway Audit</h2>

<p style="margin-top:0">
    <strong>Branch</strong> : {{ $jobs->first()->branch_name }} <br>
    <strong>Date</strong> : {{ $jobDate }} <br>
    <strong>Total Job</strong> : {{ $jobs->count() }}
</p>

<p>
    Berikut adalah ringkasan inbound putaway yang telah <strong>Confirmed</strong>.
    Detail lengkap per SKU dan lokasi tersedia pada file Excel terlampir.
</p>

<hr>

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead style="background:#f2f2f2">
        <tr>
            <th align="left">Job No</th>
            <th align="left">Principal</th>
            <th align="center">Total Item</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jobs as $job)
            <tr>
                <td>{{ $job->job_no }}</td>
                <td>{{ $job->principal_name }}</td>
                <td align="center">
                    {{ count($stocks[$job->id] ?? []) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<p style="font-size:12px;color:#666">
    Email ini dikirim otomatis oleh sistem WMS.<br>
    Mohon tidak membalas email ini.
</p>
