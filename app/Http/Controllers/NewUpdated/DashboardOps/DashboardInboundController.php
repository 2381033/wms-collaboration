<?php

namespace App\Http\Controllers\NewUpdated\DashboardOps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardInboundController extends Controller
{
    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function getPrincipal()
    {
        $principal_id = DB::table('users_principal')
            ->where('user_id', Auth::user()->id)
            ->pluck('principal_id')->toArray();
        $data = DB::table('iv_principal')
            ->orderBy('principal_name', 'ASC')
            ->whereIn('id', $principal_id)
            ->get();

        return $data;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->whereIn('id', $this->myBranch())
            ->get();

        return $branch;
    }

    public function index()
    {
        $branch    = $this->getBranch();
        $principal = $this->getPrincipal();
        return view('new.DashboardOps.index', compact('branch', 'principal'));
    }

    public function zaka()
    {
        $branch    = $this->getBranch();
        $principal = $this->getPrincipal();

        $vehicle = DB::table('iv_container_size')
            ->orderBy('size_name', 'ASC')
            ->whereIn(
                'size_name',
                [
                    'Tiny (2 CBM)',
                    'Small (6 CBM)',
                    'Medium (12 CBM)',
                    'Large (20 CBM)',
                    'Fuso (22 CBM)',
                    'Builtup (45 CBM)',
                    '20 Feet',
                    '40 Feet',
                ]
            )
            ->get();
        return view('new.DashboardOps.zaka', compact('branch', 'principal', 'vehicle'));
    }

    public function searchInbound(Request $request)
    {
        $branch    = $request->branch;
        $principal = $request->principal;
        $totalOrderToday = $this->totalOrderToday($branch, $principal)->count();
        $truckGateInToday = $this->truckGateInToday($branch, $principal);
        $ProcessUnloadingToday = $this->ProcessUnloadingToday($branch, $principal)->count();
        $finishUnloadingToday = $this->finishUnloadingToday($branch, $principal);
        $totalPalletReceiving = $this->totalPalletReceiving($branch, $principal);
        $monthlyIn = $this->monthlyInbound($branch, $principal);
        $monthlyTruck = $this->monthlyTruck($branch, $principal)->groupBy('month');
        $dataMonthly = [];
        $dataMonthlyTruck = [];
        $dataMonthlyOccupancy = [];
        $dataMaxPalletCapacity = [];
        $truckToday = $this->truckToday($branch, $principal);
        $cardOccupancy = $this->cardOccupancy($branch, $principal);
        $jobConfirmedToday = $this->jobConfirmedToday($branch, $principal);
        $chartsOccupancy = $this->chartsOccupancy($branch, $principal)->groupBy('month');
        $validate = 0;
        $total_hari = [];
        $sor = [];
        $palletCapacity = $this->getPalletCapacity($principal);
        for ($i = 1; $i <= 12; $i++) {
            $total_hari[] = Carbon::now()->month($i)->daysInMonth;
            $dataMonthly[] = $monthlyIn[$i][0]->total_pallet ?? 0;
            $dataMonthlyTruck[] = $monthlyTruck[$i][0]->total_vehicle ?? 0;
            $dataMonthlyOccupancy[] = $chartsOccupancy[$i][0]->total_pallet ?? 0;
            $dataMaxPalletCapacity[] = $palletCapacity;
            $validate += $monthlyIn[$i][0]->total_pallet ?? 0;
        }
        foreach ($dataMonthlyOccupancy as $key => $value) {
            $sor[] = (int)round($value / $palletCapacity);
        }
        $dataMonthlyOccupancy = array_map(function ($v1, $v2) {
            return (int)round($v1 / $v2);
        }, $dataMonthlyOccupancy, $total_hari);
        // $categories = [];
        // for ($i = 1; 1 <= 12; $i++) {
        //     $categories[] = [
        //         'Jan<br>aa',
        //         'Feb<br>abc',
        //         'Mar',
        //         'Apr',
        //         'May',
        //         'Jun',
        //         'Jul',
        //         'Aug',
        //         'Sep',
        //         'Oct',
        //         'Nov',
        //         'Dec'
        //     ];
        // }
        // dd($categories);

        $dataMonthlyTruck = array_map('intval', $dataMonthlyTruck);
        $principal = $this->objectPrincipal($principal);
        return response()->json(
            [
                'data' => [
                    'totalOrderToday' => $totalOrderToday,
                    'truckGateInToday' => $truckGateInToday,
                    'ProcessUnloadingToday' => $ProcessUnloadingToday,
                    'finishUnloadingToday' => $finishUnloadingToday,
                    'totalPalletReceiving' => $totalPalletReceiving,
                    'dataMonthly' => $dataMonthly,
                    'principal' => $principal,
                    'truckToday' => $truckToday,
                    'dataMonthlyTruck' => $dataMonthlyTruck,
                    'cardOccupancy' => $cardOccupancy,
                    'jobConfirmedToday' => $jobConfirmedToday,
                    'countData'  => $validate,
                    'dataMonthlyOccupancy'  => $dataMonthlyOccupancy,
                    'dataMaxPalletCapacity'  => $dataMaxPalletCapacity,
                    'palletCapacity' => $palletCapacity,
                    // 'categories' => $categories,
                ]
            ]
        );
    }

