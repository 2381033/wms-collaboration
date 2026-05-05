<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as guzzle;
use GuzzleHttp\Exception\BadResponseException;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Detail as TransferDetail;
use App\Models\Transaction\Transfer\Batch as TransferBatch;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Stock\Transaction as StockTransaction;
use App\Models\Transaction\Transfer\Job as TransferJob;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->transfer_id) && !empty($request->transfer_id)) {
                $detail = DB::table("iv_transfer_detail as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.site_name as dest_site_name", "f.area_name as dest_area_name")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->leftjoin("iv_site as e", "a.dest_site_id", "e.id")
                    ->leftjoin("iv_site_area as f", "a.dest_area_id", "f.id")
                    ->where("a.transfer_id", $request->transfer_id)
                    ->where("a.picked_flag", "Yes")
                    ->where("a.confirmed_flag", "No")
                    ->get();
            }

            return datatables()->of($detail)
                ->addColumn("check", function ($data) {
                    return "<input type='checkbox' required='required' name='confirm_id[]' class='confirm-check' id='" . $data->id . "' value='" . $data->id . "'>";
                })
                ->editColumn("exp_date", function ($data) {
                    return date("d/m/Y", strtotime($data->exp_date));
                })
                ->editColumn("mfg_date", function ($data) {
                    return date("d/m/Y", strtotime($data->mfg_date));
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }


    public function submit(Request $request)
    {
        $user_id = Auth::user()->id;
        $confirmed_by = Auth::user()->username;
        $confirmed_date = \Carbon\Carbon::now();
        $job_date = \Carbon\Carbon::today();
        try {
            $dataapi = $request->confirm_id;
            $transfer_id = $request->transfer_id;
            $data = $request->confirm_id;
            $arrid = '';
            foreach ($data as $key => $value) {
                $batchList = TransferBatch::where("transfer_id", $transfer_id)
                    ->where("line_id", $value)
                    ->orderBy("job_type", "ASC")
                    ->get();
                foreach ($batchList as $key => $valueBatch) {
                    $batch = TransferBatch::find($valueBatch->id);
                }

                if (strlen($arrid) > 0) {
                    $arrid .= "," . $value;
                } else {
                    $arrid .= $value;
                }
            }

            $stockPerMinQty = DB::select("CALL sp_transfer_batch_confirmation(?,?,?,?)", array($arrid, $transfer_id, $user_id, $confirmed_by));
            $message = ['success' => 'Data Successfully Saved'];
            $dataapi = $request->confirm_id;
            $databatch = $batch->id;
            if ($batch->principal_id == 32) {
                $this->sendAPI($dataapi, $databatch);
            }
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = ['error' => $e->getMessage()];
            return $message;
        }
        return response()->json($message);
    }

    public function submit2(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();

            try {
                $transfer_id = $request->transfer_id;
                $data = $request->confirm_id;

                $transfer = TransferJob::find($transfer_id);

                foreach ($data as $id) {
                    $detail = TransferDetail::find($id);
                    $batchList = TransferBatch::where("transfer_id", $detail->transfer_id)
                        ->where("line_id", $id)
                        ->orderBy("job_type", "ASC")
                        ->get();

                    $flag = false;
                    foreach ($batchList as $value) {
                        if ($value->job_type == "TFRI") {
                            $stock_count = StockLedger::where("principal_id", $detail->principal_id)
                                ->where("serial_no", $value->serial_no)
                                ->count();

                            if ($stock_count > 0) {
                                $flag = true;
                            }
                        }

                        if (!$flag) {
                            if ($value->job_type == "TFRO") {
                                $serial = StockLedger::find($detail->serial_id);

                                $serial->qtys = $serial->qtys - $value->qty;
                                $serial->qtyp = $serial->qtyp - $value->qty;
                                $serial->save();

                                if ($serial->qtys == 0) {
                                    $location = MasterLocation::find($detail->location_id);

                                    if ($location->status_code == "F") {
                                        $location->status_code = "E";
                                        $location->save();
                                    }
                                }
                            }

                            $manufactur_id = null;
                            $status_id = null;
                            if ($value->job_type == "TFRI") {
                                $serialOld = StockLedger::find($detail->serial_id);
                                $manufactur_id = $serialOld->manufactur_id;
                                $status_id = $serialOld->status_id;

                                $stock_ledger = [];

                                $stock_ledger[] = [
                                    "company_id" => $value->company_id,
                                    "branch_id" => $transfer->branch_id,
                                    "principal_id" => $value->principal_id,
                                    "serial_no" => $value->serial_no,
                                    "srno" => $value->srno,
                                    "line_no" => $id,
                                    "job_no" => $value->job_no,
                                    "job_date" => $job_date,
                                    "vehicle_no" => $serialOld->vehicle_no,
                                    "product_id" => $value->product_id,
                                    "product_code" => $value->product_code,
                                    "po_number" => $value->po_number,
                                    "lot_no" => $value->lot_no,
                                    "document_ref" => $value->document_ref,
                                    "mfg_date" => $value->mfg_date,
                                    "exp_date" => $value->exp_date,
                                    "manufactur_id" => $manufactur_id,
                                    "status_id" => $status_id,
                                    "site_id" => $value->site_id,
                                    "area_id" => $value->area_id,
                                    "location_id" => $value->location_id,
                                    "location_code" => $value->location_code,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "uppp" => $value->uppp,
                                    "muppp" => $value->muppp,
                                    "pqty" => $value->pqty,
                                    "mqty" => $value->mqty,
                                    "bqty" => $value->bqty,
                                    "qtyr" => $value->qty,
                                    "qtys" => $value->qty,
                                    "qtya" => $value->qty,
                                    "qtyp" => 0,
                                    "pallet_qty" => $value->pallet_qty,
                                    "base_unit" => $value->base_unit,
                                    "reference_no" => $value->job_no,
                                    "status" => $serialOld->status,
                                ];

                                StockLedger::insert($stock_ledger);
                            }

                            $transaction = [];

                            $transaction[] = [
                                "company_id" => $value->company_id,
                                "branch_id" => $transfer->branch_id,
                                "principal_id" => $value->principal_id,
                                "serial_no" => $value->serial_no,
                                "srno" => $value->srno,
                                "line_no" => $id,
                                "job_no" => $value->job_no,
                                "job_date" => $job_date,
                                "job_type" => $value->job_type,
                                "product_id" => $value->product_id,
                                "product_code" => $value->product_code,
                                "po_number" => $value->po_number,
                                "lot_no" => $value->lot_no,
                                "document_ref" => $value->document_ref,
                                "mfg_date" => $value->mfg_date,
                                "exp_date" => $value->exp_date,
                                "manufactur_id" => $manufactur_id,
                                "status_id" => $status_id,
                                "site_id" => $value->site_id,
                                "area_id" => $value->area_id,
                                "location_id" => $value->location_id,
                                "location_code" => $value->location_code,
                                "puom" => $value->puom,
                                "muom" => $value->muom,
                                "buom" => $value->buom,
                                "uppp" => $value->uppp,
                                "muppp" => $value->muppp,
                                "pqty" => $value->pqty,
                                "mqty" => $value->mqty,
                                "bqty" => $value->bqty,
                                "qty" => $value->qty,
                                "base_unit" => $value->base_unit,
                                "reference_no" => $value->job_no
                            ];

                            StockTransaction::insert($transaction);

                            $batch = TransferBatch::find($value->id);

                            $batch->confirmed_flag = "Yes";
                            $batch->confirmed_by = $confirmed_by;
                            $batch->confirmed_date = $confirmed_date;
                            $batch->save();
                        }
                    }

                    $detail->confirmed_flag = "Yes";
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();
                }

                $job = TransferJob::find($detail->transfer_id);

                $detail_count = TransferDetail::where("transfer_id", $detail->transfer_id)
                    ->where("confirmed_flag", "No")
                    ->get();

                if (is_null($detail_count)) {
                    $count = 0;
                } else {
                    $count = $detail_count->count();
                }

                if ($count == 0) {
                    $job->confirmed_flag = "Yes";
                    $job->confirmed_by = $confirmed_by;
                    $job->confirmed_date = $confirmed_date;
                    $job->save();
                }

                DB::commit();

                $message = ["success" => "Sukses"];

                $dataapi = $request->confirm_id;
                $databatch = $batch->id;
                $this->sendAPI($dataapi, $databatch);

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function sendAPI($data, $databatch)
    {
        $datasend = array();
        $jsondatasend = '';
        foreach ($data as $id) {
            $batch = TransferBatch::find($databatch);
            // $logs = DB::table("iv_epm_api_logs")
            //     ->where("activity", "STOCKTRANSFER")
            //     ->where("activity_id", $batch->transfer_id)
            //     ->where("send_status", "Y");
            // $logcount = $logs->count();
            // && $logcount > 0
            if ($batch->transfer_id > 0) {
                // $logdata = $logs->first();
                $job = TransferJob::find($batch->transfer_id);
                $detail = TransferDetail::find($id);
                $qty = ($detail->actual_pqty * $detail->muppp);
                $location_code_from = explode('.', $detail->location_code);
                $fromrow = $location_code_from[0];
                $frombin = $location_code_from[1];
                $fromlvl = $location_code_from[2];
                $location_code = explode('.', $detail->dest_location_code);
                $row = $location_code[0];
                $bin = $location_code[1];
                $lvl = $location_code[2];
                $data = [
                    "MOVEMENT_ID" => "$job->id",
                    "ORG_CODE" => "MD1",
                    "ITEM_CODE" => "$detail->product_code",
                    "LOT_NUMBER" => "$detail->lot_no",
                    "QUANTITY" => "$qty",
                    "UOM" => "$detail->muom",
                    "TRANSACTION_DATE" => date("d-m-Y H:i:m", strtotime($batch->created_at)),
                    "FROM_SUBINVENTORY" => "GOOD",
                    "FROM_ROW" => "$fromrow",
                    "FROM_BIN" => "$frombin",
                    "FROM_LEVEL" => "$fromlvl",
                    "TO_SUBINVENTORY" => "GOOD",
                    "TO_ROW" => "$row",
                    "TO_BIN" => "$bin",
                    "TO_LEVEL" => "$lvl",
                    "REMARKS" => "$job->description"
                ];
                array_push($datasend, json_encode($data));
                if (strlen($jsondatasend) > 1) {
                    $jsondatasend .= "," . json_encode($data);
                } else {
                    $jsondatasend .= json_encode($data);
                }
            }
        }
        $client = new guzzle();
        try {
            $response = $client->request('POST', 'https://egate.enseval.com/api/Principal/MiniDC/MKTMovement2', [
                'headers' => [
                    'accept' => '/',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'dGVzdDp0ZXN0'
                ],
                'body' => "[" . $jsondatasend . "]"
            ]);
            DB::beginTransaction();
            $saveresponse = DB::table('iv_epm_response_api')->insert([
                'activity' => 'STOCKTRANSFER',
                'activity_id' => $batch->transfer_id,
                'job_no' => $job->job_no,
                'status' => $response->getStatusCode(),
                'body' => "[" . $jsondatasend . "]",
                'error' => $response->getBody()->getContents(),
                'created_date' => \Carbon\Carbon::now()
            ]);
            if ($saveresponse) {
                DB::commit();
            } else {
                DB::rollback();
            }
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = (string) $response->getBody();
            // echo "<pre>";
            // print_r($jsonBody);
            // echo "</pre>";
            DB::beginTransaction();
            $saveresponse = DB::table('iv_epm_response_api')->insert([
                'activity' => 'STOCKTRANSFER',
                'activity_id' => $batch->transfer_id,
                'job_no' => $job->job_no,
                'status' => $response->getStatusCode(),
                'body' => "[" . $jsondatasend . "]",
                'error' => $jsonBody,
                'created_date' => \Carbon\Carbon::now()
            ]);
            if ($saveresponse) {
                DB::commit();
            } else {
                DB::rollback();
            }
        }
        return 'sukses';
    }
}
