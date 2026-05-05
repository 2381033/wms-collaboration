<div class="content">
    <table class="table">
        <thead>
            <tr>
                <td>NO</td>
                <td>JOB NO</td>
                <td>STOCK</td>
                <td>BOOK PICK</td>
                <td>REMAIN (SISA)</td>
                <td>FULL PALLET</td>
                <td>VECHILE NO</td>
                <td>VECHILE TYPE</td>
                <td>DESTINATION</td>
                <td>CONTAINER NO</td>
                <td>CONFIRM DATE</td>
            </tr>
            @php 
                $no=1;
            @endphp
                @foreach ($resultList as $key => $val)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$val['job_no']}}</td>
                <td>{{ $val['stock'] }}</td>
                <td>{{ $val['book_pick'] }}</td>
                <td>{{ $val['remain'] }}</td>
                <td>{{ $val['full_pallet'] }}</td>
                <td>{{ $val['vehicle_no'] }}</td>
                <td>{{ $val['vehicle_type'] }}</td>
                <td>{{ $val['destination'] }} - {{$val['city_code']}}</td>
                <td>{{ $val['container_no'] }}</td>
                <td>{{ $val['confirm_date'] }}</td>
            </tr>
            @endforeach
        </thead>
    </table>
</div>