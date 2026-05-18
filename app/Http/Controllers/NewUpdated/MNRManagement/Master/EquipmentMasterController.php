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

class EquipmentMasterController extends Controller
{


    public function index()
    {
        $equipment = DB::table('mr_master_equipment')->get();

        return view('new.MNRManagement.Master.Equipment.index', compact('equipment'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        DB::table('mr_master_equipment')->insert([
            'code' => $request->code,
            'name' => $request->name,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Equipment berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'code' => 'required',
            'name' => 'required',
        ]);

        DB::table('mr_master_equipment')->where('id', $request->id)->update([
                'code' => $request->code,
                'name' => $request->name,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Equipment berhasil diupdate!');
    }

    public function delete($id)
    {
        DB::table('mr_master_equipment')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Equipment berhasil dihapus!');
    }
}
