<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use phpDocumentor\Reflection\Types\Null_;

use App\Models\Transaction\Outbound\Despatch as outboundDespatch;

class DespatchController extends Controller
{
    public function index(Request $request)
    {

        $company_id = Auth::user()->company_id;
        if ($request->ajax()) {
            $list_data = DB::table("iv_outbound_despatch as a")
                ->select("a.*", "b.customer_name", "c.mode_name")
                ->join("iv_customer as b", "a.customer_id", "b.id")
                ->join("iv_mode as c", "a.mode_id", "c.id")
                ->where("a.company_id", $company_id)
                ->where("a.outbound_id", $request->outbound_id)
                ->get();
            $list_data->map(function ($value) {
                $value->expected_qty = DB::table("iv_outbound_detail")
                    ->where("company_id", $value->company_id)
                    ->where("outbound_id", $value->outbound_id)
                    ->sum('qty');
                return $value;
            });

            // dd($list_data);

            return datatables()->of($list_data)
                ->editColumn('etd', function ($data) {
                    return date('d/m/Y', strtotime($data->etd));
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    if (Gate::allows('gate-access', "warehouse/outbound")) {
                        $button = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-despatch btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                    }
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Print" class="print-despatch btn btn-warning btn-sm"><i class="fas fa-print"></i></a>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table("iv_outbound_despatch as a")
            ->select("a.*", "b.customer_name", "c.store_name", "c.address1", "c.address2", "c.address3", "c.address4")
            ->join("iv_customer as b", "a.customer_id", "b.id")
            ->leftJoin("tm_store as c", "a.store_id", "c.id")
            ->where("a.id", $request->id)
            ->first();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id = $request->despatch_id;

                $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->etd_despatch);
                $etd = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

                $awb_date = null;
                $send_date_doc = null;

                if (isset($request->awb_date) && !empty(isset($request->awb_date))) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->awb_date);
                    $awb_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                if (isset($request->send_date_doc) && !empty(isset($request->send_date_doc))) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->send_date_doc);
                    $send_date_doc = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                outboundDespatch::updateOrCreate(
                    ['id' => $id],

                    [
                        'store_id' => $request->store_id,
                        'reference_no' => $request->ref_no,
                        'carrier_name' => $request->carrier_name,
                        'vessel_name' => $request->vessel_name,
                        'vehicle_no' => $request->vehicle_no,
                        'driver_name' => $request->driver_name,
                        'phone' => $request->phone,
                        'seal_no' => $request->seal_no,
                        'size_id' => $request->size_id,
                        'container_no' => $request->container_no,
                        'etd' => $etd,
                        'awb_no' => $request->awb_no,
                        'awb_date' => $awb_date,
                        'send_date_doc' => $send_date_doc,
                        'delivery_type' => $request->delivery_type,
                    ]
                );

                // dd($size_id);

                DB::commit();
                $message = ['success' => 'Done'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
