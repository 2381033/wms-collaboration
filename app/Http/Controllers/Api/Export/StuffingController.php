<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;

class StuffingController extends Controller
{
    public function index($username)
    {
        $exception = DB::transaction(function () use ($username) {
            try {
                $data = $this->getHeader($username);
                $data = ['data' => $data];
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getHeader($username)
    {
        $data = DB::table('ex_outbound_header as a')
            ->select(
                'a.id',
                'a.job_no',
                'a.container_no',
                'a.destination',
                'a.total_pallet',
                'a.cbm',
                'b.forwarder_name',
                )
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->where('a.branch_id', $this->myBranch($username))
            ->where('status_flag', 'Open')
            ->get();
            $data = $data->map(function($value){
                $value->total_pallet = DB::table('ex_outbound_order')
                ->where('job_id', $value->id)
                ->count();
                $value->qty_cargo = DB::table('ex_outbound_order')
                ->where('job_id', $value->id)
                ->sum('qty_cargo');
                return $value;
            });
            
        return $data;
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

    public function getList($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_outbound_order')
                    ->where('job_id', $job_id)
                    ->get();

                $data = $data->map(function($value){
                    // $value->qty_cargo = DB::table('ex_outbound_detail')
                    // ->where('order_id', $value->id)
                    // ->value('quantity');
                    $value->consignee = DB::table('mt_consignee')
                    ->where('id', $value->consignee_id)
                    ->value('consignee_name');
                    return $value;
                });
                return $data;
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
