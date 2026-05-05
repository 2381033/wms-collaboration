<?php

namespace App\Http\Controllers\Master\CY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\InvoiceType as MasterInvoiceType;

class InvoiceTypeController extends Controller
{
    public function index (Request $request) {
        $company_id = Auth::user()->company_id;

        $details = MasterInvoiceType::where("company_id", $company_id)->get();

        if ($request->ajax()) {
            return datatables()->of($details)
            ->editColumn('active', function ($data) {
                if ($data->active == 'Yes') {
                    $status = '<div class="btn btn-sm btn-success"><i class="fas fa-check"></i><span> ' . $data->active . '</span></div>';
                } else {
                    $status = '<div class="btn btn-sm btn-warning"><i class="fas fa-trash"></i><span> ' . $data->active . '</span></div>';
                }
                return $status;
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-invoicetype btn btn-info btn-sm edit-group"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'type_name.required'=>'Description cannot be empty.',
        );

        $rules = array(
            'type_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $company_id = Auth::user()->company_id;

        $id = $request->type_id;

        MasterInvoiceType::updateOrCreate(['id' => $id],
        [
            'company_id' => $company_id,
            'type_name' => $request->type_name,
            'invoice_flag' => $request->invoice_flag,
            'free_flag' => $request->free_flag,
            'free_storage' => $request->free_storage,
            'active' => $request->type_active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = MasterInvoiceType::where('id', $id)->first();

        return response()->json($data);
    }
}
