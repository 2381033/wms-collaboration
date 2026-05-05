<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function containerType() {
        $detail = DB::table("iv_container_type as a")
                        ->where("a.active", "Yes")
                        ->orderBy("a.id", "asc")
                        ->get();

        $list = Array();

        foreach ($detail as $value) {
            $list[] = [
                "id"=>$value->id,
                "description"=>$value->type_name
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "Berhasil";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function containerSize() {
        $detail = DB::table("iv_container_size as a")
                        ->where("a.active", "Yes")
                        ->whereIn("a.id", [1, 2])
                        ->orderBy("a.id", "asc")
                        ->get();

        $list = Array();

        foreach ($detail as $value) {
            $list[] = [
                "id"=>$value->id,
                "description"=>$value->size_name
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "Berhasil";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function vehicleSize() {
        $detail = DB::table("iv_container_size as a")
                        ->where("a.active", "Yes")
                        ->orderBy("a.id", "asc")
                        ->get();

        $list = Array();

        foreach ($detail as $value) {
            $list[] = [
                "id"=>$value->id,
                "description"=>$value->size_name
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "Berhasil";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function principal($user_id) {
        $detail = DB::table("iv_principal as a")
                        ->select("a.*")
                        ->join("users_principal as b", "a.id", "b.principal_id")
                        ->where("a.active", "Yes")
                        ->where("b.user_id", $user_id)
                        ->orderBy("a.id", "asc")
                        ->get();

        $list = Array();

        foreach ($detail as $value) {
            $list[] = [
                "id"=>$value->id,
                "description"=>$value->principal_name
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "Berhasil";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function vendor() {
        $detail = DB::table("tm_vendor as a")
                        ->where("a.active", "Yes")
                        ->orderBy("a.id", "asc")
                        ->get();

        $list = Array();

        foreach ($detail as $value) {
            $list[] = [
                "id"=>$value->id,
                "description"=>$value->vendor_name
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "Berhasil";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }
}