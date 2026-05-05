<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;

class DetailController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $stock = DB::table("ex_outbound_detail as a")
                        ->where("a.order_id", $request->order_id)
                        ->get();

            return datatables()->of($stock)
            ->addColumn('check', function ($data) {
                if ( $data->status_flag == "Open" ) {
                    return '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
                }

                return "";
            })
            ->rawColumns(['check'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = $request->confirm_id;

                $job = ExportOutboundHeader::find($request->job_id);
                $order = ExportOutboundOrder::find($request->order_id);

                foreach ($data as $id) {
                    $detail = ExportOutboundDetail::find($id);

                    $detail->status_flag = 'Confirmed';
                    $detail->save();
                }

                $detail_count = ExportOutboundDetail::where("job_id", $request->job_id)
                                    ->where("order_id", $request->order_id)
                                    ->where("status_flag", "Open")
                                    ->count();

                if ( $detail_count == 0 ) {
                    $order->status_flag = "Full";
                    $order->save();
                }

                $order_count = ExportOutboundOrder::where("job_id", $request->job_id)
                                    ->where("status_flag", "Open")
                                    ->count();

                if ( $order_count == 0 ) {
                    $job->status_flag = "Confirmed";
                    $job->save();
                }

                DB::commit();

                $message = ['success'=>'Data Successfully Saved'];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
