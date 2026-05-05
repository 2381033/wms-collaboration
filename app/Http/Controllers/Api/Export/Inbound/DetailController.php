<?php

namespace App\Http\Controllers\Api\Export\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;

class DetailController extends Controller
{
    public function storeFotoCargo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $file = $request->file('photo');
                $random = Str::random(6);
                $filename = 'cargo-' . $request->job_no . "-" . $random . "." . $file->getClientOriginalExtension();
                $file->move(public_path('foto/warehouse-export/inbound-cargo/'), $filename);
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
            'weight' => 'required',
            'unit' => 'required',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' =>  'validate']);
        }
        $exception = DB::transaction(function () use ($request) {
            try {
                $serial_no = $request->po_number . "-" . $request->peb_no . "-" . Str::of($request->pallet_id)->padLeft(2, '0');
                for($i = 0; $i < $request->jumlah_dimensi; $i++)
                {
                    DB::table('ex_inbound_detail')
                        ->insert(
                            [
                                'serial_no'   => $serial_no,
                                'job_id'      => $request->job_id,
                                'pallet_id'   => $request->pallet_id,
                                'quantity'    => $request->qty[$i],
                                'length'      => $request->length[$i],
                                'width'       => $request->width[$i],
                                'height'      => $request->height[$i],
                                'weight'      => $request->weight[$i],
                                'unit'        => $request->unit[$i],
                                'user_id'     => $request->user_id,
                                'created_at'  => date('Y-m-d H:i:s'),
                            ]
                        );
                }
                DB::commit();
                $data = DB::table('ex_inbound_detail')
                    ->where('job_id', $request->job_id)
                    ->groupBy('pallet_id')
                    ->orderBy('id', 'DESC')
                    ->get();

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

    public function getFotoCargo($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->where('job_id', $job_id)
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

    public function getDetailCargo($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_inbound_detail')
                    ->selectRaw('*, sum(quantity) as qty_total, count(id) as palletize')
                    ->where('job_id', $job_id)
                    ->orderBy('id', 'ASC')
                    ->groupBy('pallet_id')
                    ->get();

                $qty = DB::table('ex_inbound_detail')->where('job_id', $job_id)->sum('quantity');
                $total_pallet = $data->count();
                
                $detailPallet = $this->getListDetail($job_id)->first();
                $header = DB::table('ex_inbound_header')->where('id', $job_id)->first();
                $message = [
                    'message' => 'Data Successfully Saved',
                    'detailPallet' => $detailPallet,
                    'qty' => $qty,
                    'data' => $data,
                    'header' => $header,
                    'totalPallet' => $total_pallet
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

    public function deletePallet($job_id, $id_detail)
    {
        $exception = DB::transaction(function () use ($job_id, $id_detail) {
            try {
                  DB::table('ex_inbound_detail')
                    ->where('id', $id_detail)
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

    public function perkalianPallet($job_id, $id_detail, $perkalian)
    {
        $exception = DB::transaction(function () use ($job_id, $id_detail, $perkalian) {
            try {
                $header = $this->detailHeader($job_id);
                $object = $this->getListDetail($job_id)->where('id', $id_detail)->first();
                for($i = 0; $i < $perkalian; $i++){
                    $last   = $this->getListDetail($job_id)->first();
                    
                    $pallet_id = $last->pallet_id + 1;
                    
                    $serial_no = $header->po_number . "-" . $header->peb_no . "-" . Str::of($pallet_id)->padLeft(2, '0');

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
                    'object' => $object
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

    public function postScanPalletTag(Request $request){
        $exception = DB::transaction(function () use ($request) {
            try {
                $inStock = DB::table('ex_inbound_detail')
                ->where('serial_no', $request->serial_no)
                ->where('job_id', $request->job_id)
                ->where('pallet_id', $request->pallet_id)
                ->first();
                if(is_null($inStock)){
                    return 'validate';
                    DB::rollBack();
                }else{
                    if($inStock->serial_no != $request->serial_no){
                        return 'validate';
                        DB::rollBack();
                    }else{
                        DB::table('ex_inbound_detail')
                        ->where('serial_no', $request->serial_no)
                        ->where('job_id', $request->job_id)
                        ->where('pallet_id', $request->pallet_id)
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

    public function postScanLocation(Request $request){
        $exception = DB::transaction(function () use ($request) {
            try {
                $masterLocation = DB::table('ex_location')
                            ->where('location_code', $request->location_code)
                            ->where('active', 'Yes')
                            ->first();

                $inStock = DB::table('ex_inbound_detail')
                        ->where('id', $request->id_detail)
                        ->first();

                if(is_null($masterLocation)){
                    return 'validate';
                    DB::rollBack();
                }else{
                    DB::table('ex_inbound_detail')
                    ->where('id', $request->id_detail)
                    ->update([
                        'scan_pallet_tag'      => 'Yes',
                        'scan_location'        => 'Yes',
                        'location_id'          => $masterLocation->id,
                        'location_code'        => $masterLocation->location_code,
                    ]);

                    DB::table('ex_inbound_detail')
                    ->where('serial_no', $inStock->serial_no)
                    ->update([
                        'scan_pallet_tag'      => 'Yes',
                        'scan_location'        => 'Yes',
                        'location_id'          => $masterLocation->id,
                        'location_code'        => $masterLocation->location_code,
                    ]);

                    $header = $this->detailHeader($inStock->job_id);
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

    private function detailHeader($id){
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

    private function getListDetail($job_id)
    {
        $data = DB::table("ex_inbound_detail")
        ->orderBy('id', 'DESC')
        ->where("job_id", $job_id)
        ->get();
        return $data;
    }
}
