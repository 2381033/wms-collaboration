<?php

namespace App\Http\Controllers\Api\CheckpointDriver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Str;

class LokasiBongkarController extends Controller
{
    private function detailUser($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)->first();
        return $data;
    }

    public function gateInLokasiBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $token = DB::table('cp_driver_detail')
                    ->where('id', $id)
                    ->value('token');

                DB::table('cp_driver_job')
                    ->updateOrInsert(
                        ['token' =>  $token],
                        [
                            'status_job' => 'gate_in_loc_bongkar',
                            'mode_next'   => 'bongkar-' . $id
                        ]
                    );

                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $id],
                        [
                            'gate_in_loc_bongkar' => date('Y-m-d H:i:s'),
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                ];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function submitFotoGateIn(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                $filename = 'in-bongkar-' . $request->id . '-' . $request->job_no . "." . $file->getClientOriginalExtension();
                if (!file_exists(public_path('foto/checkpoint-driver/lokasi-bongkar/' . $filename))) {
                    $file->move(public_path('foto/checkpoint-driver/lokasi-bongkar'), $filename);
                }
                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $request->id],
                        [
                            'file_gate_in_loc_bongkar' => $filename
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                ];
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

    public function submitFotoGateOut(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                $filename = 'out-bongkar-' . $request->id . '-' . $request->job_no . "." . $file->getClientOriginalExtension();
                if (!file_exists(public_path('foto/checkpoint-driver/lokasi-bongkar/' . $filename))) {
                    $file->move(public_path('foto/checkpoint-driver/lokasi-bongkar'), $filename);
                }
                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $request->id],
                        [
                            'file_gate_out_loc_bongkar' => $filename
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                ];
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

    public function gateOutLokasiBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $token = DB::table('cp_driver_detail')
                    ->where('id', $id)
                    ->value('token');

                DB::table('cp_driver_job')
                    ->updateOrInsert(
                        ['token' =>  $token],
                        [
                            'status_job' => 'gate_out_loc_bongkar',
                            'mode_next' => null
                        ]
                    );

                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $id],
                        [
                            'gate_out_loc_bongkar' => date('Y-m-d H:i:s'),
                            'status' => 1
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                ];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getJob($token)
    {
        $data = DB::table('cp_driver_job')
            ->where('token', $token)
            ->first();

        return $data;
    }

    private function detailJob($token)
    {
        $data = DB::table('cp_driver_detail')
            ->where('token', $token)
            ->get();
        return $data;
    }

    public function validasiLokasiBongkar($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                $header = $this->getJob($token);
                $detailJob = $this->detailJob($token);
                $lokasiMuat = $detailJob
                    ->whereNotNull('lokasi_muat')
                    ->whereNotNull('gate_out_loc_muat')
                    ->count();
                if ($lokasiMuat == $header->jumlah_loc_muat) {
                    $message = ['message' => 'ok'];
                } else {
                    $message = ['message' => 'validate'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function detailLokasiBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $job = DB::table('cp_driver_detail')->where('id', $id)->first();
                $header = $this->getJob($job->token);
                $detailUser = $this->detailUser($header->driver);
                $foto_in = $job->file_gate_in_loc_bongkar == null ? '-' : base64_encode(file_get_contents(public_path('foto/checkpoint-driver/lokasi-bongkar/' . $job->file_gate_in_loc_bongkar))) ?? '-';
                $foto_out = $job->file_gate_out_loc_bongkar == null ? '-' : base64_encode(file_get_contents(public_path('foto/checkpoint-driver/lokasi-bongkar/' . $job->file_gate_out_loc_bongkar))) ?? '-';
                $message = [
                    'message' => 'Data Successfully Saved',
                    'data' => $job,
                    'header' => $header,
                    'user' => $detailUser,
                    'foto_in' => $foto_in,
                    'foto_out' => $foto_out,
                ];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function finishJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = $this->getJob($request->token);
                $userDetails = $this->detailUser($job->driver);
                DB::table('cp_driver_job')
                    ->where('token', $request->token)
                    ->update([
                        'remarks_perjalanan' => $request->remarks_perjalanan,
                        'confirmed_flag' => 'Yes',
                        'status_job' => 'confirmed',
                        'confirmed_at' => date('Y-m-d H:i:s'),
                        'confirmed_by' => $userDetails->id,
                    ]);
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                    'job_header' => $job,
                    'userDetails' => $userDetails,
                ];

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
