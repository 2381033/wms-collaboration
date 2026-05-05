<?php

namespace App\Http\Controllers\Api\EPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Outbound\Job as outboundJob;
use App\Models\Transaction\Outbound\Order as outboundOrder;
use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Master\Customer as MasterCustomer;
use App\Models\Master\Product as MasterProduct;

class OutboundController extends Controller
{
    public function index($user_id)
    {
        $list = DB::table("iv_outbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "etd", "remarks")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("a.class_id", "<>", "3")
            ->where("b.user_id", $user_id)
            ->where("a.allocated_flag", "Yes")
            ->where("a.confirmed_flag", "No")
            ->orderBy("a.job_date", "asc")
            ->get();

        $response = array();

        foreach ($list as $value) {
            $response[] = [
                "id" => $value->id,
                "principal_name" => "Principal Name : " . $value->principal_name,
                "class_name" => "Job Class : " . $value->class_name,
                "mode_name" => "Moda Name : " . $value->mode_name,
                "job_no" => "Job No : " . $value->job_no,
                "job_date" => "Job Date : " . \Carbon\Carbon::parse($value->job_date)->format("d/m/Y"),
                "description" => "Description : " . $value->description,
                "reference_no" => $value->reference_no,
                "reference_other" => $value->reference_other == null ? "" : $value->reference_other,
                "etd" => \Carbon\Carbon::parse($value->etd)->format("d/m/Y"),
                "remarks" => $value->remarks == null ? "" : $value->remarks
            ];
        }

        return response()->json(["pesan" => "Berhasil", "job" => $response], 200);
    }

    public function submit(Request $request)
    {
        $error = 0;
        $error_data = array();
        $jsondata = json_decode($request->getContent(), true);
        $jsondata = $jsondata['data'][0];
        $EPM_data = DB::table("iv_principal as a")->select("a.id", "a.company_id", "b.principal_id", "a.principal_name", "a.short_name", "b.branch_id")
            ->join("iv_principal_branch as b", "b.principal_id", "a.id")->where("a.short_name", "Mostrans")->first();
        $order_number = '';
        if (isset($jsondata['order_no'])) {
            $order_number = $jsondata['order_no'];
        }
        if ($order_number) {
            $order_check = DB::table("iv_outbound_detail")->where("order_no", $order_number)->get();

            if (sizeof($order_check) == 0) {
                $product = $jsondata['product'];
                $productError = array();
                if (sizeof($product) == 0) {
                    $error++;
                    $error_notes = "There are no product detail send. please check your data";
                    array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $product));
                }
                foreach ($product as $key => $value) {
                    $productData = MasterProduct::select('id', 'product_code')->where('product_code', $value['product_code'])
                        ->where('company_id', $EPM_data->company_id)
                        ->where('principal_id', $EPM_data->principal_id)
                        ->first();

                    if (!$productData) {
                        $error++;
                        $error_notes = "There are no product $value[product_code] on list.";
                        array_push($error_data, array('message' => 'error Product', 'input' => $product, 'value' => $value['product_code']));
                        array_push($productError, $value['product_code']);
                    }
                }

                $description = $jsondata['description'];
                $entry_date = $jsondata['create_at'];
                if (!$entry_date) {
                    $error++;
                    $error_notes = "Please insert cretaed data";
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
                        'activity' => 'OUTBOUND',
                        'activity_id' => 0,
                        'job_no' => '',
                        'status' => 'FAILED',
                        'body' => json_encode($jsondata),
                        'error' => json_encode($error_data),
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    DB::commit();
                    return "Outbound Submit Failed with notes : $error_notes";
                } else {
                    try {
                        // Job Number
                        $confirmed_by = 'Create by API';
                        $confirmed_date = \Carbon\Carbon::now();
                        $job_date = \Carbon\Carbon::today();
                        $year = $job_date->year;
                        $month = $job_date->month;

                        $job = outboundJob::where('company_id', 1)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

                        if (is_null($job)) {
                            $increment = 1;
                        } else {
                            $increment = substr($job, 7, 4) + 1;
                        }

                        $job_no = '2' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

                        $outboundJobId = outboundJob::insertGetId([
                            'company_id' => $EPM_data->company_id, // set ke MKT
                            'branch_id' => $EPM_data->branch_id,
                            'principal_id' => $EPM_data->principal_id,
                            'job_no' => $job_no,
                            'job_date' => $job_date,
                            'class_id' => 1, // set ke regular
                            'mode_id' => 1, // set ke land
                            'description' => $description,
                            'etd' => $entry_date,
                            'remarks' => $request->remarks,
                            'entry_date' => $entry_date,
                            'created_at' => $confirmed_date,
                            'user_id' => $confirmed_by
                        ]);
                        // EPM0001 => PMS
                        // EPM0002 => BAC
                        // EPM0003 => MDN
                        // EPM0004 => LSE
                        $customer_epm = $value['customer'];
                        foreach ($product as $key => $value) {
                            switch ($value['customer']) {
                                case 'PMS':
                                    $customer_epm = 'EPM0001';
                                    break;
                                case 'BAC':
                                    $customer_epm = 'EPM0002';
                                    break;
                                case 'MDN':
                                    $customer_epm = 'EPM0003';
                                    break;
                                case 'LSE':
                                    $customer_epm = 'EPM0004';
                                    break;
                                default:
                                    $customer_epm = $value['customer'];
                                    break;
                            }
                            $Customer_data = MasterCustomer::where('company_id', $EPM_data->company_id)
                                ->where('principal_id', $EPM_data->principal_id)
                                ->where('customer_code', $value['customer'])
                                ->first();

                            if (isset($Customer_data->id)) {
                                $outboundOrderId = outboundOrder::insertGetId([
                                    'company_id' => $EPM_data->company_id,
                                    'principal_id' => $EPM_data->principal_id,
                                    'outbound_id' => $outboundJobId,
                                    'job_no' => $job_no,
                                    'customer_id' => $Customer_data->id,
                                    'order_no' => $order_number,
                                    'po_number' => $value['po_no'],
                                    'order_date' => $entry_date,
                                    'due_date' => $entry_date,
                                    'user_id' => $confirmed_by
                                ]);
                                $site = $value['site'];
                                $site_id = 0;
                                $site_query = DB::table("iv_site")->where("site_name", $site)->first();
                                if (!$site_query) {
                                    $error++;
                                    $error_notes = "There is no site $site in our database. please check again!";
                                    array_push($error_data, array('message' => 'error site', 'input' => $site, 'value' => $site_query));
                                } else {
                                    $site_id = $site_query->id;
                                }

                                $productData = MasterProduct::where('product_code', $value['product_code'])
                                    ->where('company_id', $EPM_data->company_id)
                                    ->where('principal_id', $EPM_data->principal_id)
                                    ->first();

                                $locationselected = $value['location_code_row'] . '.' . str_pad($value['location_code_bin'], 3, "0", STR_PAD_LEFT) . '.' . $value['location_code_level'];
                                if ($error == 0) {
                                    if (isset($productData->id)) {
                                        $locationData = DB::table("iv_location")->where('company_id', $EPM_data->company_id)
                                            ->where('location_code', $locationselected)
                                            ->first();
                                        if (isset($locationData->id)) {
                                            $qty = ($value['mqty']);
                                            $data_qty = ($qty - ($qty % $productData->muppp)) / $productData->muppp;
                                            $stockcheck = DB::table("iv_stock_ledger as a")
                                                ->select("a.id", "a.site_id", "b.site_name", "a.area_id", "c.area_name", "a.location_id", "a.location_code", "a.qtya", "a.muom", "a.muppp")
                                                ->leftjoin("iv_site as b", "a.site_id", "b.id")
                                                ->leftjoin("iv_site_area as c", "a.area_id", "c.id")
                                                ->join("users_site as d", "a.site_id", "d.site_id")
                                                ->where("a.company_id", $EPM_data->company_id)
                                                ->where("a.freeze_flag", "No")
                                                ->where("a.principal_id", $EPM_data->principal_id)
                                                ->where("a.product_id",  $productData->id)
                                                ->where("a.site_id",  $site_id)
                                                ->where("a.location_code", $locationData->location_code)
                                                ->groupBy("a.site_id", "b.site_name", "a.area_id", "c.area_name", "a.location_id", "a.location_code")
                                                ->orderBy("a.id", "desc");
                                            $stockdataavailable = $stockcheck->first();
                                            $stockdatacheck = $stockcheck->where("a.qtys", ">=", $data_qty)->first();

                                            if (isset($stockdatacheck->id)) {
                                                $qty = ($value['mqty']);

                                                $actual_pqty = ($qty - ($qty % $productData->muppp)) / $productData->muppp;
                                                $actual_mqty = ($qty % $productData->muppp);
                                                if ($actual_mqty > 0) {
                                                    $error++;
                                                    $error_notes = "the amount does not meet the warehouse storage criteria.\nfor $value[product_code] product, the number of items that must be stored is a multiple of $productData->muppp $productData->muom = 1 $productData->puom !";
                                                    array_push($error_data, array('message' => 'error locationData', 'input' => $locationData, 'value' => $locationselected));
                                                } else {
                                                    $outboundDetailId = outboundDetails::insertGetId(
                                                        [
                                                            'company_id' => $EPM_data->company_id,
                                                            'outbound_id' => $outboundJobId,
                                                            'order_id' => $outboundOrderId,
                                                            'principal_id' => $EPM_data->principal_id,
                                                            'customer_id' => $Customer_data->id,
                                                            'job_no' => $job_no,
                                                            'order_no' => $order_number,
                                                            'product_id' => $productData->id,
                                                            'product_code' => $value['product_code'],
                                                            'lot_no' => $value['lot_no'],
                                                            // 'mfg_date' => $mfg_date,
                                                            // 'exp_date' => $exp_date,
                                                            'site_id' => $stockdatacheck->site_id,
                                                            'area_id' => $stockdatacheck->area_id,
                                                            'location_from_id' => $locationData->id,
                                                            'location_from' => $locationData->location_code,
                                                            'location_to_id' => $locationData->id,
                                                            'location_to' => $locationData->location_code,
                                                            'puom' => $productData->puom,
                                                            'muom' => $value['muom'],
                                                            'buom' => $productData->buom,
                                                            'uppp' => $productData->uppp,
                                                            'muppp' => $productData->muppp,
                                                            'pqty' => $actual_pqty,
                                                            'mqty' => $actual_mqty,
                                                            'bqty' => 0,
                                                            'qty' => $qty,
                                                            // 'manufactur_id' => $manufactur_id,
                                                            'user_id' => $confirmed_by,
                                                            'created_at' => $confirmed_date
                                                        ]
                                                    );
                                                }
                                                // echo "sampai sini aman";
                                            } else {
                                                $stockqtya = $stockdataavailable->qtya * $stockdataavailable->muppp;
                                                $stockreqq = $value['mqty'];
                                                $error++;
                                                $error_notes = "the requested stock is greater than the available stock for product $productData->product_code in $locationData->location_code.\nstock requested = $stockreqq\nstock available = $stockqtya";
                                                array_push($error_data, array('message' => 'error stock ledger', 'input' => $stockdatacheck, 'value' => array(
                                                    'company_id' => $EPM_data->company_id,
                                                    'principal_id' => $EPM_data->principal_id,
                                                    'product_id' => $productData->id,
                                                    'site_id' => $site_id,
                                                    'locationData' => $locationData->location_code,
                                                )));
                                            }
                                        } else {
                                            $error++;
                                            $error_notes = "there is no stock in $locationData->location_code for product $productData->product_code!";
                                            array_push($error_data, array('message' => 'error locationData', 'input' => $locationData, 'value' => $locationselected));
                                        }
                                    } else {
                                        $error++;
                                        $error_notes = "unrecognize $productData->product_code!";
                                        array_push($error_data, array('message' => 'error Product', 'input' => $productData, 'value' => $productData));
                                    }
                                }
                            } else {
                                $error++;
                                $error_notes = "unrecognize customer code $customer_epm!";
                                array_push($error_data, array('message' => 'error Customer_data', 'input' => $Customer_data, 'value' => $Customer_data));
                            }
                        }
                        if ($error > 0) {
                            DB::rollBack();
                            DB::table('iv_epm_api_logs')->insert([
                                'activity' => 'OUTBOUND',
                                'activity_id' => 0,
                                'job_no' => '',
                                'status' => 'FAILED',
                                'body' => json_encode($jsondata),
                                'error' => json_encode($error_data),
                                'created_date' => \Carbon\Carbon::now()
                            ]);
                            return "Outbound Submit Failed with notes : $error_notes";
                        } else {
                            $logHeaderId = DB::table('iv_epm_api_logs')->insertGetId([
                                'activity' => 'OUTBOUND',
                                'activity_id' => $outboundJobId,
                                'job_no' => $job_no,
                                'status' => 'SUCCESS',
                                'body' => json_encode($jsondata),
                                'error' => json_encode($error_data),
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
                            return 'Succesfully create new Outbound Job with Number ' . $job_no;
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();

                        $response["error"] = "true";
                        $response["message"] = $e->getMessage();
                        dd($e);
                        return $response;
                    }
                }
            } else {
                DB::table('iv_epm_api_logs')->insert([
                    'activity' => 'OUTBOUND',
                    'activity_id' => 0,
                    'job_no' => '',
                    'status' => 'FAILED',
                    'body' => json_encode($jsondata),
                    'error' => json_encode($error_data),
                    'created_date' => \Carbon\Carbon::now()
                ]);
                DB::commit();
                return 'Job with Order Number already Created';
            }
        } else {
            DB::table('iv_epm_api_logs')->insert([
                'activity' => 'OUTBOUND',
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
}
