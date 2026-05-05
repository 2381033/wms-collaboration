<?php

namespace App\Http\Controllers\Transaction\Export;

use App\Exports\StockLedgerReportExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LedgerController extends Controller
{
    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }
    private function getShipper()
    {
        $data = DB::table('mt_shipper')
            ->whereIn('branch_id', $this->myBranch())
            ->orderBy('shipper_name', 'ASC')
            ->where('active', 'Yes')
            ->get();

        return $data;
    }

    private function getForwarder()
    {
        $data = DB::table('mt_forwarder')
            ->whereIn('branch_id', $this->myBranch())
            ->orderBy('forwarder_name', 'ASC')
            ->where('active', 'Yes')
            ->get();

        return $data;
    }

    private function getLocation()
    {
        $data = DB::table('ex_location')
            ->whereIn('branch_id', $this->myBranch())
            ->orderBy('location_code', 'ASC')
            ->where('active', 'Yes')
            ->get();

        return $data;
    }
    public function index(Request $request)
    {
        $shipper = $this->getShipper();
        $location = $this->getLocation();
        $forwarder = $this->getForwarder();
        $data = $this->occupancy($request);
        // $badge = $this->badgeOccupancy();
        // $chartData = $this->chartsOccupancy();
        return view("transaction.export.stock-ledger", compact('shipper', 'location', 'forwarder', 'data'));
    }

    public function loadCharts($branch_id)
    {
        $branchId = $branch_id;
        $badgeOccupancy = $this->badgeOccupancy($branchId);
        $chartsOccupancy = $this->chartsOccupancy($branchId);
        return response()->json([
            'badge' => $badgeOccupancy,
            'charts' => $chartsOccupancy
        ]);
    }

    public function report(Request $request)
    {
        $reportType = $request->reportType;
        $file = $request->fileType;
        $branchId = $request->branch_id;
        $searchBy = $request->searchBy;
        // Ambil semua stok awal & urut berdasarkan tanggal receiving (job_date) ascending
        $stockQuery = DB::table('ex_stock_ledger')
            ->where('branch_id', $branchId)
            ->where('status_flag', 'Inbound')
            ->orderBy('job_date', 'DESC');

        // Filter berdasarkan SearchBy (Shipper / Forwarder)
        if ($searchBy === 'shipper' && isset($request->shipper_id)) {
            if (!in_array('ALL', $request->shipper_id)) {
                $stockQuery->whereIn('shipper_id', $request->shipper_id);
            }
        }

        if ($searchBy === 'forwarder' && isset($request->forwarder_id)) {
            if (!in_array('ALL', $request->forwarder_id)) {
                $stockQuery->whereIn('forwarder_id', $request->forwarder_id);
            }
        }

        // Filter lokasi jika ada
        if (isset($request->location_code) && !in_array('ALL', $request->location_code)) {
            $stockQuery->whereIn('location_id', $request->location_code);
        }

        // Ambil data stok
        $stock = $stockQuery->get();

        // Ambil nama shipper & forwarder untuk mapping
        $shipper = DB::table('mt_shipper')
            ->select('id', 'shipper_name')
            ->pluck('shipper_name', 'id');

        $forwarder = DB::table('mt_forwarder')
            ->select('id', 'forwarder_name')
            ->pluck('forwarder_name', 'id');

        $data = [];

        // Format data sesuai reportType
        if ($reportType === "detail") {
            foreach ($stock as $value) {
                $data[] = [
                    'shipper_name'   => $shipper[$value->shipper_id] ?? '-',
                    'customer_name'  => $forwarder[$value->forwarder_id] ?? '-',
                    'receiving'      => Carbon::parse($value->job_date)->format('d-m-Y'),
                    'po_number'      => $value->po_number,
                    'peb_no'         => $value->peb_no,
                    'aju_no'         => $value->aju_no,
                    'destination'    => $value->destination,
                    'quantity'       => $value->quantity,
                    'pallet_id'      => $value->pallet_id,
                    'cbm'            => $value->cbm,
                    'total_pallet'   => $value->total_pallet,
                    'location_code'  => $value->location_code ?? '-',
                ];
            }
        } else {
            // Summary → tetap urut by shipper_name untuk tampilan summary
            foreach ($stock->groupBy(fn($item) => $item->shipper_id . '-' . $item->forwarder_id) as $key => $grouped) {
                [$shipperId, $forwarderId] = explode('-', $key);

                $data[] = [
                    'shipper_name'   => $shipper[$shipperId] ?? '-',
                    'forwarder_name' => $forwarder[$forwarderId] ?? '-',
                    'quantity'       => $grouped->sum('quantity'),
                    'cbm'            => number_format($grouped->sum('cbm'), 2),
                ];
            }
            $data = collect($data)->sortBy('shipper_name')->values();
        }

        return response()->json([
            'data' => array_values(collect($data)->toArray()),
            'reportType' => $reportType,
        ]);
    }

    private function occupancy(Request $request)
    {
        $branch = Auth::user()->branch->first();
        $capacity = 0;
        if ($branch->id == 1) {
            $capacity = 3482;
        } else if ($branch->id == 3) {
            $capacity = 870;
        }

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $start = Carbon::createFromDate($year, $month, 1);
        $end = $start->copy()->endOfMonth();

        $transactions = DB::table('ex_stock_transaction')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                'job_type',
                'peb_no as peb',
                'pallet_id'
            )
            ->where('branch_id', $branch->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $stock = 0;
        $rows = [];
        $totalIn = 0;
        $totalOut = 0;
        $lastStock = 0;

        $lastTransactionDate = $transactions->max('tanggal');

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $tanggal = $date->toDateString();
            if ($lastTransactionDate && $tanggal > $lastTransactionDate) {
                $rows[] = [
                    'tanggal' => $date->format('d-M'),
                    'kapasitas' => $capacity,
                    'in' => 0,
                    'out' => 0,
                    'stock' => 0,
                    'sor' => '0%',
                ];
                continue;
            }

            $todayData = $transactions->where('tanggal', $tanggal);

            $inCount = $todayData
                ->where('job_type', 'in')
                ->unique(fn($item) => $item->peb . '-' . $item->pallet_id)
                ->count();

            $outCount = $todayData
                ->where('job_type', 'out')
                ->unique(fn($item) => $item->peb . '-' . $item->pallet_id)
                ->count();

            $stock += ($inCount - $outCount);
            if ($stock < 0) $stock = 0;

            $sor = $capacity > 0 ? round(($stock / $capacity) * 100, 2) : 0;

            $rows[] = [
                'tanggal' => $date->format('d-M'),
                'kapasitas' => $capacity,
                'in' => $inCount,
                'out' => $outCount,
                'stock' => $stock,
                'sor' => $sor . '%',
            ];
            $totalIn += $inCount;
            $totalOut += $outCount;
            $lastStock = $stock;
        }

        $rows[] = [
            'tanggal' => 'Total',
            'kapasitas' => '-',
            'in' => $totalIn,
            'out' => $totalOut,
            'stock' => $lastStock,
            'sor' => '-',
        ];

        return $rows;
    }


    public function badgeOccupancy()
    {
        $branch = Auth::user()->branch->first();
        $capacity = 0;
        if ($branch->id == 1) {
            $capacity = 3482;
        } else if ($branch->id == 3) {
            $capacity = 870;
        }
        $occupied_slot = DB::table('ex_stock_ledger')
            ->select('quantity, status_flag')
            ->where('branch_id', $branch->id)
            ->whereYear('created_at', date('Y'))
            ->where('status_flag', 'Inbound')
            ->count();
        $available_slot = (int)$capacity - (int)$occupied_slot;
        if ($available_slot < 0) {
            $available_slot = 0;
        } else {
            $available_slot = $available_slot;
        }
        $data = [
            'total_pallet' => $capacity,
            'occupied_slot' => (int)$occupied_slot,
            'available_slot' => $available_slot,
            'percentage_occupied' => $capacity > 0 ? round(((int)$occupied_slot / (int)$capacity) * 100, 0) . '%' : '0%',
            'percentage_available' => $capacity > 0 ? round(($available_slot / (int)$capacity) * 100, 0) . '%' : '0%',
        ];
        return $data;
    }

    public function chartsOccupancy()
    {
        $branch = Auth::user()->branch->first();
        $capacity = 0;
        if ($branch->id == 1) {
            $capacity = 3482;
        } else if ($branch->id == 3) {
            $capacity = 870;
        }
        $year = Carbon::now()->year;

        $transactions = DB::table('ex_stock_transaction')
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                'job_type',
                'peb_no as peb',
                'pallet_id'
            )
            ->where('branch_id', $branch->id)
            ->whereYear('created_at', $year)
            ->get();

        $rows = [];
        $stock = 0;

        // Loop dari bulan Jan - Des
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $transactions->where('bulan', $i);

            // Count unique combination PEB + pallet_id untuk setiap jenis job
            $inCount = $monthData
                ->where('job_type', 'in')
                ->unique(fn($item) => $item->peb . '-' . $item->pallet_id)
                ->count();

            $outCount = $monthData
                ->where('job_type', 'out')
                ->unique(fn($item) => $item->peb . '-' . $item->pallet_id)
                ->count();

            // Hitung stock kumulatif bulanan
            $stock += ($inCount - $outCount);
            if ($stock < 0) $stock = 0;

            // Hitung SOR (%)
            $sor = $capacity > 0 ? round(($stock / $capacity) * 100) : 0;

            // Cek apakah bulan ini sudah lewat
            $monthDate = Carbon::createFromDate($year, $i, 1);
            if ($monthDate->isFuture()) {
                $rows[] = [
                    'month' => $monthDate->format('M'),
                    'capacity' => $capacity,
                    'occupied' => 0,
                    'sor' => 0,
                ];
            } else {
                $rows[] = [
                    'month' => $monthDate->format('M'),
                    'capacity' => $capacity,
                    'occupied' => $stock,
                    'sor' => $sor,
                ];
            }
        }

        return [
            'categories' => collect($rows)->pluck('month'),
            'occupied' => collect($rows)->pluck('occupied')->map(fn($v) => (int)$v),
            'capacity' => collect($rows)->pluck('capacity')->map(fn($v) => (int)$v),
            'sor' => collect($rows)->pluck('sor')->map(fn($v) => (int)$v),
        ];
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
