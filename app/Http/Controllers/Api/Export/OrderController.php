<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index($id, $param = "") {
        $search = $param == null ? '%' : '%' . $param . '%';

        $list = DB::table("ex_outbound_order as a")
                    ->select( "a.*", "b.consignee_name" )
                    ->join("mt_consignee as b", "a.consignee_id", "b.id")
                    ->where("a.status_flag", "Open")
                    ->where("a.job_id", $id)                  
                    ->where(function($query) use($search) {
                        $query->where("b.consignee_name","LIKE",$search)
                            ->orWhere("a.po_number","LIKE",$search)
                            ->orWhere("a.peb_no","LIKE",$search);
                    })
                    ->get();

        $response = Array();
         
        foreach ($list as $value) {
            $response[] = [
                "id"=>$value->id,
                "consignee_name"=>$value->consignee_name,
                "po_number"=>$value->po_number,
                "peb_no"=>$value->peb_no,
                "qty_cargo"=>$value->qty_cargo,
                "cbm"=>$value->cbm,
                "weight"=>$value->weight,
                "total_pallet"=>$value->total_pallet,                
            ];
        }

        return response()->json(['pesan' => 'Berhasil', 'job' => $response], 200);
    }
}