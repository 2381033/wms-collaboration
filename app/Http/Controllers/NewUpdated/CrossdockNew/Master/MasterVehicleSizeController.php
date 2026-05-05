<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UploadCustomerImport;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class MasterVehicleSizeController extends Controller
{

    public function index()
    {
        $data = DB::table('cross_mt_vehicle_size')->get();
        return view('master.vehicle_size', compact('data'));
    }

    public function delete($id)
    {
        $data = DB::table('cross_mt_vehicle_size')->where('id', $id)->delete();

        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }

    public function store(Request $request)
    {
        $data = DB::table('cross_mt_vehicle_size')->insert([
            'name' =>  $request->name,
        ]);

        Session::flash('success', 'Data Berhasil Di Simpan..');
        return back();
    }
}
