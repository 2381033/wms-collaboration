<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Export\InboundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Inbound\Detail as inboundDetails;
use App\Models\Transaction\Inbound\Batch as inboundBatch;
use App\Models\Master\Location as masterLocation;

class CancelController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = inboundDetails::from('iv_inbound_detail as a')
                ->select('a.*', 'b.product_name')
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->where('a.company_id', $company_id)
                ->where('a.inbound_id', $request->inbound_id)
                ->where('a.received_flag', 'Yes')
                ->where('a.putaway_flag', 'Yes')
                ->where('a.confirmed_flag', 'No')
                ->get();

            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    $exp_date = "";
                    if (isset($data->exp_date)) {
                        $exp_date = date('d/m/Y', strtotime($data->exp_date));
                    }
                    return $exp_date;
                })
                ->editColumn('mfg_date', function ($data) {
                    $mfg_date = "";
                    if (isset($data->mfg_date)) {
                        $mfg_date = date('d/m/Y', strtotime($data->mfg_date));
                    }
                    return $mfg_date;
                })
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox" required="required" name="cancel_id[]" class="cancel-check" id="' . $data->id . '" value="' . $data->id . '">';
                })
                ->rawColumns(['check'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $user_id = Auth::user()->id;
                $data = $request->cancel_id;

                foreach ($data as $id) {
                    $batchin = inboundBatch::where('packing_id', $id)->get();

                    $count = $batchin->count();

                    for ($i = 0; $i < $count; $i++) {
                        if ($batchin[$i]->confirmed_flag == 'Yes') {
                            $message = ['error' => 'Can not be continued, because there are data that have been processed.'];

                            return $message;
                        }

                        if ($batchin[$i]->manual_putaway == 'No') {
                            $site = DB::table("iv_site as a")
                                ->select("a.*", "b.type_name")
                                ->leftjoin("iv_site_type as b", "a.type_id", "b.id")
                                ->join('users_site as c', 'a.id', 'c.site_id')
                                ->where('c.user_id', $user_id)
                                ->where("a.id", $batchin[$i]->site_id)
                                ->first();

                            if (isset($site)) {
                                if ($site->type_name !== "Bulk") {
                                    $location = masterLocation::find($batchin[$i]->location_id);

                                    if ($location->status_code == 'R') {
                                        $location->status_code = 'E';
                                        $location->save();
                                    }
                                }
                            } else {
                                $message = ['error' => 'User site is not define.'];

                                return $message;
                            }
                        }

                        inboundBatch::where('id', $batchin[$i]->id)->delete();
                        DB::table('iv_inbound_per_pallet')->where('picking_id', $id)->delete();
                        DB::table('iv_inbound_detail')->where('id', $id)->delete();
                        DB::table('iv_inbound_batch')->where('packing_id', $id)->delete();
                    }

                    // $detail = inboundDetails::find($id);                   

                    // $detail->putaway_flag = 'No';
                    // $detail->save();
                }
                //delete data on palletize
                DB::table('iv_inbound_per_pallet')
                ->whereIn('picking_id', $data)
                ->delete(); 

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

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
