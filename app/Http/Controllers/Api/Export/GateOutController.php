<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;

class GateOutController extends Controller
{
    public function outstandingGateOut(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $type = $request->type;
                $username = $request->username;
                $data = DB::table('ex_gate_in_' . $type)
                    ->where('confirmed_flag', 'No')
                    ->where('created_by', $username)
                    ->get();
                $data = $data->map(function ($value) {
                    $value->checker_flag = $this->getHeaderByVehicle($value->vehicle_number)->checker_flag ?? 'Open';
                    $value->job_id = $this->getHeaderByVehicle($value->vehicle_number)->id ?? 0;
                    return $value;
                });
                $message = ['data' => $data];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => true, 'message' => [$e->getMessage()]];

                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getHeaderByVehicle($vehicle_no)
    {
        $data = DB::table('ex_inbound_header')
            ->where('vehicle_no', $vehicle_no)
            ->where('checker_flag', 'Confirmed')
            ->first();
        return $data;
    }

    public function getFoto($id_gate_in)
    {
        $exception = DB::transaction(function () use ($id_gate_in) {
            try {
                $data = DB::table('ex_gate_out_cargo')
                    ->where('id_gate_in', $id_gate_in)
                    ->get();
                $image = [];
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $image[] = [
                            'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/gate-out-cargo/' . $value->file)))
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

    public function closedJob($id_gate_in)
    {
        $exception = DB::transaction(function () use ($id_gate_in) {
            try {
                DB::table('ex_gate_in_cargo')
                    ->where('id', $id_gate_in)
                    ->update([
                        'confirmed_flag' => 'Yes'
                        ]);
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

    public function storeFoto(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $type = $request->type;
                $file = $request->file('photo');
                $random = Str::random(6);
                $filename = 'gate-out-' . $type . '-' . $request->vehicle_number . "-" . $random . "-" . date('Y-m-d') . "." . $file->getClientOriginalExtension();
                $file->move(public_path('foto/warehouse-export/gate-out-' . $type . '/'), $filename);
                if ($type == 'cargo') {
                    DB::table('ex_gate_in_cargo')
                        ->where('id', $request->id_gate_in)
                        ->update(['confirmed_flag'   => 'Yes']);

                    DB::table('ex_gate_out_cargo')
                        ->insert(
                            [
                                'id_gate_in'       => $request->id_gate_in,
                                'created_at'       => date('Y-m-d H:i:s'),
                                'created_by'       => $request->created_by,
                                'confirmed_flag'   => 'Yes',
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
}
