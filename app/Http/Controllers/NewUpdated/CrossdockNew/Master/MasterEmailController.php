<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class MasterEmailController extends Controller
{

    public function index()
    {
        $data = DB::table('cross_mt_email')->get();
        $data->map(function ($value) {
            $value->branch = DB::table('mt_branch')->where('id', $value->id_branch)->first()->branch_name ?? '';
        });
        $branch = DB::table('mt_branch')->get();
        return view('master.email', compact('data', 'branch'));
    }

    public function delete($id)
    {
        DB::table('cross_mt_email')->where('id', $id)->delete();

        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }

    public function store(Request $request)
    {
        DB::table('cross_mt_email')->insert([
            'kategori'  =>  $request->kategori,
            'id_branch' =>  $request->id_branch,
            'to'        =>  $request->to,
            'cc'        =>  $request->cc,
            'bcc'       =>  $request->cc,
        ]);

        Session::flash('success', 'Data Berhasil Di Simpan..');
        return back();
    }
}
