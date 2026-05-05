<?php

namespace App\Http\Controllers\Api\Export\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;

class DetailController extends Controller
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
    public function storeFotoCargo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'cargo-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/warehouse-export/inbound-cargo/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('ex_inbound_foto_cargo')
                    ->insert(
                        [
                            'file'        => $filename,
                            'po_number'   => $request->po_number,
                            'branch_id'   => $request->branch_id,
                            'job_id'      => $request->job_id,
                            'created_at'  => date('Y-m-d H:i:s'),
                            'created_by'  => $request->created_by,
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

    public function storeFotoCargoDamage(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'cargo-damage-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/warehouse-export/inbound-cargo/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);

                DB::table('ex_inbound_foto_cargo')
                    ->insert(
                        [
                            'file'        => $filename,
                            'po_number'   => $request->po_number,
                            'branch_id'   => $request->branch_id,
                            'job_id'      => $request->job_id,
                            'created_at'  => date('Y-m-d H:i:s'),
                            'created_by'  => $request->created_by,
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

    public function storeFotoTruck(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $filename = 'truck-' . $request->job_no . "-" . Str::random(6) . ".jpg";
                $destination = public_path('foto/warehouse-export/inbound-cargo/' . $filename);
                $this->compressJpeg($request->file('photo'), $destination);
                DB::table('ex_inbound_foto_cargo')
                    ->insert(
                        [
                            'file'        => $filename,
                            'branch_id'   => $request->branch_id,
                            'job_id'      => $request->job_id,
                            'created_at'  => date('Y-m-d H:i:s'),
                            'created_by'  => $request->created_by,
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

    public function storeDetail(Request $request)
    {
        $rules = array(
            'qty' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'unit' => 'required',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' =>  'validate', 'req' => $request->all()]);
        }
        $exception = DB::transaction(function () use ($request) {
            try {
                $serial_no = $request->po_number . "-" . $request->peb_no . "-" . Str::of($request->pallet_id)->padLeft(2, '0') . "-" . $request->job_id;
                for ($i = 0; $i < $request->jumlah_dimensi; $i++) {
                    DB::table('ex_inbound_detail')
                        ->updateOrInsert(
                            [
                                'serial_no' => $serial_no,
                                'job_id'    => $request->job_id,
                                'pallet_id' => $request->pallet_id,
                                'length'    => $request->length[$i],
                                'width'     => $request->width[$i],
                                'height'    => $request->height[$i],
                            ],
                            [
                                'quantity'   => $request->qty[$i],
                                'unit'       => $request->unit,
                                'user_id'    => $request->user_id,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]
                        );
                }
                DB::commit();
                $data = $this->getListDetail($request->job_id, $request->po_number);
                $qty = $data->sum('quantity');

                $detailPallet = $data->first();

                $message = [
                    'message' => 'success',
                    'detailPallet' => $detailPallet,
                    'qty' => $qty,
                    'data' => $request->all(),
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

    public function getFotoCargo($job_id, $po)
    {
        $exception = DB::transaction(function () use ($job_id, $po) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->where('job_id', $job_id)
                    ->where('po_number', base64_decode($po))
                    ->where('file', 'LIKE', 'cargo-%') // harus diawali cargo-, bukan cargo-damage
                    ->where('file', 'NOT LIKE', 'cargo-damage-%')
                    ->get();
                $image = [];
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $image[] = [
                            'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/inbound-cargo/' . $value->file)))
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

    public function getFotoCargoDamage($job_id, $po)
    {
        $exception = DB::transaction(function () use ($job_id, $po) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->where('job_id', $job_id)
                    ->where('po_number', base64_decode($po))
                    ->where('file', 'LIKE', 'cargo-damage-%')
                    ->get();
                $image = [];
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $image[] = [
                            'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/inbound-cargo/' . $value->file)))
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

    public function getDetailCargo($job_id, $po_number)
    {
        $exception = DB::transaction(function () use ($job_id, $po_number) {
            try {
                $po_number = base64_decode($po_number);
                $data = DB::table('ex_inbound_detail')
                    ->selectRaw('*, sum(quantity) as qty_total, count(id) as palletize')
                    ->where('job_id', $job_id)
                    // ->where('serial_no', 'LIKE', '%' . $po_number . '%')
                    ->orderBy('id', 'ASC')
                    ->groupBy('pallet_id')
                    ->get();

                $detail = DB::table('ex_inbound_detail')
                    ->where('job_id', $job_id)
                    ->where('serial_no', 'LIKE', '%' . $po_number . '%')
                    ->get();
                $lastPallet = $data->groupBy('pallet_id')->keys()->max();

                $qty = $detail->sum('quantity');
                $cbm = $detail->reduce(function ($carry, $item) {
                    $length = floatval($item->length);
                    $width = floatval($item->width);
                    $height = floatval($item->height);
                    $qty = intval($item->quantity);

                    $cbm = ($length * $width * $height * $qty) / 1000000;

                    return $carry + $cbm;
                }, 0);
                $cbm = round($cbm, 3);

                $detailPallet = $this->getListDetail($job_id, $po_number)->first();
                $header = DB::table('ex_inbound_header')->where('id', $job_id)->first();
                $message = [
                    'message' => 'Data Successfully Saved',
                    'detailPallet' => $detailPallet,
                    'qty' => $qty,
                    'data' => $data,
                    'header' => $header,
                    'po_number' => $po_number,
                    'cbm' => $cbm,
                    'lastPallet' => $lastPallet,
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

    public function resultPalletize($job_id, $po_number)
    {
        $exception = DB::transaction(function () use ($job_id, $po_number) {
            try {
                $po_number = base64_decode($po_number);

                $qty = DB::table('ex_inbound_detail')
                    ->where('job_id', $job_id)
                    ->where('serial_no', 'LIKE', '%' . $po_number . '%')
                    ->sum('quantity');
                $message = [
                    'message' => 'Data Successfully Saved',
                    'qty' => $qty,
                    'po_number' => $po_number
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

    public function listingPO($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_inbound_header')
                    ->where('id', $job_id)
                    ->first();
                $shipper_name = $this->getShipperByid($data->shipper_id);
                $consignee_name = $this->getConsigneeByid($data->consignee_id);
                $po_numbers = explode('|', $data->po_number);
                $qty_cargos = explode('|', $data->qty_cargo);

                $result = [];

                foreach ($po_numbers as $index => $po) {
                    $result[] = [
                        'po_number' => $po,
                        'qty_cargo' => isset($qty_cargos[$index]) ? (int)$qty_cargos[$index] : 0,
                    ];
                }
                $message = [
                    'message' => 'Data Successfully Saved',
                    'data' => $result,
                    'header' => $data,
                    'shipper' => $shipper_name,
                    'consignee' => $consignee_name,
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

    public function getFotoTruck($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->where('job_id', $job_id)
                    ->where('file', 'LIKE', '%truck%')
                    ->get();
                $image = [];
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $image[] = [
                            'id' => $value->id,
                            'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/inbound-cargo/' . $value->file)))
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

    public function deletePallet($job_id, $pallet_id)
    {
        $exception = DB::transaction(function () use ($job_id, $pallet_id) {
            try {
                DB::table('ex_inbound_detail')
                    ->where('pallet_id', $pallet_id)
                    ->where('job_id', $job_id)
                    ->delete();
                DB::commit();
                $message = [
                    'message' => 'success',
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

    public function perkalianPallet($job_id, $id_detail, $perkalian, $po_number)
    {
        $exception = DB::transaction(function () use ($job_id, $id_detail, $perkalian, $po_number) {
            try {
                $header = $this->detailHeader($job_id);
                $object = $this->getListDetail($job_id, $po_number)->where('id', $id_detail)->first();
                for ($i = 0; $i < $perkalian; $i++) {
                    $last   = $this->getListDetail($job_id, $po_number)->first();

                    $pallet_id = $last->pallet_id + 1;

                    $serial_no = base64_decode($po_number) . "-" . $header->peb_no . "-" . Str::of($pallet_id)->padLeft(2, '0') . "-" . $job_id;

                    DB::table('ex_inbound_detail')
                        ->insert([
                            'serial_no'   => $serial_no,
                            'job_id'      => $object->job_id,
                            'pallet_id'   => $pallet_id,
                            'quantity'    => $object->quantity,
                            'length'      => $object->length,
                            'width'       => $object->width,
                            'height'      => $object->height,
                            'weight'      => $object->weight,
                            'unit'        => $object->unit,
                            'user_id'     => $object->user_id,
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]);
                }
                DB::commit();
                $message = [
                    'message' => 'success',
                    'object' => $pallet_id
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

    public function storeSignature(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $typeSignature = "";
                $header = DB::table('ex_inbound_header')
                    ->where('id', $request->job_id)
                    ->first();
                $typeSignature = "";
                if (is_null($header->ttd_driver)) {
                    $typeSignature = 'driver';
                }
                if (!is_null($header->ttd_driver) and is_null($header->ttd_checker)) {
                    $typeSignature = 'checker';
                }
                if (!is_null($header->ttd_driver) and !is_null($header->ttd_checker)) {
                    $typeSignature = 'done';
                }
                $filename =  $typeSignature . "-" .  $header->job_no . "-" . date('Y-m-d') . '.png';
                $img = $request->photo;
                $folderPath = public_path('foto/warehouse-export/signature/');
                $image_parts = explode(";base64,", $img);
                $image_base64 = base64_decode($image_parts[1]);
                $file = $folderPath . $filename;
                file_put_contents($file, $image_base64);

                DB::table('ex_inbound_header')
                    ->where('id', $request->job_id)
                    ->update(
                        [
                            'ttd_' . $typeSignature     => $filename,
                        ]
                    );
                DB::commit();
                $typeSignature = "";
                $header = DB::table('ex_inbound_header')
                    ->where('id', $request->job_id)
                    ->first();
                $typeSignature = "";
                if (is_null($header->ttd_driver)) {
                    $typeSignature = 'driver';
                }
                if (!is_null($header->ttd_driver) and is_null($header->ttd_checker)) {
                    $typeSignature = 'checker';
                }
                if (!is_null($header->ttd_driver) and !is_null($header->ttd_checker)) {
                    $typeSignature = 'done';
                }
                $message = [
                    'message' => 'Data Successfully Saved',
                    'data' => $typeSignature
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

    public function postScanPalletTag(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $segments = explode('||', $request->data);
                $serial_no = $segments[0];
                $job_id = $segments[1];
                $pallet_id = $segments[2];

                $inStock = DB::table('ex_inbound_detail')
                    ->where('serial_no', $serial_no)
                    ->where('job_id', $job_id)
                    ->where('pallet_id', $pallet_id)
                    ->first();
                if (is_null($inStock)) {
                    return 'validate';
                    DB::rollBack();
                } else {
                    if ($inStock->serial_no != $serial_no) {
                        return 'validate';
                        DB::rollBack();
                    } else {
                        $like = explode('-', $serial_no);
                        $serialNo  = $like[1] . '-' . $like[2] . '-' . $like[3];
                        DB::table('ex_inbound_detail')
                            ->where('serial_no', 'LIKE', '%' . $serialNo . '%')
                            ->where('job_id', $job_id)
                            ->where('pallet_id', $pallet_id)
                            ->update([
                                'scan_pallet_tag' => 'Yes'
                            ]);
                        DB::commit();
                        $header = $this->detailHeader($inStock->job_id);
                        $shipper_name = $this->getShipperByid($header->shipper_id);
                        $consignee_name = $this->getConsigneeByid($header->consignee_id);
                        $detail = DB::table("ex_inbound_detail")->where("job_id", $header->id)->get();
                        $qty_actual = $detail->sum('quantity');
                        $total_pallet = $detail->groupBy('pallet_id')->count();
                    }
                }
                $success = ['data' => [
                    'header' => $header,
                    'detail' => $detail,
                    'shipper' => $shipper_name,
                    'consignee_name' => $consignee_name,
                    'qty_actual' => $qty_actual,
                    'total_pallet' => $total_pallet,
                ]];
                return $success;
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
                $location_code = $segments[0];
                $job_id = $segments[1];
                $pallet_id = $segments[2];
                $masterLocation = DB::table('ex_location')
                    ->where('location_code', $location_code)
                    ->where('active', 'Yes')
                    ->first();

                // $inStock = DB::table('ex_inbound_detail')
                //     ->where('job_id', $job_id)
                //     ->where('pallet_id', $pallet_id)
                //     ->first();

                if (is_null($masterLocation)) {
                    return 'validate';
                    DB::rollBack();
                } else {
                    DB::table('ex_inbound_detail')
                        ->where('job_id', $job_id)
                        ->where('pallet_id', $pallet_id)
                        ->update([
                            'scan_pallet_tag'      => 'Yes',
                            'scan_location'        => 'Yes',
                            'location_id'          => $masterLocation->id,
                            'location_code'        => $masterLocation->location_code,
                        ]);

                    $header = $this->detailHeader($job_id);
                    $shipper_name = $this->getShipperByid($header->shipper_id);
                    $detail = DB::table("ex_inbound_detail")->where("job_id", $header->id)->get();
                    $qty_actual = $detail->sum('quantity');
                    $total_pallet = $detail->groupBy('pallet_id')->count();
                    DB::commit();
                }
                $success = ['data' => [
                    'header' => $header,
                    'detail' => $detail,
                    'shipper' => $shipper_name,
                    'qty_actual' => $qty_actual,
                    'total_pallet' => $total_pallet,
                ]];
                return $success;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function detailHeader($id)
    {
        $data = DB::table("ex_inbound_header")
            ->where("id", $id)
            ->first();
        return $data;
    }

    private function getShipperByid($id)
    {
        $data = DB::table("mt_shipper")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('shipper_name');
        return $data;
    }

    private function getConsigneeByid($id)
    {
        $data = DB::table("mt_consignee")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('consignee_name');

        return $data;
    }

    private function isBase64($string)
    {
        // validasi string base64
        return base64_encode(base64_decode($string, true)) === $string;
    }

    private function getListDetail($job_id, $po_number)
    {
        if (!$this->isBase64($po_number)) {
            $po_number = base64_encode($po_number);
        }
        $po_number = base64_decode($po_number);
        $data = DB::table("ex_inbound_detail")
            ->orderBy('id', 'DESC')
            ->where("job_id", $job_id)
            ->where('serial_no', 'LIKE', '%' . $po_number . '%')
            ->get();
        return $data;
    }
}
