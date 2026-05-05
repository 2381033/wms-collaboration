<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Site as MasterSite;
use App\Models\Master\SiteType as MasterSiteType;
use App\Models\Master\SiteIndicator as MasterSiteIndicator;
use App\Models\Master\LocationType as MasterLocationType;

class SiteController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        $details = MasterSite::where('company_id', $company_id)->get();

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

        $site_type = MasterSiteType::where('company_id', $company_id)
                        ->where('active', 'Yes')->get();
        $location_type = MasterLocationType::where('active', 'Yes')->get();

        $data = [
            'site_type_list' => $site_type,
            'location_type_list' => $location_type
        ];

        return view('master.site', $data);
    }

    public function indicator(Request $request) {
        $company_id = Auth::user()->company_id;

        $indicator = MasterSiteIndicator::where('company_id', $company_id)
                            ->where('type_id', $request->type_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'indicator_name']);

        $data = [
            'indicator_list'=>$indicator
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'site_name.required'=>'Site name cannot be empty.',
            'type_id.required'=>'Type name cannot be empty.',
            'indicator_id.required'=>'Type name cannot be empty.',
            'location_id.required'=>'Type name cannot be empty.',
        );

        $rules = array(
            'site_name' => 'required',
            'type_id' => 'required',
            'indicator_id' => 'required',
            'location_id' => 'required'
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        MasterSite::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'site_name'=>$request->site_name,
            'type_id' => $request->type_id,
            'indicator_id' => $request->indicator_id,
            'location_id' => $request->location_id,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'phone' => $request->phone,
            'fax' => $request->fax,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $edit  = MasterSite::where('id', $id)->first();

        $company_id = Auth::user()->company_id;

        $indicator = MasterSiteIndicator::where('company_id', $company_id)
                            ->where('type_id', $edit->type_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'indicator_name']);

        $data = [
            'edit_view'=>$edit,
            'indicator_list'=>$indicator
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            MasterSite::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
