<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Storage as MasterStorage;
use App\Models\Reference\Currency as ReferenceCurrency;

class StorageController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $storage = DB::table("iv_storage as a")
                            ->select("a.*", "b.uom_name", "c.currency_name")
                            ->join("rt_uom as b", "a.foc", "b.code")
                            ->join("rt_currency as c", "a.currency_code", "c.currency_code")
                            ->where("a.principal_id", $request->principal_id)
                            ->get();

            return datatables()->of($storage)
            ->editColumn('cpu', function ($data)
            {
                return number_format($data->cpu, 2, ".", ",");
            })
            ->editColumn('quota', function ($data)
            {
                return number_format($data->quota, 2, ".", ",");
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-storage btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete-storage btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = MasterStorage::find($request->id);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'foc.required'=>'Form Of Charge cannot be empty.',
            'currency_id.required'=>'Currency cannot be empty.',
        );

        $rules = array(
            'foc' => 'required',
            'currency_id' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->storage_id;

        $currency = ReferenceCurrency::find($request->currency_id);

        MasterStorage::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id' => $request->principal_storage,
            'foc'=>$request->foc,
            'currency_id'=>$request->currency_id,
            'currency_code'=>$currency->currency_code,
            'cpu'=>$request->cpu,
            'quota'=>$request->quota,
            'cpu_ext'=>$request->cpu_ext,
            'flat_rate'=>$request->flat_rate,
            'remarks'=>$request->remarks,
            'active' => $request->active_storage
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function destroy(Request $request)
    {
        try {
            MasterStorage::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
