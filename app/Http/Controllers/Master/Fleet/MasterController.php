<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function index() {
        $branch_list = DB::table('mt_branch')->where("active", "Yes")->get();
        $group_list = DB::table('fm_inspection_group')->where("active", "Yes")->get();
        $type_list = DB::table('fm_vehicle_type')->where("active", "Yes")->get();        

        $data = [
            "branch_list" => $branch_list,
            "group_list" => $group_list,
            "type_list" => $type_list
        ];

        return view('master.fleet.master', $data);
    }
}