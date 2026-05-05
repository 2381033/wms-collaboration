<?php

namespace App\Http\Controllers\Transaction\Export;

use App\Exports\StockLedgerReportExport;
use App\Http\Controllers\Controller;
use BaconQrCode\Encoder\MaskUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LCLPerformanceController extends Controller
{
    public function getListGateIn($branch, $tgl_mulai, $tgl_selesai)
    {
        $branchMap = ['jkt' => 1, 'srg' => 2, 'sub' => 3];
        $branch_id = $branchMap[$branch] ?? 0;
        $latestInbound = DB::table('ex_inbound_header as a')
            ->select('a.vehicle_no', 'a.qty_cargo', 'a.shipper_id')
            ->where('a.branch_id', $branch_id)
            ->whereBetween(DB::raw('DATE(a.created_at)'), [$tgl_mulai, $tgl_selesai])
            ->whereIn('a.id', function ($query) use ($branch_id) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('ex_inbound_header')
                    ->where('branch_id', $branch_id)
                    ->groupBy('vehicle_no');
            });

        $data = DB::table('ex_gate_in_cargo as eg')
            ->select(
                'eg.id',
                'eg.driver_name',
                'eg.vehicle_number',
                'eg.vehicle_type',
                'eg.transporter_name',
                'ms.shipper_name',
                'eg.created_at',
                'eg.confirmed_flag',
                'eg.id_visitor',
                'eh.qty_cargo',
                'eo.created_at as jam_keluar'
            )
            // Join latest inbound (per vehicle) — cocok karena latestInbound sudah dibatasi branch
            ->leftJoinSub($latestInbound, 'eh', function ($join) {
                // join on vehicle number; gunakan TRIM/UPPER jika data tidak konsisten
                $join->on(DB::raw('TRIM(UPPER(eg.vehicle_number))'), '=', DB::raw('TRIM(UPPER(eh.vehicle_no))'));
            })
            ->leftJoin('mt_shipper as ms', 'eh.shipper_id', '=', 'ms.id')
            ->leftJoin('ex_gate_out_cargo as eo', 'eg.id', '=', 'eo.id_gate_in')
            // HANYA ambil eg yang punya inbound di branch ini:
            ->whereExists(function ($q) use ($branch_id, $tgl_mulai, $tgl_selesai) {
                $q->select(DB::raw(1))
                    ->from('ex_inbound_header as ih')
                    ->whereRaw('TRIM(UPPER(ih.vehicle_no)) = TRIM(UPPER(eg.vehicle_number))')
                    ->where('ih.branch_id', $branch_id)
                    ->whereBetween(DB::raw('DATE(ih.created_at)'), [$tgl_mulai, $tgl_selesai]);
            })
            ->whereBetween(DB::raw('DATE(eg.created_at)'), [$tgl_mulai, $tgl_selesai])
            ->whereNotNull('eg.id_visitor')
            ->orderByDesc('eg.confirmed_flag')
            ->get()
            ->map(function ($item) {
                $item->created_at = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i');
                $item->jam_keluar = $item->jam_keluar
                    ? \Carbon\Carbon::parse($item->jam_keluar)->format('d-m-Y H:i')
                    : '-';

                if (!empty($item->qty_cargo)) {
                    $qtys = explode('|', $item->qty_cargo);
                    $item->qty_total = array_sum(array_map('intval', $qtys));
                } else {
                    $item->qty_total = 0;
                }

                return $item;
            });

        return response()->json(['data' => $data]);
    }

    public function listGateIn()
    {
        return view("transaction.export.gate-in");
    }
    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->pluck('branch_id')
            ->toArray();

        return $branch;
    }
    // private function getShipper()
    // {
    //     $data = DB::table('mt_shipper')
    //         ->whereIn('branch_id', $this->myBranch())
    //         ->orderBy('shipper_name', 'ASC')
    //         ->where('active', 'Yes')
    //         ->get();

    //     return $data;
    // }

    // private function getLocation()
    // {
    //     $data = DB::table('ex_location')
    //         ->whereIn('branch_id', $this->myBranch())
    //         ->orderBy('location_code', 'ASC')
    //         ->where('active', 'Yes')
    //         ->get();

    //     return $data;
    // }
    public function index()
    {
        return view("transaction.export.lcl-performance");
    }

    public function post($tgl_mulai, $tgl_selesai)
    {
        $master = DB::table('ex_inbound_header as header')
            ->join("mt_consignee as consignee", "header.consignee_id", "consignee.id")
            ->join("mt_shipper as shipper", "header.shipper_id", "shipper.id")
            ->join("mt_forwarder as forwarder", "header.forwarder_id", "forwarder.id")
            ->whereDate('job_date', '>=', $tgl_mulai)
            ->whereDate('job_date', '<=', $tgl_selesai)
            ->select('header.*', 'shipper.shipper_name', 'forwarder.forwarder_name')
            ->orderBy('status_flag', 'ASC')
            ->whereIn('header.branch_id', $this->myBranch())
            ->distinct()
            ->get();
        $job_id = $master->pluck('id')->toArray();
        $arrival = DB::table('ex_gate_in_cargo')
            ->select('created_at', 'vehicle_number')
            ->whereDate('created_at', '>=', $tgl_mulai)
            ->whereDate('created_at', '<=', $tgl_selesai)
            ->get();
        $detail = DB::table('ex_inbound_detail')
            ->whereIn('job_id', $job_id)
            ->whereDate('created_at', '>=', $tgl_mulai)
            ->whereDate('created_at', '<=', $tgl_selesai)
            ->get();
        $bongkar = DB::table('ex_inbound_foto_cargo')
            ->select('created_at', 'job_id')
            ->whereIn('job_id', $job_id)
            ->whereDate('created_at', '>=', $tgl_mulai)
            ->whereDate('created_at', '<=', $tgl_selesai)
            // ->where('job_id', '89198')
            ->get();
        $summary = $detail->groupBy('job_id')->map(function ($items) {
            return [
                'job_id' => $items->first()->job_id,
                'total_quantity' => $items->sum('quantity'),
                'unit' => $items->first()->unit, // ambil satu unit saja
                'total_pallet' => $items->pluck('pallet_id')->unique()->count(),
            ];
        })->values();
        $report = $master->map(function ($item) use ($arrival, $summary, $bongkar) {
            $arrivalData = $arrival->firstWhere('vehicle_number', $item->vehicle_no);
            $summaryData = $summary->firstWhere('job_id', $item->id);
            $bongkaran = $bongkar->firstWhere('job_id', $item->id);
            return [
                'customer' => $item->forwarder_name,
                'shipper' => $item->shipper_name,
                'nopol' => optional($arrivalData)->vehicle_number ? $arrivalData->vehicle_number : '-',
                'arrival_date' => optional($arrivalData)->created_at ? \Carbon\Carbon::parse($arrivalData->created_at)->format('d-M-y') : '-',
                'arrival_time' => optional($arrivalData)->created_at ? \Carbon\Carbon::parse($arrivalData->created_at)->format('H:i') : '-',
                'qty' => collect(explode('|', $item->qty_cargo))->sum(function ($val) {
                    return is_numeric($val) ? (int)$val : 0;
                }),
                'unit' => $summaryData['unit'] ?? '-',
                'sj_from_ao' => is_null($item->pic_name) ? '-' : \Carbon\Carbon::parse($item->created_at)->format('H:i'),
                'checker' => is_null($item->pic_name) ? '-' :  $item->pic_name,
                'start_bongkar' => optional($bongkaran)->created_at ? \Carbon\Carbon::parse($bongkaran->created_at)->format('H:i') : '-',
                'striping_finish' => '-',
                'new_qty' => $summaryData['total_quantity'] ?? 0,
                'total_pallet' => $summaryData['total_pallet'] ?? 0,
                'status_flag' => $item->status_flag == 'Open' ? 'ON PROGRESS' : 'DONE'
            ];
        });
        return response()->json([
            'data' => $report
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $principal_id = $request->principal_id;
        $branch_id = $request->branch_id;
        $reportType = $request->reportType;

        $time = \Carbon\Carbon::now()->format("dmy.His");

        $principal = \App\Models\Master\Principal::find($principal_id);

        if (!empty($request->group_code_from) && !empty($request->group_code_to)) {
            $group_from = $request->group_code_from;
            $group_to = $request->group_code_to;
        } else {
            if (!empty($request->group_code_from) && empty($request->group_code_to)) {
                $group_from = $request->group_code_from;
                $group_to = "zzzzzzzzzz";
            } else if (empty($request->group_code_from) && !empty($request->group_code_to)) {
                $group_from = "";
                $group_to = $request->group_code_to;
            } else {
                $group_from = "";
                $group_to = "zzzzzzzzzz";
            }
        }

        if (!empty($request->brand_code_from) && !empty($request->brand_code_to)) {
            $brand_from = $request->brand_code_from;
            $brand_to = $request->brand_code_to;
        } else {
            if (!empty($request->brand_code_from) && empty($request->brand_code_to)) {
                $brand_from = $request->brand_code_from;
                $brand_to = "zzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->brand_code_to)) {
                $brand_from = "";
                $brand_to = $request->brand_code_to;
            } else {
                $brand_from = "";
                $brand_to = "zzzzzzzzzz";
            }
        }

        if (!empty($request->product_from) && !empty($request->product_to)) {
            $product_from = $request->product_from;
            $product_to = $request->product_to;
        } else {
            if (!empty($request->product_from) && empty($request->product_to)) {
                $product_from = $request->product_from;
                $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->product_to)) {
                $product_from = "";
                $product_to = $request->product_to;
            } else {
                $product_from = "";
                $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

        if (is_numeric($request->product_from)) {
            $product_from = (int)$product_from;
        } else {
            $product_from = $product_from;
        }
        if (is_numeric($request->product_to)) {
            $product_to = (int)$product_to;
        } else {
            $product_to = $product_to;
        }

        $area_id = "%";

        $site_list = [];
        if (!empty($request->site_id) && isset($request->site_id)) {
            $site_list[] = $request->site_id;
        } else {
            foreach ($user->site->all() as $value) {
                $site_list[] = $value->id;
            }
        }

        if (!empty($request->area_id) && isset($request->area_id)) {
            $area_id = $request->area_id;
        }

        if (!empty($request->location_from) && !empty($request->location_to)) {
            $location_from = $request->location_from;
            $location_to = $request->location_to;
        } else {
            if (!empty($request->location_from) && empty($request->location_to)) {
                $location_from = $request->location_from;
                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->location_to)) {
                $location_from = "";
                $location_to = $request->location_to;
            } else {
                $location_from = "";
                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

        $exp_date_from = "1990-01-01";
        $exp_date_to = "2999-12-31";
        if (!empty($request->exp_date_from) && !empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = $request->exp_date_to;
        } else if (!empty($request->exp_date_from) && empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = "2999-12-31";
        }

        $exp_date_from = date("Y-m-d", strtotime($exp_date_from));
        $exp_date_to = date("Y-m-d", strtotime($exp_date_to));

        $filename = "$principal->short_name-$reportType-$time.xlsx";


        return Excel::download(new StockLedgerReportExport($reportType, $branch_id, $principal_id, $group_from, $group_to, $brand_from, $brand_to, $product_from, $product_to, $exp_date_from, $exp_date_to, $site_list, $area_id, $location_from, $location_to), $filename);
    }
}
