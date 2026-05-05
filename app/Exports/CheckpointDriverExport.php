<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CheckpointDriverExport implements FromView
{
    protected $start_date;
    protected $end_date;
    protected $no_mobil;

    public function __construct($start_date, $end_date, $no_mobil)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->no_mobil = $no_mobil;
    }

    private function getDriverName($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)
            ->value('name');
        return $data;
    }

    public function view(): View
    {
        $jobHeader =  DB::table("cp_driver_job")
            ->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59'])
            ->where('confirmed_flag', 'Yes')
            ->get();
        if ($this->no_mobil != 'ALL') {
            $jobHeader->where('no_mobil', $this->no_mobil);
        }

        $detail = DB::table('cp_driver_detail')
            ->whereIn('token', $jobHeader->pluck('token')->toArray())
            ->get();
        $revenuecost = DB::table('cp_driver_revenue_cost')
            ->orderBy('id', 'DESC')
            ->whereIn('token', $jobHeader->pluck('token')->toArray())
            ->get();

        $data = [];
        foreach ($jobHeader as $value) {
            $masterMuat = $detail
                ->where('token', $value->token)
                ->where('lokasi_muat', '!=', NULL);
            $masterBongkar = $detail->where('token', $value->token)
                ->where('lokasi_bongkar', '!=', NULL);
            $start_muat_1 = Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[0] ?? '0');
            $end_muat_1 = isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[0]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[0]) : '0';
            if (is_string($end_muat_1) || is_numeric($end_muat_1)) {
                $diff_loc_muat_1 = '-';
            } else {
                $diff_loc_muat_1 = $end_muat_1->diff($start_muat_1)->format('%H:%I');
            }

            $start_muat_2 = Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[1] ?? '0');
            $end_muat_2 = isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[1]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[1]) : '0';
            if (is_string($end_muat_2) || is_numeric($end_muat_2)) {
                $diff_loc_muat_2 = '-';
            } else {
                $diff_loc_muat_2 = $end_muat_2->diff($start_muat_2)->format('%H:%I');
            }

            $start_muat_3 = Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[2] ?? '0');
            $end_muat_3 = isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[2]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[2]) : '0';
            if (is_string($end_muat_3) || is_numeric($end_muat_3)) {
                $diff_loc_muat_3 = '-';
            } else {
                $diff_loc_muat_3 = $end_muat_3->diff($start_muat_3)->format('%H:%I');
            }

            $start_bongkar_1 = Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[0] ?? '0');
            $end_bongkar_1 = isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[0]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[0]) : '0';
            if (is_string($end_bongkar_1) || is_numeric($end_bongkar_1)) {
                $diff_loc_bongkar_1 = '-';
            } else {
                $diff_loc_bongkar_1 = $end_bongkar_1->diff($start_bongkar_1)->format('%H:%I');
            }

            $start_bongkar_2 = Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[1] ?? '0');
            $end_bongkar_2 = isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[1]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[1]) : '0';
            if (is_string($end_bongkar_2) || is_numeric($end_bongkar_2)) {
                $diff_loc_bongkar_2 = '-';
            } else {
                $diff_loc_bongkar_2 = $end_bongkar_2->diff($start_bongkar_2)->format('%H:%I');
            }

            $start_bongkar_3 = Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[2] ?? '0');
            $end_bongkar_3 = isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[2]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[2]) : '0';
            if (is_string($end_bongkar_3) || is_numeric($end_bongkar_3)) {
                $diff_loc_bongkar_3 = '-';
            } else {
                $diff_loc_bongkar_3 = $end_bongkar_3->diff($start_bongkar_3)->format('%H:%I');
            }

            $data[] = [
                'no_order' => $value->no_order,
                'no_mobil' => $value->no_mobil,
                'jenis_armada' => $value->jenis_armada,
                'nama_driver' => $this->getDriverName($value->driver),
                'nama_customer' => $value->nama_customer,
                'lokasi_muat_1' => $masterMuat->pluck('lokasi_muat')->toArray()[0] ?? '-',
                'lokasi_muat_2' => $masterMuat->pluck('lokasi_muat')->toArray()[1] ?? '-',
                'lokasi_muat_3' => $masterMuat->pluck('lokasi_muat')->toArray()[2] ?? '-',
                'lokasi_bongkar_1' => $masterBongkar->pluck('lokasi_bongkar')->toArray()[0] ?? '-',
                'lokasi_bongkar_2' => $masterBongkar->pluck('lokasi_bongkar')->toArray()[1] ?? '-',
                'lokasi_bongkar_3' => $masterBongkar->pluck('lokasi_bongkar')->toArray()[2] ?? '-',

                'gatein_lokasi_muat_1' => isset($masterMuat->pluck('gate_in_loc_muat')->toArray()[0]) ? Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[0])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_muat_1' => isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[0]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[0])->format('d-m-Y H:i') : '-',
                'leadtime_muat_1' => $diff_loc_muat_1,

                'gatein_lokasi_muat_2' => isset($masterMuat->pluck('gate_in_loc_muat')->toArray()[1]) ? Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[1])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_muat_2' => isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[1]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[1])->format('d-m-Y H:i') : '-',
                'leadtime_muat_2' => $diff_loc_muat_2,


                'gatein_lokasi_muat_3' => isset($masterMuat->pluck('gate_in_loc_muat')->toArray()[2]) ? Carbon::parse($masterMuat->pluck('gate_in_loc_muat')->toArray()[2])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_muat_3' => isset($masterMuat->pluck('gate_out_loc_muat')->toArray()[2]) ? Carbon::parse($masterMuat->pluck('gate_out_loc_muat')->toArray()[2])->format('d-m-Y H:i') : '-',
                'leadtime_muat_3' => $diff_loc_muat_3,

                'gatein_lokasi_bongkar_1' => isset($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[0]) ? Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[0])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_bongkar_1' => isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[0]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[0])->format('d-m-Y H:i') : '-',
                'leadtime_bongkar_1' => $diff_loc_bongkar_1,


                'gatein_lokasi_bongkar_2' => isset($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[1]) ? Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[1])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_bongkar_2' => isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[1]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[1])->format('d-m-Y H:i') : '-',
                'leadtime_bongkar_2' => $diff_loc_bongkar_2,

                'gatein_lokasi_bongkar_3' => isset($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[2]) ? Carbon::parse($masterBongkar->pluck('gate_in_loc_bongkar')->toArray()[2])->format('d-m-Y H:i') : '-',
                'gateout_lokasi_bongkar_3' => isset($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[2]) ? Carbon::parse($masterBongkar->pluck('gate_out_loc_bongkar')->toArray()[2])->format('d-m-Y H:i') : '-',
                'leadtime_bongkar_3' => $diff_loc_bongkar_3,

                'tiba_di_garasi' =>  $value->finish_back_to_garage == null ? '-' : Carbon::parse($value->finish_back_to_garage)->format('d-m-Y H:i'),
                'revenue' =>  $revenuecost->where('token', $value->token)->first()->revenue ?? 0,
                'cost' =>  $revenuecost->where('token', $value->token)->first()->cost ?? 0,
                'margin' =>  $revenuecost->where('token', $value->token)->first()->revenue ?? 0 - $revenuecost->where('token', $value->token)->first()->cost ?? 0,
            ];
        }
        return view('new.MonitoringCheckpoint.report_excel', compact('data'));
    }
}
