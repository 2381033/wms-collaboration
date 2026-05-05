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
    private function compressJpeg($uploadedFile, $destination, $quality = 70)
    {
        $source = imagecreatefromjpeg($uploadedFile->getRealPath());
        $width  = imagesx($source);
        $height = imagesy($source);

        $maxWidth = 1600;
        $maxHeight = 1600;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);

            $newWidth  = intval($width * $ratio);
            $newHeight = intval($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            imagecopyresampled(
                $resized,
                $source,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            $source = $resized;
        }

        imagejpeg($source, $destination, $quality);

        imagedestroy($source);
    }
    private function detailUser($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)->first();
        return $data;
    }

    private function whereMyBranch($user_id)
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', $user_id)
            ->first()->branch_id;

        return $branch;
    }

    private function logicWaktu($id_user)
    {
        $branch = $this->whereMyBranch($id_user);

        if ($branch == 5) {
            return date('Y-m-d H:i:s', strtotime('+1 hour'));
        } else {
            return date('Y-m-d H:i:s');
        }
    }

    public function gateInLokasiMuat($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $token = DB::table('cp_driver_detail')
                    ->where('id', $id)
                    ->value('token');
                $job = $this->getJob($token);
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
                            'gate_in_loc_muat' => $this->logicWaktu($job->driver),
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
                $filename = 'out-muat-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-muat/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
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
                $filename = 'in-muat-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-muat/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
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
                $job = $this->getJob($token);

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
                            'gate_out_loc_muat' => $this->logicWaktu($job->driver),
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
