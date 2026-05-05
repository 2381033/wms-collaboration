<?php

namespace App\Http\Controllers\NewUpdated\DashboardOps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PermintaanEditOutboundEmail;
use App\Mail\KonfirmasiEditOutbound;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Arr;


class DashboardOutboundController extends Controller
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

        // dd($data);
        return $data;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->whereIn('id', $this->myBranch())
            ->get();

        return $branch;
    }

    public function searchOutbound(Request $request)
    {
        // dd($request->all());
        $branch    = $request->branch;
        $principal = $request->principal;
        $principal_name = $this->getPrincipal()->where('id', $principal)->pluck('principal_name')->toArray()[0];

        $totalOrder = $this->getOutboundJobToday($branch, $principal)->count();
        $truck_gate_in = $this->truckGateINToday($branch, $principal);
        $process_loading = $this->proccesLoadingToday($branch, $principal);
        $finish_loading =  $this->finishLoadingToday($branch, $principal);
        $total_pallet_day = $this->totalpalletToday($branch, $principal);
        $monthly_pallet = $this->monthlyPalletOutbound($branch, $principal)->groupBy('months');
        $monthly_vehicle = $this->monthlyVehicleOutbound($branch, $principal)->groupBy('months');

        $total_pallet_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $total_pallet_month[] = $monthly_pallet[$i][0]->row ?? 0;
        }

        $total_vehicle_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $total_vehicle_month[] = $monthly_vehicle[$i][0]->row ?? 0;
        }
        $truck_today = $this->truckToday($branch, $principal);
        $truck_todays = [];
        foreach ($truck_today as $key => $value) {
            $master_name = DB::table('iv_container_size')->where('id', $key)->value('size_name');
            $truck_todays[] = [
                'size_name' => $master_name,
                'total' => $value->count(),
            ];
        }
        return response()->json([
            'data' => [
                'totalOrder' => $totalOrder,
                'truck_gate_in' => $truck_gate_in,
                'process_loading' => $process_loading,
                'finish_loading' => $finish_loading,
                'total_pallet_day' => $total_pallet_day,
                'total_pallet_month' => $total_pallet_month,
                'total_vehicle_month' => $total_vehicle_month,
                'branch' => $branch,
                'principal' => $principal,
                'principal_name' => $principal_name,
                //=========================
                'truck_todays' => $truck_todays

            ]
        ]);
    }

    private function getOutboundJobToday($branch, $principal)
    {
        $data = DB::table('iv_outbound_job')
            ->select('created_at', 'principal_id', 'branch_id', 'confirmed_flag', 'allocated_date', 'id') // optimalisasikan query
            ->whereDate('created_at', date('Y-m-d'))
            ->where('principal_id', $principal)
            ->where('branch_id', $branch)
            ->get();

        return $data;
    }


    private function truckGateINToday($branch, $principal)
    {
        $allocated = $this->getOutboundJobToday($branch, $principal)->whereNull('allocated_date')->pluck('id')->toArray();
        // dd($allocated);
        $data =  DB::table('iv_outbound_order')
            ->select('created_at', 'outbound_id')
            ->whereDate('created_at', date('Y-m-d'))
            // ->whereIn('outbound_id', $outbound_id)
            ->whereIn('outbound_id', $allocated)
            ->groupBy('outbound_id')
            ->get()->count();
        // $data =  ABS($data - $this->proccesLoadingToday($outbound_id));
        // dd($data);
        return $data;
    }

    private function proccesLoadingToday($branch, $principal)
    {
        $outbound_id = $this->getOutboundJobToday($branch, $principal)->where('confirmed_flag', 'No')->pluck('id')->toArray();
        $data =  DB::table('iv_outbound_batch')
            ->select('created_at', 'outbound_id', 'scan_location_at')
            ->whereIn('outbound_id', $outbound_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->get()->groupBy('outbound_id');


        $despatch = DB::table('iv_outbound_despatch') // sudah cetak despatch
            ->select('store_id', 'outbound_id')
            ->whereIn('outbound_id', $outbound_id)
            ->whereNotNull('store_id')
            ->groupBy('outbound_id')
            ->pluck('outbound_id')->toArray();
        // $data =  ABS($data - $this->proccesLoadingToday($outbound_id));
        // dd($despatch);
        // return $data;


        $sum = [];
        foreach ($data as $key => $value) {
            $sum[] = $value->whereNotNull('scan_location_at')->pluck('outbound_id')->toArray(); //belum cetak despatch
        }
        // dd($sum);

        // untuk mendapatkan unik dari array sum
        $unique = [];
        foreach ($sum as $key => $value) {
            $unique[] = array_unique($value); // belum cetak dispatch
            // dd(array_unique($sum));

            // dd($key, $value);
        }

        $unique = Arr::flatten($unique);
        $unique = array_merge(array_diff($despatch, $unique), array_diff($unique, $despatch));

        // $unique = array_ass
        // dd($despatch, $unique);
        $data = collect($unique)->count();


        return $data;
    }

    private function finishLoadingToday($branch, $principal)
    {
        $outbound_id = $this->getOutboundJobToday($branch, $principal)->pluck('id')->toArray();
        $data = DB::table('iv_outbound_despatch')
            ->select('store_id', 'created_at', 'outbound_id')
            ->whereIn('outbound_id', $outbound_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->groupBy('outbound_id')
            ->whereNotNull('store_id')
            ->get()->count();
        // $data =  ABS($data - $this->proccesLoadingToday($outbound_id));


        return $data;
    }

    private function totalpalletToday($branch, $principal)
    {

        $outbound_id = $this->getOutboundJobToday($branch, $principal)->where('confirmed_flag', 'Yes')->pluck('id')->toArray();


        // dd($outbound_id);
        $data = DB::table('iv_outbound_batch')
            ->select('outbound_id')
            ->whereIn('outbound_id', $outbound_id)
            ->count();
        // dd($data);


        return $data;
    }

    private function monthlyPalletOutbound($branch, $principal)
    {

        // Job yang udah pasti di confirm siap masuk stok
        $outboundYear_id = DB::table('iv_outbound_job')
            ->select('id')
            ->whereYear('created_at', date('Y'))
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->where('confirmed_flag', 'Yes')
            ->get()->pluck('id')->toArray();

        // dd($outboundYear);
        $data = DB::table('iv_outbound_batch')
            ->selectRaw("
                    COUNT(id) AS row, 
                    DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
                    YEAR(created_at) AS years, 
                    MONTH(created_at) AS months
                ")
            ->whereIn('outbound_id', $outboundYear_id)
            ->groupBy('new_date')
            ->get();

        // dd($data);

        return $data;
    }

    private function monthlyVehicleOutbound($branch, $principal)
    {

        $outboundYear_id = DB::table('iv_outbound_job')
            ->select('id')
            ->whereYear('created_at', date('Y'))
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->where('confirmed_flag', 'Yes')
            ->get()->pluck('id')->toArray();

        $data = DB::table('iv_outbound_despatch')
            ->selectRaw("
                    COUNT(id) AS row, 
                    DATE_FORMAT(created_at, '%Y-%m') AS new_date, 
                    YEAR(created_at) AS years, 
                    MONTH(created_at) AS months
            ")
            ->whereIn('outbound_id', $outboundYear_id)

            // ->whereNotNull('location_confirm_at')
            ->groupBy('new_date')
            ->get();

        return $data;
    }


    private function truckToday($branch, $principal)
    {
        $outbound_id = $this->getOutboundJobToday($branch, $principal)->pluck('id')->toArray();

        $data = DB::table('iv_outbound_despatch')
            ->whereIn('outbound_id', $outbound_id)
            ->whereNotNull('size_id')
            ->get()->groupBy('size_id');



        // dd($data);
        return $data;
    }


    public function index()
    {
        $branch    = $this->getBranch();
        $principal = $this->getPrincipal();
        return view('new.DashboardOps.imam', compact('branch', 'principal'));
    }
}
