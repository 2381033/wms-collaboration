<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Inbound\Detail as inboundDetail;
use App\Models\Transaction\Inbound\Job as inboundJob;

class GRNController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = inboundDetail::from('iv_inbound_detail as a')
                ->select('a.*', 'b.product_name')
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->where('a.company_id', $company_id)
                ->where('a.inbound_id', $request->inbound_id)
                ->where('a.received_flag', 'No')
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
                // ->addColumn('check', function ($data) {
                //     return '<input type="checkbox" required="required" name="grn_id[]" class="grn-check" id="' . $data->id . '" value="' . $data->id . '">';
                // })
                // ->addColumn('action', function ($data) {
                //     $button = "";
                //     if ($data->received_flag == 'No') {
                //         $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-grn btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                //     }
                //     return $button;
                // })
                ->rawColumns(['action', 'check'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $qty = ($request->actual_pqty * $request->uppp) + ($request->actual_mqty * $request->muppp) + $request->actual_bqty;

        if ($qty == 0) {
            return response()->json(['error' => ['Quantity cannot be empty!']]);
        }

        $discrepancy_qty = ($request->discrepancy_pqty * $request->uppp) + ($request->discrepancy_mqty * $request->muppp) + $request->discrepancy_bqty;

        if ($discrepancy_qty > 0) {
            $messsages = array(
                'remarks.required' => 'Remarks field is required.',
            );

            $rules = array(
                'remarks' => 'required',
            );

            $validator = \Validator::make($request->all(), $rules, $messsages);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
        }

        $exception = DB::transaction(function () use ($request, $qty, $discrepancy_qty) {
            try {
                $id = $request->packing_id;

                $detail = inboundDetail::find($id);

                $detail->actual_pqty = $request->actual_pqty;
                $detail->actual_mqty = $request->actual_mqty;
                $detail->actual_bqty = $request->actual_bqty;
                $detail->actual_qty = $qty;
                $detail->discrepancy_pqty = $request->discrepancy_pqty;
                $detail->discrepancy_mqty = $request->discrepancy_mqty;
                $detail->discrepancy_bqty = $request->discrepancy_bqty;
                $detail->discrepancy_qty = $discrepancy_qty;
                $detail->remarks = $request->remarks;
                $detail->save();

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $received_by = Auth::user()->username;
            $received_date = \Carbon\Carbon::now();
            try {
                $data = $request->product_code;
                $id = $request->inbound_id;
                if(is_null($data)){
                    DB::rollBack();
                    $message = ['error' => 'Palletize is required!'];
                }else{
                    foreach ($data as $key => $value) {
                        $detail = DB::table('iv_inbound_detail')
                            ->where('inbound_id', $id)
                            ->where('product_code', $request->product_code[$key])
                            ->first();
    
                        DB::table('iv_inbound_detail')
                            ->where('inbound_id', $id)
                            ->where('product_code', $request->product_code[$key])
                            ->update([
                                'received_date' => date('Y-m-d H:i:s'),
                                'received_flag' => 'Yes',
                                'received_by'   => Auth::user()->username
                            ]);
    
                        $job = inboundJob::find($detail->inbound_id);
    
                        if (isset($job)) {
                            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->ata);
                            $ata = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');
    
                            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->unloading_start);
                            $unloading_start = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');
    
                            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->unloading_finish);
                            $unloading_finish = \Carbon\Carbon::parse($dateObject)->format('Y-m-d H:i');
    
                            if (empty($job->ata)) {
                                $job->ata = $ata;
                                $job->unloading_start = $unloading_start;
                                $job->unloading_finish = $unloading_finish;
                                $job->save();
                            }
                        }
    
                        $job->received_flag = 'Yes';
                        $job->received_by = $received_by;
                        $job->received_date = $received_date;
                        $job->save();
                    }
                    DB::commit();
                    $message = ['success' => 'Data Successfully Saved'];
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
}
