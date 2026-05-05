<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Support\Str;

use App\Models\Master\Principal as MasterPrincipal;

class DistributionCenterReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $dataParams = null;

    public function __construct($dataParams)
    {
        $this->dataParams = $dataParams;
    }

    public function collection()
    {
        $principal = MasterPrincipal::find($this->dataParams['principal_id']);
        $user = Auth::user();
        $company_id = Auth::user()->company_id;
        $principal_id = $this->dataParams['principal_id'];
        $jobName = $this->dataParams['jobName'];
        $periode_start = $this->dataParams['periode_start'];
        $periode_end = $this->dataParams['periode_end'];
        $fileType = $this->dataParams['fileType'];
        $periode_start = date("Y-m-d", strtotime($periode_start));
        $periode_end = date("Y-m-d", strtotime($periode_end));


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
                    DB::RAW("DATE_FORMAT(iij.ata, '%d/%m/%Y %H:%i') AS shipment_arrival"),
                    DB::RAW("DATE_FORMAT(iij.unloading_start, '%d/%m/%Y %H:%i') AS unloading_start"),
                    DB::RAW("DATE_FORMAT(iij.unloading_finish, '%d/%m/%Y %H:%i') AS unloading_finish"),
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
                ->groupBy('ist.job_no', 'ist.job_type', 'ist.principal_id', 'iiv.vehicle_no', 'iiv.size_id', 'ics.size_name', 'iij.ata', 'iij.unloading_start', 'iij.unloading_finish')
                ->get();

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
                    // "type_truck_id" => $value->type_truck_id,
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
                    DB::RAW("DATE_FORMAT(ioj.ata, '%d/%m/%Y %H:%i') AS gate_in_vehicle"),
                    DB::RAW("DATE_FORMAT(ioj.loading_start, '%d/%m/%Y %H:%i') AS loading_start"),
                    DB::RAW("DATE_FORMAT(ioj.loading_finish, '%d/%m/%Y %H:%i') AS loading_finish"),
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
                ->groupBy('ist.reference_no', 'ist.job_type', 'ist.principal_id', 'iod.vehicle_no', 'iod.size_id', 'ics.size_name', 'ioj.ata', 'ioj.loading_start', 'ioj.loading_finish')
                ->get();
                // dd($dataList);
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
                    // "type_truck_id" => $value->type_truck_id,
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
        }

        return new Collection($listData);
    }

    public function headings(): array
    {
        // dd($this->dataParams);
        $jobName = $this->dataParams['jobName'];
        if ($jobName == 'IMP') {
            $header = [
                "Job Number",
                "Job Type",
                "Customer",
                "Vehicle No",
                "Type Truck",
                "Qty",
                "Quantum",
                "Pallet",
                "Destinasi",
                "Shipment Arrival",
                "Unloading Start",
                "Unloading Finish",
                "Waiting Time",
                "Unloading Time"
            ];
        } else if ($jobName == 'EXP'){
            $header = [
                "Job Number",
                "Job Type",
                "Customer",
                "Vehicle No",
                "Type Truck",
                "Qty",
                "Quantum",
                "Pallet",
                "Destinasi",
                "Gate In Vehicle",
                "Loading Start",
                "Loading Finish",
                "Waiting Time",
                "Loading Time"
            ];
        }
        return $header;
    }
}
