<?php

namespace App\Http\Controllers\Api\CheckpointDriver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Str;

class LokasiMuatController extends Controller
{
    private function detailUser($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)->first();
        return $data;
    }

    public function gateInLokasiMuat($id)
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
                            'status_job' => 'gate_in_loc_muat',
                            'mode_next'   => 'muat-' . $id
                        ]
                    );

                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $id],
                        [
                            'gate_in_loc_muat' => date('Y-m-d H:i:s'),
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

    public function submitFotoGateOut(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                $filename = 'out-muat-' . $request->id . '-' . $request->job_no . "." . $file->getClientOriginalExtension();
                if (!file_exists(public_path('foto/checkpoint-driver/lokasi-muat/' . $filename))) {
                    $file->move(public_path('foto/checkpoint-driver/lokasi-muat'), $filename);
                }
                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $request->id],
                        [
                            'file_gate_out_loc_muat' => $filename
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

    public function submitFotoGateIn(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                $filename = 'in-muat-' . $request->id . '-' . $request->job_no . "." . $file->getClientOriginalExtension();
                if (!file_exists(public_path('foto/checkpoint-driver/lokasi-muat/' . $filename))) {
                    $file->move(public_path('foto/checkpoint-driver/lokasi-muat'), $filename);
                }
                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $request->id],
                        [
                            'file_gate_in_loc_muat' => $filename
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

    public function gateOutLokasiMuat($id)
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
                            'status_job' => 'gate_out_loc_muat',
                            'mode_next' => null
                        ]
                    );

                DB::table('cp_driver_detail')
                    ->updateOrInsert(
                        ['id' =>  $id],
                        [
                            'gate_out_loc_muat' => date('Y-m-d H:i:s'),
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

    public function detailLokasiMuat($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $job = $this->detailJob($id);
                $header = $this->getJob($job->token);
                $detailUser = $this->detailUser($header->driver);
                $foto_in = $job->file_gate_in_loc_muat == null ? '-' : base64_encode(file_get_contents(public_path('foto/checkpoint-driver/lokasi-muat/' . $job->file_gate_in_loc_muat))) ?? '-';
                $foto_out = $job->file_gate_out_loc_muat == null ? '-' : base64_encode(file_get_contents(public_path('foto/checkpoint-driver/lokasi-muat/' . $job->file_gate_out_loc_muat))) ?? '-';
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

    private function getJob($token)
    {
        $data = DB::table('cp_driver_job')
            ->where('token', $token)
            ->first();

        return $data;
    }

    private function detailJob($id)
    {
        $data = DB::table('cp_driver_detail')
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            "messages" => 'success',
        ]);
    }
}
