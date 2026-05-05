<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;

class GateInController extends Controller
{
    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $type = $request->type;
                $file = $request->file('photo');
                $random = Str::random(6);
                $filename = 'gate-in-' . $type . '-' . $request->vehicle_number . "-" .$random. "-" . date('Y-m-d') . "." . $file->getClientOriginalExtension();
                $file->move(public_path('foto/warehouse-export/gate-in-' . $type . '/'), $filename);
                if ($type == 'cargo') {
                    DB::table('ex_gate_in_cargo')
                        ->insert(
                            [
                                'vehicle_number'   => $request->vehicle_number,
                                'vehicle_type'     => $request->vehicle_type,
                                'driver_name'      => $request->driver_name,
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
