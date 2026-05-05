<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Handling as MasterHandling;

class HandlingController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $handling = DB::table("iv_handling as a")
                            ->select("a.*", "b.uom_name")
                            ->join("rt_uom as b", "a.foc", "b.code")
                            ->where("a.principal_id", $request->principal_id)
                            ->get();

            return datatables()->of($handling)
            ->editColumn('cpu', function ($data)
            {
                return number_format($data->cpu, 2, ".", ",");
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-handling btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete-handling btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = MasterHandling::find($request->id);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'job_type.required'=>'Type cannot be empty.',
            'foc_hand.required'=>'Form Of Charge cannot be empty.',
        );

        $rules = array(
            'job_type' => 'required',
            'foc_hand' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->handling_id;

        MasterHandling::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id' => $request->principal_handling,
            'job_type' => $request->job_type,
            'foc'=>$request->foc_hand,
            'cpu'=>$request->cpu_hand,
            'cpu_middle'=>$request->cpu_middle,
            'cpu_lowest'=>$request->cpu_lowest,
            'quota'=>$request->quota_hand,
            'cpu_ext'=>$request->cpu_ext_hand,
            'foc_return'=>$request->foc_return,
            'cpu_return'=>$request->cpu_return,
            'quota_return'=>$request->quota_return,
            'remarks'=>$request->remarks,
            'active' => $request->active_handling
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function destroy(Request $request)
    {
        try {
            MasterHandling::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
