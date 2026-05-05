<?php

namespace App\Http\Controllers\Api\EPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Master\Product as MasterProduct;

class ProductController extends Controller
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

        $total_proccess = 0;
        $total_success = 0;
        $total_error = 0;
        $list_proccess = array();
        $list_success = array();
        $list_error = array();
        $list_data = array();

        $batch_no = '';
        if (isset($jsondata['batch_no'])) {
            $batch_no = $jsondata['batch_no'];
        }
        // echo $batch_no;
        if ($batch_no) {
            $batch_no_check = DB::table("iv_epm_api_logs")->where('body', 'like', "%$batch_no%")->where("status", 'SUCCESS')->where("activity","PRODUCT")->get();
            // echo "ini check = $batch_no_check";
            if (sizeof($batch_no_check) == 0) {

                $product = $jsondata['product'];
                $productError = array();
                if (sizeof($product) == 0) {
                    $error++;
                    $error_notes = "no data product processed!";
                    array_push($error_data, array('message' => 'error Product', 'input' => '', 'value' => 'no product'));
                }
                foreach ($product as $key => $value) {
                    $productData = MasterProduct::select('id', 'product_code')->where('product_code', $value['item_code'])
                        ->where('company_id', $EPM_data->company_id)
                        ->where('principal_id', $EPM_data->principal_id)
                        ->first();

                    array_push($list_proccess, $value['item_code']);
                    $total_proccess++;
                    if (isset($productData['product_code'])) {
                        array_push($list_error, $value['item_code']);
                        $total_error++;
                    } else {
                        array_push($list_success, $value['item_code']);
                        array_push($list_data, $value);
                        $total_success++;
                    }
                }

                DB::beginTransaction();
                if ($error > 0) {
                    DB::table('iv_epm_api_logs')->insert([
                        'activity' => 'PRODUCT',
                        'activity_id' => 0,
                        'job_no' => '',
                        'status' => 'FAILED',
                        'body' => json_encode($jsondata),
                        'error' => json_encode($error_data),
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    DB::commit();
                    return "Failed to process with error notes : $error_notes";
                } else {
                    try {
                        // Job Number
                        $confirmed_by = 'Create by API';
                        $confirmed_date = \Carbon\Carbon::now();
                        $job_date = \Carbon\Carbon::today();

                        $category         = DB::table('iv_product_category')->where('category_name', 'Finish Goods')->where('principal_id', $EPM_data->principal_id)->first();
                        $group_code       = DB::table('iv_product_group')->where('principal_id', $EPM_data->principal_id)->where('group_code', 'MGD')->first();
                        $brand_code       = DB::table('iv_product_brand')->where('principal_id', $EPM_data->principal_id)->where('brand_code', 'EPM')->first();

                        // echo "<pre>";
                        // print_r($category);
                        // print_r($group_code);
                        // print_r($brand_code);
                        // print_r($list_data);
                        // die();

                        foreach ($list_data as $key => $list) {
                            DB::table('iv_product')->insert([
                                'company_id'    => '1',
                                'principal_id'  => $EPM_data->principal_id,
                                'product_code'  => $list['item_code'],
                                'product_name'  => $list['item_name'],
                                'category_id'   => $category->id,
                                'group_id' => $group_code->id,
                                'brand_id' => $brand_code->id,
                                'pick_criteria'  => 'FEFO',
                                'unit_level'  => '1',
                                'puom'  => $list['secondary_uom_code'],
                                'muom'  => $list['primary_uom_code'],
                                'buom'  => $list['primary_uom_code'],
                                'uppp'  => '1',
                                'muppp'  => $list['conversi_rate'],
                                'manufactur_id'  => null,
                                'batch_flag'  => 'No',
                                'expired_flag'  => 'Yes',
                                'freeze_flag'  => 'No',
                                'length'  => '0',
                                'width'  => '0',
                                'dimensions_unit'  => '0',
                                'volume'  => $list['volume'],
                                'volume_unit'  =>  $list['unit_volume'],
                                'gross_weight'  =>  '0',
                                'net_weight'  =>  $list['net_weight'],
                                'weight_unit'  =>  $list['unit_weight'],
                                'temperature'  =>  '0',
                                'shelf_life'  =>  '0',
                                'freeze_day'  =>  '0',
                                'base_price'  =>  '0',
                                'active'  =>  'Yes',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }

                        if ($error > 0) {
                            DB::rollBack();
                            DB::table('iv_epm_api_logs')->insert([
                                'activity' => 'PRODUCT',
                                'activity_id' => 0,
                                'job_no' => '',
                                'status' => 'FAILED',
                                'body' => json_encode($jsondata),
                                'error' => json_encode($error_notes),
                                'created_date' => \Carbon\Carbon::now()
                            ]);
                            return "Failed to process with error notes : $error_notes";
                        } else {
                            $bukanerror = array();
                            array_push($bukanerror,array('list proccess' => $list_proccess,'list success' => $list_success,'listerror' => $list_error));
                            DB::table('iv_epm_api_logs')->insert([
                                'activity' => 'PRODUCT',
                                'status' => 'SUCCESS',
                                'body' => json_encode($jsondata),
                                'error' => json_encode($bukanerror),
                                'created_date' => $confirmed_date
                            ]);

                            DB::commit();
                            $textResult = "Total data process = $total_proccess data \nTotal product success = $total_success \nTotal product error = $total_error";
                            return $textResult;
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();

                        $response["error"] = "true";
                        $response["message"] = $e->getMessage();
                        echo "<pre>";
                        print_r($e);

                        return $response;
                    }
                }
            } else {
                DB::table('iv_epm_api_logs')->insert([
                    'activity' => 'PRODUCT',
                    'status' => 'FAILED',
                    'body' => json_encode($jsondata),
                    'error' => json_encode($error_data),
                    'created_date' => \Carbon\Carbon::now()
                ]);
                DB::commit();
                return 'Batch Number Already send and processed';
            }
        } else {
            DB::table('iv_epm_api_logs')->insert([
                'activity' => 'PRODUCT',
                'status' => 'FAILED',
                'body' => json_encode($jsondata),
                'error' => json_encode($error_data),
                'created_date' => \Carbon\Carbon::now()
            ]);
            DB::commit();
            return 'Batch No can`t be empty';
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
