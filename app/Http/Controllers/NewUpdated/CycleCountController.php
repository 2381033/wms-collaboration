<?php

namespace App\Http\Controllers\NewUpdated;

use App\Exports\CycleCountSKUExport as ExportsCycleCountSKUExport;
use App\Exports\CycleCountLocationExport as ExportsCycleCountLocationExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Illuminate\Support\Facades\Session;
use DataTables;
use App\Imports\StockTransferImport;

use App\Models\Transaction\Stock\Ledger as stockLedger;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Stock\Transaction as StockTransaction;
use App\Models\User;
use PDO;
use App\Imports\CycleCountSKUImports as ImportsCycleCountSKUImports;
use App\Imports\CycleCountLocationImports as ImportsCycleCountLocationImports;

class CycleCountController extends Controller
{

    private function getHeader()
    {
        $data = DB::table('iv_cyclecount_job')
            ->whereDate('created_at', date('Y-m-d'))
            ->where('branch_id', $this->myBranch())
            ->get();

        return $data;
    }

    private function getDetail($job_no)
    {
        $data = DB::table('iv_cyclecount_detail')
            ->orderBy('location_code', 'ASC')
            ->where('job_no', $job_no)
            ->where('branch_id', $this->myBranch())
            ->get();
        $data = $data->map(function ($value) {
            $value->stock = DB::table('iv_stock_ledger')->where('id', $value->id_ledger)->first();
            $value->product_name = DB::table('iv_product')->select('product_name')->where('id', $value->product_id)->value('product_name') ?? '-';
            return $value;
        });

        return $data;
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    private function getJobNo()
    {
        $branch = $this->myBranch();

        $job = DB::table('iv_cyclecount_job')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no = 'CC' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    private function getMySite()
    {
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->pluck('site_id')
            ->toArray();
        $site = DB::table('iv_site')->whereIn('id', $site_arr)->get();

        return $site;
    }

    private function getMyPrincipal()
    {
        $data = DB::table('users_principal')
            ->where('user_id', Auth::user()->id)
            ->get();
        $principal = $data->map(function ($value) {
            $value->principal = DB::table('iv_principal')
                ->where('id', $value->principal_id)
                ->first()->principal_name ?? '-';
            return $value;
        });

        return $principal;
    }

    public function getList($param, $site)
    {
        if ($param == 'sku') {
            $data = DB::table('iv_stock_ledger')
                ->orderBy('product_code', 'ASC')
                ->where('branch_id', $this->myBranch())
                ->where('qtya', '>', 0)
                ->where('site_id', $site)
                ->whereIn('principal_id', $this->getMyPrincipal()->pluck('principal_id')->toArray())
                ->groupBy('product_id')
                ->get();
            $data = $data->map(function ($value) {
                $value->principal = DB::table('iv_principal')
                    ->where('id', $value->principal_id)
                    ->first()->principal_name ?? '-';
                return $value;
            });
        } else {
            $data =  DB::table('iv_location')
                ->where('site_id', $site)
                ->groupBy('location_code')
                ->get();
        }
        return response()->json($data);
    }

    public function index()
    {
        $site = $this->getMySite();
        $check = DB::table('iv_cyclecount_job')
            ->where('branch_id', $this->myBranch())
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        return view("new.CycleCount.index", compact('check', 'site'));
    }

    public function setup()
    {
        $job_no   = $this->getJobNo();
        $site      = $this->getMySite();
        $validate_tools = false;
        $detail = [];

        $list_today = DB::table('iv_cyclecount_job')
            ->where('branch_id', $this->myBranch())
            ->where('created_by', Auth::user()->username)
            ->whereDate('created_at', date('Y-m-d'))
            ->get();

        if (count($list_today) > 0) {
            $detail      = $this->getDetail($list_today->first()->job_no);
            $validate_tools = count($detail->where('scan_flag', 'Yes')) == 0 ? true : false;
        }

        $list_today->map(function ($value) {
            $value->site_name = DB::table('iv_site')->where('id', $value->site_id)->first()->site_name ?? '-';
            return $value;
        });

        return view("new.CycleCount.setup", compact('job_no', 'site', 'list_today', 'detail', 'validate_tools'));
    }

    private function getStock($site, $location)
    {
        $data = DB::table('iv_stock_ledger')
            ->orderBy('product_code', 'ASC')
            ->where('qtya', '>', 0)
            ->where('site_id', $site)
            ->whereIn('location_code', $location)
            ->where('branch_id', $this->myBranch())
            ->get();

        return $data;
    }

    private function getStockBySKU($site, $product_id)
    {
        $data = DB::table('iv_stock_ledger')
            ->orderBy('product_code', 'ASC')
            ->where('qtya', '>', 0)
            ->where('site_id', $site)
            ->whereIn('product_id', $product_id)
            ->where('branch_id', $this->myBranch())
            ->get();
        return $data;
    }

    public function storeJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = $this->getHeader()
                    ->where('site_id', $request->site_id)
                    ->count();
                if ($validate > 0) {
                    DB::rollBack();
                    $message = ['message' => 'exist'];
                } else {
                    if ($request->type == 'sku') {
                        $stock = $this->getStockBySKU($request->site_id, $request->values);
                    } else {
                        $stock = $this->getStock($request->site_id, $request->values);
                    }
                    if (count($stock) > 0) {
                        $job[] = [
                            'site_id' => $request->site_id,
                            'branch_id' => $this->myBranch(),
                            'job_no'  => $request->job_no,
                            'type'  => $request->type,
                            'description' => $request->description,
                            'created_by'   => Auth::user()->username,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        foreach ($stock as $value) {
                            $detail[] = [
                                'job_no' => $request->job_no,
                                'branch_id' => $value->branch_id,
                                'principal_id' => $value->principal_id,
                                'id_ledger' => $value->id,
                                'product_id' => $value->product_id,
                                'product_code' => $value->product_code,
                                'site_id' => $value->site_id,
                                'area_id' => $value->area_id,
                                'location_id' => $value->location_id,
                                'location_code' => $value->location_code,
                                'puom' => $value->puom,
                                'muom' => $value->muom,
                                'uppp' => $value->uppp,
                                'muppp' => $value->muppp,
                                'pqty' => $value->pqty,
                                'mqty' => $value->mqty,
                                'created_by'   => Auth::user()->username,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        DB::table('iv_cyclecount_job')->insert($job);
                        DB::table('iv_cyclecount_detail')->insert($detail);

                        DB::commit();
                        $message = ['message' => 'Data Successfully Saved'];
                    } else {
                        DB::rollBack();
                        $message = ['message' => 'not_found'];
                    }
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function editJob($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $header = DB::table('iv_cyclecount_job')
                    ->where('id', $id)
                    ->where('branch_id', $this->myBranch())
                    ->first();
                $detail =  $this->getDetail($header->job_no);
                $loop = [];
                $myDetail = [];
                if ($header->type == 'sku') {
                    $myDetail = $detail->pluck('product_id')->toArray();
                    $loop = DB::table('iv_stock_ledger')
                        ->orderBy('product_code', 'ASC')
                        ->where('qtya', '>', 0)
                        ->whereNotIn('product_id', $myDetail)
                        ->where('site_id', $header->site_id)
                        ->groupBy('product_id')
                        ->where('branch_id', $this->myBranch())
                        ->get();
                } else {
                    $myDetail = $detail->pluck('location_code')->toArray();
                    $loop =  DB::table('iv_location')
                        ->where('site_id', $header->site_id)
                        ->whereNotIn('location_code', $myDetail)
                        ->groupBy('location_code')
                        ->pluck('location_code')->toArray();
                }
                $data = [
                    'header' => $header,
                    'myDetail' => $myDetail,
                    'loop' => $loop,
                ];

                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function updateJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                if ($request->has('values')) {
                    if ($request->type == 'sku') {
                        $stock = $this->getStockBySKU($request->site_id, $request->values);
                    } else {
                        $stock = $this->getStock($request->site_id, $request->values);
                    }
                    foreach ($stock as $value) {
                        $detail[] = [
                            'job_no' => $request->job_no,
                            'branch_id' => $value->branch_id,
                            'principal_id' => $value->principal_id,
                            'id_ledger' => $value->id,
                            'product_id' => $value->product_id,
                            'product_code' => $value->product_code,
                            'site_id' => $value->site_id,
                            'area_id' => $value->area_id,
                            'location_id' => $value->location_id,
                            'location_code' => $value->location_code,
                            'puom' => $value->puom,
                            'muom' => $value->muom,
                            'uppp' => $value->uppp,
                            'muppp' => $value->muppp,
                            'pqty' => $value->pqty,
                            'mqty' => $value->mqty,
                            'created_by'   => Auth::user()->username,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                    DB::table('iv_cyclecount_detail')->insert($detail);
                }
                DB::table('iv_cyclecount_job')
                    ->where('job_no', $request->job_no)
                    ->update([
                        'description' => $request->description,
                    ]);
                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteJob($id)
    {
        $job_no = DB::table('iv_cyclecount_job')
            ->where('id', $id)
            ->where('branch_id', $this->myBranch())
            ->value('job_no');

        DB::table('iv_cyclecount_detail')
            ->where('branch_id', $this->myBranch())
            ->where('job_no', $job_no)
            ->delete();

        DB::table('iv_cyclecount_job')
            ->where('branch_id', $this->myBranch())
            ->where('job_no', $job_no)
            ->delete();

        Session::flash('success', 'Data has been deleted successfully');
        return back();
    }

    public function stokTransfer($id)
    {
        $data = DB::table('iv_stock_ledger')
            ->where('branch_id', $this->myBranch())
            ->where('id', $id)
            ->first();

        $product_name = DB::table('iv_product')->where('id', $data->product_id)->first()->product_name;

        return response()->json([
            'data' => [
                'data' => $data,
                'product_name' => $product_name,
            ],
        ]);
    }

    public function getListData($site_id, $location)
    {
        $exception = DB::transaction(function () use ($site_id, $location) {
            try {
                $data = $this->getHeader()
                    ->where('confirmed_flag', 'No')
                    ->where('site_id', $site_id)
                    ->first();

                if (!is_null($data)) {
                    if ($location == 'All') {
                        $data = $this->getDetail($data->job_no)->where('scan_flag', 'No');
                    } else {
                        $data = $this->getDetail($data->job_no)->where('scan_flag', 'No')
                            ->where('location_code', $location);
                    }

                    $location = array_unique($data->pluck('location_code')->toArray());
                    $result = [
                        'data' => $data,
                        'location' => $location,
                    ];
                    $message = $result;
                } else {
                    DB::commit();
                    $message = ['message' => 'not_found'];
                }

                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function countByChecker($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('iv_cyclecount_detail')
                    ->where('branch_id', $this->myBranch())
                    ->where('id', $id)->update([
                        'scan_flag' => 'Yes',
                        'match_flag' => 'Yes',
                        'scan_by' => Auth::user()->username,
                        'scan_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function postStokTransfer(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('iv_cyclecount_detail')
                    ->where('branch_id', $this->myBranch())
                    ->where('id', $request->id_detail)
                    ->update([
                        'scan_flag' => 'Yes',
                        'scan_by'   => Auth::user()->username,
                        'scan_at'   => date('Y-m-d H:i:s'),
                        'match_flag'   => 'No',
                        'remarks'   => $request->remarks,
                    ]);

                DB::commit();
                $message = ['message' => 'Data has been transferred successfully..'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function monitoring()
    {
        $header = $this->getHeader()->first();

        $data = DB::table('iv_cyclecount_detail')
            ->where('branch_id', $this->myBranch())
            ->whereDate('created_at', date('Y-m-d'))
            ->whereNotNull('match_flag')
            ->get();

        $data->map(function ($value) {
            $value->principal_name = DB::table('iv_principal')
                ->where('id', $value->principal_id)->first()->principal_name ?? '-';
            $value->site_name = DB::table('iv_site')
                ->where('id', $value->site_id)->first()->site_name ?? '-';
            $value->stock = DB::table('iv_stock_ledger')
                ->where('id', $value->id_ledger)->first()->qtya ?? '-';
        });

        return view('new.CycleCount.monitoring', compact('data', 'header'));
    }

    public function getTransferLokasi($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $data = DB::table('iv_cyclecount_detail')
                    ->where('branch_id', $this->myBranch())
                    ->where('id', $id)
                    ->first();
                $data = DB::table('iv_stock_ledger')->where('id', $data->id_ledger)->first();

                DB::commit();
                $data = $data;
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $data = ['error' => $e->getMessage()];
                return $data;
            }
        });
        return response()->json($exception);
    }

    public function cariData($tgl_mulai, $tgl_selesai)
    {
        $data = DB::table('iv_cyclecount_detail')
            ->where('scan_flag', 'Yes')
            ->whereBetween('created_at', [$tgl_mulai . ' 00:00:00', $tgl_selesai . ' 23:59:00'])
            ->get();

        $data->map(function ($value) {
            $value->site_name = DB::table('iv_site')
                ->where('id', $value->site_id)
                ->value('site_name') ?? '-';
            $value->stock = DB::table('iv_stock_ledger')
                ->where('id', $value->id_ledger)->first()->qtya ?? '-';
            return $value;
        });
        return Datatables::of($data)->make(true);
    }

    public function confirm($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                $this->confirmFlag();
                $this->updateLocationStock($job_no);

                DB::commit();
                $message = ['message' => 'Data has been successfully..'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function confirmFlag()
    {
        $exception = DB::transaction(function () {
            try {

                DB::table('iv_cyclecount_detail')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->where('branch_id', $this->myBranch())
                    ->whereNull('match_flag')
                    ->update([
                        'scan_flag' => 'Yes',
                        'scan_by' => Auth::user()->username,
                        'scan_at' => date('Y-m-d H:i:s'),
                        'match_flag' => 'Yes',
                    ]);

                DB::table('iv_cyclecount_job')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->where('branch_id', $this->myBranch())
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'confirmed_by' => Auth::user()->username,
                        'confirmed_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                $message = ['message' => 'Data has been successfully..'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getLocation($site_id)
    {
        $data = DB::table('iv_location')
            ->where('site_id', $site_id)
            ->where('location_code', 'LIKE', '%Lock Area%')
            ->first();
        return $data;
    }
    private function updateLocationStock($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                $data = $this->getDetail($job_no)->where('match_flag', 'No');

                foreach ($data as $value) {
                    $location = $this->getLocation($value->site_id);
                    DB::table('iv_stock_ledger')
                        ->where('id', $value->id_ledger)
                        ->update([
                            'location_id'   => $location->id,
                            'area_id'       => $location->area_id,
                            'location_code' => $location->location_code,
                            'status'        => NULL,
                        ]);
                }

                DB::commit();
                $message = ['message' => 'Data has been successfully..'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function addLocationByChecker($site_id, $location_code)
    {
        $exception = DB::transaction(function () use ($site_id, $location_code) {
            try {
                $loc_exist = DB::table('iv_cyclecount_detail')
                    ->where('site_id', $site_id)
                    ->where('location_code', $location_code)
                    ->whereDate('created_at', date('Y-m-d'))
                    ->count();
                if ($loc_exist > 0) {
                    $message = ['message' => 'exist'];
                    DB::rollBack();
                } else {
                    $location = DB::table('iv_location')
                        ->where('site_id', $site_id)
                        ->where('location_code', $location_code)
                        ->count();
                    if ($location > 0) {
                        $data = DB::table('iv_stock_ledger')
                            ->where('site_id', $site_id)
                            ->where('location_code', $location_code)
                            ->value('id');
                        if (!is_null($data)) {
                            $job_no = DB::table('iv_cyclecount_job')
                                ->where('site_id', $site_id)
                                ->whereDate('created_at', date('Y-m-d'))
                                ->value('job_no');
                            $location_code = array($location_code);

                            $stock = $this->getStock($site_id, $location_code);
                            foreach ($stock as $value) {
                                $detail[] = [
                                    'job_no' => $job_no,
                                    'branch_id' => $value->branch_id,
                                    'principal_id' => $value->principal_id,
                                    'id_ledger' => $value->id,
                                    'product_id' => $value->product_id,
                                    'product_code' => $value->product_code,
                                    'site_id' => $value->site_id,
                                    'area_id' => $value->area_id,
                                    'location_id' => $value->location_id,
                                    'location_code' => $value->location_code,
                                    'puom' => $value->puom,
                                    'muom' => $value->muom,
                                    'uppp' => $value->uppp,
                                    'muppp' => $value->muppp,
                                    'pqty' => $value->pqty,
                                    'mqty' => $value->mqty,
                                    'created_by'   => Auth::user()->username,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                            DB::table('iv_cyclecount_detail')->insert($detail);

                            $message = ['message' => 'successfully'];
                            DB::commit();
                        } else {
                            $message = ['message' => 'stock'];
                            DB::rollBack();
                        }
                    } else {
                        $message = ['message' => 'location'];
                        DB::rollBack();
                    }
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function templateExport($site_id, $type)
    {
        if ($type == 'sku') {
            return Excel::download(new ExportsCycleCountSKUExport($site_id), "tempalte-bysku-cycle-count.xlsx");
        } else {
            return Excel::download(new ExportsCycleCountLocationExport($site_id), "tempalte-bylocation-cycle-count.xlsx");
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new ImportsCycleCountSKUImports($request->site_id), $file);
        return back();
    }
    public function importByLocation(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new ImportsCycleCountLocationImports($request->site_id), $file);
        return back();
    }
}
