<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Reference\Country as ReferenceCountry;
use App\Models\Reference\City as ReferenceCity;
use App\Models\Reference\Region as ReferenceRegion;

class CityController extends Controller
{
    public function index(Request $request) {
        $details = ReferenceCity::where('country_code', $request->country_code)->get();

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

        $country = ReferenceCountry::where('active', 'Yes')->get();

        $data = [
            'country_list' => $country
        ];

        return view('reference.city', $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'country_code.required'=>'Country name cannot be empty.',
            'region_code.required'=>'Region name cannot be empty.',
            'city_code.required'=>'City code cannot be empty.',
            'city_name.required'=>'City name cannot be empty.',
        );

        $rules = array(
            'country_code' => 'required',
            'region_code' => 'required',
            'city_code' => 'required',
            'city_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        ReferenceCity::updateOrCreate(['id' => $id],
        [
            'country_code'=>$request->country_code,
            'region_code'=>$request->region_code,
            'city_code'=>$request->city_code,
            'city_name' => $request->city_name,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $edit  = ReferenceCity::where($where)->first();

        $region = ReferenceRegion::where('country_code', $edit->country_code)
                            ->where('active', 'Yes')
                            ->get(['region_code', 'region_name']);

        $data = [
            'edit_view'=>$edit,
            'region_list'=>$region
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            $data = ReferenceCity::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
