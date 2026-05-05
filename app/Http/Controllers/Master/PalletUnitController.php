<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Product as MasterProduct;
use App\Models\Master\PalletUnit as MasterPalletUnit;

class PalletUnitController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $details = MasterPalletUnit::from('iv_pallet_unit as a')
                        ->select('a.*', 'b.description')
                        ->join('iv_location_type as b', 'a.type_id', 'b.id')
                        ->where('a.company_id', $company_id)
                        ->where('a.product_id', $request->product_id)
                        ->get();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-pallet btn btn-info btn-sm"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" id="'.$data->id.'" class="delete-pallet btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'type_id.required'=>'Location type cannot be empty.',
            'pallet_qty.required'=>'Pallet quantity cannot be empty.',
        );

        $rules = array(
            'type_id' => 'required',
            'pallet_qty' => 'required|integer'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $company_id = Auth::user()->company_id;
        $id = $request->pallet_id;

        $product = MasterProduct::find($request->product_unit);

        $base_qty = $product->uppp * $request->pallet_qty;

        MasterPalletUnit::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id'=>$product->principal_id,
            'product_id'=>$product->id,
            'type_id'=>$request->type_id,
            'pallet_qty' => $request->pallet_qty,
            'uom' => $product->puom,
            'base_qty' => $base_qty,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function destroy(Request $request)
    {
        try {
            MasterPalletUnit::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

    public function edit($id) {
        $data = MasterPalletUnit::from('iv_pallet_unit as a')
            ->select('a.*', 'b.description')
            ->join('iv_location_type as b', 'a.type_id', 'b.id')
            ->where('a.id', $id)
            ->first();

        return response()->json($data);
    }
}
