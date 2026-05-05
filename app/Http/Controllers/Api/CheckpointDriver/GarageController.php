<?php

namespace App\Http\Controllers\Api\CheckpointDriver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Str;

class GarageController extends Controller
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
    private function myBranch($user_id)
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', $user_id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function whereMyBranch($user_id)
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', $user_id)
            ->first()->branch_id;

        return $branch;
    }

    private function detailUser($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)->first();
        return $data;
    }

    public function submitFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'on-garasi-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-garage/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('cp_driver_job')
                    ->updateOrInsert(
                        ['token' =>  $request->token],
                        [
                            'foto_km'   => $filename,
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                    'request' => $request->all()
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

    public function submitSuratJalan(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'surat-jalan-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/surat-jalan/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('cp_driver_job')
                    ->where('token', $request->token)
                    ->update(
                        [
                            'file_surat_jalan'   => $filename
                        ]
                    );
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                    'request' => $request->all()
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

    public function startFromGarage($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                $job = $this->getJob($token);

                DB::table('cp_driver_job')
                    ->updateOrInsert(
                        ['token' =>  $token],
                        [
                            'start_from_garage' => $this->logicWaktu($job->driver),
                            'status_job' => 'to_loc_muat'
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

    private function logicWaktu($id_user)
    {
        $branch = $this->whereMyBranch($id_user);

        if ($branch == 5) {
            return date('Y-m-d H:i:s', strtotime('+1 hour'));
        } else {
            return date('Y-m-d H:i:s');
        }
    }

    public function balikKeGarasi(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = $this->getJob($request->token);
                $userDetails = $this->detailUser($job->driver);
                DB::table('cp_driver_job')
                    ->where('token', $request->token)
                    ->update([
                        'remarks_perjalanan' => $request->remarks_perjalanan,
                        'back_to_garage' => 'Yes',
                        'start_back_to_garage' => $this->logicWaktu($job->driver),
                        'status_job' => 'to_garage'
                    ]);
                DB::commit();

                $message = [
                    'message' => 'Data Successfully Saved',
                    'header' => $job,
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

    public function tibaDiGarasi($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                $job = $this->getJob($token);
                $userDetails = $this->detailUser($job->driver);
                DB::table('cp_driver_job')
                    ->where('token', $job->token)
                    ->update([
                        'finish_back_to_garage' => $this->logicWaktu($job->driver),
                        // 'confirmed_flag' => 'Yes',
                        // 'status_job' => 'confirmed',
                        // 'confirmed_at' => date('Y-m-d H:i:s'),
                        // 'confirmed_by' => $userDetails->id,
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

    public function uploadFotoKmFinish(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = $this->getJob($request->token);
                $userDetails = $this->detailUser($job->driver);

                $filename = 'finish-garasi-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/checkpoint-driver/lokasi-garage/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('cp_driver_job')
                    ->where('token', $request->token)
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'status_job' => 'confirmed',
                        'confirmed_at' => $this->logicWaktu($job->driver),
                        'confirmed_by' => $userDetails->id,
                        'foto_km_finish'   => $filename,
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

    private function getJob($token)
    {
        $data = DB::table('cp_driver_job')
            ->where('token', $token)
            ->first();

        return $data;
    }

    private function getDetailJob($token)
    {
        $data = DB::table('cp_driver_detail')
            ->where('token', $token)
            ->get();

        return $data;
    }

    public function timelinePerjalanan($token)
    {
        $header = $this->getJob($token);
        $detailJob = $this->getDetailJob($token);
        $userDetails = $this->detailUser($header->driver);

        return response()->json([
            'header' => $header,
            'detailJob' => $detailJob,
            'userDetails' => $userDetails,
        ]);
    }

    public function getJobMe($user_id)
    {
        $data = DB::table('cp_driver_job')
            ->where('driver', $user_id)
            ->where('confirmed_flag', 'No')
            ->get();
        $data->map(function ($value) {
            $value->driver = $this->detailUser($value->driver);
            return $value;
        });
        return response()->json($data);
    }

    public function getJenisArmada()
    {
        $data = DB::table('cp_driver_armada')->get();
        return response()->json($data);
    }

    public function detailJobMe($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                $header = $this->getJob($token);
                $userDetails = $this->detailUser($header->driver);
                $lokasi_muat = DB::table('cp_driver_detail')
                    ->where('token', $token)
                    ->whereNotNull('lokasi_muat')
                    ->where('status', 0)
                    ->get();
                $lokasi_bongkar = DB::table('cp_driver_detail')
                    ->where('token', $token)
                    ->whereNotNull('lokasi_bongkar')
                    ->where('status', 0)
                    ->get();

                $message = [
                    'userDetails' => $userDetails,
                    'header' => $header,
                    'lokasi_muat' => $lokasi_muat,
                    'lokasi_bongkar' => $lokasi_bongkar,
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
}
