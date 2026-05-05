<?php

namespace App\Http\Controllers\Report\KPI;

use App\Exports\DistributionCenterReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;


use App\Models\Master\Principal as MasterPrincipal;

class DistributionCenterController extends Controller
{
    public function index()
    {
        return view("report.distribution-center.index");
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;
        $jobName = $request->jobName;
        $periode_start = $request->periode_start;
        $periode_end = $request->periode_end;
        $fileType = $request->fileType;

        $periode_start = \Carbon\Carbon::parse($periode_start)->format("Y-m-d");
        $periode_end = \Carbon\Carbon::parse($periode_end)->format("Y-m-d");
        $title = 'KPI Report - Distribution Center';
        if ($jobName == 'IMP') {
            $dataList = DB::table("iv_stock_transaction as ist")
                ->select(
                    "ist.job_no AS job_number",
                    DB::RAW("'Inbound' AS job_type"),
                    "ip.principal_name AS customer",
                    "iiv.vehicle_no AS vehicle_no",
                    "iiv.size_id AS type_truck_id",
                    "ics.size_name AS type_truck",
                    DB::RAW("SUM(ist.qty) AS qty"),
                    DB::RAW("SUM(ist.muppp * qty) AS quantum"),
                    DB::RAW("COUNT(ist.location_code) AS pallet"),
                    DB::RAW("'-' AS destinasi"),
                    DB::RAW("DATE_FORMAT(iij.ata, '%d-%m-%Y %H:%i') AS shipment_arrival"),
                    DB::RAW("DATE_FORMAT(iij.unloading_start, '%d-%m-%Y %H:%i') AS unloading_start"),
                    DB::RAW("DATE_FORMAT(iij.unloading_finish, '%d-%m-%Y %H:%i') AS unloading_finish"),
                    DB::RAW("TIMESTAMPDIFF(MINUTE ,iij.unloading_start,iij.ata)*-1 AS waiting_time"),
                    DB::RAW("TIMESTAMPDIFF(MINUTE ,iij.unloading_finish,iij.unloading_start)*-1 AS unloading_time")
                )
                ->leftjoin("iv_inbound_vehicle AS iiv", "iiv.job_no", "ist.job_no")
                ->leftjoin("iv_container_size AS ics", "ics.id", "iiv.size_id")
                ->leftjoin("iv_inbound_job AS iij", "iij.job_no", "ist.job_no")
                ->leftjoin("iv_principal AS ip", "ip.id", "ist.principal_id")
                ->where("ist.company_id", $company_id)
                ->where("ist.principal_id", $principal_id)
                ->where("ist.job_type", $jobName)
                ->whereBetween("ist.created_at", [$periode_start, $periode_end])
                ->groupBy('ist.job_no', 'ist.job_type', 'ist.principal_id', 'iiv.vehicle_no', 'iiv.size_id', 'ics.size_name', 'iij.ata', 'iij.unloading_start', 'iij.unloading_finish');
                $dataListSql = $dataList->toSql();
                $dataListBindings = $dataList->getBindings();
                $dataList = $dataList->get();

            $listData = [];
            foreach ($dataList as $key => $value) {
                $waiting_hour = floor((int) $value->waiting_time / 60);
                $waiting_minute = (int) $value->waiting_time % 60;
                $waiting_time_new = "$waiting_hour:$waiting_minute";
                $unloading_hour = floor((int) $value->unloading_time / 60);
                $unloading_minute = (int) $value->unloading_time % 60;
                $unloading_time_new = "$unloading_hour:$unloading_minute";
                $listData[] = [
                    "job_number" => $value->job_number,
                    "job_type" => $value->job_type,
                    "customer" => $value->customer,
                    "vehicle_no" => $value->vehicle_no,
                    "type_truck_id" => $value->type_truck_id,
                    "type_truck" => $value->type_truck,
                    "qty" => $value->qty,
                    "quantum" => $value->quantum,
                    "pallet" => $value->pallet,
                    "destinasi" => $value->destinasi,
                    "shipment_arrival" => $value->shipment_arrival,
                    "unloading_start" => $value->unloading_start,
                    "unloading_finish" => $value->unloading_finish,
                    "waiting_time" => $waiting_time_new,
                    "unloading_time" => $unloading_time_new,
                ];
            };

            $headOne = collect([
                ["name" => "Job Number\n(Nomor Kegiatan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Job Type\n(Tipe Kegiatan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Customer\n(Principal)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Vehicle No\n(No Kendaraan)", "rowspan" => "1", "colspan" => "1"],
                // ["name" => "Type_truck_id", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Type Truck\n(Tipe Kendaraan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Qty\n(Jumlah)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Quantum\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Pallet\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Destinasi\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Shipment Arrival\n(Kedatangan Pengiriman)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Unloading Start\n(Mulai Bongkar)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Unloading Finish\n(Selesai Bongkar)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Waiting Time\n(Waktu Tunggu)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Unloading Time\n(Waktu Bongkar)", "rowspan" => "1", "colspan" => "1"]
            ]);

            $bodyOne = collect([
                ["name" => "Job Number", "field_name" => "job_number", "class" => "left"],
                ["name" => "Job Type", "field_name" => "job_type", "class" => "left"],
                ["name" => "Customer", "field_name" => "customer", "class" => "left"],
                ["name" => "Vehicle No", "field_name" => "vehicle_no", "class" => "left"],
                // ["name" => "Type_truck_id", "field_name" => "type_truck_id", "class" => "left"],
                ["name" => "Type Truck", "field_name" => "type_truck", "class" => "left"],
                ["name" => "Qty", "field_name" => "qty", "class" => "right"],
                ["name" => "Quantum", "field_name" => "quantum", "class" => "right"],
                ["name" => "Pallet", "field_name" => "pallet", "class" => "right"],
                ["name" => "Destinasi", "field_name" => "destinasi", "class" => "left"],
                ["name" => "Shipment Arrival", "field_name" => "shipment_arrival", "class" => "center"],
                ["name" => "Unloading Start", "field_name" => "unloading_start", "class" => "center"],
                ["name" => "Unloading Finish", "field_name" => "unloading_finish", "class" => "center"],
                ["name" => "Waiting Time", "field_name" => "waiting_time", "class" => "center"],
                ["name" => "Unloading Time", "field_name" => "unloading_time", "class" => "center"]
            ]);
            $title = 'KPI Report Inbound - Distribution Center';
        } else if ($jobName == 'EXP') {
            $dataList = DB::table("iv_stock_transaction as ist")
                ->select(
                    "ist.reference_no AS job_number",
                    DB::RAW("'Outbound' AS job_type"),
                    "ip.principal_name AS customer",
                    "iod.vehicle_no AS vehicle_no",
                    "iod.size_id AS type_truck_id",
                    "ics.size_name AS type_truck",
                    DB::RAW("SUM(ist.qty) AS qty"),
                    DB::RAW("SUM(ist.muppp * qty) AS quantum"),
                    DB::RAW("COUNT(ist.location_code) AS pallet"),
                    "ts.store_name AS destinasi",
                    DB::RAW("DATE_FORMAT(ioj.ata, '%d-%m-%Y %H:%i') AS gate_in_vehicle"),
                    DB::RAW("DATE_FORMAT(ioj.loading_start, '%d-%m-%Y %H:%i') AS loading_start"),
                    DB::RAW("DATE_FORMAT(ioj.loading_finish, '%d-%m-%Y %H:%i') AS loading_finish"),
                    DB::RAW("TIMESTAMPDIFF (MINUTE, ioj.loading_start, ioj.ata) * -1 AS waiting_time"),
                    DB::RAW("TIMESTAMPDIFF (MINUTE, ioj.loading_finish, ioj.loading_start) * -1 AS loading_time")
                )
                ->leftjoin("iv_outbound_despatch AS iod", "iod.job_no", "ist.reference_no")
                ->leftjoin("iv_container_size AS ics", "ics.id", "iod.size_id")
                ->leftjoin("iv_outbound_job AS ioj", "ioj.job_no", "ist.reference_no")
                ->leftjoin("iv_principal AS ip", "ip.id", "ist.principal_id")
                ->leftjoin("tm_store AS ts", "ts.id", "iod.store_id")
                ->where("ist.company_id", $company_id)
                ->where("ist.principal_id", $principal_id)
                ->where("ist.job_type", $jobName)
                ->whereBetween("ist.created_at", [$periode_start, $periode_end])
                ->groupBy('ist.reference_no', 'ist.job_type', 'ist.principal_id', 'iod.vehicle_no', 'iod.size_id', 'ics.size_name', 'ioj.ata', 'ioj.loading_start', 'ioj.loading_finish');
                $dataListSql = $dataList->toSql();
                $dataListBindings = $dataList->getBindings();
                $dataList = $dataList->get();
            // dd($dataListSql,$dataListBindings);
            $listData = [];
            foreach ($dataList as $key => $value) {
                $waiting_hour = floor((int) $value->waiting_time / 60);
                $waiting_minute = (int) $value->waiting_time % 60;
                $waiting_time_new = "$waiting_hour:$waiting_minute";
                $loading_hour = floor((int) $value->loading_time / 60);
                $loading_minute = (int) $value->loading_time % 60;
                $loading_time_new = "$loading_hour:$loading_minute";
                $listData[] = [
                    "job_number" => $value->job_number,
                    "job_type" => $value->job_type,
                    "customer" => $value->customer,
                    "vehicle_no" => $value->vehicle_no,
                    "type_truck_id" => $value->type_truck_id,
                    "type_truck" => $value->type_truck,
                    "qty" => $value->qty,
                    "quantum" => $value->quantum,
                    "pallet" => $value->pallet,
                    "destinasi" => $value->destinasi,
                    "gate_in_vehicle" => $value->gate_in_vehicle,
                    "loading_start" => $value->loading_start,
                    "loading_finish" => $value->loading_finish,
                    "waiting_time" => $waiting_time_new,
                    "loading_time" => $loading_time_new,
                ];
            };

            $headOne = collect([
                ["name" => "Job Number\n(Nomor Kegiatan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Job Type\n(Tipe Kegiatan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Customer\n(Principal)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Vehicle No\n(No Kendaraan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Type Truck\n(Tipe Kendaraan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Qty\n(Jumlah)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Quantum\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Pallet\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Destinasi\n", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Gate In Vehicle\n(Gate In Kendaraan)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Loading Start\n(Mulai Muat)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Loading Finish\n(Selesai Muat)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Waiting Time\n(Waktu Tunggu)", "rowspan" => "1", "colspan" => "1"],
                ["name" => "Loading Time\n(Waktu Muat)", "rowspan" => "1", "colspan" => "1"]
            ]);

            $bodyOne = collect([
                ["name" => "Job Number", "field_name" => "job_number", "class" => "left"],
                ["name" => "Job Type", "field_name" => "job_type", "class" => "left"],
                ["name" => "Customer", "field_name" => "customer", "class" => "left"],
                ["name" => "Vehicle No", "field_name" => "vehicle_no", "class" => "left"],
                // ["name" => "Type_truck_id", "field_name" => "type_truck_id", "class" => "left"],
                ["name" => "Type Truck", "field_name" => "type_truck", "class" => "left"],
                ["name" => "Qty", "field_name" => "qty", "class" => "right"],
                ["name" => "Quantum", "field_name" => "quantum", "class" => "right"],
                ["name" => "Pallet", "field_name" => "pallet", "class" => "right"],
                ["name" => "Destinasi", "field_name" => "destinasi", "class" => "left"],
                ["name" => "Gate In Vehicle", "field_name" => "gate_in_vehicle", "class" => "center"],
                ["name" => "Loading Start", "field_name" => "loading_start", "class" => "center"],
                ["name" => "Loading Finish", "field_name" => "loading_finish", "class" => "center"],
                ["name" => "Waiting Time", "field_name" => "waiting_time", "class" => "center"],
                ["name" => "Loading Time", "field_name" => "loading_time", "class" => "center"]
            ]);
            $title = 'KPI Report Outbound - Distribution Center';
        }

        $columnCount = 11;

        $data = [
            "title" => "$title",
            "css" => "landscape",
            "headOne" => $headOne->toArray(),
            "bodyOne" => $bodyOne->toArray(),
            "listData" => $listData,
            "columnCount" => $columnCount
        ];

        return view("report", $data);
    }

    public function export(Request $request)
    {
        // dd($_GET);
        $user = Auth::user();
        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;
        $jobName = $request->jobName;
        $periode_start = $request->periode_start;
        $periode_end = $request->periode_end;
        $fileType = $request->fileType;
        $periode_start = date("Y-m-d", strtotime($periode_start));
        $periode_end = date("Y-m-d", strtotime($periode_end));

        $time = \Carbon\Carbon::now()->format("dmy.His");
        $principal = MasterPrincipal::find($principal_id);

        $filename = "$principal->short_name-$fileType-$time.xlsx";

        $data = array(
            'user' => $user,
            'company_id' => $company_id,
            'principal_id' => $principal_id,
            'jobName' => $jobName,
            'periode_start' => $periode_start,
            'periode_end' => $periode_end,
            'fileType' => $fileType,
            'periode_start' => $periode_start,
            'periode_end' => $periode_end
        );


        return Excel::download(new DistributionCenterReportExport($data), $filename);
    }
}
