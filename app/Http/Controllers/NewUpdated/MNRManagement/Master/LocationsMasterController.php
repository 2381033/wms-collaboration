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

class LocationsMasterController extends Controller
{


    public function index()
    {
        $locations = DB::table('mr_master_locations')->get();
        $branch = DB::table('mt_branch')->get();
        return view('new.MNRManagement.Master.Location.index', compact('locations', 'branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'     => 'required',
            'location_code' => 'required',
            'location_name' => 'required',
        ]);
        DB::table('mr_master_locations')->insert([
            'branch_id'     => $request->branch_id,
            'location_code' => $request->location_code,
            'location_name' => $request->location_name,
            'created_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Location berhasil ditambahkan!');
    }
}
