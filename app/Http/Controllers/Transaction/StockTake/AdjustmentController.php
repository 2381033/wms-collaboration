<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;
use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Adjustment\Batch as AdjustmentBatch;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;
use App\Models\Transaction\Stock\Transaction as StockTransaction;
use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Transaction\StockTake\Job as StockTakeJob;
use App\Models\Master\AdjustmentType as MasterAdjustmentType;

class AdjustmentController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->take_id) && !empty($request->take_id)) {
                $details = DB::table('iv_stocktake_detail as a')
                            ->select('a.*', 'b.product_name', 'c.site_name', 'd.area_name')
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->join('iv_site as c', 'a.site_id', 'c.id')
                            ->join('iv_site_area as d', 'a.area_id', 'd.id')
                            ->where('a.stocktake_id', '=', $request->take_id)
                            ->where('a.confirmed_flag', '=', 'No')
                            ->where(DB::raw("CASE WHEN a.pqty <> a.actual_pqty OR a.mqty <> a.actual_mqty OR a.bqty <> a.actual_bqty OR a.lot_no <> a.actual_lot_no OR a.mfg_date <> a.actual_mfg_date OR a.exp_date <> a.actual_exp_date THEN 1 ELSE 0 END"), '=', 1)
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn('mfg_date', function ($data)
            {
                $mfg_date = "";
                if (isset($data->mfg_date)) {
                    $mfg_date = date('d/m/Y', strtotime($data->mfg_date) );
                }
                return $mfg_date;
            })
            ->editColumn('exp_date', function ($data)
            {
                $exp_date = "";
                if (isset($data->exp_date)) {
                    $exp_date = date('d/m/Y', strtotime($data->exp_date) );
                }
                return $exp_date;
            })
            ->editColumn('actual_mfg_date', function ($data)
            {
                $actual_mfg_date = "";
                if (isset($data->actual_mfg_date)) {
                    $actual_mfg_date = date('d/m/Y', strtotime($data->actual_mfg_date) );
                }
                return $actual_mfg_date;
            })
            ->editColumn('actual_exp_date', function ($data)
            {
                $actual_exp_date = "";
                if (isset($data->actual_exp_date)) {
                    $actual_exp_date = date('d/m/Y', strtotime($data->actual_exp_date) );
                }
                return $actual_exp_date;
            })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" required="required" name="adjust_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
            })
            ->rawColumns(['check'])
            ->addIndexColumn()
            ->make(true);
        }
    }


    public function submit(Request $request)
    {
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $adjust_date = \Carbon\Carbon::today();
        try {
            $dataapi = $request->confirm_id;
            $transfer_id = $request->transfer_id;
            $data = $request->confirm_id;
            $arrid = '';
            foreach ($data as $key => $value) {
                if (strlen($arrid) > 0) {
                    $arrid .= "," . $value;
                } else {
                    $arrid .= $value;
                }
            }

            $stockPerMinQty = DB::select("CALL sp_adjustment_batch_confirmation(?,?,?,?)", array($arrid, $user_id, $company_id, $confirmed_by));
            $message = ['success' => 'Data Successfully Saved'];
            $dataapi = $request->confirm_id;
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = ['error' => $e->getMessage()];
            return $message;
        }
        return response()->json($message);
    }

    public function submit2(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $adjust_date = \Carbon\Carbon::today();

            $year = $adjust_date->year;
            $month = $adjust_date->month;

            try {
                $data = $request->adjust_id;

                foreach ($data as $id) {
                    $detail = StockTakeDetail::find($id);

                    $job = StockTakeJob::find($detail->stocktake_id);
                    $principal = MasterPrincipal::find($job->principal_id);

                    $adjust_qty = $detail->qty - $detail->actual_qty;

                    if ($adjust_qty > 0) {
                        $adjust_type = 'Minus';
                        $job_type = '-';
                    } else {
                        $adjust_type = 'Plus';
                        $job_type = '+';
                    }

                    $qty = abs($adjust_qty);

                    $pqty = ($qty  - ($qty % $detail->uppp)) / $detail->uppp;
                    $mqty = (($qty % $detail->uppp) - (($qty % $detail->uppp) % $detail->muppp)) / $detail->muppp;
                    $bqty = $qty % $detail->uppp % $detail->muppp;

                    $serial = StockLedger::find($detail->serial_id);

                    if ($qty > 0) {
                        $adjustJob = AdjustmentJob::where('cycle_no', '=', $job->stocktake_no)->first();

                        if (is_null($adjustJob)) {
                            $adjustmentType = MasterAdjustmentType::where('type_name', '=', 'Cycle Count Adjustment')->first();

                            $create_job = AdjustmentJob::where('company_id', '=', $company_id)
                                        ->whereYear('adjust_date', '=', $year)
                                        ->whereMonth('adjust_date', '=', $month)->max("adjust_no");

                            if (is_null($create_job)) {
                                $increment = 1;
                            } else {
                                $increment = substr($create_job, 7, 4) + 1;
                            }

                            $adjust_no = '4' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

                            $NewadjustJob = new AdjustmentJob();
                            $NewadjustJob->company_id = $detail->company_id;
                            $NewadjustJob->adjust_no = $adjust_no;
                            $NewadjustJob->adjust_date = $adjust_date;
                            $NewadjustJob->cycle_no = $job->stocktake_no;
                            $NewadjustJob->description = 'AUTO ADJ CYC ' . $principal->principal_name;
                            $NewadjustJob->type_id = $adjustmentType->id;
                            $NewadjustJob->save();

                            $adjust_id = $NewadjustJob->id;
                        } else {
                            $adjust_id = $adjustJob->id;
                        }

                        if ($adjust_type == 'Minus') {
                            $serial->qtya = $serial->qtya - $qty;
                            $serial->qtyp = $serial->qtyp + $qty;
                        }

                        $adjustment_detail = new AdjustmentDetail();

                        $adjustment_detail->company_id = $company_id;
                        $adjustment_detail->principal_id = $detail->principal_id;
                        $adjustment_detail->adjust_id = $adjust_id;
                        $adjustment_detail->status_flag = 'Exist';
                        $adjustment_detail->adjust_type = $adjust_type;
                        $adjustment_detail->job_no = $detail->job_no;
                        $adjustment_detail->serial_id = $detail->serial_id;
                        $adjustment_detail->serial_no = $detail->serial_no;
                        $adjustment_detail->product_id = $detail->product_id;
                        $adjustment_detail->product_code = $detail->product_code;
                        $adjustment_detail->po_number = $detail->po_number;
                        $adjustment_detail->lot_no = $detail->lot_no;
                        $adjustment_detail->document_ref = $detail->document_ref;
                        $adjustment_detail->mfg_date = $detail->mfg_date;
                        $adjustment_detail->exp_date = $detail->exp_date;
                        $adjustment_detail->manufactur_id = $detail->manufactur_id;
                        $adjustment_detail->status_id = $detail->status_id;
                        $adjustment_detail->site_id = $detail->site_id;
                        $adjustment_detail->area_id = $detail->area_id;
                        $adjustment_detail->location_id = $detail->location_id;
                        $adjustment_detail->location_code = $detail->location_code;
                        $adjustment_detail->puom = $detail->puom;
                        $adjustment_detail->muom = $detail->muom;
                        $adjustment_detail->buom = $detail->buom;
                        $adjustment_detail->uppp = $detail->uppp;
                        $adjustment_detail->muppp = $detail->muppp;
                        $adjustment_detail->pqty = $pqty;
                        $adjustment_detail->mqty = $mqty;
                        $adjustment_detail->bqty = $bqty;
                        $adjustment_detail->qty = $qty;
                        $adjustment_detail->actual_pqty = $pqty;
                        $adjustment_detail->actual_mqty = $mqty;
                        $adjustment_detail->actual_bqty = $bqty;
                        $adjustment_detail->actual_qty = $qty;
                        $adjustment_detail->pallet_qty = $detail->pallet_qty;
                        $adjustment_detail->base_unit = $detail->base_unit;
                        $adjustment_detail->picked_flag = 'Yes';
                        $adjustment_detail->picked_by = $confirmed_by;
                        $adjustment_detail->picked_date = $confirmed_date;
                        $adjustment_detail->created_at = $confirmed_date;
                        $adjustment_detail->save();

                        $adjustment_batch = [];

                        $adjustment_batch[] = [
                            'company_id' => $company_id,
                            'principal_id' => $detail->principal_id,
                            'adjust_id' => $adjust_id,
                            'line_id' => $adjustment_detail->id,
                            'job_no' => $detail->job_no,
                            'job_type' => 'ADJ' . $job_type,
                            'serial_id' => $detail->serial_id,
                            'serial_no' => $detail->serial_no,
                            'product_id' => $detail->product_id,
                            'product_code' => $detail->product_code,
                            'po_number' => $detail->po_number,
                            'lot_no' => $detail->lot_no,
                            'document_ref' => $detail->document_ref,
                            'mfg_date' => $detail->mfg_date,
                            'exp_date' => $detail->exp_date,
                            'manufactur_id' => $detail->manufactur_id,
                            'status_id' => $detail->status_id,
                            'site_id' => $detail->site_id,
                            'area_id' => $detail->area_id,
                            'location_id' => $detail->location_id,
                            'location_code' => $detail->location_code,
                            'puom' => $detail->puom,
                            'muom' => $detail->muom,
                            'buom' => $detail->buom,
                            'uppp' => $detail->uppp,
                            'muppp' => $detail->muppp,
                            'pqty' => $pqty,
                            'mqty' => $mqty,
                            'bqty' => $bqty,
                            'qty' => $qty,
                            'reference_no' => $job->stocktake_no,
                            'pallet_qty' => $detail->pallet_qty,
                            'base_unit' => $detail->base_unit,
                            'created_at' => $confirmed_date
                        ];

                        AdjustmentBatch::insert($adjustment_batch);
                    }

                    if ($detail->lot_no != $detail->actual_lot_no) {
                        StockLedger::where('id', $detail->serial_id)->update(['lot_no'=>$detail->actual_lot_no]);
                        StockTransaction::where('serial_no', $detail->serial_no)->update(['lot_no'=>$detail->actual_lot_no]);
                    }

                    if ($detail->mfg_date != $detail->actual_mfg_date) {
                        StockLedger::where('id', $detail->serial_id)->update(['mfg_date'=>$detail->actual_mfg_date]);
                        StockTransaction::where('serial_no', $detail->serial_no)->update(['mfg_date'=>$detail->actual_mfg_date]);
                    }

                    if ($detail->exp_date != $detail->actual_exp_date) {
                        StockLedger::where('id', $detail->serial_id)->update(['exp_date'=>$detail->actual_exp_date]);
                        StockTransaction::where('serial_no', $detail->serial_no)->update(['exp_date'=>$detail->actual_exp_date]);
                    }

                    $serial->freeze_flag = 'No';
                    $serial->save();

                    $detail->confirmed_flag = 'Yes';
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();
                }

                $job = StockTakeJob::find($detail->stocktake_id);
                $detail_count = StockTakeDetail::where('stocktake_id', '=', $detail->stocktake_id)
                                    ->where('confirmed_flag', '=', 'No')
                                    ->get();

                if (is_null($detail_count)) {
                    $count = 0;
                } else {
                    $count = $detail_count->count();
                }

                if ($count == 0) {
                    $job->confirmed_flag = 'Yes';
                    $job->confirmed_by = $confirmed_by;
                    $job->confirmed_date = $confirmed_date;
                    $job->save();
                }

                DB::commit();

                $message = ['success'=>"Sukses"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
