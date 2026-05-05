<?php

namespace App\Http\Controllers\NewUpdated\ToolsManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;

class MasterController extends Controller
{
    public function index()
    {
        $branch = $this->myBranch();
        $tools = DB::table('mr_tools as a')
            ->join('mr_area as b', 'a.area_id', '=', 'b.id')
            ->get();
        $spareparts = DB::table('mr_spareparts as a')
            ->leftJoin('mr_tools as b', 'a.tool_id', '=', 'b.id')
            ->get();
        return view('new.ToolsManagement.Master.index', compact('tools', 'spareparts'));
    }

    private function myBranch()
    {
        $branch = DB::table('sm_user_branch as a')
            ->join('mt_branch as b', 'a.branch_id', '=', 'b.id')
            ->where('a.user_id', Auth::user()->id)
            ->where('b.active', 'Yes')
            ->get();

        return $branch;
    }

    public function getMaster($type)
    {
        $table = 'mr_' . $type;
        $query = DB::table($table)
            ->join('mt_branch as b', $table . '.branch_id', '=', 'b.id')
            ->whereIn($table . '.branch_id', $this->myBranch()->pluck('branch_id'));
        if ($type === 'spareparts') {
            $query->leftJoin('mr_tools as t', $table . '.tool_id', '=', 't.id');
            $query->select(
                $table . '.*',
                'b.branch_name',
                't.code_name as tool_code_name',
                't.name as tool_name'
            );
        } else {
            $query->select(
                $table . '.*',
                'b.branch_name'
            );
        }
        $data = $query->get();
        return response()->json([
            'data' => $data,
            'status' => 'success',
        ]);
    }

    public function store(Request $request)
    {
        $validate = DB::table('iv_gate_in_cargo')
            ->where('vehicle_number', $request->no_mobil)
            ->whereNull('gate_out_at')
            ->count();

        if ($validate > 0) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Vehicle already entered today'
            ]);
        }

        $gateIn = \Carbon\Carbon::parse($request->gate_in_at);
        $now = now();
        if ($this->myBranch() == 5) {
            $now->addHour();
        }
        $gateIn->seconds(0);
        $now->addMinute()->seconds(0);

        if ($gateIn->gt($now)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tanggal Gate In tidak boleh melebihi waktu sekarang'
            ]);
        }

        DB::table('iv_gate_in_cargo')->insert([
            'branch_id' => $this->myBranch(),
            'site_id' => $this->mySite(),
            'vehicle_number' => strtoupper($request->no_mobil),
            'principal_name' => $request->principal_name,
            'vehicle_type' => strtoupper($request->jenis_mobil),
            'driver_name' => strtoupper($request->nama_supir),
            'activity' => strtoupper($request->activity),
            'transporter_name' => strtoupper($request->transporter_name),
            'gate_in_at' => $gateIn->format('Y-m-d H:i:s'),
            'created_at' => now(),
            'gate_in_by' => Auth::user()->username,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function gateOut($id)
    {
        DB::table('iv_gate_in_cargo')
            ->where('id', $id)->update(
                [
                    'gate_out_at' => date('Y-m-d H:i:s'),
                    'gate_out_by' => Auth::user()->username,
                ]
            );
        return response()->json([
            'status' => true,
        ]);
    }
}
