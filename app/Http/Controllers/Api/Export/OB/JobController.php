<?php

namespace App\Http\Controllers\Api\Export\OB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use Illuminate\Support\Str;

class JobController extends Controller
{

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

    public function getJobOB($type, $username)
    {
        $field = $type . '_name';
        $data = DB::table("ex_ob_header")
            ->where($field, $username)
            ->where("branch_id", $this->myBranch($username))
            ->where("confirmed_flag", "Open")
            ->get();
        $data = $data->map(function ($value) {
            $value->consignee_name = $this->getConsigneeByid($value->consignee_id);
            return $value;
        });
        return response()->json(['data' => $data]);
    }

    public function postScanPalletTag(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $segments = explode('||', $request->data);
                $barcode = $segments[0];
                $id_detail = $segments[1];

                $inStock = DB::table('ex_ob_detail')
                    ->where('id', $id_detail)
                    ->first();
                if (is_null($inStock)) {
                    $message = ['message' => 'validate'];
                    DB::rollBack();
                } else {
                    if ($inStock->serial_no != $barcode) {
                        $message = ['message' => 'validate'];
                        DB::rollBack();
                    } else {
                        $like = explode('-', $barcode);
                        $serialNo  = $like[1] . '-' . $like[2] . '-' . $like[3];
                        DB::table('ex_ob_detail')
                            ->where('peb_no', $like[1])
                            ->where('serial_no', 'LIKE', '%' . $serialNo . '%')
                            ->update([
                                $request->type . '_scan_pallet_at' => date('Y-m-d H:i:s'),
                            ]);
                        DB::commit();
                        if ($request->type == 'stapel') {
                            $counting = DB::table('ex_ob_detail')
                                ->where('job_no', $inStock->job_no)
                                ->get();
                            if ($counting->whereNotNull('stapel_scan_pallet_at')->count() == $counting->count()) {
                                DB::table('ex_ob_header')
                                    ->where('job_no', $inStock->job_no)
                                    ->update([
                                        'stapel_confirmed_at' => date('Y-m-d H:i:s'),
                                    ]);
                                $message = ['message' => 'confirmed'];
                            } else {
                                $message = ['message' => 'next'];
                            }
                        } else {
                            $message = ['message' => 'success'];
                        }
                        DB::commit();
                        return $message;
                    }
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function postScanLocation(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $segments = explode('||', $request->data);
                $location = $segments[0];
                $id_detail = $segments[1];

                $inStock = DB::table('ex_ob_detail')
                    ->where('id', $id_detail)
                    ->first();
                if (is_null($inStock)) {
                    $message = ['message' => 'validate'];
                    DB::rollBack();
                } else {
                    if ($inStock->location_code != $location) {
                        $message = ['message' => 'validate'];
                        DB::rollBack();
                    } else {
                        DB::table('ex_ob_detail')
                            ->where('id', $id_detail)
                            ->update([
                                'stapel_scan_location_at' => date('Y-m-d H:i:s'),
                            ]);
                        DB::commit();
                    }
                    $message = ['message' => 'success'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function detailOB($type, $job_no)
    {
        $map = [
            'checker' => 'checker_scan_pallet_at',
            'stapel'  => 'stapel_scan_pallet_at',
        ];

        if (!isset($map[$type])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $header = DB::table("ex_ob_header as a")
            ->select('a.*', 'b.consignee_name', 'c.shipper_name', 'd.forwarder_name')
            ->join('mt_consignee as b', 'a.consignee_id', '=', 'b.id')
            ->join('mt_shipper as c', 'a.shipper_id', '=', 'c.id')
            ->join('mt_forwarder as d', 'a.forwarder_id', '=', 'd.id')
            ->where("a.job_no", $job_no)
            ->where('a.confirmed_flag', 'Open')
            ->first();
        $detail = DB::table("ex_ob_detail")
            ->where("job_no", $job_no)
            ->groupBy('pallet_id')
            ->whereNull($map[$type])
            ->get();

        $status = 'next';

        if ($type === 'checker') {
            $totalDetail = DB::table("ex_ob_detail")
                ->where("job_no", $job_no)
                ->count();

            $totalScanned = DB::table("ex_ob_detail")
                ->where("job_no", $job_no)
                ->whereNotNull('checker_scan_pallet_at')
                ->count();

            if ($totalDetail > 0 && $totalDetail === $totalScanned) {
                $status = 'take_picture';
            }
        }

        return response()->json([
            'data' => [
                'status' => $status,
                'header' => $header,
                'detail' => $detail,
            ]
        ]);
    }

    public function getFoto($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                $data = DB::table('ex_ob_image')
                    ->where('job_no', $job_no)
                    ->get();
                $image = [];
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $image[] = [
                            'id' => $value->id,
                            'foto' => url('foto/warehouse-export/ob-cargo/' . $value->file)
                            // 'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/ob-cargo/' . $value->file)))
                        ];
                    }
                } else {
                    $image = [];
                }
                $message = [
                    'message' => 'Data Successfully Saved',
                    'images' => $image
                ];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });

        return response()->json($exception);
    }

    public function storeFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'ob-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/warehouse-export/ob-cargo/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('ex_ob_image')
                    ->insert(
                        [
                            'file'        => $filename,
                            'job_no'      => $request->job_no,
                            'user_id'     => $request->user_id,
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]
                    );
                DB::commit();
                $message = [
                    'message' => 'Data Successfully Saved',
                ];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => true, 'message' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
    private function getConsigneeByid($id)
    {
        $data = DB::table("mt_consignee")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('consignee_name');

        return $data;
    }

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

    public function confirmJobChecker($job_no)
    {
        try {
            $result = DB::transaction(function () use ($job_no) {
                $validate = DB::table('ex_ob_image')
                    ->where('job_no', $job_no)
                    ->count();

                if ($validate <= 5) {
                    return [
                        'message' => 'validate',
                    ];
                }

                DB::table('ex_ob_header')
                    ->where('job_no', $job_no)
                    ->update([
                        'checker_confirmed_at' => now(),
                    ]);

                return [
                    'message' => 'Success',
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
