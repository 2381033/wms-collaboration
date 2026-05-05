@extends('layouts.new.base')
@section('title', 'MKT - Bea Cukai Monthly')
@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card card-custom card-stretch">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="">Start Date</label>
                            <input type="date" class="form-control" min="2024-01-01" max="2045-01-01" required
                                value="{{ date('Y-m-01') }}" id="startDate">
                        </div>
                        <div class="col-sm-4">
                            <label for="">End Date</label>
                            <input type="date" class="form-control" required value="{{ date('Y-m-t') }}" id="endDate">
                        </div>
                        <div class="col-sm-2" style="margin-top: 25px;">
                            <a href="#" onclick="cariData()" class="btn btn-block btn-dark"><i
                                    class="fas fa-search"></i>
                            </a>
                        </div>
                        <div class="col-sm-12">
                            <div class="float-right mb-3">

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableList">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
                                            <th colspan="2">PEB</th>
                                            <th colspan="2">NPE</th>
                                            <th colspan="2">PKBE</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Eksportir</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Forwarder</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Receiving Date</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Asal Barang</th>
                                            <th colspan="2">Jenis Barang</th>
                                            <th colspan="2" style="vertical-align : middle;text-align:center;">Kemasan
                                            </th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Valuta
                                            </th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Nilai
                                                Barang</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">No. Peti
                                                Kemas</th>
                                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Negara
                                                Tujuan</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">

                                    </tbody>
                                    <tfoot>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
        <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Export To Excel"
            data-placement="right">
            <a class="btn btn-sm btn-icon btn-bg-light btn-icon-dark btn-hover-success" href="#">
                <i class="flaticon-settings"></i>
            </a>
        </li>
    </ul>

    <div id="kt_demo_panel" class="offcanvas offcanvas-right p-10">
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
            <h4 class="font-weight-bold m-0">
                Toggle Quick Menu
            </h4>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_demo_panel_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="offcanvas-content">
            <div class="offcanvas-wrapper mb-5 scroll-pull">
                <div class="row">
                    <a href="#" style="border-radius: 25px;" onclick="reportInbound()" class="btn btn-dark btn-block">
                        Report Inbound <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportOutbound()"
                        class="btn btn-info btn-block"> Report Outbound <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportStock()"
                        class="btn btn-primary btn-block">
                        Report Stock <i class="fas fa-arrow-circle-right"></i>
                    </a>
                    <a href="#" style="border-radius: 25px;" onclick="reportMonthly()"
                        class="btn btn-success btn-block">
                        Report Monthly <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $('.selectMonths').select2();
        $('.selectYears').select2();

        function reportInbound() {
            var url = "{{ url('export/BeaCukai/Report/inbound/') }}";
            window.open(url, '_blank');
        }

        function reportOutbound() {
            var url = "{{ url('export/BeaCukai/Report/outbound/') }}";
            window.open(url, '_blank');
        }

        function reportStock() {
            var url = "{{ url('export/BeaCukai/Report/stock-report/') }}";
            window.open(url, '_blank');
        }

        function reportMonthly() {
            var url = "{{ url('export/BeaCukai/Report/monthly/') }}";
            window.open(url, '_blank');
        }

        function cariData() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            if (startDate == null || endDate == null) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Pariode Waktu Wajib di isi..',
                })
            } else {
                $.ajax({
                    url: "{{ url('export/BeaCukai/Report/monthly/') }}/" + startDate + '/' + endDate,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('.float-right').html("")
                        if (response.data.length > 0) {
                            $('.float-right').append(`    
                              <a href="#" onclick="downloadPDF('${startDate}', '${endDate}')" class="btn btn-md text-white mt-2" style="background-color: maroon"><i
                                          class="fas fa-download text-white"></i> Download To PDF</a>
                              <a href="#" onclick="downloadExcel('${startDate}', '${endDate}')" class="btn btn-md text-white mt-2" style="background-color: green"><i
                                    class="fas fa-file-excel text-white"></i>
                              Download To Excel</a>`)
                        }
                        $('#tbody').html("");
                        $.each(response.data, function(key, val) {
                            var peb_date = new Date(val.peb_date).toLocaleDateString('en-GB');
                            var npe_date = new Date(val.npe_date).toLocaleDateString('en-GB');
                            var pkbe_date = new Date(val.pkbe_date).toLocaleDateString('en-GB');
                            var asal_barang = null
                            if(val.asal_barang == null){
                                var asal_barang = '-';
                            }else{
                                var asal_barang = val.asal_barang;
                            }
                            if(val.forwarder_name == null){
                                var forwarder = '-';
                            }else{
                                var forwarder = val.forwarder_name;
                            }
                            if(val.receiving_date == null){
                                var receiving_date = '-';
                            }else{
                                var receiving_date = new Date(val.receiving_date).toLocaleDateString('en-GB');
                            }
                            $('#tbody').append(`
                              <tr>
                                    <td>${key+1}</td>
                                    <td>${val.peb_no}</td>
                                    <td>${peb_date}</td>
                                    <td>${val.npe_no}</td>
                                    <td>${npe_date}</td>
                                    <td>${val.pkbe_no}</td>
                                    <td>${pkbe_date}</td>
                                    <td>${val.eksportir}</td>
                                    <td>${forwarder}</td>
                                    <td>${receiving_date}</td>
                                    <td>${asal_barang}</td>
                                    <td>${val.jumlah_jenis_barang}</td>
                                    <td>${val.satuan_jenis_barang}</td>
                                    <td>${val.jumlah_kemasan}</td>
                                    <td>${val.satuan_kemasan}</td>
                                    <td>${val.valuta}</td>
                                    <td>${val.nilai_barang}</td>
                                    <td>${val.no_peti_kemas}</td>
                                    <td>${val.negara_tujuan}</td>
                              </tr>`);
                        });
                    },
                    error: function(response) {
                        alert('Internal Server Error, Please refresh page and try again..')
                    }
                });
            }
        }

        function downloadPDF(start, end) {
            var url = "{{ url('export/BeaCukai/Report/downloadPDF/monthly') }}/" + start + '/' + end;
            window.open(url, '_blank');
        }

        function downloadExcel(start, end) {
            var url = "{{ url('export/BeaCukai/Report/downloadExcel/monthly') }}/" + start + '/' + end;
            window.open(url, '_blank');
        }
    </script>
@endpush
