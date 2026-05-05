<?php

namespace App\Http\Controllers\NewUpdated\GateInDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;
use App\Models\Master\Principal as MasterPrincipal;

class GateInController extends Controller
{

    public function index()
    {
        $vehicles = DB::table('ex_master_vehicle')->get();
        $branch = $this->myBranch();
        return view('new.GateInDC.index', [
            'vehicles' => $vehicles,
            'isMakassar' => $this->myBranch() == 5
        ]);
    }

    public function list()
    {
        $data = DB::table('iv_gate_in_cargo')
            ->whereNull('gate_out_at')
            ->where('branch_id', $this->myBranch())
            ->where('site_id', $this->mySite())
            ->orderBy('gate_in_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'vehicle_number' => $item->vehicle_number,
                    'driver_name' => $item->driver_name,
                    'vehicle_type' => $item->vehicle_type,
                    'activity' => $item->activity,
                    'gate_in_at' => \Carbon\Carbon::parse($item->gate_in_at)->format('d-m-Y H:i')
                ];
            });

        return response()->json([
            'inbound' => $data->where('activity', 'INBOUND')->values(),
            'outbound' => $data->where('activity', 'OUTBOUND')->values(),
        ]);
    }


    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');

        return $data;
    }

    private function mySite()
    {
        $data = DB::table('users_site')->where('user_id', Auth::user()->id)->value('site_id');
        return $data;
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
