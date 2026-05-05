<?php

namespace App\Http\Controllers;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonitoringVehicleDCController extends Controller
{
    private function getSite($site)
    {
        $data = DB::table('iv_site')
            ->where('site_name', $site)
            ->first();
        return $data;
    }

    public function index($site)
    {
        $siteMaster = $this->getSite($site);
        if (is_null($siteMaster)) {
            abort(404);
        }
        $data = DB::table('iv_gate_in_cargo')
            ->where('site_id', $siteMaster->id)
            ->whereNull('gate_out_at')
            ->get();
        $vehicles = DB::table('ex_master_vehicle')->get();
        return view('new.GateInDC.monitoring', compact('data', 'vehicles', 'siteMaster'));
    }
    public function listMonitoring($site_id)
    {
        $data = DB::table('iv_gate_in_cargo')
            ->select(
                'id',
                'vehicle_number as no_mobil',
                'driver_name as supir',
                'vehicle_type as type',
                'gate_in_at',
                'gate_out_at',
                'activity',
                'transporter_name',
                'principal_name'
            )
            ->where('site_id', $site_id)
            ->where(function ($q) {
                $q->whereNull('gate_out_at')
                    ->orWhereDate('gate_out_at', now()->toDateString());
            })
            ->get();
        dd($data);

        $mapped = $data->map(function ($item) {

            $start = strtotime($item->gate_in_at);

            if ($item->activity === 'INBOUND') {
                $status = 'unloading';
            } else {
                $status = 'loading';
            }
            if (!empty($item->gate_in_at) && !empty($item->gate_out_at)) {
                $status = 'done';
            }

            return [
                'id' => $item->id,
                'no_mobil' => $item->no_mobil,
                'supir' => $item->supir,
                'type' => $item->type,
                'status' => $status,
                'transporter_name' => $item->transporter_name,
                'principal_name' => $item->principal_name,
                'time' => $item->gate_in_at ? date('H:i', $start) : '-',
                'start_time' => $item->gate_in_at
                    ? \Carbon\Carbon::parse($item->gate_in_at)->toIso8601String()
                    : null
            ];
        });

        $inbound = $mapped->where('status', 'unloading')->values();
        $outbound = $mapped->where('status', 'loading')->values();
        $done = $mapped->where('status', 'done')->values();

        return response()->json([
            'inbound' => $inbound,
            'outbound' => $outbound,
            'doneList' => $done,
            'total' => $mapped->count(),
            'loading' => $outbound->count(),
            'unloading' => $inbound->count(),
            'done' => $done->count(),
        ]);
    }


    public function exportData(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $site_id    = $request->site_id;

        $data = DB::table('iv_gate_in_cargo')
            ->select(
                'vehicle_number as no_mobil',
                'driver_name as supir',
                'vehicle_type as type',
                'activity',
                'transporter_name',
                'principal_name',
                'gate_in_at',
                'gate_out_at'
            )
            ->where('site_id', $site_id)
            ->whereDate('gate_in_at', '>=', $start_date)
            ->whereDate('gate_in_at', '<=', $end_date)
            ->orderBy('gate_in_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    $item->no_mobil,
                    $item->supir,
                    $item->type,
                    $item->activity,
                    $item->transporter_name,
                    $item->principal_name,
                    $item->gate_in_at,
                    $item->gate_out_at ?? '-'
                ];
            });

        $fileName = "report_kendaraan_{$start_date}_to_{$end_date}.xlsx";

        return Excel::download(
            new class($data) implements FromCollection, WithHeadings {

                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return [
                        'No Mobil',
                        'Supir',
                        'Type',
                        'Activity',
                        'Transporter',
                        'Principal',
                        'Gate In',
                        'Gate Out'
                    ];
                }
            },
            $fileName
        );
    }
}
