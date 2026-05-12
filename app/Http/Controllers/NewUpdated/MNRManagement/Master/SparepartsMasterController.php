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

class SparepartsMasterController extends Controller
{


    public function index()
    {
        $spareparts = DB::table('mr_master_spareparts')->get();
        // dd($spareparts);
        $branch = DB::table('mt_branch')->get();
        $tools = DB::table('mr_master_tools')->get();
        $locations = DB::table('mr_master_locations')->get();
        $uom = DB::table('rt_uom')->where('active', 'Yes')->get();

        return view('new.MNRManagement.Master.Spareparts.index', compact('spareparts', 'branch', 'tools', 'locations', 'uom'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'branch_id' => 'required',
            'tools_id' => 'required',
            'location_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'uom_name' => 'required',
        ]);

        DB::table('mr_master_spareparts')->insert([
            'branch_id' => $request->branch_id,
            'tools_id' => $request->tools_id,
            'location_id' => $request->location_id,
            'name' => $request->name,
            'type' => $request->type,
            'uom' => $request->uom_name,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Spareparts berhasil ditambahkan');
    }
}
