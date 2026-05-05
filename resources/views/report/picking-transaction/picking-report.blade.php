
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
    <title>{{$title}}</title>
    <style>
        .small-column {
            width: 2%; /* Sesuaikan dengan ukuran yang diinginkan */
            word-wrap: break-word; /* Agar teks yang panjang dipotong jika terlalu besar */
        }

        .large-column {
            width: 200px; /* Sesuaikan dengan ukuran yang diinginkan */
        }
    </style>
</head>
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
                            <h3 class="title-header">
                                {{ 'Picking Report' }} 
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table-header-kerangka">
                                <tr>
                                    <td>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>JOB NO</th>
                                                    <th>STOCK</th>
                                                    <th style="width: 5%;">BOOK PICK</th>
                                                    <th style="width: 5%;">REMAIN (SISA)</th>
                                                    <th style="width: 5%;">FULL PALLET</th>
                                                    <th>VEHICLE NO</th>
                                                    <th>VEHICLE TYPE</th>
                                                    <th style="width: 20%;">DESTINATION</th>
                                                    <th>CONTAINER NO</th>
                                                    <th>CONFIRM DATE</th>
                                                </tr>
                                            </thead>
                                            
                                            @php 
                                                $no=1;
                                                
                                            @endphp
                                              <tbody>
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
                                                    <td>{{ date("d/m/Y", strtotime($val['confirm_date'])) }}</td>
                                                </tr>
                                                @endforeach
                                              </tbody>
                                            
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
<script>
    window.print();
</script>