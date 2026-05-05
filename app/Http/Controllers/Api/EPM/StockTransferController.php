<?php

namespace App\Http\Controllers\Api\EPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Transfer\Job as TransferJob;
use App\Models\Master\Product as MasterProduct;
use App\Models\Transaction\Transfer\Detail as TransferDetails;
use App\Models\Transaction\Stock\Ledger as StockLedger;

class StockTransferController extends Controller{
    public function index($user_id){
        $job_list = DB::table("iv_transfer_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "a.description")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("a.class_id", "<>", "3")
            ->where("b.user_id", $user_id)
            ->orderBy("a.job_no", "desc")
            ->where("a.entry_flag", "Yes")
            ->where("a.confirmed_flag", "No")
            ->get();

        $list = array();

        foreach ($job_list as $value) {
            $list[] = [
                "id" => $value->id,
                "principal_name" => $value->principal_name,
                "job_no" => $value->job_no,
                "job_date" => \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "class_name" => $value->class_name,
                "mode_name" => $value->mode_name,
                "description" => $value->description
            ];
        }

        $response = [];

        if (count($list) == 0) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function submit(Request $request)
    {
        $error = 0;
        $error_data = array();
        $jsondata = json_decode($request->getContent(), true);
        // $jsondata = $jsondata['data'][0];
        $EPM_data = DB::table("iv_principal as a")->select("a.id", "a.company_id", "b.principal_id", "a.principal_name", "a.short_name", "b.branch_id")
            ->join("iv_principal_branch as b", "b.principal_id", "a.id")->where("a.short_name", "Mostrans")->first();
        $site_id = 0;
        $site = $jsondata['site'];
        $site_query = DB::table("iv_site")->where("site_name", $site)->first();
        if (!$site_query) {
            $error++;
            array_push($error_data, array('message' => 'error site', 'input' => $site, 'value' => $site_query));
        } else {
            $site_id = $site_query->id;
        }
        $product = $jsondata['product'];
        $productError = array();
        if (sizeof($product) == 0) {
            $error++;
            array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $product));
        }
        foreach ($product as $key => $value) {
            $productData = MasterProduct::select('id', 'product_code')->where('product_code', $value['product_code'])
                ->where('company_id', $EPM_data->company_id)
                ->where('principal_id', $EPM_data->principal_id)
                ->first();

            if (!$productData) {
                $error++;
                array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $value['product_code']));
                array_push($productError, $value['product_code']);
            }
        }

        $description = $jsondata['description'];
        $entry_date = $jsondata['created_at'];
        if (!$entry_date) {
            $error++;
            array_push($error_data, array('message' => 'error Entry Date', 'input' => $entry_date, 'value' => $entry_date));
        } else {
            $entry_date = date('Y-m-d', strtotime($entry_date));
            // $entry_date_format = explode('-', $entry_date);
            // $entry_date = $entry_date_format[2].'-'.$entry_date_format[1].'-'.$entry_date_format[0];
        }

        // $serial_id = $jsondata['serial_id'];
        $serial = DB::table("iv_stock_ledger as a")->select("a.id")->orderby("a.id", "desc")->first();
        // echo'<pre>';die(var_dump($serial_id->id));
        $serial_id = strval($serial->id);
        // echo'<pre>';die(var_dump($serial_id));
        $stock = StockLedger::find($serial_id);
        // echo'<pre>';die(var_dump($stock->serial_no));

        DB::beginTransaction();
        if ($error > 0) {
            DB::table('iv_epm_api_logs')->insert([
                'activity' => 'STOCKTRANSFER',
                'activity_id' => 0,
                'job_no' => '',
                'status' => 'FAILED',
                'body' => json_encode($jsondata),
                'error' => json_encode($error_data),
                'created_date' => \Carbon\Carbon::now()
            ]);
            DB::commit();
        } else {
            try {
                // Job Number
                $confirmed_by = 'Create by API';
                $confirmed_date = \Carbon\Carbon::now();
                $job_date = \Carbon\Carbon::today();
                $year = $job_date->year;
                $month = $job_date->month;
                $job = TransferJob::where('company_id', 1)
                    ->whereYear('job_date', $year)
                    ->whereMonth('job_date', $month)
                    ->max("job_no");

                if (is_null($job)) {
                    $increment = 1;
                } else {
                    $increment = substr($job, 7, 4) + 1;
                }

                $job_no = '3' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

                $transferJobId = TransferJob::insertGetId([
                    'company_id' => $EPM_data->company_id, // set ke MKT
                    'branch_id' => $EPM_data->branch_id,
                    'principal_id' => $EPM_data->principal_id,
                    'job_no' => $job_no,
                    'job_date' => $job_date,
                    'site_id' => $site_id,
                    // 'class_id' => 1, // set ke regular
                    // 'mode_id' => 1, // set ke land
                    'description' => $description,
                    'entry_date' => $entry_date,
                    'created_at' => $confirmed_date,
                    'user_id' => $confirmed_by
                ]);

                // $inboundVehicleId = inboundVehicle::insertGetId(
                //     [
                //         'inbound_id' => $inboundJobId,
                //         'company_id' => $EPM_data->company_id,
                //         'principal_id' => $EPM_data->principal_id,
                //         'job_no' => $job_no,
                //         'vehicle_no' => '-', //set to -
                //         'transporter_name' => '-', //set to -
                //         'driver_name' => '-', //set to -
                //         'type_id' => '1', //set to 1
                //         'created_at' => $confirmed_date,
                //         'user_id' => $confirmed_by,
                //         'size_id' => '1' //set to 1
                //     ]
                // );

                foreach ($product as $key => $value) {
                    $productData = MasterProduct::where('product_code', $value['product_code'])
                        ->where('company_id', $EPM_data->company_id)
                        ->where('principal_id', $EPM_data->principal_id)
                        ->first();

                    if ($productData) {
                        $qty = ($value['mqty'] * $productData->muppp);

                        $actual_pqty = ($qty - ($qty % $productData->uppp)) / $productData->uppp;
                        $actual_mqty = (($qty % $productData->uppp) - ($qty % $productData->uppp % $productData->muppp)) / $productData->muppp;
                        $actual_bqty = $qty % $productData->uppp % $productData->muppp;

                        $transferDetailId = TransferDetails::insertGetId(
                            [
                                'company_id' => $EPM_data->company_id,
                                'principal_id' => $EPM_data->principal_id,
                                'transfer_id' => $transferJobId,
                                'job_no' => $job_no,
                                'serial_id' => $serial->id,
                                'serial_no' => $stock->serial_no,
                                'product_id' => $productData->id,
                                'product_code' => $value['product_code'],
                                'po_number' => $stock->po_number,
                                'lot_no' => $value['lot_no'],
                                // 'mfg_date' => $mfg_date,
                                // 'exp_date' => $exp_date,
                                'status_id' => null,
                                'puom' => $productData->puom,
                                'muom' => $value['muom'],
                                'buom' => $productData->buom,
                                'uppp' => $productData->uppp,
                                'muppp' => $productData->muppp,
                                'pqty' => 0,
                                'mqty' => $value['mqty'],
                                'bqty' => 0,
                                'qty' => $qty,
                                'actual_pqty' => $actual_pqty,
                                'actual_mqty' => $actual_mqty,
                                'actual_bqty' => $actual_bqty,
                                'actual_qty' => $qty,
                                'site_id' => $stock->site_id,
                                'area_id' => $stock->area_id,
                                'location_id' => $stock->location_id,
                                // 'location_id' => $value['from_subinventory'], //(belum ditambahkan karena tidak tau masuk ke field  yang mana)
                                'location_code' => $value['location_code_from_row'].'.'.$value['location_code_from_bin'].'.'.$value['location_code_from_level'],
                                // 'dest_site_id' => $stock->site_id,
                                // 'dest_area_id' => $stock->area_id,
                                // 'dest_location_id' => $value['to_subinventory'], //(belum ditambahkan karena tidak tau masuk ke field  yang mana)
                                'dest_location_code' => $value['location_code_to_row'].'.'.$value['location_code_to_bin'].'.'.$value['location_code_to_level'],
                                'confirmed_flag' => $value['confirmed_flag'],
                                'user_id' => $confirmed_by,
                                'created_at' => $confirmed_date
                            ]
                        );
                    } else {
                        $error++;
                        array_push($error_data, array('message' => 'error Product', 'input' => $productData, 'value' => $productData));
                    }
                }

                if ($error > 0) {
                    DB::rollBack();
                    DB::table('iv_epm_api_logs')->insert([
                        'activity' => 'STOCKTRANSFER',
                        'activity_id' => 0,
                        'job_no' => '',
                        'status' => 'FAILED',
                        'body' => json_encode($jsondata),
                        'error' => json_encode($error_data),
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    return 'StockTransfer Submit Failed';
                } else {
                    DB::table('iv_epm_api_logs')->insert([
                        'activity' => 'STOCKTRANSFER',
                        'activity_id' => $transferJobId,
                        'job_no' => $job_no,
                        'status' => 'SUCCESS',
                        'body' => json_encode($jsondata),
                        'created_date' => $confirmed_date
                    ]);
                    DB::commit();
                    return 'Succesfully create new StockTransfer Job with Number ' . $job_no;
                }
            } catch (\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        }
    }
}