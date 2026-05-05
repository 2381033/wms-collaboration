<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Reference\Country as ReferenceCountry;

class CountryController extends Controller
{
    public function index(Request $request) {
        $details = ReferenceCountry::all();

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

        return view('reference.country');
    }

    public function store(Request $request) {
        $messsages = array(
            'country_code.required'=>'Country code cannot be empty.',
            'country_name.required'=>'Country name cannot be empty.',
        );

        $rules = array(
            'country_code' => 'required',
            'country_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        ReferenceCountry::updateOrCreate(['id' => $id],
        [
            'country_code'=>$request->country_code,
            'country_name' => $request->country_name,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id) {
        $where = array('id' => $id);
        $data  = ReferenceCountry::where($where)->first();

        return response()->json($data);
    }

    public function destroy(Request $request) {
        try {
            $data = ReferenceCountry::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
