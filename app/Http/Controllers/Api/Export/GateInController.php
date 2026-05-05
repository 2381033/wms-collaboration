<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class GateInController extends Controller
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
    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $type = $request->type;
                $filename = 'gate-in-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/warehouse-export/gate-in-' . $type . '/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                if ($type == 'cargo') {
                    DB::table('ex_gate_in_cargo')
                        ->insert(
                            [
                                'transporter_name' => Str::upper($request->transporter_name),
                                'vehicle_number'   => Str::upper($request->vehicle_number),
                                'vehicle_type'     => Str::upper($request->vehicle_type),
                                'driver_name'      => Str::upper($request->driver_name),
                                // 'shipper_name'     => Str::upper($request->shipper_name),
                                'id_visitor'       => Str::upper($request->id_visitor),
                                'created_at'       => date('Y-m-d H:i:s'),
                                'created_by'       => $request->created_by,
                                'file'             => $filename
                            ]
                        );
                } else {
                }
                DB::commit();
                $message = [
                    'message' => 'Data Successfully Saved',
                    'request' => $request->all()
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

    public function detailGateIn($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $data = DB::table('ex_gate_in_cargo')
                    ->where('id', $id)
                    ->where('confirmed_flag', 'No')
                    ->first();
                $foto = base64_encode(file_get_contents(public_path('foto/warehouse-export/gate-in-cargo/' . $data->file)));
                $message = [
                    'message' => 'Data Successfully Saved',
                    'fotoGateIn' => $foto,
                    'detailGateIn' => $data
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
}
