<?php

namespace App\Http\Controllers\NewUpdated\DashboardOps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DashboardExportController extends Controller
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
        // $principal_id = DB::table('users_principal')
        //     ->where('user_id', Auth::user()->id)
        //     ->pluck('principal_id')->toArray();
        // $data = DB::table('iv_principal')
        //     ->orderBy('principal_name', 'ASC')
        //     ->whereIn('id', $principal_id)
        //     ->get();
        $data = ['ALL'];
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
        return view('new.DashboardExport.index', compact('branch', 'principal'));
    }

    public function searchData($branch, $principal)
    {
        $cardOccupancy = $this->cardOccupancy($branch, $principal);
        $palletCapacity = $this->getPalletCapacity($principal);

        $today = date('j');
        $daysInMonth = date('t');
        $dataDailyOccupancy = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $timestamp = strtotime(date('Y-m') . '-' . $day);
            $dayOfWeek = date('N', $timestamp); // 1 = Senin, ..., 7 = Minggu

            $value = 0;

            if ($dayOfWeek == 1) {
                $value = 1500 + rand(-50, 50); // Senin ±50
            } elseif ($dayOfWeek >= 2 && $dayOfWeek <= 5) {
                $step = (2500 - 1500) / 4;
                $base = 1500 + $step * ($dayOfWeek - 1);
                $value = round($base + rand(-30, 30)); // Naik perlahan, ada variasi
            } elseif ($dayOfWeek == 6) {
                $value = 1800 + rand(-50, 50); // Sabtu ±50
            } elseif ($dayOfWeek == 7) {
                $value = 1000 + rand(-100, 100); // Minggu ±100
            }

            // Hari setelah hari ini: kosongkan
            if ($day > $today) {
                $value = 0;
            }

            $dataDailyOccupancy[$day] = intval($value);
        }

        $category = range(1, $daysInMonth);

        return response()->json([
            'data' => [
                'principal' => $principal,
                'cardOccupancy' => $cardOccupancy,
                'dataMonthlyOccupancy' => array_values($dataDailyOccupancy),
                'dataMaxPalletCapacity' => array_fill(0, $daysInMonth, $palletCapacity),
                'palletCapacity' => $palletCapacity,
                'category' => $category,
            ]
        ]);
    }

    private function getPalletCapacity($principal)
    {
        $data = [
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500,
            3500
        ];
        return $data;
    }

    private function cardOccupancy($branch, $principal)
    {
        $total_pallet = 3500;
        $occupied_slot = DB::table('ex_stock_ledger')
            ->where('status_flag', 'Inbound')
            ->where('branch_id', $branch)
            ->count();

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
        $currentYear = date('Y');
        $currentMonth = date('m');

        $occupancy = DB::table('ex_stock_ledger')
            ->select(
                DB::raw("DAY(created_at) as day"),
                DB::raw("SUM(CASE WHEN status_flag = 'Book' THEN 1 ELSE 0 END) as total_book"),
                DB::raw("SUM(CASE WHEN status_flag = 'Inbound' THEN 1 ELSE 0 END) as total_inbound"),
                DB::raw("SUM(CASE WHEN status_flag = 'Book' THEN 1 ELSE 0 END) - SUM(CASE WHEN status_flag = 'Inbound' THEN 1 ELSE 0 END) as difference")
            )
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->groupBy(DB::raw("DAY(created_at)"))
            ->orderBy(DB::raw("DAY(created_at)"))
            ->get();

        $data = [];
        foreach ($occupancy as $item) {
            $data[(int) $item->day] = abs((int) $item->difference);
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

    public function getListOccupancy($branch, $principal, $start, $end)
    {
        // dd('tess');



        $startDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = \Carbon\Carbon::parse($end)->endOfDay();


        // dd($start);
        // dd($startDate);
        // Mendapatkan saldo awal dari tanggal sebelum tanggal start
        $balanceStart = DB::table('iv_occupancy_daily')
            ->where('principal_id', $principal)
            // ->whereDate('transaction_date', '<=', $start)
            ->whereBetween('transaction_date', [$start, $end])
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
