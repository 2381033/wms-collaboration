<?php

namespace App\Http\Controllers\Api\EPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Transaction\Inbound\Vehicle as inboundVehicle;
use App\Models\Master\Product as MasterProduct;
use App\Models\Transaction\Inbound\Detail as inboundDetails;

class InboundController extends Controller
{
    public function index($user_id)
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "a.description", "a.eta")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("a.class_id", "<>", "3")
            ->where("b.user_id", $user_id)
            ->orderBy("a.job_no", "desc")
            ->where("a.received_flag", "Yes")
            ->where("a.allocated_flag", "Yes")
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
                "description" => $value->description,
                "eta" => \Carbon\Carbon::parse($value->eta)->format('d/m/Y')
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
        $error_notes = '';
        $error_data = array();
        $jsondata = json_decode($request->getContent(), true);
        $jsondata = $jsondata['data'][0];
        $EPM_data = DB::table("iv_principal as a")->select("a.id", "a.company_id", "b.principal_id", "a.principal_name", "a.short_name", "b.branch_id")
            ->join("iv_principal_branch as b", "b.principal_id", "a.id")->where("a.short_name", "Mostrans")->first();

        $podo_number = '';
        if (isset($jsondata['po_do_no'])) {
            $podo_number = $jsondata['po_do_no'];
        }
        // echo $podo_number;
        if ($podo_number) {
            $podo_check = DB::table("iv_inbound_detail")->where("po_number", $podo_number)->get();
            // echo "ini check = $podo_check";
            if (sizeof($podo_check) == 0) {
                $manufactur = $jsondata['manufactur'];
                $manufactur_id = null;
                $site_id = 0;
                if ($manufactur) {
                    $manufactur_query = DB::table("iv_manufactur")->where("manufactur_code", $manufactur)->first();
                    if (!$manufactur_query) {
                        // $error++;
                        $error_notes = "unrecognize Manufacture $manufactur!";
                        array_push($error_data, array('message' => 'error manufactur', 'input' => $manufactur, 'value' => $manufactur_query));
                    } else {
                        $manufactur_id = $manufactur_query->id;
                    }
                }
                $site = $jsondata['site'];
                $site_query = DB::table("iv_site")->where("site_name", $site)->first();
                if (!$site_query) {
                    $error++;
                    $error_notes = "unrecognize Site $site!";
                    array_push($error_data, array('message' => 'error site', 'input' => $site, 'value' => $site_query));
                } else {
                    $site_id = $site_query->id;
                }
                $product = $jsondata['product'];
                $productError = array();
                if (sizeof($product) == 0) {
                    $error++;
                    $error_notes = "unrecognize product $product!";
                    array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $product));
                }
                foreach ($product as $key => $value) {
                    $productData = MasterProduct::select('id', 'product_code')->where('product_code', $value['product_code'])
                        ->where('company_id', $EPM_data->company_id)
                        ->where('principal_id', $EPM_data->principal_id)
                        ->first();

                    if (!$productData) {
                        $error++;
                        $error_notes = "unrecognize product $value[product_code]!";
                        array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $value['product_code']));
                        array_push($productError, $value['product_code']);
                    }
                }

                $description = $jsondata['description'];
                $entry_date = $jsondata['entry_date'];
                if (!$entry_date) {
                    $error++;
                    $error_notes = "Entry Date $entry_date can't be empty!";
                    array_push($error_data, array('message' => 'error Entry Date', 'input' => $entry_date, 'value' => $entry_date));
                } else {
                    $entry_date = date('Y-m-d', strtotime($entry_date));
                    if ($entry_date == '1970-01-01') {
                        $error++;
                        $error_notes = "Wrong format for Entry Date $jsondata[entry_date]!";
                        array_push($error_data, array('message' => 'error Entry Date', 'input' => $entry_date, 'value' => $entry_date));
                    }
                    // $entry_date_format = explode('-', $entry_date);
                    // $entry_date = $entry_date_format[2].'-'.$entry_date_format[1].'-'.$entry_date_format[0];
                }
                DB::beginTransaction();
                if ($error > 0) {
                    DB::table('iv_epm_api_logs')->insert([
                        'activity' => 'INBOUND',
                        'activity_id' => 0,
                        'job_no' => '',
                        'status' => 'FAILED',
                        'body' => json_encode($jsondata),
                        'error' => json_encode($error_data),
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    DB::commit();
                    return "Failed to add new Job with error notes : $error_notes";
                } else {
                    try {
                        // Job Number
                        $confirmed_by = 'Create by API';
                        $confirmed_date = \Carbon\Carbon::now();
                        $job_date = \Carbon\Carbon::today();
                        $year = $job_date->year;
                        $month = $job_date->month;
                        $job = inboundJob::where('company_id', 1)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

                        if (is_null($job)) {
                            $increment = 1;
                        } else {
                            $increment = substr($job, 7, 4) + 1;
                        }

                        $job_no = '1' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

                        $inboundJobId = InboundJob::insertGetId([
                            'company_id' => $EPM_data->company_id, // set ke MKT
                            'branch_id' => $EPM_data->branch_id,
                            'principal_id' => $EPM_data->principal_id,
                            'job_no' => $job_no,
                            'job_date' => $job_date,
                            'class_id' => 1, // set ke regular
                            'mode_id' => 1, // set ke land
                            'description' => $description,
                            'eta' => $entry_date,
                            'remarks' => $request->remarks,
                            'entry_date' => $entry_date,
                            'created_at' => $confirmed_date,
                            'user_id' => $confirmed_by,
                            'entry_date' => $entry_date
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
                                $exp_date = '1970-01-01';
                                if (!$value['exp_date'] && $productData->expired_flag == "Yes") {
                                    $error++;
                                    $error_notes = "exp date can't be empty!";
                                    array_push($error_data, array('message' => $error_notes, 'input' => 'error qty not settle', 'value' => ''));
                                } else if($value['exp_date'] && $productData->expired_flag == "Yes") {
                                    $exp_date = date('Y-m-d', strtotime($value['exp_date']));
                                    if ($exp_date <= \Carbon\Carbon::now()) {
                                        $error++;
                                        $error_notes = "Wrong format or expired date is less than today for Entry Date $value[exp_date]!";
                                        array_push($error_data, array('message' => 'error Entry Date', 'input' => $exp_date, 'value' => $exp_date));
                                    }
                                } else {
                                    $exp_date = '';
                                }
                                $qty = ($value['mqty']);
                                $actual_pqty = ($qty - ($qty % $productData->muppp)) / $productData->muppp;
                                $actual_mqty = ($qty % $productData->muppp);
                                if ($actual_mqty != 0) {
                                    $error++;
                                    $error_notes = "the amount does not meet the warehouse storage criteria.\nfor $value[product_code] product, the number of items that must be stored is a multiple of $productData->muppp $productData->muom = 1 $productData->puom !";
                                    array_push($error_data, array('message' => $error_notes, 'input' => 'error qty not settle', 'value' => $qty));
                                } else {
                                    $inboundDetailId = inboundDetails::insertGetId(
                                        [
                                            'company_id' => $EPM_data->company_id,
                                            'inbound_id' => $inboundJobId,
                                            'principal_id' => $EPM_data->principal_id,
                                            'job_no' => $job_no,
                                            'vehicle_no' => '-',
                                            'product_id' => $productData->id,
                                            'product_code' => $value['product_code'],
                                            'po_number' => $podo_number,
                                            'lot_no' => $value['lot_no'],
                                            // 'mfg_date' => $mfg_date,
                                            'exp_date' => $exp_date,
                                            'puom' => $productData->puom,
                                            'muom' => $value['muom'],
                                            'buom' => $productData->buom,
                                            'uppp' => $productData->uppp,
                                            'muppp' => $productData->muppp,
                                            'pqty' => $actual_pqty,
                                            'mqty' => $actual_mqty,
                                            'bqty' => 0,
                                            'qty' => $qty,
                                            'actual_pqty' => $actual_pqty,
                                            'actual_mqty' => $actual_mqty,
                                            'actual_bqty' => 0,
                                            'actual_qty' => $qty,
                                            'manufactur_id' => $manufactur_id,
                                            'status_id' => null,
                                            'qrcode' => Str::random(30),
                                            'user_id' => $confirmed_by,
                                            'created_at' => $confirmed_date
                                        ]
                                    );
                                }
                            } else {
                                $error++;
                                $error_notes = "unrecognize product $product!";
                                array_push($error_data, array('message' => 'error Product', 'input' => $productData, 'value' => $productData));
                            }
                        }

                        if ($error > 0) {
                            DB::rollBack();
                            DB::table('iv_epm_api_logs')->insert([
                                'activity' => 'INBOUND',
                                'activity_id' => 0,
                                'job_no' => '',
                                'status' => 'FAILED',
                                'body' => json_encode($jsondata),
                                'error' => json_encode($error_data),
                                'created_date' => \Carbon\Carbon::now()
                            ]);
                            return "Failed to add new Job with error notes : $error_notes";
                        } else {
                            $logHeaderId = DB::table('iv_epm_api_logs')->insertGetId([
                                'activity' => 'INBOUND',
                                'activity_id' => $inboundJobId,
                                'job_no' => $job_no,
                                'status' => 'SUCCESS',
                                'body' => json_encode($jsondata),
                                'created_date' => $confirmed_date
                            ]);

                            foreach ($product as $key => $value) {
                                $productData = MasterProduct::where('product_code', $value['product_code'])
                                    ->where('company_id', $EPM_data->company_id)
                                    ->where('principal_id', $EPM_data->principal_id)
                                    ->first();
                                if ($productData) {
                                    $qty = ($value['mqty']);
                                    DB::table('iv_epm_api_log_details')->insert(
                                        [
                                            'header_id' => $logHeaderId,
                                            'product_id' => $productData->id,
                                            'product_code' => $value['product_code'],
                                            'lot_no' => $value['lot_no'],
                                            'muom' => $value['muom'],
                                            'mqty' => $value['mqty']
                                            // 'rqty' => $dataini,
                                        ]
                                    );
                                }
                            }
                            DB::commit();
                            return 'Succesfully create new Inbound Job with Number ' . $job_no;
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();

                        $response["error"] = "true";
                        dd($e);
                        $response["message"] = $e->getMessage();

                        return $response;
                    }
                }
            } else {
                DB::table('iv_epm_api_logs')->insert([
                    'activity' => 'INBOUND',
                    'activity_id' => 0,
                    'job_no' => '',
                    'status' => 'FAILED',
                    'body' => json_encode($jsondata),
                    'error' => json_encode($error_data),
                    'created_date' => \Carbon\Carbon::now()
                ]);
                DB::commit();
                return 'Job with PO DO Number already Created';
            }
        } else {
            DB::table('iv_epm_api_logs')->insert([
                'activity' => 'INBOUND',
                'activity_id' => 0,
                'job_no' => '',
                'status' => 'FAILED',
                'body' => json_encode($jsondata),
                'error' => json_encode($error_data),
                'created_date' => \Carbon\Carbon::now()
            ]);
            DB::commit();
            return 'Failed To retrieve DO Number';
        }
    }
    public function test()
    {
        echo "<pre>";
        print_r('$request');
        echo "</pre>";
        return response()->json("dapat response disini", 200);
    }
}
