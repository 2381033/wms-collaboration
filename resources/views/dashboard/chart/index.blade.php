@extends('layouts.main')

@section('title')
    Dashboard - Chart
@endsection

@section('content')    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Dashboard - Chart</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Dashboard - Chart</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs -->

    <section id="about-us" class="about-us">
        <div class="container info-wrap">
            <div class="row">                
                <div class="col-md-4">                                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="principal_id">Principal Name</label>
                                <select name="principal_id" id="principal_id" class="custom-select">
                                    <option value="All">All</option>
                                    @foreach (Auth::user()->principal as $item)
                                        <option value="{{$item->id}}">{{$item->principal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="month_number">Month</label>
                                <select name="month_number" id="month_number" class="custom-select">
                                    <option value="1" @isset ($month_number) @if ($month_number == 1) selected @endif @endisset>January</option>
                                    <option value="2" @isset ($month_number) @if ($month_number == 2) selected @endif @endisset>February</option>
                                    <option value="3" @isset ($month_number) @if ($month_number == 3) selected @endif @endisset>March</option>
                                    <option value="4" @isset ($month_number) @if ($month_number == 4) selected @endif @endisset>April</option>
                                    <option value="5" @isset ($month_number) @if ($month_number == 5) selected @endif @endisset>May</option>
                                    <option value="6" @isset ($month_number) @if ($month_number == 6) selected @endif @endisset>June</option>
                                    <option value="7" @isset ($month_number) @if ($month_number == 7) selected @endif @endisset>July</option>
                                    <option value="8" @isset ($month_number) @if ($month_number == 8) selected @endif @endisset>August</option>
                                    <option value="9" @isset ($month_number) @if ($month_number == 9) selected @endif @endisset>September</option>
                                    <option value="10" @isset ($month_number) @if ($month_number == 10) selected @endif @endisset>October</option>
                                    <option value="11" @isset ($month_number) @if ($month_number == 11) selected @endif @endisset>November</option>
                                    <option value="12" @isset ($month_number) @if ($month_number == 12) selected @endif @endisset>December</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year_number">Year</label>
                                <select name="year_number" id="year_number" class="custom-select">
                                    @foreach ($year_list as $item)
                                        <option value="{{$item}}" @isset ($year_number) @if ($year_number == $item) selected @endif @endisset>{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="report_type">Chart Group</label>
                                <select name="report_type" id="report_type" class="custom-select">
                                    <option value="WTQ">Transaction Quantity</option>
                                    <option value="WTV">Transaction Volume</option>
                                    <option value="WTW">Transaction Weight</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chart_type">Chart Type</label>
                                <select name="chart_type" id="chart_type" class="custom-select">
                                    <option value="BAR-VER">Bar Vertical</option>
                                    <option value="BAR-HOR">Bar Horizontal</option>
                                    <option value="LINE">Line</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="form-chart" name="form-chart" method="post">
                                @csrf
                            </form>  
                            <form id="form-pdf" name="form-pdf" method="post">
                                @csrf
                            </form>  
                            <div class="text-center">
                                <button type="button" onclick="retrieveData();" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> <span>Print</span></button>
                                <button type="button" onclick="printPdf();" class="btn btn-info btn-sm"><i class="fas fa-export"></i> <span>Send PDF</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="draw-charts" id="draw-charts">
                            </div>
                            <div class="draw-images" id="draw-images" style="display: none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')

@endsection

@push('scripts')
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'); 
        let periode = "";
        let principal = "";
        let jsonData = "";
        let charts = "";

        function generate_div(container) {
            jQuery("div.draw-charts").append('<div class="card mb-md-2 mt-md-2"><div class="card-body"><div id="' + container + '"></div></div></div>');
            jQuery("div.draw-images").append('<div class="card mb-md-2 mt-md-2"><div class="card-body"><div id="i_' + container + '"></div></div></div>');
        }

        function generate_chart(title, id, data) {  
            jQuery("div.draw-charts").empty();
            jQuery("div.draw-images").empty();
            periode = title;
            principal = id;
            jsonData = data;
            
            google.charts.load('current', {'packages':['corechart', 'bar']});
            google.charts.setOnLoadCallback(draw_chart);
        }
        
        function draw_chart() {
            for (var i = 0; i < periode.length; i++) {
                var data = google.visualization.arrayToDataTable(jsonData[i]);
                
                var options = {
                    title: periode[i],          
                    hAxis: {
                        title: "Days",
                        gridlines: { count: 31 }
                    },
                    vAxis: {
                        title: "Quantity"
                    },           
                    width: 700,
                    height: 500,
                    bar: {groupWidth: "95%"},
                    bars: 'vertical',
                    legend: { 
                        position : 'top'
                    }
                };

                let containerDiv = "principal_" + principal[i];
                let imageDiv = "i_principal_" + principal[i];

                generate_div(containerDiv);

                let chart_div = document.getElementById(containerDiv);
                let image_div = document.getElementById(imageDiv);
                var chart = new google.visualization.ColumnChart(chart_div);

                google.visualization.events.addListener(chart, 'ready', function(){
                    image_div.innerHTML = '<img src="'+chart.getImageURI()+'"">';          
                });

                chart.draw(data, options);
            }

            let drawCharts = document.getElementById('draw-images');
            charts = drawCharts.innerHTML;
        }
        
        function retrieveData() {
            var principal_id = $("#principal_id").val();
            var month_number = $("#month_number").val();
            var year_number = $("#year_number").val();
            var report_type = $("#report_type").val();
            
            $('#form-chart').trigger("reset");

            $('.hidden-chart').remove();

            $('#form-chart').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'principal_id')
                    .attr('class', 'hidden-chart')
                    .val(principal_id)
            );

            $('#form-chart').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'month_number')
                    .attr('class', 'hidden-chart')
                    .val(month_number)
            );

            $('#form-chart').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'year_number')
                    .attr('class', 'hidden-chart')
                    .val(year_number)
            );

            $('#form-chart').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'report_type')
                    .attr('class', 'hidden-chart')
                    .val(report_type)
            );

            $.ajax({
                data: $("#form-chart").serialize(), 
                url: "{{ route('dashboard-chart.data') }}",
                type: "POST",
                dataType: "json",
                success: function (data) {  
                    let periode = JSON.parse(data.periode);
                    let principal = JSON.parse(data.principal);
                    let chart_data = JSON.parse(data.chart_data);

                    generate_chart(periode, principal, chart_data);                    
                },
                error: function (data) {
                    console.log("Error:", data);
                }
            });
        }   
        
        function printPdf() {            
            $('#form-pdf').trigger("reset");

            $('.hidden-pdf').remove();

            $('#form-pdf').append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'chartData')
                    .attr('class', 'hidden-pdf')
                    .val(charts)
            );

            $.ajax({
                data: $("#form-pdf").serialize(), 
                url: "{{ route('dashboard-chart.print') }}",
                type: "POST",
                dataType: "json",
                success: function (data) {  
                    console.log(data);                
                },
                error: function (data) {
                    console.log("Error:", data);
                }
            });
        }            
    </script>
@endpush