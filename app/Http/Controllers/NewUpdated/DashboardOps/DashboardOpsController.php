<?php

namespace App\Http\Controllers\NewUpdated\DashboardOps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DashboardOpsController extends Controller
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

    public function searchData(Request $request)
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
        $chartsOccupancy = $this->chartsOccupancy($branch, $principal);
        $validate = 0;
        $total_hari = [];
        $sor = [];
        $palletCapacity = $this->getPalletCapacity($principal);
        $categories = [];
        for ($i = 1; $i <= 12; $i++) {
            $total_hari[$i] = Carbon::now()->month($i)->daysInMonth;
            $dataMonthly[] = $monthlyIn[$i][0]->total_pallet ?? 0;
            $dataMonthlyTruck[] = $monthlyTruck[$i][0]->total_vehicle ?? 0;
            $dataMonthlyOccupancy[$i] = $chartsOccupancy[$i] ?? 0;
            $dataMaxPalletCapacity[] = $palletCapacity;
            $validate += $monthlyIn[$i][0]->total_pallet ?? 0;
        }
        for ($i = 1; $i <= count($dataMonthlyOccupancy); $i++) {
            $sor[] = number_format($dataMonthlyOccupancy[$i] / $palletCapacity * 100, 0);
        }
        $categories  =
            [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ];
        $category = [];
        foreach ($categories as $key => $value) {
            $category[] = '<b>' . $value . '</b><br><br>SOR: <br>' . $sor[$key] . '%';
        }

        $dataMonthlyTruck = array_map('intval', $dataMonthlyTruck);
        $outbound = $this->searchOutbound($branch, $principal);
        $principal = $this->objectPrincipal($principal);
        $dataMonthlyOccupancy = array_values($dataMonthlyOccupancy);
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
                    'category' => $category,
                    // 'categories' => $categories,
                    'totalOrder' => $outbound['totalOrder'],
                    'truck_gate_in' => $outbound['truck_gate_in'],
                    'process_loading' => $outbound['process_loading'],
                    'finish_loading' => $outbound['finish_loading'],
                    'total_pallet_day' => $outbound['total_pallet_day'],
                    'total_pallet_month' => $outbound['total_pallet_month'],
                    'total_vehicle_month' => $outbound['total_vehicle_month'],
                    //=========================
                    'truck_todays' => $outbound['truck_todays']
                ]
            ]
        );
    }

    function searchOutbound($branch, $principal)
    {
        $totalOrder = $this->getOutboundJobToday($branch, $principal)->count();
        $truck_gate_in = $this->truckGateINTodayOutbound($branch, $principal);
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

        // $this->getMonthlyVehicle(count($total_vehicle_month),$branch, $principal);

        // dd($get_truck_month);


        $truck_today = $this->truckTodayOutbound($branch, $principal);
        $truck_todays = [];
        foreach ($truck_today as $key => $value) {
            $master_name = DB::table('iv_container_size')->where('id', $key)->value('size_name');
            $truck_todays[] = [
                'size_name' => $master_name,
                'total' => collect($value)->count(),
            ];
        }
        // dd($truck_todays);

        $data = [
            'totalOrder' => $totalOrder,
            'truck_gate_in' => $truck_gate_in,
            'process_loading' => $process_loading,
            'finish_loading' => $finish_loading,
            'total_pallet_day' => $total_pallet_day,
            'total_pallet_month' => $total_pallet_month,
            'total_vehicle_month' => $total_vehicle_month,
            'branch' => $branch,
            'principal' => $principal,
            //=========================
            'truck_todays' => $truck_todays
        ];

        return $data;
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
            ->select('id', 'confirmed_flag', 'eta')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->whereDate('eta', date('Y-m-d'))
            ->get();
        
        // dd($data);
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


        // dd($data);
        return $data;
    }

    private function monthlyTruck($branch, $principal)
    {
        $inboundID = DB::table('iv_inbound_job')
            ->select('id')
            ->whereYear('created_at', date('Y'))
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
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

        // dd($data);
        return $data;
    }

    private function cardOccupancy($branch, $principal)
    {
        $total_pallet = DB::table('iv_principal')
            ->where('id', $principal)
            ->value('pallet_capacity');
        $occupied_slot = DB::table('iv_occupancy_daily')
            ->orderBy('id', 'DESC')
            // ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            // ->whereYear('created_at', date('Y'))
            // ->where('qtya', '>', 0)
            // ->first()
            ->value('qty');
        $available_slot = (int)$total_pallet - (int)$occupied_slot;
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
        $occupancy = DB::table('iv_occupancy_daily')
            ->selectRaw("
                DATE_FORMAT(transaction_date, '%Y-%m') AS new_date, 
                YEAR(transaction_date) AS year, 
                MONTH(transaction_date) AS month,
                qty
            ")
            ->whereYear('transaction_date', date('Y'))
            ->where('principal_id', $principal)
            ->get()->groupBy('month');
        $data = [];
        foreach ($occupancy as $key => $value) {
            $data[$key] = (int)round($value->where('month', $key)->avg('qty'));
        }
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

    //=======================================================================
    private function getOutboundJobToday($branch, $principal)
    {
        $data = DB::table('iv_outbound_job')
            ->select('etd', 'principal_id', 'branch_id', 'confirmed_flag', 'allocated_date', 'id') // optimalisasikan query
            ->whereDate('etd', date('Y-m-d'))
            ->where('principal_id', $principal)
            ->where('branch_id', $branch)
            ->get();

        return $data;
    }


    private function truckGateINTodayOutbound($branch, $principal)
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
            ->whereNull('store_id')
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
            ->select('store_id', 'etd', 'outbound_id')
            ->whereIn('outbound_id', $outbound_id)
            ->whereDate('etd', date('Y-m-d'))
            ->groupBy('outbound_id')
            ->whereNotNull('store_id')
            ->get()->count();

        // dd($data);
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
        // dd($branch, $principal);

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
            // ->whereNotNull('size_id')
            ->whereIn('outbound_id', $outboundYear_id)

            // ->whereNotNull('location_confirm_at')
            ->groupBy('new_date')
            ->get();

        return $data;
    }


    private function truckTodayOutbound($branch, $principal)
    {
        $outbound_id = $this->getOutboundJobToday($branch, $principal)->pluck('id')->toArray();

        $data = DB::table('iv_outbound_despatch')
            ->whereIn('outbound_id', $outbound_id)
            ->whereNotNull('size_id')
            ->get()->groupBy('size_id');
        return $data;
    }

    public function getMonthTruck($m, $branch, $principal)
    {

        // $outbound_id = $this->getOutboundJobToday($branch, $principal)->pluck('id')->toArray();

        $m = $m + 1;

        $outids = DB::table('iv_outbound_job')
            ->whereMonth('created_at', date($m))
            ->whereYear('created_at', date('Y'))
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->pluck('id')->toArray();

        // dd($outids);

        $data = DB::table('iv_outbound_despatch')
            ->whereIn('outbound_id', $outids)
            // ->whereNotNull('size_id')
            ->get();
            // ->groupBy('size_id');
        // dd($data);

        $truck_todays = [];
        foreach ($data as $key => $value) {
            $master_name = DB::table('iv_container_size')->where('id', $key)->value('size_name');
            $truck_todays[] = [
                'size_name' => $master_name,
                'total' => collect($value)->count(),
            ];
        }


        return response()->json($truck_todays);
    }


    public function getDetailVehicle($bulan, $branch, $principal)
    {
        $bulan = $bulan + 1;
        $inboundID = DB::table('iv_inbound_job')
            ->select('id', 'created_at')
            ->where('branch_id', $branch)
            ->where('principal_id', $principal)
            ->whereMonth('created_at', date($bulan))
            ->whereYear('created_at', date('Y'))
            ->get()->pluck('id')->toArray();

        $data = DB::table('iv_inbound_vehicle')
            ->select('inbound_id', 'principal_id', 'size_id', 'created_at')
            ->whereIn('inbound_id', $inboundID)
            ->groupBy('inbound_id')
            ->get()->groupBy('size_id');
        $loop = [];
        foreach ($data as $key => $value) {
            $master = DB::table('iv_container_size')->where('id', $key)->value('size_name');
            $loop[] = [
                'size_name' => $master,
                'count' => $value->count()
            ];
        }
        return response()->json($loop);
    }

    public function getListOccupancy($branch,$principal,$start,$end){
        // dd('tess');


        
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();


            // dd($start);
            // dd($startDate);
            // Mendapatkan saldo awal dari tanggal sebelum tanggal start
            $balanceStart = DB::table('iv_occupancy_daily')
                ->where('principal_id', $principal)
                // ->whereDate('transaction_date', '<=', $start)
                ->whereBetween('transaction_date',[$start,$end])
                ->groupBy('transaction_date')
                ->orderBy('transaction_date', 'asc')
                ->first();

            // dd($balanceStart);
            $selected_balance = $balanceStart ? $balanceStart->qty : 0;

            $currentBalance = $selected_balance;
            $result = [];

            for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');

                $dailyData = DB::table('iv_occupancy_daily')
                    ->where('principal_id', $principal)
                    ->whereDate('transaction_date', $dateStr . " 00:00:00")
                    ->first();

                $in = $dailyData ? $dailyData->in : 0;
                $out = $dailyData ? $dailyData->out : 0;

                $currentBalance += $in - $out;

                $result[] = [
                    'transaction_date' => $date->format('d/m/Y'),
                    'in' => $in,
                    'out' => $out,
                    'stock' => $currentBalance,
                ];
            }

            // dd($result);

            return response()->json([
                'opening_balance' => $selected_balance,
                'data' => $result,
            ]);
        }
    
        
    
}
