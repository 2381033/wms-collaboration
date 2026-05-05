<?php

namespace App\Http\Controllers\Api\Export\Stuffing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StuffingController extends Controller
{
    public function login(Request $request)
    {
        $user = DB::table("users as a")
            ->where("a.username", $request->username)
            ->first();

        if ($user) {
            $auth = DB::table('auth_group')
                ->where('id', $user->auth_group_id)
                ->value('name');
            if (password_verify($request->password, $user->password)) {
                $branch_id = $this->myBranch($user->username);
                return response()->json([
                    "error" => FALSE,
                    "users" => $user,
                    "auth" => $auth,
                    "branch_id" => $branch_id,
                ]);
            }

            return $this->error("Password salah.");
        }

        return $this->error("User tidak ditemukan.");
    }

    private function error($pesan)
    {
        return response()->json([
            "error" => TRUE,
            "user" => $pesan
        ]);
    }

    private function myBranch($username)
    {
        $idUser = DB::table('users')
            ->select('id')
            ->where('username', $username)
            ->value('id');
        $data = DB::table('sm_user_branch')
            ->where('user_id', $idUser)
            ->value('branch_id');
        return $data;
    }

    public function scanPalletTag(Request $request)
    {
        try {
            $pallet = DB::table('ex_outbound_detail')
                ->where('serial_no', $request->pallet_tag)
                ->first();

            if (!$pallet) {
                return response()->json([
                    'error' => 'Pallet tag not found'
                ], 404);
            }

            if ($pallet->status_flag == 'Confirmed') {
                return response()->json([
                    'error' => 'Pallet has already been scanned'
                ], 409);
            }


            DB::table('ex_outbound_detail')
                ->where('serial_no', $request->pallet_tag)
                ->where('status_flag', 'Open')
                ->update([
                    'status_flag' => 'Confirmed',
                    'updated_at' => now(),
                ]);

            $header = DB::table('ex_outbound_header')
                ->where('id', $pallet->job_id)
                ->first();

            if (is_null($header->user_process)) {
                DB::table('ex_outbound_header')
                    ->where('id', $header->id)
                    ->update([
                        'user_process' => $request->username,
                    ]);
            }

            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getCounting(Request $request)
    {
        $branch_id = $this->myBranch($request->username);
        $data = DB::table('ex_outbound_header as a')
            ->join('ex_outbound_detail as b', 'b.job_id', '=', 'a.id')
            ->where('a.branch_id', $branch_id)
            ->where('b.job_id', $request->job_id)
            ->groupBy('b.serial_no')
            ->get();
        $hasScanned = $data->where('status_flag', 'Confirmed')->count();
        return response()->json([
            'data' => $data,
            'hasScanned' => $hasScanned
        ]);
    }
    public function getList(Request $request)
    {
        $branch_id = $this->myBranch($request->username);

        $rows = DB::table('ex_outbound_header as a')
            ->join('ex_outbound_detail as b', 'b.job_id', '=', 'a.id')
            ->whereDate('a.created_at', '>=', Carbon::today()->subDays(7))
            ->where('a.branch_id', $branch_id)
            ->where('a.status_flag', 'Open')
            ->select(
                'a.id as id_header',
                'a.container_no',
                'a.destination',
                'b.po_number',
                'b.serial_no',
                'b.quantity',
                'b.status_flag',
                'a.user_process'
            )
            ->orderBy('a.created_at', 'desc')
            ->get();

        $grouped = $rows->groupBy('container_no');
        $result = $grouped->map(function ($items) {

            $uniquePO = collect();
            $uniquePallet = collect();

            foreach ($items as $item) {
                foreach (explode('|', $item->po_number) as $po) {
                    $uniquePO->push(trim($po));
                }
                $serialParts = explode('-', $item->serial_no);

                if (isset($serialParts[0], $serialParts[1], $serialParts[2])) {
                    $palletKey = $serialParts[0] . '-' . $serialParts[1] . '-' . $serialParts[2];
                    $uniquePallet->push($palletKey);
                }
            }

            $totalPallet = $uniquePallet->unique()->count();

            return [
                'id_header'      => $items->first()->id_header,
                'container_no'   => $items->first()->container_no,
                'destination'    => $items->first()->destination,
                'total_pallet'   => $totalPallet,
                'total_po'       => $uniquePO->unique()->count(),
                'user_process'   => $items->first()->user_process,
                'status'         => $items->first()->status_flag === 'Open'
                    ? 'Outstanding'
                    : 'On Progress By ' . $items->first()->user_process,
            ];
        })->values();

        return response()->json($result);
    }
}
