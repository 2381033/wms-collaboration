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

    public function gateInLokasiBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $token = DB::table('cp_driver_detail')
                    ->where('id', $id)
                    ->value('token');
                $job = $this->getJob($token);

                DB::table('cp_driver_job')
                    ->where(
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
                            'gate_in_loc_bongkar' => $this->logicWaktu($job->id_user),
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
                $filename = 'in-bongkar-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-bongkar/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
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
                $filename = 'out-bongkar-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-bongkar/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
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
                $job = $this->getJob($token);

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
                            'gate_out_loc_bongkar' => $this->logicWaktu($job->driver),
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
                        'confirmed_at' => $this->logicWaktu($job->driver),
                        'confirmed_by' => $userDetails->id,
                        'foto_km_finish' => $job->file_surat_jalan,
                        'finish_back_to_garage' => $this->logicWaktu($job->driver)
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

    private function logicWaktu($id_user)
    {
        $branch = $this->whereMyBranch($id_user);

        if ($branch == 5) {
            return date('Y-m-d H:i:s', strtotime('+1 hour'));
        } else {
            return date('Y-m-d H:i:s');
        }
    }
}
