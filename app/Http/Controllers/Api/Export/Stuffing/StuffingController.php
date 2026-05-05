<?php

namespace App\Http\Controllers\Api\Export\Stuffing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getList($branch_id)
    {
        $exception = DB::transaction(function () use ($branch_id) {
            try {
                $data = $this->getJob($branch_id);
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function headerPallet($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $data = DB::table('ex_outbound_header')
                    ->where('id', $id)
                    ->first();
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function detailPallet($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = $this->getDetailPallet($job_id);
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getCargoNotCompleted($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_outbound_detail')
                    ->orderBy('id', 'ASC')
                    ->where('job_id', $job_id)
                    ->where('status_flag', 'Open')
                    ->get();
                // $data = $data->map(function($value) {
                //     $value->explode = explode('-', $value->serial_no);
                //     $value->pallet_id = (int)$value->explode[2];
                //     return $value;
                // });
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getCargoCompleted($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_outbound_detail')
                    ->orderBy('id', 'ASC')
                    ->where('job_id', $job_id)
                    ->where('status_flag', 'Confirmed')
                    ->get();
                // $data = $data->map(function($value) {
                //     $value->explode = explode('-', $value->serial_no);
                //     $value->pallet_id = (int)$value->explode[2];
                //     return $value;
                // });
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function scanPalletTag(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('ex_outbound_detail')
                    ->where('id', $request->id_cargo)
                    ->where('job_id', $request->job_id)
                    ->where('serial_no', $request->serial_no)
                    ->update([
                        'status_flag' => 'Confirmed',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                $header = DB::table('ex_outbound_header')
                    ->where('id', $request->job_id)
                    ->first();
                if (is_null($header->user_process)) {
                    DB::table('ex_outbound_header')
                        ->where('id', $request->job_id)
                        ->update([
                            'user_process' => $request->user_process
                        ]);
                }
                return 'success';
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getJob($branch_id)
    {
        $data = DB::table('ex_outbound_header')
            ->where('branch_id', $branch_id)
            ->where('status_flag', 'Open')
            ->get();
        $data  = $data->map(function ($value) {
            $value->hasScanned = DB::table('ex_outbound_detail')
                ->where('job_id', $value->id)
                ->where('status_flag', 'Confirmed')->count();
            return $value;
        });
        return $data;
    }
    private function getDetailPallet($job_id)
    {
        $data = DB::table('ex_outbound_detail')
            ->where('job_id', $job_id)
            ->get();
        return $data;
    }
}
