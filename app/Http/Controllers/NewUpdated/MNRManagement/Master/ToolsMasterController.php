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

class ToolsMasterController extends Controller
{


    public function index()
    {
        $tools = DB::table('mr_master_tools')->get();

        return view('new.MNRManagement.Master.Tools.index', compact('tools'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        DB::table('mr_master_tools')->insert([
            'code' => $request->code,
            'name' => $request->name,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tools berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'code' => 'required',
            'name' => 'required',
        ]);

        DB::table('mr_master_tools')->where('id', $request->id)->update([
                'code' => $request->code,
                'name' => $request->name,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Tools berhasil diupdate!');
    }

    public function delete($id)
    {
        DB::table('mr_master_tools')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Tools berhasil dihapus!');
    }
}
