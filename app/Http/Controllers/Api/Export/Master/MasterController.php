<?php

namespace App\Http\Controllers\Api\Export\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    private function myBranch($username)
    {
        $idUser = DB::table('users')
            ->select('id')
            ->where('username', $username)
            ->value('id');
        $data = DB::table('sm_user_branch')
            ->where('user_id', $idUser)
            ->value('branch_id');
        return $data;
    }

    public function branchMe($username)
    {
        $idUser = DB::table('users')
            ->select('id')
            ->where('username', $username)
            ->value('id');
        $data = DB::table('sm_user_branch')
            ->where('user_id', $idUser)
            ->value('branch_id');
        $data = DB::table('mt_branch')
            ->where('id', $data)
            ->first();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getVehicleNo()
    {
        $data = DB::table("ex_gate_in_cargo")
            ->select('vehicle_number', 'id')
            ->where("confirmed_flag", "No")
            ->get();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getVehicleType()
    {
        $data = DB::table("ex_master_vehicle")
            ->where("status", 1)
            ->pluck('vehicle')->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getChecker($username)
    {
        $auth_group_id = DB::table('auth_group')
        ->where('name', 'Checker')
        ->value('id');

        $data = DB::table("users")
            ->where("auth_group_id", $auth_group_id)
            ->pluck('username')->toArray();

        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getForwarder($username)
    {
        $data = DB::table("mt_forwarder")
            ->select('forwarder_name')
            ->where('branch_id', $this->myBranch($username))
            ->pluck('forwarder_name')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getLocation($username)
    {
        $data = DB::table("ex_location")
            ->select('location_code')
            ->where('branch_id', $this->myBranch($username))
            ->where('active', 'Yes')
            ->pluck('location_code')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getShipper($username)
    {
        $data = DB::table("mt_shipper")
            ->select('shipper_name')
            ->where('branch_id', $this->myBranch($username))
            ->pluck('shipper_name')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getConsignee($username)
    {
        $data = DB::table("mt_consignee")
            ->select('consignee_name')
            ->where('branch_id', $this->myBranch($username))
            ->pluck('consignee_name')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getDestination()
    {
        $data = DB::table("mt_country")
            ->select('name')
            ->pluck('name')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }

    public function getUom()
    {
        $data = DB::table("rt_uom")
            ->select('code')
            ->pluck('code')
            ->toArray();
        return response()->json(['pesan' => 'Berhasil', 'data' => $data]);
    }
}
