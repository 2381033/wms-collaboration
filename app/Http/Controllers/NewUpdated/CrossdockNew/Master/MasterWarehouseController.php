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


class MasterWarehouseController extends Controller
{

    public function index()
    {
        $data = DB::table('cross_mt_warehouse')->get();
        $branch = DB::table('mt_branch')->get();
        return view('master.warehouse', compact('data', 'branch'));
    }

    public function delete($id)
    {
        $data = DB::table('cross_mt_warehouse')->where('id', $id)->delete();

        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }

    public function store(Request $request)
    {
        $data = DB::table('cross_mt_warehouse')->insert([
            'id_branch' =>  $request->id_branch,
            'name'      =>  $request->warehouse_name,
        ]);

        Session::flash('success', 'Data Berhasil Di Simpan..');
        return back();
    }
}
