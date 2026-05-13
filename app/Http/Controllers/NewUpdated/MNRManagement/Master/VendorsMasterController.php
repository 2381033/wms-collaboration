<?php

namespace App\Http\Controllers\NewUpdated\MNRManagement\Master;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Carbon;

class VendorsMasterController extends Controller
{


    public function index()
    {
        $vendors = DB::table('mr_master_vendors')->get();
        $branch = DB::table('mt_branch')->get();

        return view('new.MNRManagement.Master.Vendors.index', compact('vendors', 'branch'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'branch_id' => 'required',
            'vendor_code' => 'required',
            'vendor_name' => 'required',
        ]);
        DB::table('mr_master_vendors')->insert([
            'branch_id' => $request->branch_id,
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'status' => 'active',
            'created_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Vendor berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'branch_id' => 'required',
            'vendor_code' => 'required',
            'vendor_name' => 'required',
        ]);

        DB::table('mr_master_vendors')->where('id', $request->id)->update([
                'branch_id' => $request->branch_id,
                'vendor_code' => $request->vendor_code,
                'vendor_name' => $request->vendor_name,
            ]);

        return redirect()->back()->with('success', 'Vendor berhasil diupdate!');
    }

    public function delete($id)
    {
        DB::table('mr_master_vendors')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Vendor berhasil dihapus!');
    }
}
