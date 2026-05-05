<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as guzzle;
use GuzzleHttp\Exception\BadResponseException;

use App\Models\Transaction\Outbound\Batch as outboundBatch;
use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Transaction\Stock\Ledger as stockLedger;
use App\Models\Master\Location as masterLocation;
use App\Models\Transaction\Outbound\Job as outboundJob;
use App\Models\Transaction\Stock\Transaction as Transaction;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = DB::table("iv_outbound_batch as a")
                ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.company_id", $company_id)
                ->where("a.outbound_id", $request->outbound_id)
                ->where("a.confirmed_flag", "No")
                ->get();
            // dd($list_data);
            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    $exp_date = "";
                    if (isset($data->exp_date)) {
                        $exp_date = date('d/m/Y', strtotime($data->exp_date));
                    }
                    return $exp_date;
                })
                ->editColumn('mfg_date', function ($data) {
                    $mfg_date = "";
                    if (isset($data->mfg_date)) {
                        $mfg_date = date('d/m/Y', strtotime($data->mfg_date));
                    }
                    return $mfg_date;
                })
                ->addColumn("check", function ($data) {
                    return "<input type='checkbox' required='required' name='confirm_id[]' class='confirm-check' id='" . $data->id . "' value='" . $data->id . "'>";
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function submit(Request $request)
    {
        ini_set('default_socket_timeout', 6000);
        ini_set('max_execution_time', 6000);
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $confirmed_by = Auth::user()->username;
        $confirmed_date = \Carbon\Carbon::now();
        $job_date = \Carbon\Carbon::today();
        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->ata);
        $ata = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->loading_start);
        $loading_start = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->loading_finish);
        $loading_finish = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

        // dd("test");

        try {
            $dataapi = $request->confirm_id;
            $data = $request->confirm_id;
            // dd($data);
            $arrid = '';
            $jobprincipal = 0;
            // // NEW CODE BY ARI RIZKITA
            // $list_data = DB::table("iv_outbound_batch as a")
            //     ->select("a.*", "b.customer_name")
            //     ->join("iv_customer as b", "a.customer_id", "b.id")
            //     ->leftjoin('users_principal as c', 'a.principal_id', '=', 'c.principal_id')
            //     ->leftjoin('iv_principal as d', 'a.principal_id', '=', 'd.id')
            //     ->where("a.company_id", $company_id)
            //     ->whereIn("a.id", $data) // array
            //     ->get();

            // $job_view = DB::table('iv_outbound_batch as a')
            //     ->select("a.id", "c.multi_level")
            //     ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
            //     ->join('iv_principal as c', 'a.principal_id', 'c.id')
            //     ->where('b.user_id', $user_id)
            //     ->whereIn('a.id', $data)
            //     ->get();
            // // Debugging: melihat hasil pemetaan multi_level
            // // dd($multi_level_map);
            // $qty_list_data = DB::table("iv_outbound_batch as a")
            //     ->whereIn("a.id", $data)->sum('pqty');
            // $qty_match = false;
            // $list_data->map(function ($value_1) use (&$qty_match, $qty_list_data) {

            //     $value_1->expected_qty = DB::table("iv_outbound_detail")
            //         ->where("company_id", $value_1->company_id)
            //         ->where("outbound_id", $value_1->outbound_id)
            //         ->sum('qty');
            //     if ($qty_list_data != $value_1->expected_qty) {
            //         $qty_match = true;
            //     }
            //     return $value_1;
            // });
            // // dd($multi_level_map);
            // if ($job_view->contains(function ($multi_level) use ($qty_match) {
            //     return $multi_level->multi_level == "No" && $qty_match;
            // })) {
            //     return response()->json([
            //         'error' => 'Expected qty tidak sama dengan actual, Periksa kembali datanya!'
            //     ]);
            // }
            foreach ($data as $key => $value) {
                // BATAS
                if ($jobprincipal == 0) {
                    $batch = outboundBatch::find($value);
                    $jobprincipal = $batch->principal_id;
                }
                if (strlen($arrid) > 0) {
                    $arrid .= "," . $value;
                } else {
                    $arrid .= $value;
                }
            }
            // dd("insert data");
            // dd($arrid, $ata, $loading_start, $loading_finish, $user_id, $confirmed_by);
            $stockPerMinQty = DB::select("CALL sp_outbound_batch_confirmation(?,?,?,?,?,?)", array($arrid, $ata, $loading_start, $loading_finish, $user_id, $confirmed_by));
            $message = ['success' => 'Data Successfully Saved'];
            // $message = ['success' => 'Done'];
            $dataapi = $request->confirm_id;
            if ($jobprincipal == 32) {
                $this->sendAPI($dataapi);
            }
            // return $message;
            // NEW CODE BY ARI RIZKITA
            return response()->json($message);
            // BATAS
        } catch (\Exception $e) {
            DB::rollBack();
            // $message = ['error' => $e->getMessage()];
            // return $message;
            // NEW CODE BY ARI RIZKITA
            // BATAS
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // return response()->json($message);
    }

    public function submit2(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();

            try {
                $data = $request->confirm_id;

                foreach ($data as $id) {
                    $batch = outboundBatch::find($id);
                    $detail = outboundDetails::find($batch->picking_id);
                    $serial = stockLedger::find($batch->serial_id);
                    $location = masterLocation::find($batch->location_id);

                    $job = outboundJob::find($batch->outbound_id);

                    if (isset($job)) {
                        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->ata);
                        $ata = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

                        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->loading_start);
                        $loading_start = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

                        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->loading_finish);
                        $loading_finish = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');

                        if (empty($job->loading_start)) {
                            $job->ata = $ata;
                            $job->loading_start = $loading_start;
                            $job->loading_finish = $loading_finish;
                            $job->save();
                        }
                    }

                    $serial->qtys = $serial->qtys - $batch->qty;
                    $serial->qtyp = $serial->qtyp - $batch->qty;
                    $serial->save();

                    if ($serial->qtys == 0 && isset($location)) {
                        if ($location->status_code == "F") {
                            $location->status_code = 'E';
                            $location->save();
                        }
                    }

                    $transaction = [];

                    $transaction[] = [
                        'company_id' => $batch->company_id,
                        'branch_id' => $job->branch_id,
                        'principal_id' => $batch->principal_id,
                        'serial_no' => $batch->serial_no,
                        'srno' => $batch->serial_no,
                        'line_no' => $id,
                        'job_no' => $serial->job_no,
                        'job_date' => $job_date,
                        'job_type' => 'EXP',
                        'product_id' => $batch->product_id,
                        'product_code' => $batch->product_code,
                        'po_number' => $batch->po_number,
                        'lot_no' => $batch->lot_no,
                        'document_ref' => $batch->document_ref,
                        'mfg_date' => $batch->mfg_date,
                        'exp_date' => $batch->exp_date,
                        'manufactur_id' => $serial->manufactur_id,
                        'status_id' => $serial->status_id,
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
                        'reference_no' => $batch->job_no,
                        'created_at' => \Carbon\Carbon::now()
                    ];

                    Transaction::insert($transaction);

                    $detail->confirmed_flag = 'Yes';
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();

                    $batch->confirmed_flag = 'Yes';
                    $batch->confirmed_by = $confirmed_by;
                    $batch->confirmed_date = $confirmed_date;
                    $batch->save();
                }

                $job = outboundJob::find($batch->outbound_id);

                $batch_count = outboundBatch::where('id', '=', $batch->outbound_id)
                    ->where('confirmed_flag', '=', 'No')
                    ->get();

                if (is_null($batch_count)) {
                    $count = 0;
                } else {
                    $count = $batch_count->count();
                }

                if ($count == 0) {
                    $job->confirmed_flag = 'Yes';
                    $job->confirmed_by = $confirmed_by;
                    $job->confirmed_date = $confirmed_date;
                    $job->save();
                }

                DB::commit();

                $message = ['success' => 'Done'];
                $dataapi = $request->confirm_id;
                $this->sendAPI($dataapi);

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    // private function autocorrection($data)
    // {

    // }

    private function sendAPI($data)
    {
        $datasend = array();
        $jsondatasend = '';
        foreach ($data as $id) {
            $batch = outboundBatch::find($id);
            $logs = DB::table("iv_epm_api_logs")
                ->where("activity", "OUTBOUND")
                ->where("activity_id", $batch->outbound_id)
                ->where("send_status", "Y");
            $logcount = $logs->count();
            if ($batch->outbound_id > 0 && $logcount > 0) {
                $logdata = $logs->first();
                $job = outboundJob::find($batch->outbound_id);
                // $log_details = DB::table("iv_epm_api_log_details")
                //     ->where("header_id", $logdata->id)
                //     ->where("product_code", $batch->product_code)
                //     ->first();
                $order = DB::table("iv_outbound_order")
                    ->where("outbound_id", $batch->outbound_id)
                    ->where("order_no", $batch->order_no)
                    ->first();
                $qty = ($batch->pqty * $batch->muppp);
                $details = outboundDetails::Find($batch->picking_id);
                $customer_code = DB::table('iv_customer')->where('id', $details->customer_id)->first();
                // $qty = $batch->pqty * $batch->muppp;
                $location_code = explode('.', $batch->location_code);
                $row = $location_code[0];
                $bin = $location_code[1];
                $lvl = $location_code[2];
                $data = [
                    // "order_no" => $order->order_no,
                    // "po_no" => $order->po_number,
                    // "site_id" => "MD1",
                    // "customer_id" => "",
                    // "product_code" => $details->product_code,
                    // "lot_no" => $details->lot_no,
                    // "mqty" => $qty,
                    // "muom" => $details->muom,
                    // "location_code_row" => $row,
                    // "location_code_bin" => $bin,
                    // "location_code_level" => $lvl,
                    // "picking_flag": "string",
                    // "picked_date": date("d-m-Y h:i:m", strtotime($batch->created_at)),
                    // "picking_by": "2020-01-01 23:59:59",
                    // "confirmed_date": "2020-01-01 23:59:59",
                    // "create_at" => $job->created_at,
                    // "description" => $job->description,
                    "loc_bin" => $bin,
                    "loc_row" => $row,
                    "item_code" => $details->product_code,
                    "loc_level" => $lvl,
                    "quantity" => $qty,
                    "uom" => $batch->muom,
                    "picked_by" => 1,
                    "iso_number" => $order->po_number,
                    "lot_number" => $details->lot_no,
                    "picked_date" => date("d-m-Y H:i:m", strtotime($details->picking_date)),
                    "picked_status" => $details->picking_flag,
                    "shipment_date" => date("d-m-Y H:i:m", strtotime($details->confirmed_date)),
                    "delivery_number" => $order->order_no,
                    "to_org_code" => 'MDN',
                    "from_org_code" => "MD1"
                ];
                array_push($datasend, json_encode($data));
                if (strlen($jsondatasend) > 1) {
                    $jsondatasend .= "," . json_encode($data);
                } else {
                    $jsondatasend .= json_encode($data);
                }
            }
        }
        if (strlen($jsondatasend) > 1) {
            $client = new guzzle();
            $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/outbound';
            try {
                $response = $client->request('POST', $urlapi, [
                    'headers' => [
                        'accept' => '/',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'dGVzdDp0ZXN0'
                    ],
                    'body' => "[" . $jsondatasend . "]"
                ]);
                DB::beginTransaction();
                $saveresponse = DB::table('iv_epm_response_api')->insert([
                    'activity' => 'OUTBOUND',
                    'activity_id' => $batch->outbound_id,
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
                DB::beginTransaction();
                $saveresponse = DB::table('iv_epm_response_api')->insert([
                    'activity' => 'OUTBOUND',
                    'activity_id' => $batch->outbound_id,
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
        } else {
            return 'sukses';
        }
    }

    public function mapping_lokasi()
    {
        $data = DB::table('iv_outbound_batch')
            ->where('scan_location', 'N')
            ->groupBy('job_no')
            ->get();

        $data->map(function ($row) {
            $row->principal = DB::table('iv_principal')
                ->where('id', $row->principal_id)
                ->value('principal_name');

            $row->branch_id = DB::table('iv_outbound_job')
                ->where('id', $row->outbound_id)
                ->value('branch_id');

            $row->branch = DB::table('mt_branch')
                ->where('id', $row->branch_id)
                ->value('branch_name');
            return $row;
        });
        return view('transaction.outbound.mapping_lokasi', compact('data'));
    }

    public function getListDetailMapping($job_no)
    {
        $data = DB::table('iv_outbound_batch')
            ->where('scan_location', 'N')
            ->where('job_no', $job_no)
            ->get();

        return response()->json($data);
    }

    public function postMappingLokasi(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    DB::table('iv_outbound_batch')
                        ->where('id', $request->id[$i])
                        ->update([
                            'location_confirm' => 'Y',
                            'location_confirm_at' => date('Y-m-d H:i:s'),
                            'scan_location' => 'Y',
                            'scan_location_at' => date('Y-m-d H:i:s'),
                            'scan_location_by' => Auth::user()->username,
                        ]);
                }
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
}
