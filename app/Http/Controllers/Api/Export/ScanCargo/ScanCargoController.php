<?php

namespace App\Http\Controllers\Api\Export\ScanCargo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;
use Illuminate\Support\Carbon;

class ScanCargoController extends Controller
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
                return response()->json([
                    "error" => FALSE,
                    "users" => $user,
                    "auth" => $auth,
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

    private function getJobNo($username)
    {
        $branch = $this->myBranch($username);

        $job = DB::table('ex_scan_cargo')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'IN' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    public function generateJobNoReceive($username)
    {
        $data = $this->getJobNo($username);
        return $data;
    }

    public function postReceive(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $carton = substr($request->carton, 2);
                $warehouse = substr($request->warehouse, 2);

                $validate = DB::table('ex_scan_cargo')
                    ->where('carton', $carton)
                    ->count();

                if ($validate > 0) {
                    return response()->json([
                        'success' => 'double',
                    ]);
                }

                // Simpan data dan ambil ID-nya
                $id = DB::table('ex_scan_cargo')->insertGetId([
                    'pallet' => $request->pallet,
                    'warehouse' => $warehouse,
                    'carton' => $carton,
                    'branch_id' => $this->myBranch($request->username),
                    'job_no' => $request->job_no,
                    'stock_flag' => 'Yes',
                    'created_by' => $request->username,
                    'created_at' => now(),
                ]);

                // Ambil kembali data yang baru disimpan
                $newItem = DB::table('ex_scan_cargo')
                    ->select('id', 'pallet', 'warehouse', 'carton')
                    ->where('id', $id)
                    ->first();

                DB::commit();

                return response()->json([
                    'success' => 'success',
                    'data' => $newItem,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
        return $exception; // Ini sudah berbentuk response JSON
    }


    public function getListReceive($job_no)
    {
        try {
            $items = DB::table('ex_scan_cargo')
                ->select('id', 'warehouse', 'carton', 'pallet')
                ->where('job_no', $job_no)
                ->orderBy('pallet')
                ->get();

            $grouped = [];
            foreach ($items as $item) {
                $grouped[$item->pallet][] = [
                    'id' => $item->id,
                    'warehouse' => $item->warehouse,
                    'carton' => $item->carton
                ];
            }

            return response()->json(['data' => $grouped]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getListOutstanding($username)
    {
        $exception = DB::transaction(function () use ($username) {
            try {
                $data = [];
                $master = DB::table('ex_scan_cargo')
                    ->where('branch_id', $this->myBranch($username))
                    ->where('created_by', $username)
                    ->where('confirmed_flag', 'No')
                    ->get()->groupBy('job_no');
                foreach ($master as $jobNo => $records) {
                    $status = Str::startsWith($jobNo, 'IN') ? 'RECEIVING' : 'STUFFING';
                    $jumlahLinePallet = $records->count();
                    $lastCreated = \Carbon\Carbon::parse($records->max('created_at'));
                    $jamTerakhir = $lastCreated->diffForHumans();
                    $data[] = [
                        'job_no' => $jobNo,
                        'total_pallet' => $jumlahLinePallet,
                        'jam_terakhir' => $jamTerakhir,
                        'status' => $status,
                    ];
                }

                $message = ['data' => $data];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteListReceive($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('ex_scan_cargo')
                    ->where('id', $id)
                    ->delete();
                $message = ['data' => 'success'];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function confirmJobReceive($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                DB::table('ex_scan_cargo')
                    ->where('job_no', $job_no)
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'updated_at'  => date('Y-m-d H:i:s')
                    ]);
                $message = ['data' => 'success'];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function generateJobNoStuffing($username)
    {
        $branch = $this->myBranch($username);
        $job = DB::table('ex_scan_cargo')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();
        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'OUT' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');
        return $job_no;
    }

    public function postStuffing(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = DB::table('ex_scan_cargo')
                    ->whereNotNull('container_no')
                    ->where('pallet', $request->pallet)
                    ->count();
                if ($validate > 0) {
                    DB::rollBack();
                    $message = [
                        'success' => 'double',
                    ];
                } else {
                    $validateStock = DB::table('ex_scan_cargo')
                        ->whereNull('container_no')
                        ->where('pallet', $request->pallet)
                        ->count();
                    if ($validateStock > 0) {
                        DB::table('ex_scan_cargo')->insert([
                            'branch_id'  => $this->myBranch($request->username),
                            'job_no'  => $request->job_no,
                            'created_by'   => $request->username,
                            'created_at' => now(),
                            'container_no' => $request->container,
                            'pallet' => $request->pallet,
                        ]);
                        DB::table('ex_scan_cargo')->where('pallet', $request->pallet)
                            ->update([
                                'stock_flag'  => 'No',
                                'updated_by' => $request->username,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        DB::commit();
                        $message = [
                            'success' => 'success',
                        ];
                    } else {
                        DB::rollBack();
                        $message = [
                            'success' => 'not_found',
                        ];
                    }
                }
                return response()->json($message);
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getListStuffing($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                $master = DB::table('ex_scan_cargo')
                    ->where('job_no', $job_no)
                    ->get();
                $pallet = $master->pluck('pallet')->toArray();
                $container = $master->first()->container_no ?? '';

                $data = DB::table('ex_scan_cargo')
                    ->orderBy('id', 'ASC')
                    ->whereNull('container_no')
                    ->whereIn('pallet', $pallet)
                    ->get();
                $grouped = $data->groupBy('pallet')->map(function ($items, $pallet) {
                    return [
                        'pallet' => $pallet,
                        'total_cartons' => $items->count()
                    ];
                });
                $list = $data
                    ->groupBy('pallet')
                    ->map(function ($items, $pallet) {
                        return [
                            'pallet' => $pallet,
                            'qty' => $items->count(),
                        ];
                    })
                    ->values();
                $message = ['data' => [
                    'label' => $grouped,
                    'list' => $list,
                    'container_no' => $container
                ]];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteListStuffing($pallet)
    {
        $exception = DB::transaction(function () use ($pallet) {
            try {
                //delete pallet stuffing
                DB::table('ex_scan_cargo')
                    ->where('pallet', $pallet)
                    ->whereNotNull('container_no')
                    ->delete();

                //ubah flag menjadi stock kembali
                DB::table('ex_scan_cargo')
                    ->where('pallet', $pallet)
                    ->whereNull('container_no')
                    ->update([
                        'stock_flag' => 'Yes',
                    ]);
                $message = ['success' => 'success'];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function confirmJobStuffing($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                DB::table('ex_scan_cargo')
                    ->where('job_no', $job_no)
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'updated_at'  => date('Y-m-d H:i:s')
                    ]);
                $message = ['data' => 'success'];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }
}
