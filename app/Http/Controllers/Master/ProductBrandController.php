<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\ProductBrand as MasterProductBrand;
use App\Models\Master\ProductGroup as MasterProductGroup;

class ProductBrandController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        $details = MasterProductBrand::where('company_id', $company_id)
                    ->where('principal_id', $request->principal_id)
                    ->get();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('master.product-brand');
    }

    public function group(Request $request) {
        $company_id = Auth::user()->company_id;

        $product_group = MasterProductGroup::where('company_id', $company_id)
                            ->where('principal_id', $request->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'group_name']);

        $data = [
            'group_list'=>$product_group,
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'group_id.required'=>'Group name cannot be empty.',
            'brand_code.required'=>'Brand code cannot be empty.',
            'brand_name.required'=>'Brand name cannot be empty.',
        );

        $rules = array(
            'group_id' => 'required',
            'brand_code' => 'required',
            'brand_name' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        MasterProductBrand::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id' => $request->principal_id,
            'group_id'=>$request->group_id,
            'brand_code'=>$request->brand_code,
            'brand_name'=>$request->brand_name,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $company_id = Auth::user()->company_id;
        $brand  = MasterProductBrand::where('id', $id)->first();

        $product_group = MasterProductGroup::where('company_id', $company_id)
                            ->where('principal_id', $brand->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'group_name']);

        $data = [
            'job'=>$brand,
            'group_list'=>$product_group,
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            MasterProductBrand::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
