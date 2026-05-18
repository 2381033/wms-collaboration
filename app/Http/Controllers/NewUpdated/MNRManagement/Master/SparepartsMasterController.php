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
        $equipment = DB::table('mr_master_equipment')->get();
        $locations = DB::table('mr_master_locations')->get();
        $uom = DB::table('rt_uom')->where('active', 'Yes')->get();

        return view('new.MNRManagement.Master.Spareparts.index', compact('spareparts', 'branch', 'equipment', 'locations', 'uom'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'branch_id' => 'required',
            'equipment_id' => 'required',
            'location_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'uom_name' => 'required',
        ]);

        DB::table('mr_master_spareparts')->insert([
            'branch_id' => $request->branch_id,
            'equipment_id' => $request->equipment_id,
            'location_id' => $request->location_id,
            'name' => $request->name,
            'type' => $request->type,
            'uom' => $request->uom_name,
            'created_at' => now(),
          
        ]);
        return redirect()->back()->with('success', 'Spareparts berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'          => 'required',
            'branch_id'   => 'required',
            'equipment'    => 'required',
            'location_id' => 'required',
            'name'        => 'required',
            'type'        => 'required',
            'uom_name'    => 'required',
        ]);

        DB::table('mr_master_spareparts')
            ->where('id', $request->id)
            ->update([
                'branch_id'   => $request->branch_id,
                'equipment'    => $request->equipment_id,
                'location_id' => $request->location_id,
                'name'        => $request->name,
                'type'        => $request->type,
                'uom'         => $request->uom_name,
                'updated_at' => now(),
                // 'updated_at' dihapus agar tidak error jika kolom tidak ada
            ]);
        return redirect()->back()->with('success', 'Spareparts berhasil diperbarui');
    }

    public function delete($id)
    {
        DB::table('mr_master_spareparts')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Spareparts berhasil dihapus');
    }
}
