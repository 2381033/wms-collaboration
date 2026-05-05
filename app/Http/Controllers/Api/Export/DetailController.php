<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;

class DetailController extends Controller
{
    public function index($id) {
        $list = DB::table("ex_outbound_detail as a")
                    ->select( "a.*")
                    ->where("a.status_flag", "Open")
                    ->where("a.order_id", $id)
                    ->get();

        $response = Array();

        foreach ($list as $value) {
            $response[] = [
                "id"=>$value->id,
                "serial_no"=>$value->serial_no,
                "po_number"=>$value->po_number,
                "peb_no"=>$value->peb_no,
                "quantity"=>$value->quantity,
            ];
        }

        return response()->json(['pesan' => 'Berhasil', 'job' => $response], 200);
    }

    public function view (Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $user = \App\User::find($request->user_id)->username;

                $job = ExportOutboundHeader::find($request->job_id);

                if ( $job->user_process !== $user && $job->user_process !== null ) {
                    $message = ['error'=> 'false', 'message'=>"User harus dengan user yang sama!!!"];

                    return $message;
                }

                $detail = ExportOutboundDetail::where("serial_no", trim($request->serial_no))->first();

                $order = ExportOutboundOrder::find($detail->order_id);

                if ( $job->id !== $detail->job_id ) {
                    $error = 'false';
                    $message = "reject";
                } else {
                    if ( $detail->status_flag == "Confirmed" ) {
                        $error = 'false';
                        $message = "Scanned Done";
                    } else {
                        $detail->status_flag = "Confirmed";
                        $detail->save();

                        $error = 'false';
                        $message = "success";

                        $detail_count = ExportOutboundDetail::where("job_id", $request->job_id)->where("order_id", $detail->order_id)->where("status_flag", "Open")->count();

                        $job->user_process = $user;
                        $job->save();

                        if ( $detail_count == 0 ) {
                            $order->status_flag = "Full";
                            $order->save();

                            // $order_count = ExportOutboundOrder::where("job_id", $request->job_id)->where("status_flag", "Open")->count();

                            // if ( $order_count == 0 ) {
                            //     $job->status_flag = "Confirmed";
                            //     $job->save();
                            // }
                        }
                    }
                }

                DB::commit();

                $message = ['error'=> $error, 'message'=>$message];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=> true, 'message'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
