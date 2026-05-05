<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Adjustment\Batch as AdjustmentBatch;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;
use App\Models\Transaction\Stock\Transaction as StockTransaction;

class ConfirmController extends Controller
{
    public function index(Request $request)
    {
        $detail = [];
        if ($request->ajax()) {
            if (!empty($request->adjust_id) && !empty($request->adjust_id)) {
                $detail = DB::table('iv_adjustment_detail as a')
                    ->select('a.*', 'b.principal_name', 'c.product_name', 'd.site_name', 'e.area_name')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->join('iv_product as c', 'a.product_id', 'c.id')
                    ->join('iv_site as d', 'a.site_id', 'd.id')
                    ->leftjoin('iv_site_area as e', 'a.area_id', 'e.id')
                    ->where('a.adjust_id', $request->adjust_id)
                    ->where('a.picked_flag', 'Yes')
                    ->where('a.confirmed_flag', 'No')
                    ->get();
            }

            return datatables()->of($detail)
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
                })
                ->editColumn('exp_date', function ($data) {
                    return date('d/m/Y', strtotime($data->exp_date));
                })
                ->editColumn('mfg_date', function ($data) {
                    return date('d/m/Y', strtotime($data->mfg_date));
                })
                ->rawColumns(['check'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();

            try {
                $adjustment_id = $request->adjustment_id;
                $data = $request->confirm_id;
                $message = [];

                $job = AdjustmentJob::find($adjustment_id);

                foreach ($data as $id) {
                    $error = 0;

                    $detail = AdjustmentDetail::find($id);
                    $batch = AdjustmentBatch::where('adjust_id', $detail->adjust_id)
                        ->where('line_id', $id)
                        ->first();

                    if ($batch->status_flag == "No") {
                        $error++;
                        $message = [
                            "Serial Number $batch->serial_no tidak dapat diproses, dibutuhkan autorisasi!!!"
                        ];
                    }

                    if ($error == 0) {
                        if ($detail->status_flag == 'Exist') {
                            if ($batch->job_type == 'ADJ-') {
                                $serial = StockLedger::find($batch->serial_id);

                                $serial->qtys = $serial->qtys - $batch->qty;
                                $serial->qtyp = $serial->qtyp - $batch->qty;
                                $serial->save();

                                if ($serial->qtys == 0) {
                                    $location = MasterLocation::find($batch->location_id);

                                    if ($location->status_code == 'E') {
                                        $location->status_code = 'F';
                                        $location->save();
                                    }
                                }
                            }

                            if ($batch->job_type == 'ADJ+') {
                                $serial = StockLedger::find($batch->serial_id);

                                $serial->qtys = $serial->qtys + $batch->qty;
                                $serial->qtya = $serial->qtya + $batch->qty;
                                $serial->save();
                            }
                        }

                        if ($detail->status_flag == 'New' && $detail->adjust_type == 'Plus') {
                            $stock_ledger = [];

                            $stock_ledger[] = [
                                'company_id' => $batch->company_id,
                                'branch_id' => $job->branch_id,
                                'principal_id' => $batch->principal_id,
                                'serial_no' => $batch->serial_no,
                                'line_no' => $id,
                                'job_no' => $batch->job_no,
                                'job_date' => $job_date,
                                'product_id' => $batch->product_id,
                                'product_code' => $batch->product_code,
                                'po_number' => $batch->po_number,
                                'lot_no' => $batch->lot_no,
                                'document_ref' => $batch->document_ref,
                                'mfg_date' => $batch->mfg_date,
                                'exp_date' => $batch->exp_date,
                                'site_id' => $batch->site_id,
                                'area_id' => $batch->area_id,
                                'manufactur_id' => $batch->manufactur_id,
                                'status_id' => $batch->status_id,
                                'location_id' => $batch->location_id,
                                'location_code' => $batch->location_code,
                                'puom' => $batch->puom,
                                'muom' => $batch->muom,
                                'buom' => $batch->buom,
                                'uppp' => $batch->uppp,
                                'muppp' => $batch->muppp,
                                'pqty' => $batch->pqty,
                                'mqty' => $batch->mqty,
                                'bqty' => $batch->bqty,
                                'qtyr' => $batch->qty,
                                'qtys' => $batch->qty,
                                'qtya' => $batch->qty,
                                'qtyp' => 0,
                                'pallet_qty' => $batch->pallet_qty,
                                'base_unit' => $batch->base_unit,
                                'reference_no' => $batch->reference_no
                            ];

                            $stock = StockLedger::insert($stock_ledger);
                        }

                        $transaction = [];

                        $transaction[] = [
                            'company_id' => $batch->company_id,
                            'branch_id' => $job->branch_id,
                            'principal_id' => $batch->principal_id,
                            'serial_no' => $batch->serial_no,
                            'line_no' => $id,
                            'job_no' => $batch->job_no,
                            'job_date' => $job_date,
                            'job_type' => $batch->job_type,
                            'product_id' => $batch->product_id,
                            'product_code' => $batch->product_code,
                            'po_number' => $batch->po_number,
                            'lot_no' => $batch->lot_no,
                            'document_ref' => $batch->document_ref,
                            'mfg_date' => $batch->mfg_date,
                            'exp_date' => $batch->exp_date,
                            'manufactur_id' => $batch->manufactur_id,
                            'status_id' => $batch->status_id,
                            'site_id' => $batch->site_id,
                            'area_id' => $batch->area_id,
                            'location_id' => $batch->location_id,
                            'location_code' => $batch->location_code,
                            'puom' => $batch->puom,
                            'muom' => $batch->muom,
                            'buom' => $batch->buom,
                            'uppp' => $batch->uppp,
                            'muppp' => $batch->muppp,
                            'pqty' => $batch->pqty,
                            'mqty' => $batch->mqty,
                            'bqty' => $batch->bqty,
                            'qty' => $batch->qty,
                            'base_unit' => $batch->base_unit,
                            'reference_no' => $batch->reference_no
                        ];

                        StockTransaction::insert($transaction);

                        $batch->confirmed_flag = 'Yes';
                        $batch->confirmed_by = $confirmed_by;
                        $batch->confirmed_date = $confirmed_date;
                        $batch->save();

                        $detail->confirmed_flag = 'Yes';
                        $detail->confirmed_by = $confirmed_by;
                        $detail->confirmed_date = $confirmed_date;
                        $detail->save();
                    }
                }

                $detail_count = AdjustmentDetail::where('adjust_id', $detail->adjust_id)
                    ->where('confirmed_flag', 'No')
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

                $response = ["success" => "Successfully", "message" => $message];

                return $response;
            } catch (\Exception $e) {
                DB::rollBack();

                $response = ["error" => $e->getMessage()];

                return $response;
            }
        });

        return response()->json($exception);
    }
}