    private function getPalletCapacity($principal)
    {
        $data = DB::table('iv_principal')
            ->where('id', $principal)->value('pallet_capacity');
        return $data;
    }

    private function objectPrincipal($principal)
    {
        $data = DB::table('iv_principal')
            ->where('id', $principal)
            ->value('principal_name');
        return $data;
    }

    private function totalOrderToday($branch, $principal)
    {
        $data = DB::table('iv_inbound_job')
            ->select('id', 'confirmed_flag', 'ata')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->whereDate('created_at', date('Y-m-d'))
            ->get();
        return $data;
    }

    private function inboundIDToday($branch, $principal)
    {
        $data = $this->totalOrderToday($branch, $principal)->pluck('id')->toArray();
        $data = array_unique($data);
        return $data;
    }

    private function truckGateInToday($branch, $principal)
    {
        $inboundID = $this->inboundIDToday($branch, $principal);

        $data = DB::table('iv_inbound_vehicle')
            ->select('inbound_id', 'principal_id', 'created_at')
            ->whereDate('created_at', date('Y-m-d'))
            ->where('principal_id', $principal)
            ->whereIn('inbound_id', $inboundID)
            ->where('confirmed_flag', 'No')
            ->groupBy('inbound_id')
            ->get()->count();
        return $data;
    }

    private function truckToday($branch, $principal)
    {
        $inboundID = $this->inboundIDToday($branch, $principal);

        $master = DB::table('iv_inbound_vehicle')
            ->select('size_id')
            ->where('principal_id', $principal)
            ->whereIn('inbound_id', $inboundID)
            ->groupBy('inbound_id')
            ->get()->groupBy('size_id');
        $data = [];
        foreach ($master as $key => $value) {
            $size_name = DB::table('iv_container_size')->where('id', $key)->value('size_name');
            $data[] = [
                'size_name' => $size_name,
                'count' => $value->count(),
            ];
        }
        return $data;
    }

    private function ProcessUnloadingToday($branch, $principal)
    {
        $inboundID = $this->totalOrderToday($branch, $principal)
            ->where('confirmed_flag', 'No')
            ->whereNull('ata')
            ->pluck('id')->toArray();

        $data = DB::table('iv_inbound_per_pallet')
            ->select('inbound_id', 'created_at')
            ->whereDate('created_at', date('Y-m-d'))
            ->whereIn('inbound_id', $inboundID)
            ->groupBy('inbound_id')
            ->get();
        return $data;
    }

