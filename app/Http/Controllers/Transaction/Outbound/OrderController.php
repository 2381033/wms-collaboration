<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Models\Transaction\Stock\Ledger as stockLedger;
use App\Models\Master\Location as masterLocation;
use App\Models\Transaction\Outbound\Job as outboundJob;

use App\Models\Transaction\Outbound\Despatch as outboundDespatch;
use App\Models\Transaction\Outbound\Order as outboundOrder;



class OrderController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = outboundOrder::from('iv_outbound_order as a')
                ->select('a.*', 'b.customer_code', 'b.customer_name')
                ->join('iv_customer as b', 'a.customer_id', 'b.id')
                ->where('a.company_id', $company_id)
                ->where('a.outbound_id', $request->outbound_id)
                ->get();

            return datatables()->of($list_data)
                ->editColumn('order_date', function ($data) {
                    return date('d/m/Y', strtotime($data->order_date));
                })
                ->editColumn('due_date', function ($data) {
                    return date('d/m/Y', strtotime($data->due_date));
                })
                ->addColumn('ean_code_count', function ($data) {
                    if (is_null($data->manufactur_code)) {
                        $countingActual = $data->pqty;
                    } else {
                        $countingActual = $data->ean_code_count;
                    }
                    return $countingActual;
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    if ($data->confirmed_flag == 'No') {
                        if (Gate::allows('gate-access', "warehouse/outbound")) {
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-order btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                            $button .= '&nbsp;&nbsp;';
                            $button .= '<button type="button" id="' . $data->id . '" class="delete-order btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table("iv_outbound_order as a")
            ->select("a.*", "b.customer_name")
            ->join("iv_customer as b", "a.customer_id", "b.id")
            ->where("a.id", $request->id)
            ->first();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->username;
        $outbound_id = $request->outbound_order;

        if ($outbound_id > 0) {
            $job_status = outboundJob::find($outbound_id);

            if ($job_status->allocated_flag == 'Yes') {
                return response()->json(['error' => ['Job Allocated.']]);
            }
        }

        $messsages = array(
            'customer_id.required' => 'Customer name field is required.',
            'po_number.required' => 'PO number field is required.',
            'order_no.required' => 'Order number field is required.',
            'order_date.required' => 'Order date field is required.',
            'due_date.required' => 'Due date field is required.',
        );

        $rules = array(
            'customer_id' => 'required',
            'po_number' => 'required',
            'order_no' => 'required',
            'order_date' => 'required',
            'due_date' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->order_id;
        $company_id = Auth::user()->company_id;

        $job = outboundJob::find($outbound_id);

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->order_date);
        $order_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->due_date);
        $due_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $data   =   outboundOrder::updateOrCreate(
            ['id' => $id],
            [
                'company_id' => $company_id,
                'principal_id' => $job->principal_id,
                'outbound_id' => $outbound_id,
                'job_no' => $job->job_no,
                'customer_id' => $request->customer_id,
                'order_no' => $request->order_no,
                'po_number' => $request->po_number,
                'order_date' => $order_date,
                'due_date' => $due_date,
                'user_id' => $user_id
            ]
        );

        return response()->json(['success' => $data]);
    }

    public function destroy(Request $request)
    {
        try {
            outboundOrder::where('id', $request->id)->delete();

            $data = ['success' => 'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex) {
            $data = ['error' => 'Cannot be deleted, this data is already used.'];
        }

        return response()->json($data);
    }
}
