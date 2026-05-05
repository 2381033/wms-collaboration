<?php

namespace App\Http\Controllers\Api\EPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use GuzzleHttp\Client as guzzle;

use App\Models\Transaction\Stock\Ledger as Ledger;
use GuzzleHttp\Exception\BadResponseException;

class StockOnHandController extends Controller
{
    public function all(Request $request)
    {
        $error = 0;
        $error_notes = '';
        $error_data = array();
        $jsondatasend = '';
        $datasend = array();
        $EPM_data = DB::table("iv_principal as a")->select("a.id", "a.company_id", "b.principal_id", "a.principal_name", "a.short_name", "b.branch_id")
            ->join("iv_principal_branch as b", "b.principal_id", "a.id")->where("a.short_name", "Mostrans")->first();
            // $data = Ledger::where('company_id', $EPM_data->company_id)->where('principal_id', $EPM_data->principal_id)->where('qtys', '>', 0)->get();
            
            // $location_code = explode('.', $batch->location_code);
            // $row = $location_code[0];
            // $bin = $location_code[1];
            // $lvl = $location_code[2];
        $data = DB::table("iv_stock_ledger as a")->select(
            "c.site_preference as site_id",
            "a.product_code as product_code",
            "a.lot_no as lot_no",
            "a.exp_date as exp_date",
            "a.location_code as location_code",
            "a.qtys as mqty",
            "a.muom as muom",
            DB::raw('(a.qtys*a.muppp) as qty') 
        )
        ->join("iv_site as c", "a.site_id", "c.id")
        ->where('a.company_id', $EPM_data->company_id)
        ->where('a.principal_id', $EPM_data->principal_id)
        ->where('a.qtys', '>', 0)->get();
        // {
        //     "data": [
        //         {
        //             "site_id": "string",
        //             "customer_id": "string",
        //             "product_code": "string",
        //             "lot_no": "string",
        //             "exp_date": "2020-01-01",
        //             "location_code_row": "string",
        //             "location_code_bin": "string",
        //             "location_code_level": "string",
        //             "mqty": "string",
        //             "muom": "string"
        //         }
        //     ]
        // }
        foreach ($data as $key => $value) {
            $location_code = explode('.', $value->location_code);
            $row = $location_code[0];
            $bin = $location_code[1];
            $lvl = $location_code[2];
            $qty = ($value->qty);
            $data = [
                "site_id" => "$value->site_id",
                "customer_id" => "EPM",
                "product_code" => "$value->product_code",
                "lot_no" => "$value->lot_no",
                "exp_date" => ($value->exp_date) ? date("d-m-Y", strtotime($value->exp_date)) : '' ,
                "location_code_row" => "$row",
                "location_code_bin" => "$bin",
                "location_code_level" => "$lvl",
                "mqty" => "$qty",
                "muom" => "$value->muom"
            ];
            array_push($datasend, json_encode($data));
            if (strlen($jsondatasend) > 1) {
                $jsondatasend .= "," . json_encode($data);
            } else {
                $jsondatasend .= json_encode($data);
            }
        }
        return "{\"data\":[" . $jsondatasend . "]}";
    }
}