    private function monthlyInbound($branch, $principal)
    {
        // $inboundID = DB::table('iv_inbound_job')
        //     ->select('id')
        //     ->whereYear('created_at', date('Y'))
        //     ->where('branch_id', $branch)
        //     ->where('principal_id', $principal)
        //     ->where('confirmed_flag', 'Yes')
        //     ->get()->pluck('id')->toArray();
        // $data = DB::table('iv_inbound_per_pallet')
        //     ->selectRaw("
        //             SUM(total_pallet) AS total_pallet, 
        //             DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
        //             YEAR(created_at) AS year, 
        //             MONTH(created_at) AS month
        //         ")
        //     ->whereIn('inbound_id', $inboundID)
        //     ->groupBy('new_date')
        //     ->get();
        $data = DB::table('iv_stock_transaction')
            ->selectRaw("
            COUNT(id) AS total_pallet, 
            DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
            YEAR(created_at) AS year, 
            MONTH(created_at) AS month
        ")
            ->whereYear('created_at', date('Y'))
            ->where('job_type', 'IMP')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->groupBy('new_date')
            ->get()->groupBy('month');
        return $data;
    }

    private function monthlyTruck($branch, $principal)
    {
        $inboundID = DB::table('iv_inbound_job')
            ->select('id')
            ->whereYear('created_at', date('Y'))
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->where('confirmed_flag', 'Yes')
            ->get()->pluck('id')->toArray();

        $data = DB::table('iv_inbound_vehicle')
            ->selectRaw("
                    COUNT(id) AS total_vehicle, 
                    DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
                    YEAR(created_at) AS year, 
                    MONTH(created_at) AS month
                ")
            ->whereIn('inbound_id', $inboundID)
            ->groupBy('new_date')
            ->get();
        return $data;
    }

    private function cardOccupancy($branch, $principal)
    {
        $total_pallet = DB::table('iv_principal')
            ->where('id', $principal)
            ->value('pallet_capacity');
        $occupied_slot = DB::table('iv_stock_ledger')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('qtya', '>', 0)
            ->sum('qtya');
        $available_slot = (int)$total_pallet - $occupied_slot;
        if ($available_slot < 0) {
            $available_slot = 0;
        } else {
            $available_slot = $available_slot;
        }
        $data = [
            'total_pallet' => $total_pallet,
            'occupied_slot' => (int)$occupied_slot,
            'available_slot' => $available_slot,
        ];
        return $data;
    }

    private function chartsOccupancy($branch, $principal)
    {
        $data = DB::table('iv_stock_transaction')
            ->selectRaw("
                COUNT(id) AS total_pallet, 
                DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
                YEAR(created_at) AS year, 
                MONTH(created_at) AS month
            ")
            ->whereYear('created_at', date('Y'))
            ->where('job_type', 'IMP')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->groupBy('new_date')
            ->get();
        return $data;
    }

    private function finishUnloadingToday($branch, $principal)
    {
        $inboundID = $this->totalOrderToday($branch, $principal)
            ->where('confirmed_flag', 'No')
            ->whereNotNull('ata')
            ->pluck('id')->toArray();
        $data = DB::table('iv_inbound_per_pallet')
            ->whereIn('inbound_id', $inboundID)
            ->where('scan_pallet_tag', 'Yes')
            ->groupBy('inbound_id')
            ->get()->count();
        return $data;
    }

    private function totalPalletReceiving($branch, $principal)
    {
        $inboundID = $this->totalOrderToday($branch, $principal)->where('confirmed_flag', 'Yes')->pluck('id')->toArray();
        $data = DB::table('iv_inbound_batch')
            ->select('id')
            ->whereIn('inbound_id', $inboundID)
            ->count();
        return $data;
    }

    private function jobConfirmedToday($branch, $principal)
    {
        $data = $this->totalOrderToday($branch, $principal)->where('confirmed_flag', 'Yes')->count();
        return $data;
    }
}
