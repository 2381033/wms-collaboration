<?php

namespace App\Http\Controllers\Api\Import\FotoManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class FotoManagementController extends Controller
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

    public function storeJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $masterbl = str_replace('/', '=', $request->masterbl);
                $housebl = str_replace('/', '=', $request->housebl);
                $token = Str::random(5) . '.' . $masterbl . '.' . $housebl;
                DB::table('imp_image_header')
                    ->insert([
                        'branch_id' => $request->branch_id,
                        'user_id' => $request->user_id,
                        'token'   => $token,
                        'principal' => Str::Upper($request->principal),
                        'consignee' => Str::Upper($request->consignee),
                        'vessel' => Str::Upper($request->vessel),
                        'voyage' => Str::Upper($request->voyage),
                        'container' => Str::Upper($request->container),
                        'masterbl' => $request->masterbl,
                        'housebl' => $request->housebl,
                        'eta' => $request->eta,
                        'qty' => $request->qty,
                        'package' => Str::Upper($request->uom),
                        // 'remarks' => Str::Upper($request->remarks),
                        'flag_foto' => 'Yes',
                        'created_by' => $request->username,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                $message = ['message' => 'success'];
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


    public function scanBarcode(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = [
                    'principal' => explode("|", $request->barcode)[1],
                    'vessel' => explode("|", $request->barcode)[2],
                    'voyage' => explode("|", $request->barcode)[3],
                    'container' => explode("|", $request->barcode)[4],
                    'consignee' => explode("|", $request->barcode)[5],
                    'masterbl' => explode("|", $request->barcode)[6],
                    'housebl' => explode("|", $request->barcode)[7],
                    'qty' => explode("|", $request->barcode)[8],
                    'package' => explode("|", $request->barcode)[9],
                    'eta' => explode("|", $request->barcode)[10]
                ];
                $message = ['data' => $data];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['data' => 'error'];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function checkJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = DB::table('imp_image_header')
                    ->where('housebl', $request->housebl)
                    ->where('masterbl', $request->masterbl)
                    ->where('container', $request->container)
                    // ->where('eta', $request->eta)
                    ->first();
                if (is_null($data)) {
                    $message = ['message' => 'null'];
                } else {
                    $message = ['message' => $data];
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

    public function checkFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = DB::table('imp_image_header')
                    ->where('housebl', $request->housebl)
                    ->where('masterbl', $request->masterbl)
                    ->where('container', $request->container)
                    // ->where('eta', $request->eta)
                    ->First();
                if (is_null($data)) {
                    $message = ['message' => 'null'];
                } else {
                    $validate = DB::table('imp_image_detail')
                        ->where('token', $data->token)
                        ->count();
                    if ($validate == 0) {
                        $message = ['message' => 'null'];
                    } else {
                        if ($data->confirmed_flag == 'No') {
                            $message = ['message' => 'ok'];
                        } else {
                            $message = ['message' => 'null'];
                        }
                    }
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

    public function storeFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                if (!$file) {
                    throw new \Exception("File 'photo' tidak ditemukan. Pastikan form-data dan nama field tepat.");
                }
                $token = DB::table('imp_image_header')
                    ->where('masterbl', $request->masterbl)
                    ->where('housebl', $request->housebl)
                    ->where('container', $request->container)
                    ->value('token');

                $filename = $token . "-" . Str::random(3) . ".jpg";
                $folder = public_path('foto/warehouse-import/foto-management');
                $destination = $folder . '/' . $filename;
                $this->compressJpeg($file, $destination);
                DB::table('imp_image_detail')->insert([
                    'file'   => $filename,
                    'token' => $token,
                    'masterbl' => $request->masterbl,
                    'housebl' => $request->housebl,
                    'created_at' => now(),
                    'created_by' => $request->created_by,
                ]);

                DB::commit();
                return $request->all();
            } catch (\Exception $e) {
                DB::rollBack();
                return ['error' => $e->getMessage()];
            }
        });

        return response()->json($exception);
    }


    public function getFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $token =  DB::table('imp_image_header')
                    ->where('masterbl', $request->masterbl)
                    ->where('housebl', $request->housebl)
                    // ->where('eta', $request->eta)
                    ->where('container', $request->container)
                    ->value('token');

                $data = DB::table('imp_image_detail')
                    ->where('token', $token)
                    ->get();
                $images = [];
                if (count($data) > 0) {
                    foreach ($data as $key => $value) {
                        $images[] = [
                            'id' => $key,
                            'uri' => base64_encode(file_get_contents(public_path('foto/warehouse-import/foto-management/' . $value->file)))
                        ];
                    }
                } else {
                    $images = [];
                }
                $message = [
                    'images' => $images
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

    public function confirmJob(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = DB::table('imp_image_header')
                    ->where('housebl', $request->housebl)
                    ->where('masterbl', $request->masterbl)
                    ->where('container', $request->container)
                    // ->where('eta', $request->eta)
                    ->update([
                        'remarks'      => $request->remarks,
                        'confirmed_at' => date('Y-m-d H:i:s'),
                        'confirmed_by' => $request->username,
                        'confirmed_flag' => 'Yes',
                    ]);
                $message = ['message' => 'success'];
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
