<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/portrait.css') }}">
</head>

<style>
    .grid {
        display: grid;
        width: 114rem;
        grid-gap: 3rem;
        grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
    }

    @media (max-width: 60em) {
        .grid {
            grid-gap: 3rem;
        }
    }

    .grid .card {
        display: flex;
        flex-direction: column;
        background-color: #fff;
        border-radius: 0.4rem;
        overflow: hidden;
        box-shadow: 0 3rem 6rem rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: 0.2s;
    }
</style>

<body>
    <div class="page">
        <table class="table-template">
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table-header-kerangka">
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>JOB NO.</th>
                                        <th>JOB TYPE</th>
                                        <th>CUSTOMER</th>
                                        <th>VEHICLE NO</th>
                                        <th>TYPE TRUCK</th>
                                        <th>QTY</th>
                                        <th>SHIPMENT ARRIVAL DATE</th>
                                        @if ($jobName == 'IMP')
                                            <th>UNLOADING START</th>
                                            <th>UNLOADING FINISH</th>
                                            <th>WAITING TIME</th>
                                            <th>UNLOADING TIME</th>
                                        @elseif ($jobName == 'EXP')
                                            <th>LOADING START</th>
                                            <th>LOADING FINISH</th>
                                            <th>WAITING TIME</th>
                                            <th>LOADING TIME</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listData as $value)
                                        <tr>
                                            <td class="center">{{ $value['job_no'] }}</td>
                                            <td class="center">{{ $value['job_type'] }}</td>
                                            <td class="center">{{ $value['customer'] }}</td>
                                            <td class="center">{{ $value['vehicle_no'] }}</td>
                                            <td class="center">{{ $value['type_truck'] }}</td>
                                            <td class="center">{{ $value['qty'] }}</td>
                                            <td class="center">{{ $value['ata'] }}</td>
                                            @if ($jobName == 'IMP')
                                                <td class="center">{{ $value['unloading_start'] }}</td>
                                                <td class="center">{{ $value['unloading_finish'] }}</td>
                                                <td class="right">{{ $value['waitingTime'] }}</td>
                                                <td class="right">{{ $value['unloadingTime'] }}</td>
                                            @elseif ($jobName == 'EXP')
                                                <td class="center">{{ $value['loading_start'] }}</td>
                                                <td class="center">{{ $value['loading_finish'] }}</td>
                                                <td class="right">{{ $value['waitingTime'] }}</td>
                                                <td class="right">{{ $value['loadingTime'] }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="center" colspan="9">
                                            <b class="center" style="text-align: center !important;">AVERAGE</b>
                                        </td>
                                        <td class="left"><b>{{ $averageWaiting }}</b></td>
                                        @if ($jobName == 'IMP')
                                            <td class="left"><b>{{ $averageUnload }}</b></td>
                                        @elseif ($jobName == 'EXP')
                                            <td class="left"><b>{{ $averageload }}</b></td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>


                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Download Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Download By
            {{ Auth::user()->username }}
        </div>
    </div>
</body>

</html>
